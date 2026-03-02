<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\PengajuanPengadaan;
use App\Models\Satuan;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\StreamReader;

class PengajuanPengadaanController extends Controller
{
    public function __construct()
    {
        Gate::define('create-pengajuan', function ($user) {
            return $user->role === 'laboran';
        });
    }

    public function index()
    {
        $user = Auth::user();
        $query = PengajuanPengadaan::with('programStudi')->latest();

        if ($user->role === 'laboran') {
            $query->where('id_user', $user->id);
        }

        $pengajuans = $query->get();
        return view('pengajuan-pengadaan.index', compact('pengajuans'));
    }

    public function create()
    {
        $this->authorize('create-pengajuan');
        $user = Auth::user();

        $bahans = Bahan::where('id_program_studi', $user->id_program_studi)
            ->with('satuanRel:id,nama_satuan')
            ->orderBy('nama_bahan')
            ->get();

        $satuans = Satuan::orderBy('nama_satuan')->get();
        $bahanOptions = $bahans->map(function ($b) {
            return [
                'id' => $b->id,
                'text' => $b->nama_bahan,
                'stock' => $b->jumlah_stock,
                'stock_text' => $b->formatted_stock,
                'satuan' => $b->satuanRel->nama_satuan ?? '-',
            ];
        })->values();

        return view('pengajuan-pengadaan.create', compact('bahans', 'satuans', 'bahanOptions'));
    }

