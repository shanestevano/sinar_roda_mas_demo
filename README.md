# Sinar Roda Mas - Sistem Otomasi Toko & Gudang Ban Terintegrasi

Aplikasi berbasis web Monolithic MVC dibangun dengan **PHP 8.4**, **Laravel 11**, **Blade Templating**, **Alpine.js**, dan **Tailwind CSS**. Sistem ini mengintegrasikan seluruh siklus operasional bisnis penjualan ban otomotif: dari penerimaan barang fisik di gudang, pengawasan kualitas tahun produksi (DOT), hingga transaksi kasir (POS) di lini depan dengan garansi integritas data transaksi (*Database Transactions*).

---

## 🚀 Fitur Utama & Modul Sistem

### 1. Modul Kasir (Point of Sales / POS)
- **Smart Search:** Pencarian instan ketersediaan ban berdasarkan Merk, Ukuran, Ring, Kode DOT, atau scan Barcode fisik dengan *Eager Loading*.
- **Keranjang Belanja Real-Time:** Dikelola menggunakan Alpine.js untuk manipulasi DOM ringan tanpa reload halaman.
- **Automasi Mutasi Stok Atomik:** Proses *checkout* dibungkus dengan `DB::transaction()` dengan *row-level locking* untuk mencegah *overselling* atau *race condition*.
- **Cetak Struk Thermal:** Struk penjualan format 80mm standar struk POS dengan detail nomor faktur, kasir, pelanggan, nomor polisi kendaraan, rincian barang, dan footer garansi.
- **Pembatalan Transaksi (Void) Aman:** Menggunakan validasi PIN otorisasi supervisor/owner untuk mengubah status transaksi menjadi VOID dan otomatis mengembalikan stok fisik ban ke gudang serta mencatat mutasi retur.
- **Rekapitulasi Shift Kasir:** Pencatatan modal awal kasir (*starting cash*), total omset penjualan tunai, dan rekonsiliasi kas fisik harian.

### 2. Modul Inventaris & Gudang (Warehouse)
- **Master Data Ban (SKU):** Manajemen data ban lengkap mencakup SKU, Barcode, Merk (Bridgestone, Michelin, Dunlop, GT Radial, dll), Model Kembangan (Pattern), Dimensi (Lebar, Aspek Rasio, Ring), Kode DOT, Harga Modal (HPP), dan Harga Jual.
- **Penerimaan Barang Masuk (Inbound):** Formulir dinamis pencatatan pasokan dari supplier/distributor yang secara otomatis menambah stok fisik dan mencatat histori harga modal terbaru.
- **Generator Barcode Label Ban:** Menghasilkan kode batang standar Code-128 dalam format SVG murni beresolusi tinggi yang siap dicetak langsung pada stiker ban.
- **Manajemen Rak Gudang:** Relasi *One-to-Many* antara lokasi rak (`rack_locations`) dan ban (`tires`) beserta visualisasi persentase kapasitas tampung rak.
- **Penyesuaian Stok (Stock Opname):** Penyesuaian stok manual dilengkapi alasan audit fisik (kehilangan, kerusakan, atau temuan audit).

### 3. Modul Analitik & Laporan (Owner)
- **Executive Dashboard:** Visualisasi KPI pendapatan hari ini, omset bulan ini, estimasi laba kotor (*gross profit*), marjin keuntungan (%), total nilai aset stok, grafik tren penjualan 7 hari terakhir, dan 5 model ban paling laris.
- **Laporan Penjualan & Laba Rugi:** Laporan komprehensif dengan filter rentang tanggal, kasir, dan metode pembayaran, dilengkapi tombol **Ekspor ke CSV/Excel**.
- **Kartu Pergerakan Stok (Stock Card):** Histori lengkap mutasi keluar-masuk setiap ban secara kronologis beserta saldo awal, saldo akhir, nomor referensi dokumen, dan operator penanggung jawab.
- **Pencegahan Ban Kadaluarsa (*Dead Stock*):** Deteksi dini ban dengan kode tahun produksi (DOT) lama (&ge; 2 atau 3 tahun) untuk mencegah kerugian karet ban mengeras, dilengkapi estimasi modal tertahan dan rekomendasi diskon cuci gudang (*clearance sale*).
- **Audit Trail:** Log kronologis seluruh aktivitas penting pengguna (login, checkout, void, tambah ban, ubah setting, dll) lengkap dengan alamat IP dan data perubahan JSON.

### 4. Modul Manajemen Sistem (Admin IT)
- **Manajemen Pengguna (RBAC):** CRUD akun staf, pengaturan peran wewenang (Admin, Owner, Gudang, Kasir), blokir/aktifkan akun, reset kata sandi, dan atur PIN void.
- **Pengaturan Toko:** Konfigurasi nama toko, alamat, kontak WhatsApp, catatan garansi struk kasir, dan default master PIN void.
- **System Audit & Error Logs:** Pemantau catatan exception dan error Laravel dari `storage/logs/laravel.log` dengan badge level log (ERROR, WARNING, INFO).
- **Pencadangan Database (Backup):** Informasi koneksi database aktif dan tombol 1-klik unduh file cadangan database snapshot.

---

## 👥 Hak Akses & Kredensial Pengguna Demo

