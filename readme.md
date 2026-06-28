# CafeeID

CafeeID adalah aplikasi web berbasis CodeIgniter 3 untuk pencarian cafe, reservasi/checkout, member, owner cafe, dan panel super admin.

## Kebutuhan

- XAMPP dengan Apache, PHP 8.x, dan MySQL/MariaDB
- Database MySQL bernama `cafee`
- Browser lokal melalui `http://localhost/cafeeid/`

## Instalasi Lokal

1. Letakkan project di:
   `C:\xampp\htdocs\cafeeid`

2. Buat database dan tabel awal dari file:
   `setup_db.sql`

   Bisa lewat phpMyAdmin atau MySQL CLI:

   ```sql
   SOURCE C:/xampp/htdocs/cafeeid/setup_db.sql;
   ```

3. Cek konfigurasi database di:
   `application/config/database.php`

   Default lokal:

   ```php
   'hostname' => 'localhost',
   'username' => 'root',
   'password' => '',
   'database' => 'cafee',
   ```

4. Cek konfigurasi utama di:
   `application/config/config.php`

   Untuk lokal XAMPP:

   ```php
   $config['base_url'] = 'http://localhost/cafeeid/';
   ```

5. Buka aplikasi:
   `http://localhost/cafeeid/`

## Akun Login

Super admin:

- Username: `admin`
- Password: `admin`
- Halaman setelah login: `admin_super/manage_cafe`

Cafe owner demo dari `setup_db.sql`:

- Username: `cafe001`
- Password mengikuti hash di `setup_db.sql`

Member demo dari `setup_db.sql`:

- Phone: `081234567890`
- Password mengikuti hash di `setup_db.sql`

## Struktur Penting

- `application/controllers/Auth.php`: login, register, logout, dan super admin auth
- `application/controllers/Admin_super.php`: panel super admin
- `application/controllers/Admin_cafe.php`: panel owner cafe
- `application/models/Auth_model.php`: validasi member dan owner cafe
- `application/config/config.php`: base URL, super admin, session, CSRF, log
- `application/config/database.php`: koneksi database
- `assets/`: CSS dan asset publik
- `uploads/`: file upload aplikasi

## Catatan Keamanan

- Jangan simpan file bypass/debug login di web root. File seperti `direct_login.php`, `set_admin.php`, dan `test_login.html` sudah dihapus.
- Ganti password super admin sebelum dipakai di production.
- Untuk production, aktifkan HTTPS dan sesuaikan:

  ```php
  $config['base_url'] = 'https://domain-kamu/';
  $config['cookie_secure'] = TRUE;
  $config['csrf_protection'] = TRUE;
  $config['log_threshold'] = 1;
  ```

- Jangan log password atau input sensitif user.

## Troubleshooting Login Admin

Jika login admin gagal:

1. Pastikan username `admin`.
2. Pastikan `cafeeid_super_pass_hash` di `application/config/config.php` cocok dengan password yang dipakai.
3. Hapus file rate-limit login di `application/cache/login_*.json` jika akun terkunci karena terlalu banyak percobaan gagal.
4. Pastikan session path bisa ditulis. Untuk lokal, konfigurasi saat ini memakai:

   ```php
   $config['sess_save_path'] = sys_get_temp_dir();
   ```

5. Cek log di:
   `application/logs/`

## Route Utama

- `/` atau `/home`: halaman utama
- `/auth`: halaman login
- `/auth/register`: register member
- `/admin_super/manage_cafe`: panel super admin
- `/admin_cafe/settlement`: panel owner cafe
- `/callback`: callback pembayaran
- `/invoice/{kode}`: invoice/print invoice

##