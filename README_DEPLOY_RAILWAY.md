# Panduan Deploy Sinar Roda Mas ke Railway (Menggunakan Docker)

Proyek ini telah dikonfigurasi penuh menggunakan **Docker (Production-Grade)** untuk deployment ke [Railway](https://railway.com).

---

## 1. Berkas Docker yang Tersedia

1. [Dockerfile](file:///d:/Kuliah/Antig/sinar_roda_mas_prototype/Dockerfile)
   - **Multi-stage build**:
     - *Stage 1 (Node 22)*: Mengompilasi asset frontend Vite dan Tailwind CSS v4 (`npm run build`).
     - *Stage 2 (PHP 8.4 Alpine)*: Menggabungkan Nginx, PHP-FPM, Supervisor, dan dependensi Composer yang dioptimalkan (`--no-dev --optimize-autoloader`).
   - Mendukung ekstensi database `pdo_mysql`, `pdo_pgsql`, dan `pdo_sqlite`.
2. [docker/nginx.conf](file:///d:/Kuliah/Antig/sinar_roda_mas_prototype/docker/nginx.conf)
   - Konfigurasi Nginx reverse proxy yang otomatis beradaptasi dengan port dinamis Railway (`$PORT`), kompresi Gzip, caching asset, dan routing Laravel.
3. [docker/php.ini](file:///d:/Kuliah/Antig/sinar_roda_mas_prototype/docker/php.ini)
   - Pengaturan performa tinggi: Opcache aktif, timezone `Asia/Jakarta`, memory limit 256M.
4. [docker/supervisord.conf](file:///d:/Kuliah/Antig/sinar_roda_mas_prototype/docker/supervisord.conf)
   - Process manager untuk menjalankan Nginx dan PHP-FPM secara bersamaan dalam container.
5. [docker/entrypoint.sh](file:///d:/Kuliah/Antig/sinar_roda_mas_prototype/docker/entrypoint.sh)
   - Skrip boot container otomatis: menyetel port Nginx, auto-detect database Railway, permission storage, caching Laravel, serta `php artisan migrate --force`.
6. [railway.json](file:///d:/Kuliah/Antig/sinar_roda_mas_prototype/railway.json)
   - Konfigurasi Railway untuk membangun menggunakan `Dockerfile` dan memantau kesehatan melalui `/up`.
7. [.dockerignore](file:///d:/Kuliah/Antig/sinar_roda_mas_prototype/.dockerignore)
   - Memastikan cache lokal dan node_modules tidak mengotori proses build image.

---

## 2. Langkah 1: Push Proyek ke GitHub

Buka terminal pada direktori proyek dan push ke GitHub:

```bash
git init
git add .
git commit -m "feat: setup docker deployment for railway"
git branch -M main
git remote add origin https://github.com/<username-anda>/<nama-repo-anda>.git
git push -u origin main
```

---

## 3. Langkah 2: Deploy di Railway

1. Buka [Railway Dashboard](https://railway.com/dashboard).
2. Klik tombol **New Project**.
3. Pilih opsi **Deploy from GitHub repo**.
4. Pilih repositori GitHub `sinar_roda_mas_prototype`.
5. Railway akan mendeteksi `Dockerfile` dan `railway.json` secara otomatis untuk membangun container.

---

## 4. Langkah 3: Setting Environment Variables di Railway

Di dashboard Railway proyek Anda:
1. Klik service aplikasi Anda.
2. Buka tab **Variables**.
3. Tambahkan environment variables berikut:

| Key | Value Contoh | Penjelasan |
| :--- | :--- | :--- |
| `APP_NAME` | `Sinar Roda Mas` | Nama aplikasi |
| `APP_ENV` | `production` | Mode environment |
| `APP_DEBUG` | `false` | Nonaktifkan debug di production |
| `APP_KEY` | `base64:uNjN0bShmtUexf5mFrdlMLz3mOOBna7RY8cPiGMV0sg=` | Kunci enkripsi Laravel *(atau jalankan `php artisan key:generate --show`)* |
| `APP_URL` | `https://${{RAILWAY_PUBLIC_DOMAIN}}` | Mengambil domain publik Railway secara otomatis |
| `DB_CONNECTION` | `sqlite` | Bawaan aplikasi *(otomatis beralih jika tambah MySQL/Postgres)* |
| `SESSION_DRIVER` | `database` | Driver sesi login |
| `CACHE_STORE` | `database` | Driver cache |
| `RUN_MIGRATIONS` | `true` | Menjalankan migrasi database saat container start |
| `DB_SEED` | `true` | **Aktifkan `true` pada deploy pertama** agar akun login dan katalog ban langsung terisi |

---

## 5. Langkah 4: Generate Domain Publik

1. Di tab **Settings** pada service aplikasi Anda.
2. Gulir ke bagian **Networking** > **Public Networking**.
3. Klik tombol **Generate Domain** (misalnya `sinar-roda-mas-production.up.railway.app`).
4. Klik link tersebut di browser untuk membuka web aplikasi Anda.

---

## 6. Opsi Database di Railway

- **SQLite (Bawaan)**:
  - Sudah siap digunakan langsung tanpa database eksternal.
  - Untuk menjaga data tetap tersimpan saat container di-restart/redeploy, tambahkan Volume di tab **Settings** > **Volumes** > **Add Volume** dengan mount path: `/var/www/html/database`.
- **MySQL / PostgreSQL (Opsional)**:
  - Di Railway, klik **Create** > **Database** > pilih **Add MySQL** atau **Add PostgreSQL**.
  - Skrip `entrypoint.sh` container otomatis mendeteksi variabel `MYSQL_URL` atau `DATABASE_URL` dari database Railway.

---

## 7. Akun Login Bawaan (Default Seeder)

Setelah proses `DB_SEED=true` berjalan:

- **Admin IT**: `admin@sinarrodamas.com` / `password`
- **Owner**: `owner@sinarrodamas.com` / `password`
- **Gudang**: `gudang@sinarrodamas.com` / `password`
- **Kasir**: `kasir@sinarrodamas.com` / `password`
- **PIN Void Kasir**: `123456`
