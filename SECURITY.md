# Security Notes

Dokumen ini merangkum pengamanan yang sudah diterapkan pada project CI3 CariCafe dan hal yang wajib disiapkan saat publish ke server production.

## Status Produksi

- `ENVIRONMENT` default adalah `production` di `index.php`.
- Error detail PHP/CodeIgniter tidak ditampilkan di production.
- `db_debug` mati di production.
- Query log database dimatikan di production untuk mengurangi kebocoran data dan penggunaan memori.
- `base_url` dinamis mengikuti protocol, host, port, dan subfolder tempat aplikasi dipasang.

## File `.env` Production cPanel

Project ini sudah dilengkapi loader `.env` native di `index.php`, jadi aman untuk cPanel yang biasanya tidak mudah mengatur environment variable server.

File production ada di root project:

```text
.env
```

Isi `.env` saat ini:

```text
CAFEEID_DB_HOST=45.130.230.158
CAFEEID_DB_USER=u1485990_cafeeid
CAFEEID_DB_PASS=Sukses@2026!
CAFEEID_DB_NAME=u1485990_cafeeid
```

Super admin production diset memakai hash:

```text
CAFEEID_SUPER_USER=admin
CAFEEID_SUPER_PASS_HASH=...
```

Password super admin yang sesuai dengan hash saat ini:

```text
Sukses@Admin2026!
```

`.env.example` juga disediakan sebagai template jika credential cPanel berubah.

Catatan: login default `admin / admin` hanya aktif di localhost. Di server publik, default tersebut otomatis ditolak.

## Proteksi Login dan Session

- Password member lama berbasis `md5` otomatis dimigrasikan ke `password_hash()` saat login berhasil.
- Password admin cafe lama plaintext otomatis dimigrasikan ke `password_hash()` saat login berhasil.
- Admin cafe baru disimpan dengan `password_hash()`.
- Session diregenerasi setelah login berhasil untuk mencegah session fixation.
- Cookie session memakai nama khusus `cafeeid_session`.
- Cookie dibuat `HttpOnly`.
- Cookie `Secure` otomatis aktif saat request HTTPS.
- Login dibatasi dengan rate limit 5 kali gagal selama 5 menit berbasis session dan cache IP/username.
- Redirect setelah login divalidasi agar tidak bisa diarahkan ke domain asing.
- Logout wajib memakai POST + CSRF.

## Proteksi Form dan Request

- CSRF protection aktif global.
- Form POST utama sudah memakai CSRF token:
  - Login
  - Checkout
  - Profile update
  - Tambah cafe
  - Hapus cafe
  - Logout
- Aksi hapus cafe sudah dipindah dari GET ke POST.
- Checkout memvalidasi ulang item menu, harga, dan status menu dari database. Harga dari browser tidak dipercaya.
- Tipe order checkout dibatasi ke whitelist: `dine_in`, `reservation`, `take_away`.
- Input tambah cafe divalidasi: username, password minimal, status meja, dan koordinat.

## Callback Pembayaran

- Callback pembayaran sudah dipindahkan ke controller CI3 `Callback`.
- URL utama callback:

```text
https://cafeeid.japrime.id/callback?prefix=KODE_KAFE
```

- Route legacy juga diarahkan ke controller yang sama:

```text
/callback.php
/api/callback
/api/callback/callback.php
```

- Callback dikecualikan dari CSRF karena dipanggil sistem eksternal/MacroDroid, bukan form browser.
- Untuk produksi, sangat disarankan mengisi `CAFEEID_CALLBACK_SECRET` di `.env`, lalu kirim callback dengan query `key` atau header `X-Callback-Key`.
- Token Telegram dibaca dari `CAFEEID_TELEGRAM_BOT_TOKEN`, bukan hardcoded di controller.

## Security Headers

Security headers aktif melalui hook CodeIgniter:

```text
X-Frame-Options: SAMEORIGIN
X-Content-Type-Options: nosniff
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: camera=(), microphone=(), geolocation=(self)
X-Permitted-Cross-Domain-Policies: none
Strict-Transport-Security: aktif otomatis saat HTTPS
```

Header serupa juga ditambahkan di `.htaccess` untuk Apache.

## Proteksi File dan Server

`.htaccess` sudah diperkuat:

- Directory listing dimatikan dengan `Options -Indexes`.
- File sensitif seperti `.env`, `composer.json`, `composer.lock`, `router.php`, `license.txt`, dan `readme.rst` diblok dari akses publik.
- Rewrite route dibuat tanpa `RewriteBase` hardcoded agar fleksibel saat publish di root domain atau subfolder.

Pastikan folder berikut tidak bisa diakses langsung dari browser:

```text
application/
system/
```

CodeIgniter sudah memiliki guard `defined('BASEPATH')` pada file PHP, tetapi proteksi web server tetap disarankan.

## Checklist Sebelum Publish

- Pastikan `.env` sudah berisi credential database cPanel yang benar.
- Pastikan `.env` sudah berisi `CAFEEID_SUPER_USER` dan `CAFEEID_SUPER_PASS_HASH`.
- Gunakan HTTPS.
- Pastikan SSL valid dan redirect HTTP ke HTTPS di server/hosting.
- Pastikan permission folder tidak terlalu longgar. Gunakan writable hanya untuk folder yang memang perlu ditulis, seperti `application/cache` dan `application/logs`.
- Jangan upload file backup database, `.sql`, `.zip`, atau credential ke public web root.
- Ganti semua password admin cafe yang masih lemah.
- Hapus akun admin cafe yang tidak dipakai.
- Backup database sebelum deploy perubahan besar.

## Catatan Risiko Tersisa

- CodeIgniter 3 adalah framework lama. Keamanan aplikasi sudah diperketat, tetapi update dependency/server PHP tetap penting.
- CDN eksternal seperti Tailwind, Font Awesome, Google Fonts, dan DiceBear masih digunakan di beberapa view. Untuk lingkungan yang sangat ketat, sebaiknya self-host asset tersebut.
- Jangan simpan `.env` di repository publik. Untuk upload manual ke cPanel, pastikan file `.env` ikut terupload dan tetap diblok oleh `.htaccess`.
