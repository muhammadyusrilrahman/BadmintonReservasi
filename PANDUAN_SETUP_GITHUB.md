# Panduan Pull & Setup Repository di Laptop Lain

Panduan ini dibuat untuk memastikan aplikasi berjalan dengan lancar saat di-pull atau di-clone ke laptop/komputer lain, dan mencegah terjadinya error terkait *environment*, *database*, maupun *cache*.

## A. Jika Baru Pertama Kali (Clone)

Jika laptop tersebut **belum pernah** mendownload project ini sama sekali, ikuti langkah berikut:

1. **Clone Repository**
   Buka terminal di folder Laragon (misal: `C:\laragon\www`) dan jalankan:
   ```bash
   git clone https://github.com/muhammadyusrilrahman/BadmintonReservasi.git badminton
   cd badminton
   ```

2. **Jalankan Auto-Setup**
   Aplikasi ini sudah dilengkapi dengan script otomatis. Cukup jalankan perintah ini:
   ```bash
   composer run setup
   ```
   *(Perintah di atas otomatis akan menginstal composer, mencopy .env, generate key, migrate database, dan menginstal dependensi frontend NodeJS).*

3. **Konfigurasi File `.env` (PENTING)**
   Buka file `.env` di editor Anda dan pastikan hal-hal berikut sudah diisi:
   - **Database**:
     Pastikan `DB_CONNECTION=sqlite` (jika menggunakan SQLite bawaan) atau konfigurasi MySQL sesuai dengan laptop tersebut.
   - **Queue**:
     Ubah `QUEUE_CONNECTION=database` agar fitur email/notifikasi tidak error timeout.
   - **Email SMTP**:
     Jika menggunakan Gmail, pastikan baris ini diisi dengan benar:
     ```env
     MAIL_MAILER=smtp
     MAIL_HOST=smtp.gmail.com
     MAIL_PORT=465
     MAIL_USERNAME="akunmidtrans111@gmail.com"
     MAIL_PASSWORD="password_app_gmail_anda"
     MAIL_ENCRYPTION=ssl
     ```
   - **Midtrans**:
     Pastikan `MIDTRANS_SERVER_KEY`, `MIDTRANS_CLIENT_KEY`, dll sudah diisi.

4. **Tautkan Storage (Untuk Upload Bukti Transfer/Gambar)**
   Jalankan perintah berikut agar gambar bisa diakses publik:
   ```bash
   php artisan storage:link
   ```

---

## B. Jika Hanya Mengupdate (Pull) Perubahan Baru

Jika project sudah ada di laptop tersebut, dan Anda hanya ingin mengambil *update* terbaru (seperti perbaikan *Type Casting* barusan), ikuti langkah berikut secara berurutan:

1. **Tarik Perubahan dari GitHub**
   ```bash
   git pull origin main
   ```

2. **Perbarui Dependensi (Opsional tapi Disarankan)**
   Jika ada library/paket baru yang ditambahkan:
   ```bash
   composer install
   ```

3. **Perbarui Database**
   Sangat disarankan dijalankan untuk mencegah error 'Table not found' akibat belum ada tabel baru (seperti tabel `jobs` dll):
   ```bash
   php artisan migrate
   ```

4. **Bersihkan Cache**
   Ini adalah langkah **paling penting** agar konfigurasi lama yang tersimpan di memori laptop tersebut terhapus dan diganti dengan kode/konfigurasi yang baru di-pull:
   ```bash
   php artisan optimize:clear
   ```

5. **Jalankan Worker (Jika Ingin Test Kirim Email)**
   Jika Anda mengetes fitur yang mengirim notifikasi/email secara background, jangan lupa buka terminal baru dan jalankan:
   ```bash
   php artisan queue:work
   ```

## Catatan Khusus
- **Versi PHP**: Pastikan laptop tersebut menggunakan minimal **PHP 8.2**. Jika menggunakan PHP 8.1 ke bawah, aplikasi akan mengalami *Syntax Error* karena penggunaan fitur modern (seperti `readonly` property).
- Jangan pernah melakukan *commit* atau *push* file `.env` ke GitHub demi keamanan data sensitif.