    public function store(Request $request)
    {
        $this->authorize('create-pengajuan');
        $this->validatePengajuan($request);

        try {
            DB::transaction(function () use ($request) {
                $user = Auth::user();
                $status = $request->action === 'submit' ? 'Diajukan' : 'Draft';

                $pengajuan = PengajuanPengadaan::create([
                    'id_user' => $user->id,
                    'id_program_studi' => $user->id_program_studi,
                    'tahun_ajaran' => $request->tahun_ajaran,
                    'semester' => $request->semester,
                    'status' => $status,
                ]);

                $this->persistItems($pengajuan, $request->items, $user->id_program_studi);
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan pengajuan: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('pengajuan-pengadaan.index')->with('success', 'Pengajuan berhasil disimpan.');
    }

    public function show(PengajuanPengadaan $pengajuanPengadaan)
    {
        $pengajuanPengadaan->load(['user', 'programStudi', 'details.bahan.satuanRel', 'details.satuan']);
        return view('pengajuan-pengadaan.show', compact('pengajuanPengadaan'));
    }

    public function cetakNotaDinas(PengajuanPengadaan $pengajuanPengadaan)
    {
        $pengajuanPengadaan->load(['user', 'programStudi', 'details.bahan', 'details.satuan']);

        $itemsPerPage = 20;
        $jumlah_lampiran_angka = (int) ceil(count($pengajuanPengadaan->details) / $itemsPerPage);

        if (class_exists('NumberFormatter')) {
            $formatter = new \NumberFormatter('id', \NumberFormatter::SPELLOUT);
            $jumlah_lampiran = ucwords($formatter->format(max(1, $jumlah_lampiran_angka)));
        } else {
            $jumlah_lampiran = (string) max(1, $jumlah_lampiran_angka);
        }

        $logo_base64 = base64_encode(@file_get_contents(public_path('images/logo.png'))) ?: '';

        $data = [
            'pengajuan'       => $pengajuanPengadaan,
            'jumlah_lampiran' => $jumlah_lampiran,
            'logo_base64'     => $logo_base64,
        ];

        try {
            $pdfPortrait = app('dompdf.wrapper')
                ->loadView('pengajuan-pengadaan.pdf.nota_dinas_portrait', $data)
                ->setPaper('a4', 'portrait')
                ->output();

            $pdfLandscape = app('dompdf.wrapper')
                ->loadView('pengajuan-pengadaan.pdf.nota_dinas_lampiran_landscape', $data)
                ->setPaper('a4', 'landscape')
                ->output();
        } catch (\Throwable $e) {
            return redirect()->route('pengajuan-pengadaan.show', $pengajuanPengadaan)
                ->with('error', 'Fitur cetak PDF belum tersedia/konfigurasi bermasalah: ' . $e->getMessage());
        }

        $final = new Fpdi();

        foreach ([$pdfPortrait, $pdfLandscape] as $pdfString) {
            $pageCount = $final->setSourceFile(StreamReader::createByString($pdfString));
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $tpl = $final->importPage($pageNo);
                $size = $final->getTemplateSize($tpl);
                $final->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $final->useTemplate($tpl);
            }
        }

        $output = $final->Output('S');

        return response($output, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="nota-dinas-' . $pengajuanPengadaan->id . '.pdf"');
    }

    public function destroy(PengajuanPengadaan $pengajuanPengadaan)
    {
        if (Auth::id() !== $pengajuanPengadaan->id_user || $pengajuanPengadaan->status !== 'Draft') {
            abort(403, 'AKSI TIDAK DIIZINKAN.');
        }

        $pengajuanPengadaan->delete();

        return redirect()->route('pengajuan-pengadaan.index')->with('success', 'Pengajuan berhasil dihapus.');
    }

    public function edit(PengajuanPengadaan $pengajuanPengadaan)
    {
        if (Auth::id() !== $pengajuanPengadaan->id_user || $pengajuanPengadaan->status !== 'Draft') {
            abort(403, 'AKSI TIDAK DIIZINKAN.');
        }

        $pengajuanPengadaan->load('details');

        $user = Auth::user();
        $bahans = Bahan::where('id_program_studi', $user->id_program_studi)
            ->with('satuanRel:id,nama_satuan')
            ->orderBy('nama_bahan')
            ->get();
        $satuans = Satuan::orderBy('nama_satuan')->get();
        $bahanOptions = $bahans->map(function ($b) {
            return [
                'id' => $b->id,
                'text' => $b->nama_bahan,
                'stock' => $b->jumlah_stock,
                'stock_text' => $b->formatted_stock,
                'satuan' => $b->satuanRel->nama_satuan ?? '-',
            ];
        })->values();

        $detailItemsForJs = $pengajuanPengadaan->details->map(function ($d) {
            return [
                'item_ref' => $d->id_bahan ?: $d->nama_barang_input,
                'spesifikasi' => $d->spesifikasi,
                'jumlah' => $d->jumlah,
                'id_satuan' => $d->id_satuan,
                'harga_satuan' => $d->harga_satuan,
                'link_referensi' => $d->link_referensi,
            ];
        })->values();

        return view('pengajuan-pengadaan.edit', compact('pengajuanPengadaan', 'bahans', 'satuans', 'bahanOptions', 'detailItemsForJs'));
    }

    public function update(Request $request, PengajuanPengadaan $pengajuanPengadaan)
    {
        if (Auth::id() !== $pengajuanPengadaan->id_user || $pengajuanPengadaan->status !== 'Draft') {
            abort(403, 'AKSI TIDAK DIIZINKAN.');
        }

        $this->validatePengajuan($request);

        try {
            DB::transaction(function () use ($request, $pengajuanPengadaan) {
                $status = $request->action === 'submit' ? 'Diajukan' : 'Draft';

                $pengajuanPengadaan->update([
                    'tahun_ajaran' => $request->tahun_ajaran,
                    'semester' => $request->semester,
                    'status' => $status,
                ]);

                $pengajuanPengadaan->details()->delete();
                $this->persistItems($pengajuanPengadaan, $request->items, $pengajuanPengadaan->id_program_studi);
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui pengajuan: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('pengajuan-pengadaan.index')->with('success', 'Pengajuan berhasil diperbarui.');
    }


    public function ajukanFinal(PengajuanPengadaan $pengajuanPengadaan)
    {
        if (Auth::id() !== $pengajuanPengadaan->id_user || $pengajuanPengadaan->status !== 'Draft') {
            abort(403, 'AKSI TIDAK DIIZINKAN.');
        }

        if (! $pengajuanPengadaan->details()->exists()) {
            return redirect()->route('pengajuan-pengadaan.show', $pengajuanPengadaan)
                ->with('error', 'Pengajuan tidak bisa diajukan karena belum memiliki item.');
        }

        DB::transaction(function () use ($pengajuanPengadaan) {
            $pengajuanPengadaan->update(['status' => 'Diajukan']);
            $pengajuanPengadaan->details()->update([
                'status_item' => 'diajukan',
                'approved_jumlah' => DB::raw('jumlah'),
                'catatan_revisi' => null,
            ]);
        });

        return redirect()->route('pengajuan-pengadaan.show', $pengajuanPengadaan)
            ->with('success', 'Pengajuan berhasil diajukan dan siap direview Fakultas.');
    }


    public function realisasiStokMasuk(PengajuanPengadaan $pengajuanPengadaan)
    {
        if (Auth::id() !== $pengajuanPengadaan->id_user || $pengajuanPengadaan->status !== 'Disetujui') {
            abort(403, 'AKSI TIDAK DIIZINKAN.');
        }

        $pengajuanPengadaan->load('details');
        $eligibleDetails = $pengajuanPengadaan->details->filter(function ($detail) {
            return !is_null($detail->id_bahan)
                && in_array($detail->status_item, ['disetujui', 'disetujui_sebagian'])
                && (float) $detail->approved_jumlah > 0;
        });

        if ($eligibleDetails->isEmpty()) {
            return redirect()->route('pengajuan-pengadaan.show', $pengajuanPengadaan)
                ->with('error', 'Tidak ada item existing dengan jumlah disetujui untuk direalisasikan ke stok.');
        }

        DB::transaction(function () use ($eligibleDetails, $pengajuanPengadaan) {
            foreach ($eligibleDetails as $detail) {
                $bahan = Bahan::where('id', $detail->id_bahan)->lockForUpdate()->firstOrFail();

                $stokSebelum = (float) $bahan->jumlah_stock;
                $jumlahMasuk = (float) $detail->approved_jumlah;
                $stokSesudah = $stokSebelum + $jumlahMasuk;

                Transaksi::create([
                    'id_bahan' => $bahan->id,
                    'id_user' => Auth::id(),
                    'jenis_transaksi' => 'masuk',
                    'jumlah' => $jumlahMasuk,
                    'stock_sebelum' => $stokSebelum,
                    'stock_sesudah' => $stokSesudah,
                    'tanggal_transaksi' => now(),
                    'keterangan' => 'Realisasi pengadaan #' . $pengajuanPengadaan->id,
                ]);

                $bahan->update(['jumlah_stock' => $stokSesudah]);
            }

            $pengajuanPengadaan->update(['status' => 'Selesai']);
        });

        return redirect()->route('pengajuan-pengadaan.show', $pengajuanPengadaan)
            ->with('success', 'Realisasi stok masuk dari pengajuan berhasil diproses.');
    }

    public function setujui(Request $request, PengajuanPengadaan $pengajuanPengadaan)
    {
        $this->authorize('manage-pengajuan');

        if ($pengajuanPengadaan->status !== 'Diajukan') {
            return redirect()->back()->with('error', 'Hanya pengajuan dengan status "Diajukan" yang dapat diproses.');
        }

        $request->validate([
            'approval_items' => 'required|array|min:1',
            'approval_items.*.status_item' => 'required|in:disetujui,disetujui_sebagian,ditolak',
            'approval_items.*.approved_jumlah' => 'nullable|numeric|min:0',
            'approval_items.*.catatan_revisi' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request, $pengajuanPengadaan) {
            $details = $pengajuanPengadaan->details()->get()->keyBy('id');

            foreach ($request->approval_items as $detailId => $approvalItem) {
                if (!isset($details[$detailId])) {
                    continue;
                }

                $detail = $details[$detailId];
                $statusItem = $approvalItem['status_item'];
                $approvedJumlah = $approvalItem['approved_jumlah'] ?? null;

                if ($statusItem === 'disetujui') {
                    $approvedJumlah = $detail->jumlah;
                }

                if ($statusItem === 'ditolak') {
                    $approvedJumlah = 0;
                }

                if ($statusItem === 'disetujui_sebagian' && ($approvedJumlah === null || $approvedJumlah > $detail->jumlah || $approvedJumlah <= 0)) {
                    throw new \RuntimeException('Jumlah disetujui sebagian harus > 0 dan <= jumlah diajukan.');
                }

                $detail->update([
                    'status_item' => $statusItem,
                    'approved_jumlah' => $approvedJumlah,
                    'catatan_revisi' => $approvalItem['catatan_revisi'] ?? null,
                ]);
            }

            $hasApproved = $pengajuanPengadaan->details()->whereIn('status_item', ['disetujui', 'disetujui_sebagian'])->exists();
            $pengajuanPengadaan->update(['status' => $hasApproved ? 'Disetujui' : 'Ditolak']);
        });

        return redirect()->route('pengajuan-pengadaan.show', $pengajuanPengadaan)
            ->with('success', 'Keputusan approval berhasil disimpan.');
    }

    public function tolak(PengajuanPengadaan $pengajuanPengadaan)
    {
        $this->authorize('manage-pengajuan');

        if ($pengajuanPengadaan->status !== 'Diajukan') {
            return redirect()->back()->with('error', 'Hanya pengajuan dengan status "Diajukan" yang dapat diproses.');
        }

        DB::transaction(function () use ($pengajuanPengadaan) {
            $pengajuanPengadaan->details()->update([
                'status_item' => 'ditolak',
                'approved_jumlah' => 0,
                'catatan_revisi' => 'Ditolak pada level pengajuan.',
            ]);

            $pengajuanPengadaan->update(['status' => 'Ditolak']);
        });

        return redirect()->route('pengajuan-pengadaan.show', $pengajuanPengadaan)
            ->with('success', 'Pengajuan telah ditolak.');
    }

    private function validatePengajuan(Request $request): void
    {
        $request->validate([
            'tahun_ajaran' => 'required|string|max:9',
            'semester' => 'required|in:Ganjil,Genap',
            'items' => 'required|array|min:1',
            'items.*.item_ref' => 'required|string|max:255',
            'items.*.jumlah' => 'required|numeric|gt:0',
            'items.*.id_satuan' => 'required|exists:satuans,id',
            'items.*.harga_satuan' => 'required|integer|min:0',
            'items.*.spesifikasi' => 'nullable|string',
            'items.*.link_referensi' => 'nullable|url|max:2048',
        ]);
    }

    private function persistItems(PengajuanPengadaan $pengajuan, array $items, int $prodiId): void
    {
        foreach ($items as $item) {
            $itemRef = trim((string) ($item['item_ref'] ?? ''));
            $idBahan = null;
            $namaBarangInput = null;

            if (ctype_digit($itemRef)) {
                $bahan = Bahan::where('id', (int) $itemRef)
                    ->where('id_program_studi', $prodiId)
                    ->firstOrFail();
                $idBahan = $bahan->id;
            } else {
                $namaBarangInput = $itemRef;
            }

            $pengajuan->details()->create([
                'id_bahan' => $idBahan,
                'nama_barang_input' => $namaBarangInput,
                'spesifikasi' => $item['spesifikasi'] ?? null,
                'jumlah' => $item['jumlah'],
                'approved_jumlah' => $item['jumlah'],
                'status_item' => 'diajukan',
                'id_satuan' => $item['id_satuan'],
                'harga_satuan' => $item['harga_satuan'],
                'link_referensi' => $item['link_referensi'] ?? null,
            ]);
        }
    }
}
