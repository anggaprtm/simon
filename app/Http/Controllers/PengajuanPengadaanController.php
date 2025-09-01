<?php

namespace App\Http\Controllers;

use App\Models\PengajuanPengadaan;
use App\Models\DetailPengadaan;
use App\Models\MasterBarang;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use PDF;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\StreamReader;

class PengajuanPengadaanController extends Controller
{
    public function __construct()
    {
        // Definisikan Gate untuk hak akses
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
        // Jika superadmin/fakultas, bisa melihat semua (bisa ditambahkan filter prodi nanti)

        $pengajuans = $query->get();
        return view('pengajuan-pengadaan.index', compact('pengajuans'));
    }

    public function create()
    {
        $this->authorize('create-pengajuan');
        $masterBarangs = MasterBarang::orderBy('nama_barang')->get();
        $satuans = Satuan::orderBy('nama_satuan')->get();
        return view('pengajuan-pengadaan.create', compact('masterBarangs', 'satuans'));
    }

    public function store(Request $request)
    {
        $this->authorize('create-pengajuan');
        
        $request->validate([
            'tahun_ajaran' => 'required|string|max:9', // cth: 2024/2025
            'semester' => 'required|in:Ganjil,Genap',
            'items' => 'required|array|min:1',
            'items.*.id_master_barang' => 'required|exists:master_barangs,id',
            'items.*.jumlah' => 'required|numeric|gt:0',
            'items.*.id_satuan' => 'required|exists:satuans,id',
            'items.*.harga_satuan' => 'required|integer|min:0',
            'items.*.spesifikasi' => 'nullable|string',
            'items.*.link_referensi' => 'nullable|url|max:2048',
        ]);

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

                foreach ($request->items as $item) {
                    $pengajuan->details()->create([
                        'id_master_barang' => $item['id_master_barang'],
                        'spesifikasi' => $item['spesifikasi'],
                        'jumlah' => $item['jumlah'],
                        'id_satuan' => $item['id_satuan'],
                        'harga_satuan' => $item['harga_satuan'],
                        'link_referensi' => $item['link_referensi'],
                        // Anda bisa menambahkan 'merk', 'volume', 'link_referensi' jika sudah ada di form
                    ]);
                }
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan pengajuan: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('pengajuan-pengadaan.index')->with('success', 'Pengajuan berhasil disimpan.');
    }

    public function show(PengajuanPengadaan $pengajuanPengadaan)
    {
        // Tambahkan otorisasi untuk melihat
        // Gate::authorize('view', $pengajuanPengadaan);
        $pengajuanPengadaan->load(['user', 'programStudi', 'details.masterBarang', 'details.satuan']);
        return view('pengajuan-pengadaan.show', compact('pengajuanPengadaan'));
    }

    public function cetakNotaDinas(PengajuanPengadaan $pengajuanPengadaan)
    {
        // 1) Load relasi
        $pengajuanPengadaan->load(['user', 'programStudi', 'details.masterBarang', 'details.satuan']);

        // 2) Hitung jumlah lampiran (teks)
        $itemsPerPage = 20; // estimasi per halaman, opsional
        $jumlah_lampiran_angka = (int) ceil(count($pengajuanPengadaan->details) / $itemsPerPage);
        $formatter = new \NumberFormatter('id', \NumberFormatter::SPELLOUT);
        $jumlah_lampiran = ucwords($formatter->format(max(1, $jumlah_lampiran_angka)));

        // 3) Logo base64
        $logo_base64 = base64_encode(@file_get_contents(public_path('images/logo.png'))) ?: '';

        // 4) Data untuk view
        $data = [
            'pengajuan'       => $pengajuanPengadaan,
            'jumlah_lampiran' => $jumlah_lampiran,
            'logo_base64'     => $logo_base64,
        ];

        // 5) Render PDF portrait (halaman 1)
        $pdfPortrait = Pdf::loadView('pengajuan-pengadaan.pdf.nota_dinas_portrait', $data)
            ->setPaper('a4', 'portrait')
            ->output();

        // 6) Render PDF landscape (lampiran, multi halaman)
        $pdfLandscape = Pdf::loadView('pengajuan-pengadaan.pdf.nota_dinas_lampiran_landscape', $data)
            ->setPaper('a4', 'landscape')
            ->output();

        // 7) Merge keduanya pakai FPDI
        $final = new Fpdi();

        foreach ([$pdfPortrait, $pdfLandscape] as $pdfString) {
            $pageCount = $final->setSourceFile(StreamReader::createByString($pdfString));
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $tpl = $final->importPage($pageNo);
                $size = $final->getTemplateSize($tpl);

                // Tambah halaman dengan orientasi & ukuran sesuai template
                $final->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $final->useTemplate($tpl);
            }
        }

        $output = $final->Output('S');

        // 8) Return 1 file PDF final (portrait + landscape)
        return response($output, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="nota-dinas-' . $pengajuanPengadaan->id . '.pdf"');
    }

