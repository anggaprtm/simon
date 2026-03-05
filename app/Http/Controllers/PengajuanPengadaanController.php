<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\PengajuanPengadaan;
use App\Models\Satuan;
use App\Models\DetailPengadaan;
use App\Models\Gudang;
use App\Models\PeriodeStok;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\StreamReader;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Bahan;

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
        $pengajuanPengadaan->load(['user', 'programStudi', 'details.bahan.satuanRel', 'details.satuan']);

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

    public function realisasiForm(PengajuanPengadaan $pengajuanPengadaan)
    {
        if (Auth::id() !== $pengajuanPengadaan->id_user || ! in_array($pengajuanPengadaan->status, ['Disetujui', 'Selesai'])) {
            abort(403, 'AKSI TIDAK DIIZINKAN.');
        }

        $pengajuanPengadaan->load(['details.bahan.satuanRel', 'details.satuan']);
        $gudangs = Gudang::whereNull('id_program_studi')
            ->orWhere('id_program_studi', Auth::user()->id_program_studi)
            ->orderBy('nama_gudang')
            ->get();
        $satuans = Satuan::orderBy('nama_satuan')->get();

        return view('pengajuan-pengadaan.realisasi', compact('pengajuanPengadaan', 'gudangs', 'satuans'));
    }

    public function realisasiItem(Request $request, PengajuanPengadaan $pengajuanPengadaan, DetailPengadaan $detailPengadaan)
    {
        if (Auth::id() !== $pengajuanPengadaan->id_user || $pengajuanPengadaan->status !== 'Disetujui') {
            abort(403, 'AKSI TIDAK DIIZINKAN.');
        }

        if ($detailPengadaan->id_pengajuan_pengadaan !== $pengajuanPengadaan->id) {
            abort(404);
        }

        if (! in_array($detailPengadaan->status_item, ['disetujui', 'disetujui_sebagian'])) {
            return redirect()->back()->with('error', 'Item ini tidak dalam status disetujui.');
        }

        if ($detailPengadaan->is_direalisasi) {
            return redirect()->back()->with('error', 'Item ini sudah pernah direalisasikan.');
        }

        DB::transaction(function () use ($request, $detailPengadaan, $pengajuanPengadaan) {
            if (!is_null($detailPengadaan->id_bahan)) {
                $bahan = Bahan::where('id', $detailPengadaan->id_bahan)->lockForUpdate()->firstOrFail();
                $konversi = 1;

                if ((int) $detailPengadaan->id_satuan !== (int) $bahan->id_satuan) {
                    $request->validate(['konversi_nilai' => 'required|numeric|gt:0']);
                    $konversi = (float) $request->konversi_nilai;
                }

                $jumlahMasuk = (float) $detailPengadaan->approved_jumlah * $konversi;

                $stokSebelum = (float) $bahan->jumlah_stock;
                $stokSesudah = $stokSebelum + $jumlahMasuk;

                Transaksi::create([
                    'id_bahan' => $bahan->id,
                    'id_user' => Auth::id(),
                    'jenis_transaksi' => 'masuk',
                    'jumlah' => $jumlahMasuk,
                    'stock_sebelum' => $stokSebelum,
                    'stock_sesudah' => $stokSesudah,
                    'tanggal_transaksi' => now(),
                    'keterangan' => 'Realisasi pengadaan #' . $pengajuanPengadaan->id . ' item #' . $detailPengadaan->id,
                ]);

                $bahan->update(['jumlah_stock' => $stokSesudah]);

                $detailPengadaan->update([
                    'is_direalisasi' => true,
                    'realisasi_qty' => $jumlahMasuk,
                    'konversi_nilai' => $konversi,
                ]);
            } else {
                $request->validate([
                    'nama_bahan_baru' => 'required|string|max:255',
                    'id_gudang' => 'required|exists:gudangs,id',
                    'id_satuan_baru' => 'required|exists:satuans,id',
                    'qty_realisasi' => 'required|numeric|gt:0',
                    'merk_baru' => 'nullable|string|max:255',
                    'jenis_bahan_baru' => 'nullable|string|max:100',
                    'minimum_stock_baru' => 'nullable|numeric|min:0',
                ]);

                $kode = 'AUTO-' . $pengajuanPengadaan->id . '-' . $detailPengadaan->id;
                $bahanBaru = Bahan::create([
                    'kode_bahan' => $kode,
                    'nama_bahan' => $request->nama_bahan_baru,
                    'merk' => $request->merk_baru,
                    'jenis_bahan' => $request->jenis_bahan_baru,
                    'format_kimia' => false,
                    'id_program_studi' => Auth::user()->id_program_studi,
                    'id_gudang' => $request->id_gudang,
                    'id_satuan' => $request->id_satuan_baru,
                    'minimum_stock' => $request->minimum_stock_baru ?? 0,
                    'jumlah_stock' => 0,
                ]);

                PeriodeStok::create([
                    'id_bahan' => $bahanBaru->id,
                    'tahun_periode' => date('Y'),
                    'stok_awal' => 0,
                    'status' => 'aktif',
                ]);

                $jumlahMasuk = (float) $request->qty_realisasi;
                Transaksi::create([
                    'id_bahan' => $bahanBaru->id,
                    'id_user' => Auth::id(),
                    'jenis_transaksi' => 'masuk',
                    'jumlah' => $jumlahMasuk,
                    'stock_sebelum' => 0,
                    'stock_sesudah' => $jumlahMasuk,
                    'tanggal_transaksi' => now(),
                    'keterangan' => 'Realisasi item baru pengadaan #' . $pengajuanPengadaan->id . ' item #' . $detailPengadaan->id,
                ]);

                $bahanBaru->update(['jumlah_stock' => $jumlahMasuk]);

                $detailPengadaan->update([
                    'id_bahan' => $bahanBaru->id,
                    'is_direalisasi' => true,
                    'realisasi_qty' => $jumlahMasuk,
                    'konversi_nilai' => null,
                ]);
            }

            $totalApproved = $pengajuanPengadaan->details()->whereIn('status_item', ['disetujui', 'disetujui_sebagian'])->count();
            $totalRealized = $pengajuanPengadaan->details()->whereIn('status_item', ['disetujui', 'disetujui_sebagian'])->where('is_direalisasi', true)->count();
            if ($totalApproved > 0 && $totalApproved === $totalRealized) {
                $pengajuanPengadaan->update(['status' => 'Selesai']);
            }
        });

        $pengajuanPengadaan->refresh();

        if ($pengajuanPengadaan->status === 'Selesai') {
            return redirect()->route('pengajuan-pengadaan.show', $pengajuanPengadaan)
                ->with('success', 'Realisasi item berhasil diproses. Semua item approved sudah direalisasikan.');
        }

        return redirect()->route('pengajuan-pengadaan.realisasiForm', $pengajuanPengadaan)
            ->with('success', 'Realisasi item berhasil diproses.');
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

    public function parseExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $parsedData = [];
            $user = Auth::user();

            // Ambil data referensi untuk mapping
            $satuans = Satuan::all()->keyBy(fn($item) => strtolower(trim($item->nama_satuan)));
            $bahans = Bahan::where('id_program_studi', $user->id_program_studi)
                ->get()
                ->keyBy(fn($item) => strtolower(trim($item->nama_bahan)));

            foreach ($rows as $index => $row) {
                // Lewati baris pertama (asumsi baris pertama adalah Header)
                if ($index === 0) continue; 

                $namaBahan = trim((string)($row[0] ?? ''));
                if (empty($namaBahan)) continue; // Skip jika nama bahan kosong

                $spesifikasi = trim((string)($row[1] ?? ''));
                $jumlah = (float)($row[2] ?? 0);
                $namaSatuan = strtolower(trim((string)($row[3] ?? '')));
                $hargaSatuan = (int)($row[4] ?? 0);
                $link = trim((string)($row[5] ?? ''));

                // Cocokkan ID Satuan
                $id_satuan = $satuans->has($namaSatuan) ? $satuans[$namaSatuan]->id : '';

                // Cocokkan ID Bahan (Jika existing kirim ID, jika baru kirim teksnya)
                $bahanKey = strtolower($namaBahan);
                $item_ref = $bahans->has($bahanKey) ? (string)$bahans[$bahanKey]->id : $namaBahan;

                $parsedData[] = [
                    'item_ref' => $item_ref,
                    'spesifikasi' => $spesifikasi,
                    'jumlah' => $jumlah,
                    'id_satuan' => $id_satuan,
                    'harga_satuan' => $hargaSatuan,
                    'link_referensi' => $link,
                ];
            }

            return response()->json(['status' => 'success', 'data' => $parsedData]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal membaca file: ' . $e->getMessage()], 500);
        }
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        
        // --- SHEET 1: TEMPLATE INPUT ---
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Pengajuan');

        $headers = ['Nama Bahan', 'Spesifikasi', 'Jumlah', 'Satuan', 'Harga Satuan', 'Link Referensi'];
        $sheet->fromArray($headers, NULL, 'A1');
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

        // --- SHEET 2: MASTER BAHAN ---
        // Kita buat sheet tersembunyi/tambahan khusus untuk nyimpen data bahan existing
        $masterSheet = $spreadsheet->createSheet();
        $masterSheet->setTitle('Master Bahan Existing');
        
        $user = Auth::user();
        // Ambil semua nama bahan sesuai prodi user
        $bahans = Bahan::where('id_program_studi', $user->id_program_studi)
            ->orderBy('nama_bahan')
            ->pluck('nama_bahan')
            ->toArray();

        // Masukkan data bahan ke Sheet 2 (Mulai dari A1 ke bawah)
        foreach ($bahans as $index => $namaBahan) {
            $masterSheet->setCellValue('A' . ($index + 1), $namaBahan);
        }

        // --- TAMBAHKAN DROPDOWN KE SHEET 1 ---
        $totalBahan = count($bahans);
        if ($totalBahan > 0) {
            // Buat rule validasinya
            $validation = $sheet->getCell('A2')->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $validation->setAllowBlank(true);
            $validation->setShowInputMessage(true);
            // PENTING: setShowErrorMessage(false) agar laboran BISA ngetik manual bahan baru
            $validation->setShowErrorMessage(false); 
            $validation->setShowDropDown(true);
            $validation->setPromptTitle('Pilih / Ketik Bahan');
            $validation->setPrompt('Pilih dari dropdown untuk existing, atau ketik langsung jika bahan baru.');
            
            // Ambil range dari Sheet 2
            $validation->setFormula1('\'Master Bahan Existing\'!$A$1:$A$' . $totalBahan);

            // Copy validation ke baris 3 sampai 200 (asumsi max 200 baris sekali import)
            for ($i = 3; $i <= 200; $i++) {
                $sheet->getCell("A{$i}")->setDataValidation(clone $validation);
            }
        }

        // Rapihkan ukuran kolom
        foreach(range('A','F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        $masterSheet->getColumnDimension('A')->setAutoSize(true);

        // Kembalikan fokus ke Sheet 1 agar pas file dibuka langsung di form input
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="Template_Pengajuan_Pengadaan.xlsx"',
        ]);
    }
}
