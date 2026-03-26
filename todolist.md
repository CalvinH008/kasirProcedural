TO-DO LIST PROJECT (STEP BY STEP)
🔹 PHASE 1 — Setup (Hari 1)✅
* Buat folder project
* Buat database kasir
* Buat tabel:
   * users
   * products
   * orders
   * order_items
* Buat file koneksi config.php
* Test koneksi berhasil
👉 Output:
Bisa connect ke database tanpa error
🔹 PHASE 2 — Login System (Hari 2–3)
* Buat form login
* Ambil data pakai $_POST
* Ambil user dari database
* Verifikasi password (password_verify)
* Simpan session login
* Redirect ke dashboard
* Buat logout
👉 Output:
Bisa login & logout, session jalan
🔹 PHASE 3 — Proteksi Halaman
* Cek $_SESSION di setiap halaman
* Kalau belum login → redirect ke login
👉 Output:
User ga bisa akses tanpa login
🔹 PHASE 4 — Admin CRUD Produk (Hari 4–6)
* Halaman list produk
* Tambah produk
* Edit produk
* Hapus produk
👉 Output:
Data produk bisa dikelola penuh
🔹 PHASE 5 — Cart System (Hari 7–8)
* Tambah produk ke cart (pakai session)
* Tampilkan isi cart
* Update qty
* Hapus item dari cart
👉 Output:
Cart jalan pakai $_SESSION
🔹 PHASE 6 — Checkout (Hari 9–10)
* Hitung total harga
* Simpan ke tabel orders
* Simpan ke order_items
* Kosongkan cart setelah checkout
👉 Output:
Transaksi tersimpan di database
🔹 PHASE 7 — History (Hari 11–12)
* Tampilkan list transaksi
* Detail transaksi (pakai JOIN)
👉 Output:
Bisa lihat riwayat pembelian