    public function destroy(PengajuanPengadaan $pengajuanPengadaan)
    {
        // 1. Otorisasi: Pastikan user yang login adalah pemilik dan statusnya Draft
        if (Auth::id() !== $pengajuanPengadaan->id_user || $pengajuanPengadaan->status !== 'Draft') {
            abort(403, 'AKSI TIDAK DIIZINKAN.');
        }

        // 2. Hapus data
        // Karena ada onDelete('cascade') di migrasi, detailnya akan ikut terhapus.
        $pengajuanPengadaan->delete();

        // 3. Redirect dengan pesan sukses
        return redirect()->route('pengajuan-pengadaan.index')
                        ->with('success', 'Pengajuan berhasil dihapus.');
    }

    public function edit(PengajuanPengadaan $pengajuanPengadaan)
    {
        // Otorisasi: Pastikan user yang login adalah pemilik dan statusnya Draft
        if (Auth::id() !== $pengajuanPengadaan->id_user || $pengajuanPengadaan->status !== 'Draft') {
            abort(403, 'AKSI TIDAK DIIZINKAN.');
        }

        // Load relasi detail agar bisa diakses di view
        $pengajuanPengadaan->load('details');

        // Ambil data untuk dropdown (sama seperti di method create)
        $masterBarangs = MasterBarang::orderBy('nama_barang')->get();
        $satuans = Satuan::orderBy('nama_satuan')->get();

        return view('pengajuan-pengadaan.edit', compact('pengajuanPengadaan', 'masterBarangs', 'satuans'));
    }

    public function update(Request $request, PengajuanPengadaan $pengajuanPengadaan)
    {
        // Otorisasi
        if (Auth::id() !== $pengajuanPengadaan->id_user || $pengajuanPengadaan->status !== 'Draft') {
            abort(403, 'AKSI TIDAK DIIZINKAN.');
        }

        // Validasi (sama seperti store)
        $request->validate([
            'tahun_ajaran' => 'required|string|max:9',
            'semester' => 'required|in:Ganjil,Genap',
            'items' => 'required|array|min:1',
            'items.*.id_master_barang' => 'required|exists:master_barangs,id',
            // ... validasi item lainnya ...
        ]);

        try {
            DB::transaction(function () use ($request, $pengajuanPengadaan) {
                $status = $request->action === 'submit' ? 'Diajukan' : 'Draft';

                // 1. Update data header pengajuan
                $pengajuanPengadaan->update([
                    'tahun_ajaran' => $request->tahun_ajaran,
                    'semester' => $request->semester,
                    'status' => $status,
                ]);

                // 2. Hapus semua detail item yang lama
                $pengajuanPengadaan->details()->delete();

                // 3. Buat ulang detail item berdasarkan data form yang baru
                foreach ($request->items as $item) {
                    $pengajuanPengadaan->details()->create([
                        'id_master_barang' => $item['id_master_barang'],
                        'spesifikasi' => $item['spesifikasi'],
                        'jumlah' => $item['jumlah'],
                        'id_satuan' => $item['id_satuan'],
                        'harga_satuan' => $item['harga_satuan'],
                        'link_referensi' => $item['link_referensi'] ?? null, // Link referensi opsional
                    ]);
                }
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui pengajuan: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('pengajuan-pengadaan.index')->with('success', 'Pengajuan berhasil diperbarui.');
    }

    public function setujui(PengajuanPengadaan $pengajuanPengadaan)
    {
        // 1. Otorisasi menggunakan Gate yang sudah dibuat
        $this->authorize('manage-pengajuan');

        // 2. Pastikan hanya pengajuan berstatus 'Diajukan' yang bisa diproses
        if ($pengajuanPengadaan->status !== 'Diajukan') {
            return redirect()->back()->with('error', 'Hanya pengajuan dengan status "Diajukan" yang dapat diproses.');
        }

        // 3. Ubah status menjadi 'Disetujui'
        $pengajuanPengadaan->update(['status' => 'Disetujui']);

        // 4. Redirect kembali dengan pesan sukses
        return redirect()->route('pengajuan-pengadaan.show', $pengajuanPengadaan)
                         ->with('success', 'Pengajuan telah berhasil disetujui.');
    }

    /**
     * Menolak sebuah pengajuan.
     */
    public function tolak(PengajuanPengadaan $pengajuanPengadaan)
    {
        // 1. Otorisasi
        $this->authorize('manage-pengajuan');

        // 2. Pastikan statusnya 'Diajukan'
        if ($pengajuanPengadaan->status !== 'Diajukan') {
            return redirect()->back()->with('error', 'Hanya pengajuan dengan status "Diajukan" yang dapat diproses.');
        }

        // 3. Ubah status menjadi 'Ditolak'
        $pengajuanPengadaan->update(['status' => 'Ditolak']);

        // 4. Redirect kembali dengan pesan sukses
        return redirect()->route('pengajuan-pengadaan.show', $pengajuanPengadaan)
                         ->with('success', 'Pengajuan telah ditolak.');
    }
    }