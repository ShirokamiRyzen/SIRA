# SIRA — Sistem Informasi Ruang Aman

Platform pengaduan publik fasilitas wilayah dan infrastruktur berbasis GIS OpenMap, algoritma prioritas ranking Wilson Score, dan keterlibatan warga secara transparan.

---

## 🔐 Panduan Setup Google OAuth 2.0 (Google Login & Bind Account)

Aplikasi SIRA mendukung autentikasi Google untuk:
- **Masuk dengan Google (1-Klik)**
- **Daftar Akun Baru via Google** (input Nama dan Username, kata sandi opsional)
- **Tautkan / Lepas Tautan Akun (Bind / Unbind Google)** di Menu Pengaturan Akun

Berikut adalah panduan langkah demi langkah untuk mendapatkan kredensial `GOOGLE_CLIENT_ID` dan `GOOGLE_CLIENT_SECRET` dari Google Cloud Console.

---

### Langkah 1: Akses Google Cloud Console
1. Buka browser dan kunjungi [Google Cloud Console](https://console.cloud.google.com/).
2. Masuk menggunakan akun Google Anda.
3. Buat proyek baru atau pilih proyek yang sudah ada:
   - Klik dropdown proyek di bagian kiri atas navbar.
   - Klik **New Project**.
   - Beri nama proyek (misal: `SIRA-App`) dan klik **Create**.

---

### Langkah 2: Konfigurasi OAuth Consent Screen (Layar Persetujuan OAuth)
Sebelum membuat Client ID, Google mewajibkan konfigurasi layar persetujuan:
1. Pada menu navigasi sebelah kiri, buka **APIs & Services** > **OAuth consent screen** (atau cari *"OAuth consent screen"* di kotak pencarian).
2. Pilih **User Type**:
   - Pilih **External** (agar dapat diakses oleh akun Google publik), lalu klik **Create**.
3. Lengkapi informasi aplikasi (**App information**):
   - **App name**: `SIRA — Sistem Informasi Ruang Aman`
   - **User support email**: Pilih email Google Anda.
   - **Developer contact information**: Masukkan alamat email Anda.
   - Klik **Save and Continue**.
4. Bagian **Scopes**:
   - Klik **Add or Remove Scopes**.
   - Centang izin standar berikut:
     - `.../auth/userinfo.email`
     - `.../auth/userinfo.profile`
     - `openid`
   - Klik **Update** lalu klik **Save and Continue**.
5. Bagian **Test Users** (Jika status aplikasi masih *Testing*):
   - Tambahkan email Google Anda (dan email dosen/penguji) sebagai akun penguji.
   - Klik **Save and Continue** hingga selesai.

---

### Langkah 3: Membuat OAuth 2.0 Client Credentials
1. Pada menu navigasi sebelah kiri, buka **APIs & Services** > **Credentials**.
2. Klik tombol **+ CREATE CREDENTIALS** di bagian atas, lalu pilih **OAuth client ID**.
3. Isi formulir pembuatan kredensial:
   - **Application type**: Pilih **Web application**.
   - **Name**: `SIRA Web Client` (atau nama lain yang mudah dikenali).
4. Bagian **Authorized JavaScript origins**:
   - Tambahkan URI lokal:
     ```text
     http://localhost:8000
     http://127.0.0.1:8000
     ```
   *(Jika dideploy ke server produksi, tambahkan juga URL domain Anda, misal: `https://sira.yourdomain.com`)*
5. Bagian **Authorized redirect URIs** *(Sangat Penting)*:
   - Tambahkan Callback URL aplikasi SIRA:
     ```text
     http://localhost:8000/auth/google/callback
     http://127.0.0.1:8000/auth/google/callback
     ```
   *(Jika di server produksi, tambahkan: `https://sira.yourdomain.com/auth/google/callback`)*
6. Klik tombol **CREATE**.
7. Jendela pop-up akan menampilkan:
   - **Client ID** (contoh: `xxxxxxxxxxxx-xxxxxxxxxxxxxxxx.apps.googleusercontent.com`)
   - **Client Secret** (contoh: `GOCSPX-xxxxxxxxxxxxxxxxxxxxxxxx`)
   - Salin kedua nilai tersebut.

---

### Langkah 4: Simpan Kredensial ke File `.env`
Buka file `.env` di direktori proyek SIRA Anda, lalu tambahkan atau sesuaikan konfigurasi berikut:

```env
# URL Aplikasi Utama (Pastikan port sesuai dengan server lokal Anda)
APP_URL=http://localhost:8000

# Kredensial Google OAuth 2.0
GOOGLE_CLIENT_ID="MASUKKAN_CLIENT_ID_DARI_GOOGLE_CONSOLE"
GOOGLE_CLIENT_SECRET="MASUKKAN_CLIENT_SECRET_DARI_GOOGLE_CONSOLE"
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

> **Catatan Penting**:
> Jika Anda mengubah nilai di `.env`, jalankan pembersihan cache konfigurasi:
> ```bash
> php artisan config:clear
> ```

---

### Langkah 5: Menjalankan Aplikasi & Pengujian Fitur
1. Jalankan migrasi database (jika belum dijalankan):
   ```bash
   php artisan migrate
   ```
2. Jalankan server lokal:
   ```bash
   php artisan serve
   ```
3. Uji alur autentikasi di browser:
   - **Masuk dengan Google**: Buka `http://localhost:8000/login` lalu klik tombol **Masuk dengan Google**.
   - **Daftar Akun Baru via Google**: Pengguna baru akan diarahkan untuk memasukkan *Username* unik (nama otomatis terisi, kata sandi bersifat opsional).
   - **Tautkan Akun (Bind Google)**: Masuk dengan akun yang sudah ada, kunjungi **Menu Akun** (`http://localhost:8000/account`), lalu klik **Hubungkan Akun Google (Bind)**.
   - **Lepas Tautan (Unbind Google)**: Di Menu Akun, klik **Lepas Tautan (Unbind)**. *(Sistem akan meminta Anda membuat kata sandi terlebih dahulu jika akun belum memiliki kata sandi)*.

---

## 🧪 Menjalankan Automated Tests

Aplikasi SIRA dilengkapi dengan unit dan feature test otomatis menggunakan Pest PHP:

```bash
# Menjalankan seluruh test suite
php artisan test --compact

# Menjalankan pengujian khusus Google Auth & Akun
php artisan test tests/Feature/GoogleAuthAndAccountTest.php
```

---

## 📄 Lisensi
SIRA didistribusikan di bawah lisensi [MIT License](LICENSE).
