Product Requirements Document (PRD): Aplikasi Reservasi Lapangan Badminton
1. Ringkasan Produk
Aplikasi Reservasi Lapangan Badminton adalah platform digital yang dirancang untuk memudahkan pelanggan dalam mencari, memesan, dan membayar sewa lapangan secara online. Sistem ini juga menyediakan portal manajemen terpadu bagi pemilik/pengelola (Admin, Staf, Kasir) untuk mengelola ketersediaan lapangan, status pembayaran, proses check-in, hingga pengembalian dana (refund).

2. Tujuan dan Sasaran
Efisiensi Pemesanan: Mengotomatiskan proses booking lapangan yang sebelumnya manual untuk mencegah bentrok jadwal.

Transparansi Pembayaran: Menyediakan integrasi berbagai metode pembayaran dengan pencatatan status yang real-time.

Manajemen Operasional: Memudahkan staf dalam melakukan check-in pelanggan dan kasir dalam mengelola transaksi atau refund.

Peningkatan Layanan: Memberikan ruang bagi pelanggan untuk memberikan ulasan (review) dan mendapatkan notifikasi terkait pemesanan mereka.

3. Pengguna dan Peran (User Roles)
Sesuai dengan tabel users pada database, sistem ini memiliki 4 peran utama:

Customer: Pengguna akhir yang mencari lapangan, melakukan pemesanan, membayar, dan memberikan ulasan.

Admin: Pengelola utama yang memiliki akses penuh ke sistem (CRUD lapangan, jam operasional, promo, dan manajemen pengguna).

Kasir: Bertugas memverifikasi pembayaran (terutama manual/kasir) dan memproses permintaan refund.

Staff: Bertugas di lokasi untuk melakukan check-in pelanggan yang datang sesuai jadwal reservasi.

4. Fitur Utama dan Kebutuhan Sistem (Berdasarkan Database)
4.1. Manajemen Akun dan Otentikasi (users, activity_logs)
Registrasi & Login: Pengguna dapat mendaftar menggunakan nama, email, nomor telepon, dan password.

Pencatatan Aktivitas: Setiap aktivitas penting pengguna di dalam sistem akan dicatat otomatis, termasuk IP Address dan jenis aktivitasnya (Audit Trail).

4.2. Katalog dan Manajemen Lapangan (courts, reviews)
Daftar Lapangan (Customer): Menampilkan daftar lapangan yang tersedia beserta deskripsi dan harga per jam.

Manajemen Lapangan (Admin): Admin dapat menambah, mengubah detail, atau mengubah status lapangan (available, maintenance).

Sistem Ulasan: Setelah selesai bermain, customer dapat memberikan rating (bintang) dan komentar terhadap lapangan yang disewa.

4.3. Pemesanan dan Jadwal (reservations, time_slots)
Pemilihan Jadwal: Customer memilih tanggal, lapangan, dan slot waktu (berdasarkan tabel time_slots yang sudah ditentukan admin, misalnya 18:00-19:00).

Validasi Ketersediaan: Sistem harus memastikan kombinasi court_id, time_slot_id, dan date tidak tumpang tindih dengan reservasi berstatus confirmed atau pending.

Status Reservasi: Reservasi memiliki alur status yang jelas: pending -> confirmed / cancelled / expired -> checked_in.

4.4. Harga dan Promosi (promo_codes)
Kode Promo: Customer dapat memasukkan kode promo saat checkout. Sistem akan memvalidasi periode aktif promo (valid_from, valid_until), status (active), dan memotong total harga sesuai discount_percentage.

4.5. Pembayaran (payments)
Metode Pembayaran: Mendukung berbagai metode transaksi: transfer, qris, ewallet, dan cash (untuk pembayaran di tempat/melalui kasir).

Status Pembayaran: Setiap transaksi dipantau dengan status pending, success, atau failed. Perubahan status pembayaran yang sukses otomatis mengubah status reservasi menjadi confirmed.

4.6. Operasional di Tempat (check_ins)
Proses Check-In: Staff lapangan dapat memvalidasi kedatangan pelanggan dengan mencatat check-in. Sistem akan merekam staf yang bertugas dan waktu kedatangan. Status check-in dapat berupa checked_in atau no_show.

4.7. Pembatalan dan Pengembalian Dana (refunds)
Pengajuan Refund: Customer atau Kasir dapat mengajukan refund atas reservasi yang dibatalkan dengan menyertakan alasan.

Proses Persetujuan: Kasir atau Admin mengubah status refund (requested, approved, rejected, completed) dan mencatat siapa yang memprosesnya (processed_by).

4.8. Notifikasi dan Log Sistem (notifications, reservation_status_logs)
Notifikasi Pengguna: Sistem mengirimkan pesan dalam aplikasi (berstatus read/unread) kepada customer terkait pengingat jadwal, status pembayaran, atau perubahan reservasi.

Log Status Reservasi: Setiap perubahan status pesanan dicatat secara historis (dari status lama ke status baru) beserta siapa yang melakukan perubahan tersebut untuk mencegah kebingungan data.

5. Alur Kerja Utama (Key Workflows)
A. Alur Pemesanan Normal:

Customer masuk dan memilih lapangan & tanggal.

Sistem menampilkan time_slots yang tersedia.

Customer memilih slot, memasukkan kode promo (opsional), lalu sistem membuat reservations dengan status pending.

Sistem membuat rekaman di tabel payments.

Customer membayar. Sistem memperbarui payments menjadi success dan reservations menjadi confirmed.

B. Alur Kedatangan (Check-In):

Customer datang ke lokasi menunjukkan bukti reservasi.

Staff mencari data reservasi, lalu membuat rekaman di tabel check_ins menjadi checked_in.

Status reservations berubah menjadi checked_in.

C. Alur Refund:

Terjadi pembatalan lapangan (baik oleh admin karena maintenance atau customer sesuai kebijakan).

Data ditambahkan ke tabel refunds dengan status requested.

Kasir memverifikasi dan mentransfer dana kembali, lalu mengubah status menjadi completed.