Tersedia fitur **1-Click Quick Login** pada halaman login (`/login`) untuk memudahkan pengujian seluruh 4 peran wewenang tanpa perlu mengetik manual:

| Peran (Role) | Email | Password | PIN Void | Fokus Wewenang |
| :--- | :--- | :--- | :--- | :--- |
| **Admin IT** | `admin@sinarrodamas.com` | `password` | `123456` | Akses penuh seluruh modul, IT config, audit log, backup database, user management |
| **Owner** | `owner@sinarrodamas.com` | `password` | `123456` | Executive dashboard, analisis laba kotor, kartu stok, dead stock DOT, audit trail |
| **Gudang** | `gudang@sinarrodamas.com` | `password` | - | Master data ban, penerimaan inbound, lokasi rak gudang, cetak barcode ban, opname |
| **Kasir** | `kasir@sinarrodamas.com` | `password` | `123456` | Kasir POS, smart search ban, cetak struk thermal 80mm, rekap shift kasir |

---

## ⚙️ Cara Menjalankan Aplikasi

### 1. Menjalankan Server Lokal (Built-in)
Jalankan perintah berikut di terminal:

```bash
php artisan serve
```

Buka peramban (browser) di alamat: **`http://localhost:8000`**

### 2. Menjalankan di Laragon / XAMPP
Aplikasi ini kompatibel dengan MySQL maupun SQLite:
- Untuk menggunakan MySQL di Laragon: cukup sesuaikan konfigurasi database pada file `.env`:
  ```ini
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=sinar_roda_mas
  DB_USERNAME=root
  DB_PASSWORD=
  ```
- Kemudian jalankan migrasi & data awal:
  ```bash
  php artisan migrate:fresh --seed
  ```

### 3. Kompilasi Aset Frontend (Jika Melakukan Modifikasi)
```bash
npm run build
```
Atau untuk mode pengembangan:
```bash
npm run dev
```

### 4. Menjalankan Unit & Feature Tests
Aplikasi telah dilengkapi **17 skenario automated test** yang menguji autentikasi, transaksi POS atomik, pencegahan stok minus, otorisasi PIN void, inbound gudang, barcode, dan ekspor laporan CSV:

```bash
php artisan test
```

---

## 📁 Struktur Direktori Utama

```
sinar_roda_mas_prototype/
├── app/
│   ├── Helpers/
│   │   └── BarcodeHelper.php          # Generator Barcode SVG murni (Code 128)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php      # Login, Logout, 1-Click Quick Demo Switcher
│   │   │   ├── DashboardController.php # KPI Eksekutif, Omset, Grafik Tren 7 Hari
│   │   │   ├── PosController.php        # POS Kasir, Smart Search, DB::transaction checkout, Struk, Void
│   │   │   ├── ProductController.php    # CRUD Ban, Barcode view, Stock Opname
│   │   │   ├── InboundController.php    # Penerimaan pasokan ban fisik & mutasi stok otomatis
│   │   │   ├── RackController.php       # Lokasi rak fisik gudang (1:N Tires)
│   │   │   ├── SupplierController.php   # Data kontak distributor ban
│   │   │   ├── ReportController.php     # Laporan Penjualan, Kartu Stok, Dead Stock DOT, CSV
│   │   │   ├── ShiftController.php      # Rekapitulasi shift kasir & rekonsiliasi kas
│   │   │   ├── UserController.php       # Manajemen akun pengguna & RBAC wewenang
│   │   │   ├── SettingController.php    # Konfigurasi toko & struk kasir
│   │   │   └── SystemController.php     # Log error sistem & cadangan database
│   │   └── Middleware/
│   │       └── RoleMiddleware.php       # Middleware proteksi hak akses peran (RBAC)
│   ├── Models/
│   │   ├── User.php
│   │   ├── Tire.php                     # Model Ban (DOT, dimensi, harga, stok)
│   │   ├── RackLocation.php             # Model Rak Gudang
│   │   ├── Supplier.php
│   │   ├── Inbound.php & InboundItem.php
│   │   ├── Transaction.php & Item.php   # Transaksi POS
│   │   ├── CashierShift.php             # Shift Kasir
│   │   ├── StockMutation.php            # Kartu Stok Mutasi Fisik
│   │   ├── Setting.php
│   │   └── ActivityLog.php              # Audit Trail
├── database/
│   ├── migrations/                      # Skema tabel database presisi
│   └── seeders/                         # Master data ban dan akun siap pakai
├── resources/
│   ├── views/                           # Antarmuka Blade terstruktur + Tailwind CSS + Alpine.js
│   │   ├── layouts/ (app, guest)
│   │   ├── auth/ (login)
│   │   ├── dashboard/ (index)
│   │   ├── pos/ (index, history, receipt)
│   │   ├── products/ (index, create, edit, barcode)
│   │   ├── inbounds/ (index, create, show)
│   │   ├── racks/ (index)
│   │   ├── suppliers/ (index)
│   │   ├── reports/ (sales, stock_card, dead_stock, audit_trail)
│   │   ├── users/ (index)
│   │   ├── settings/ (index)
│   │   ├── system/ (logs, backup)
│   │   └── shifts/ (index)
└── tests/Feature/                       # 17 Automated Feature Tests
```
