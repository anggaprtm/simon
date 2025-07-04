# 📦 SiMon: Sistem Informasi Monitoring & Opname
SiMon adalah sebuah aplikasi web modern yang dirancang untuk merevolusi cara institusi pendidikan, khususnya fakultas dengan banyak program studi, mengelola inventaris bahan laboratorium. Ucapkan selamat tinggal pada pencatatan manual yang rentan kesalahan dan sambut era baru manajemen stok yang efisien, terpusat, dan akurat.

Dibangun dengan Laravel, SiMon menyediakan platform yang kuat dan aman untuk memonitor stok, mencatat setiap transaksi, dan melakukan proses tutup buku tahunan dengan mudah.

## ✨ Fitur Utama
SiMon dilengkapi dengan serangkaian fitur canggih yang dirancang untuk memenuhi kebutuhan kompleks manajemen laboratorium:

### 📦 Manajemen Inventaris Bahan
- **CRUD Penuh:** Tambah, lihat, edit, dan hapus data master bahan laboratorium dengan detail lengkap.
- **Atribut Lengkap:** Catat informasi penting seperti kode bahan, nama, merk, jenis bahan (cair, padat, dll.), satuan, hingga tanggal kedaluwarsa.
- **Stok Minimum:** Atur batas stok minimum untuk setiap bahan dan dapatkan peringatan visual di dashboard jika stok menipis.
- **Format Subscript Dinamis:** Nama bahan kimia seperti H₂O atau C₆H₁₂O₆ ditampilkan dengan benar secara otomatis.

### 🔀 Manajemen Transaksi Stok
- **Pencatatan Real-time:** Catat setiap aktivitas barang masuk, keluar, dan penyesuaian stok dengan mudah.
- **Jejak Audit:** Setiap transaksi mencatat siapa yang melakukan, kapan, jumlah, serta stok sebelum dan sesudah, menciptakan jejak audit yang jelas.
- **Riwayat Lengkap**: Lihat riwayat transaksi mendetail untuk setiap bahan.

### 📅 Sistem Periodisasi Tahunan (Tutup Buku)
- **Siklus Tahunan:** Aplikasi beroperasi dalam siklus periode tahunan yang aktif.
- **Proses Tutup Tahun:** Fitur khusus untuk Superadmin yang memungkinkan penguncian transaksi di akhir tahun, mencatat stok akhir, dan secara otomatis membawanya menjadi stok awal untuk periode tahun berikutnya.
- **Akurasi Historis:** Data dari periode yang sudah ditutup tetap tersimpan dan dapat diakses melalui laporan, memastikan data historis tidak berubah.

### 🔐 Manajemen Pengguna Berbasis Peran
- **Tiga Level Akses:** Sistem memiliki tiga peran utama dengan hak akses yang berbeda:
  - **Superadmin:** Akses penuh ke seluruh sistem, termasuk manajemen data master (Program Studi, Satuan) dan proses Tutup Tahun.
  - **Fakultas:** Dapat memonitor stok dan transaksi dari semua program studi, namun tidak dapat mengubah data operasional. Sempurna untuk pimpinan atau auditor.
  - **Laboran:** Pengguna operasional utama. Hanya dapat mengelola bahan dan transaksi milik program studinya sendiri.

### 📊 Laporan Dinamis & Cetak
- **Laporan Stok & Transaksi:** Hasilkan laporan stok (real-time atau historis) dan laporan riwayat transaksi.
- **Filter Canggih:** Saring laporan berdasarkan program studi dan rentang tanggal atau tahun periode.
- **Versi Cetak Profesional:** Setiap laporan memiliki versi cetak yang bersih, lengkap dengan kop surat dan area tanda tangan dinamis untuk validasi resmi.

### 🚀 Fitur Canggih Lainnya
- **Import dari Excel:** Efisiensikan entri data dalam jumlah besar dengan fitur import bahan dari file Excel, lengkap dengan template dan validasi data yang kuat.
- **Pencarian & Paginasi:** Temukan bahan dengan cepat melalui fitur pencarian dan kelola data dalam jumlah besar dengan mudah berkat paginasi.
- **Seleksi & Hapus Massal:** Pilih beberapa baris data bahan atau gudang sekaligus untuk dihapus, mempercepat proses manajemen.
- **UI Modern & Responsif:** Dibangun dengan Tailwind CSS, memberikan tampilan yang bersih, modern, dan nyaman digunakan di berbagai perangkat.

## 🛠️ Teknologi yang Digunakan
- **Backend:** Laravel Framework, PHP
- **Frontend:** Blade, Tailwind CSS, Alpine.js
- **Database:** MySQL (dapat disesuaikan)
- **Library Utama:** `Maatwebsite/Laravel-Excel` untuk fungsionalitas import.

## 🚀 Instalasi & Setup
Ikuti langkah-langkah berikut untuk menjalankan aplikasi ini di lingkungan lokal Anda:
1. **Clone Repositori**
```
git clone https://github.com/username/simon-app.git
cd simon-app
```

2. **Instal Dependensi**
```
composer install
npm install
```

3. **Konfigurasi Environment**
- Salin file .env.example menjadi .env.
```
cp .env.example .env
```
- Generate kunci aplikasi.
```
php artisan key:generate
```
- Atur koneksi database Anda (DB_DATABASE, DB_USERNAME, DB_PASSWORD) di dalam file `.env`.
- Atur locale aplikasi ke Bahasa Indonesia untuk format tanggal yang sesuai.
```
APP_LOCALE=id
```

4. **Jalankan Migrasi dan Seeder**
- Perintah ini akan membuat semua tabel database dan mengisi data awal (seperti data program studi, satuan, dan user superadmin).
```
php artisan migrate:fresh --seed
```

5. **Compile Aset Frontend**
```
npm run dev
```

6. **Jalankan Development Server**
```
php artisan serve
```
- Aplikasi sekarang dapat diakses di `http://localhost:8000.`

## 👤 Struktur Role Pengguna
- **Superadmin:**
  - Login default: `superadmin@example.com`
  - Password default: `password`
  - Tugas: Mengelola data master (Prodi, Satuan), mengelola akun pengguna lain (fitur masa depan), dan melakukan proses Tutup Tahun.

- **Fakultas:**
  - Login default: `fakultas@example.com`
  - Password default: `password`
  - Tugas: Memonitor semua data tanpa hak edit.

- **Laboran:**
  - Login default: `laboran.tif@example.com` (dan lainnya dari seeder)
  - Password default: `password`
  - Tugas: Melakukan semua pekerjaan operasional harian untuk prodinya masing-masing.