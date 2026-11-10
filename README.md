<p align="center">
  <img src="public/android-chrome-512x512.png" width="160" height="160" alt="SIRA Icon 512x512px" style="border-radius: 28px; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
</p>

<h1 align="center">SIRA — Sistem Informasi Ruang Aman</h1>

<p align="center">
  <strong>Platform Terbuka Pengawasan Fasilitas Kota & Kawal Ruang Publik Berbasis GIS OpenFreeMap</strong>
</p>

<p align="center">
  <a href="https://php.net"><img src="https://img.shields.io/badge/PHP-8.3%2B-777BB4.svg?style=flat-square&logo=php&logoColor=white" alt="PHP Version"></a>
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-13.x-FF2D20.svg?style=flat-square&logo=laravel&logoColor=white" alt="Laravel Framework"></a>
  <a href="https://tailwindcss.com"><img src="https://img.shields.io/badge/Tailwind_CSS-v4.0-38B2AC.svg?style=flat-square&logo=tailwind-css&logoColor=white" alt="Tailwind CSS v4"></a>
  <a href="https://livewire.laravel.com"><img src="https://img.shields.io/badge/Livewire-v4-FB70A9.svg?style=flat-square&logo=livewire&logoColor=white" alt="Livewire"></a>
  <a href="https://pestphp.com"><img src="https://img.shields.io/badge/Pest-v5.0-blueviolet.svg?style=flat-square&logo=pest&logoColor=white" alt="Pest Tests"></a>
  <a href="https://vitejs.dev"><img src="https://img.shields.io/badge/Vite-v8.0-646CFF.svg?style=flat-square&logo=vite&logoColor=white" alt="Vite"></a>
  <a href="LICENSE"><img src="https://img.shields.io/badge/License-MIT-green.svg?style=flat-square" alt="License MIT"></a>
</p>

---

## 📌 Daftar Isi
1. [Tentang SIRA](#-tentang-sira)
2. [Ikon Aplikasi (512x512px & Aset PWA)](#-ikon-aplikasi-512x512px--aset-pwa)
3. [Fitur Unggulan](#-fitur-unggulan)
4. [Tech Stack](#-tech-stack)
5. [Dokumentasi & Panduan Instalasi (Docs)](#-dokumentasi--panduan-instalasi-docs)
   - [Persyaratan Sistem](#persyaratan-sistem)
   - [Langkah Instalasi Cepat](#langkah-instalasi-cepat)
   - [Menjalankan Server Pengembangan](#menjalankan-server-pengembangan)
   - [Pengujian Otomatis (Testing)](#pengujian-otomatis-testing)
   - [Format Kode (Pint)](#format-kode-lint-dan-format)
   - [Konfigurasi Lingkungan (.env)](#konfigurasi-lingkungan-env)
   - [Panduan Setup Google OAuth 2.0](#-panduan-setup-google-oauth-20-google-login--bind-account)
   - [Struktur Direktori Utama](#struktur-direktori-utama)
   - [Endpoint API & Integrasi](#endpoint-api--integrasi)
6. [Optimasi SEO & Metadata](#-optimasi-seo--metadata)
   - [Dynamic Open Graph 1200x630px](#1-dynamic-open-graph-1200x630px-gambar-banner)
   - [Twitter Cards & Rich Previews](#2-twitter-cards--rich-previews)
   - [PWA Manifest & Multiresolution Icons](#3-pwa-manifest--multiresolution-icons)
   - [JSON-LD Structured Data Schema](#4-json-ld-structured-data-schema)
   - [XML Sitemap Otomatis](#5-xml-sitemap-otomatis)
   - [SEO-Friendly Semantic Markup](#6-seo-friendly-semantic-markup)
7. [Lisensi](#-lisensi)

---

## 🏙️ Tentang SIRA

**SIRA (Sistem Informasi Ruang Aman)** adalah platform urun daya (*civic crowdfunding of awareness*) modern yang dirancang untuk menjembatani suara warga dengan pemangku kebijakan publik. SIRA memungkinkan warga mendokumentasikan, memetakan, memprioritaskan, dan mengawal perbaikan fasilitas publik secara transparan, akuntabel, dan bebas birokrasi berbelit.

Mulai dari jalan berlubang, lampu penerangan padam (PJU), saluran drainase tersumbat, trotoar rusak, fasilitas ramah difabel, tumpukan sampah liar, hingga fasilitas umum terbengkalai—semua dapat dilaporkan lengkap dengan bukti visual, titik koordinat GPS presisi, dan dievaluasi urgensinya oleh masyarakat setempat melalui sistem perankingan statistik.

---

## 🎨 Ikon Aplikasi (512x512px & Aset PWA)

Aplikasi SIRA dilengkapi dengan bundel ikon resmi beresolusi tinggi yang dirancang untuk standar Web, Mobile, dan Progressive Web App (PWA):

<div align="center">
  <table>
    <tr>
      <td align="center">
        <img src="public/android-chrome-512x512.png" width="128" height="128" alt="512x512px Icon" style="border-radius: 18px;" /><br>
        <strong>512 x 512 px</strong><br>
        <code>android-chrome-512x512.png</code><br>
        <em>(PWA Master Icon / Splash)</em>
      </td>
      <td align="center">
        <img src="public/android-chrome-192x192.png" width="96" height="96" alt="192x192px Icon" style="border-radius: 14px;" /><br>
        <strong>192 x 192 px</strong><br>
        <code>android-chrome-192x192.png</code><br>
        <em>(Android Launcher)</em>
      </td>
      <td align="center">
        <img src="public/apple-touch-icon.png" width="90" height="90" alt="180x180px Icon" style="border-radius: 12px;" /><br>
        <strong>180 x 180 px</strong><br>
        <code>apple-touch-icon.png</code><br>
        <em>(iOS Home Screen)</em>
      </td>
      <td align="center">
        <img src="public/favicon-32x32.png" width="32" height="32" alt="32x32px Icon" /><br>
        <strong>32 x 32 px</strong><br>
        <code>favicon-32x32.png</code><br>
        <em>(Browser Tab HD)</em>
      </td>
      <td align="center">
        <img src="public/favicon-16x16.png" width="16" height="16" alt="16x16px Icon" /><br>
        <strong>16 x 16 px</strong><br>
        <code>favicon-16x16.png</code><br>
        <em>(Browser Tab Favicon)</em>
      </td>
    </tr>
  </table>
</div>

---

## 🚀 Fitur Unggulan

- **Pemetaan Geospasial Heatmap (OpenFreeMap + MapLibre GL)**: Visualisasi titik koordinat laporan interaktif dengan gradasi warna berdasarkan tingkat urgensi.
- **Sistem Prioritas Wilson Score**: Algoritma statistik batas bawah interval kepercayaan binomial 95% untuk menyaring laporan paling mendesak secara objektif.
- **Autentikasi Fleksibel (Google OAuth & Manual)**: Mendukung login sekali klik dengan Google, registrasi cepat via Google, serta fitur bind/unbind akun Google di Menu Akun.
- **Kamera & Dokumentasi Cepat**: Pengambilan bukti foto langsung via kamera perangkat (Webcam API) dengan kompresi sisi klien otomatis.
- **Diskusi & Urun Rembug Warga**: Kolom komentar bertingkat (nested replies) dengan dukungan mention `@username` otomatis dan asisten AI responsif (`@Sira`).
- **Dashboard Manajemen Admin**: Panel verifikasi lencana warga terpercaya (*Verified Citizen*), moderasi status laporan (*In Review, Processed, Resolved, Rejected*), dan pengelolaan pengguna.
- **Notifikasi Real-time**: Menggunakan Server-Sent Events (SSE) untuk pembaruan instan pada mention, reply, dan status laporan.
- **Desain Minimalis Notion-style & Responsif Penuh**: Mendukung tema gelap (*dark mode*) dan tema terang (*light mode*) dengan palet monokrom bersih.

---

## 🛠️ Tech Stack

### Backend
- **Framework**: [Laravel 13.x](https://laravel.com)
- **Reaktivitas**: [Livewire 4.x](https://livewire.laravel.com), [Livewire Volt](https://livewire.laravel.com/docs/volt), [Flux UI](https://flux.laravel.com)
- **Database**: SQLite (default untuk fleksibilitas lokal) / MySQL / MariaDB / PostgreSQL dengan pengindeksan geospasial (`latitude`, `longitude`, `geohash`)
- **Autentikasi Sosial**: [Laravel Socialite](https://laravel.com/docs/socialite) untuk Google OAuth 2.0
- **CLI & Formatting**: [Laravel Boost](https://laravel.com/docs/ai), [Laravel Pint](https://laravel.com/docs/pint)
- **Testing**: [Pest PHP 5](https://pestphp.com) & [PHPUnit](https://phpunit.de)

### Frontend
- **CSS Styling**: [Tailwind CSS v4.0](https://tailwindcss.com) (dengan `@tailwindcss/vite` modern)
- **Peta & GIS**: [OpenFreeMap](https://openfreemap.org) + [MapLibre GL JS](https://maplibre.org)
- **Pustaka Rendering**: [Marked.js](https://marked.js.org) (Markdown compiler), [KaTeX](https://katex.org) (Math & formula rendering)
- **Interaktivitas**: [Alpine.js](https://alpinejs.dev) & Vanilla JavaScript
- **Asset Bundler**: [Vite 8.0](https://vitejs.dev)

### AI & Grafis Dinamis
- **AI Integration**: OpenAI-compatible client API (DeepSeek V3/Pro, GPT-4o, dsb.) dengan fallback cerdas.
- **Dynamic Image Generator**: PHP GD Library dengan TrueType font rendering (*Plus Jakarta Sans* & *Roboto*) untuk memproduksi banner Open Graph beresolusi 1200x630px secara *on-the-fly*.

---

## 📚 Dokumentasi & Panduan Instalasi (Docs)

### Persyaratan Sistem
Pastikan lingkungan Anda memenuhi spesifikasi berikut:
- **PHP** >= 8.3 (Disarankan PHP 8.4)
- **Ekstensi PHP**: `ext-gd`, `ext-mbstring`, `ext-curl`, `ext-sqlite3` (atau driver database yang digunakan), `ext-pdo`, `ext-xml`, `ext-zip`
- **Composer** >= 2.6
- **Node.js** >= 18.x & **NPM** >= 9.x
- **Git**

---

### Langkah Instalasi Cepat

1. **Clone Repositori**
   ```bash
   git clone https://github.com/ShirokamiRyzen/SIRA.git
   cd SIRA
   ```

2. **Pasang Dependensi Backend (Composer)**
   ```bash
   composer install
   ```

3. **Duplikasi Berkas Lingkungan (.env)**
   ```bash
   cp .env.example .env
   ```

4. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

5. **Inisialisasi Database & Migrasi**
   ```bash
   # Jalankan migrasi tabel
   php artisan migrate
   ```

6. **Buat Akun Pertama (Interaktif)**
 ```bash
 php artisan app:add-user
 ```
 Perintah akan menanyakan **username**, **nama lengkap**, **password** (minimal 6 karakter), dan **role** (`admin`, `verified`, atau `user`). Role `admin` otomatis mendapatkan akses administrator sekaligus status terverifikasi.

 Bisa juga dijalankan non-interaktif dengan opsi:
 ```bash
 php artisan app:add-user --username=admin --name="Administrator" --password=rahasia123 --role=admin
 ```

7. **Pasang Dependensi Frontend & Build Aset**
 ```bash
 npm install
 npm run build
 ```

---

### Menjalankan Server Pengembangan

SIRA telah dilengkapi dengan skrip pengembangan paralel menggunakan `concurrently`. Cukup jalankan perintah berikut:

```bash
composer run dev
```

Perintah di atas secara otomatis akan menjalankan:
1. **Server HTTP Laravel**: `php artisan serve` (biasanya berjalan di `http://127.0.0.1:8000`)
2. **Antrean Pekerjaan**: `php artisan queue:listen --tries=1`
3. **Vite Live Server**: `npm run dev` (HMR untuk Blade & Tailwind CSS v4)

Atau jika ingin menjalankan secara terpisah di terminal terpisah:
```bash
# Terminal 1
php artisan serve

# Terminal 2
npm run dev
```

---

### Pengujian Otomatis (Testing)

Proyek ini memiliki cakupan pengujian komprehensif (fitur pembuatan laporan, webcam, perankingan Wilson Score, komentar bertingkat, notifikasi, serta Google OAuth & Account Management).

Jalankan pengujian menggunakan runner Pest atau Artisan:
```bash
# Menjalankan seluruh test suite ringkas
php artisan test --compact

# Menjalankan pengujian khusus Google Auth & Akun
php artisan test tests/Feature/GoogleAuthAndAccountTest.php

# Atau jalankan Pest langsung
vendor/bin/pest
```

---

### Format Kode (Lint dan Format)

Untuk menjaga standar kode PSR-12 dan konvensi Laravel:
```bash
vendor/bin/pint --format agent
```

---

### Konfigurasi Lingkungan (.env)

Beberapa variabel lingkungan penting yang dapat Anda sesuaikan di berkas `.env`:

```env
APP_NAME=SIRA
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

# Pengaturan Database
DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sira
DB_USERNAME=root
DB_PASSWORD=

# Kredensial Google OAuth 2.0 (Google Login & Bind Account)
GOOGLE_CLIENT_ID=your_google_client_id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"

# Pengaturan AI Assistant (@Sira)
OPENAI_API=https://ai.rizuu.id/v1
OPENAI_KEY=your_api_key_here
OPENAI_MODEL=deepseek-v4-pro
```

---

### 🔐 Panduan Setup Google OAuth 2.0 (Google Login & Bind Account)

Aplikasi SIRA mendukung autentikasi Google untuk:
- **Masuk dengan Google (1-Klik)**
- **Daftar Akun Baru via Google** (input Nama dan Username, kata sandi opsional)
- **Tautkan / Lepas Tautan Akun (Bind / Unbind Google)** di Menu Pengaturan Akun

Berikut adalah panduan langkah demi langkah untuk mendapatkan kredensial `GOOGLE_CLIENT_ID` dan `GOOGLE_CLIENT_SECRET` dari Google Cloud Console.

#### Langkah 1: Akses Google Cloud Console
1. Kunjungi [Google Cloud Console](https://console.cloud.google.com/).
2. Masuk menggunakan akun Google Anda.
3. Buat proyek baru atau pilih proyek yang sudah ada:
   - Klik dropdown proyek di bagian kiri atas navbar.
   - Klik **New Project**.
   - Beri nama proyek (misal: `SIRA-App`) dan klik **Create**.

#### Langkah 2: Konfigurasi OAuth Consent Screen (Layar Persetujuan OAuth)
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

#### Langkah 3: Membuat OAuth 2.0 Client Credentials
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
   *(Jika dideploy ke server produksi, tambahkan juga URL domain publik Anda, misal: `https://sira.yourdomain.com`)*
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
   - Salin kedua nilai tersebut ke file `.env` Anda.

#### Langkah 4: Simpan Kredensial ke File `.env`
Buka file `.env` di direktori proyek SIRA Anda, lalu tambahkan atau sesuaikan konfigurasi berikut:

```env
# URL Aplikasi Utama (Pastikan port sesuai dengan server lokal Anda)
APP_URL=http://localhost:8000

# Kredensial Google OAuth 2.0
GOOGLE_CLIENT_ID="MASUKKAN_CLIENT_ID_DARI_GOOGLE_CONSOLE"
GOOGLE_CLIENT_SECRET="MASUKKAN_CLIENT_SECRET_DARI_GOOGLE_CONSOLE"
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

> **Catatan**: Jika Anda mengubah nilai di `.env`, jalankan pembersihan cache konfigurasi:
> ```bash
> php artisan config:clear
> ```

#### Langkah 5: Pengujian Fitur Google Auth
1. Pastikan migrasi database telah berjalan (`php artisan migrate`).
2. Jalankan server lokal (`php artisan serve`).
3. Buka halaman masuk di `http://localhost:8000/login` lalu klik tombol **Masuk dengan Google**.
4. Akun baru yang belum terdaftar akan diarahkan untuk melengkapi *Username* unik (nama otomatis terisi dari Google, kata sandi bersifat opsional).
5. Pada akun yang sudah terdaftar, Anda dapat menautkan atau melepas akun Google kapan saja melalui **Menu Akun** (`/account`).

---

### Struktur Direktori Utama

```
SIRA/
├── app/
│   ├── Http/Controllers/       # Logika controller (Report, Comment, Heatmap, OgImage, Admin, Auth, GoogleAuth, Account)
│   ├── Models/                 # Model Eloquent (Report, Comment, ReportVote, User, Notification)
│   ├── Services/               # Layanan bisnis (AiSummaryService, dsb.)
│   └── View/Components/        # Blade Component classes
├── config/                     # Konfigurasi aplikasi Laravel
├── database/
│   ├── factories/              # Factory data uji
│   ├── migrations/             # Skema tabel database (Reports, Votes, Comments, Users, Geohash Indexes)
│   └── seeders/                # Pengisian data dummy awal
├── public/                     # Aset publik, favicon, manifest, dan master icon (512x512px)
├── resources/
│   ├── css/                    # Tailwind CSS v4 entrypoint
│   ├── fonts/                  # TrueType Fonts (Plus Jakarta Sans, Roboto untuk banner OG)
│   ├── js/                     # Skrip interaktif & inisialisasi MapLibre GL
│   └── views/                  # Tampilan Blade (reports, heatmap, admin, layouts, components, auth, account)
├── routes/
│   ├── web.php                 # Rute web utama, otentikasi Google, dan sitemap XML
│   └── console.php             # Rute artisan command
└── tests/                      # Suite pengujian Pest PHP (Unit & Feature tests)
```

---

### Endpoint API & Integrasi

SIRA menyediakan beberapa endpoint REST & GeoJSON publik untuk integrasi eksternal:

| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `GET` | `/auth/google` | Pengalihan ke Google OAuth 2.0 untuk login / bind akun |
| `GET` | `/auth/google/callback` | Callback penanganan autentikasi dari Google OAuth 2.0 |
| `GET` | `/api/reports/heatmap` | Menghasilkan FeatureCollection GeoJSON untuk visualisasi heatmap peta |
| `GET` | `/api/geocode/search?q={query}` | Pencarian lokasi berbasis OpenStreetMap Nominatim |
| `GET` | `/api/users/mention?q={query}` | Rekomendasi nama pengguna untuk autocomplete mention `@username` |
| `GET` | `/notifications/stream` | Endpoint Server-Sent Events (SSE) untuk streaming notifikasi real-time |
| `GET` | `/reports/{id}/og-image` | Gambar dinamis OpenGraph berukuran 1200x630px untuk laporan spesifik |
| `GET` | `/og-image/default` | Gambar dinamis OpenGraph berukuran 1200x630px untuk beranda platform |
| `GET` | `/sitemap.xml` | Indeks peta situs XML untuk web crawler mesin pencari |

---

## 🔍 Optimasi SEO & Metadata

SIRA dirancang dengan arsitektur **SEO-First** agar laporan warga mudah terindeks oleh mesin pencari (Google, Bing) dan tampil profesional saat dibagikan ke platform media sosial maupun aplikasi perpesanan (WhatsApp, Telegram, X/Twitter, LinkedIn, Facebook).

### 1. Dynamic Open Graph (1200x630px Gambar Banner)
Setiap laporan publik secara otomatis memiliki kartu visual OpenGraph 1200×630 piksel yang di-render secara dinamis menggunakan driver PHP GD (`OgImageController`).
- **Elemen Gambar**: Judul laporan, kategori, status penanganan, badge urgensi (*Critical / Urgent / Normal*), titik koordinat GIS, jumlah dukungan suara, identitas pelapor, serta *preview thumbnail* foto asli laporan warga.
- **Response Header**: Memanfaatkan *cache-control* cerdas (`public, max-age=86400, stale-while-revalidate=604800`) untuk performa rendering secepat kilat.

### 2. Twitter Cards & Rich Previews
Dukungan penuh tag metadata Twitter Card (`summary_large_image`) dengan judul, deskripsi kontekstual, dan tautan kanonikal:
```html
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Judul Laporan Fasilitas — SIRA">
<meta name="twitter:description" content="Deskripsi ringkas masalah fasilitas publik...">
<meta name="twitter:image" content="https://domain.tld/reports/{id}/og-image">
```

### 3. PWA Manifest & Multiresolution Icons
Dikonfigurasi pada `public/site.webmanifest` dan termuat otomatis pada layout utama:
- `favicon-16x16.png` & `favicon-32x32.png` untuk tab peramban.
- `apple-touch-icon.png` (180x180px) untuk iOS Safari Add to Home Screen.
- `android-chrome-192x192.png` & `android-chrome-512x512.png` (any maskable) untuk Android Splash Screen dan instalasi PWA desktop.
- `theme-color` adaptif terhadap mode gelap (`#111111`) dan mode terang (`#FBFBFA`).

### 4. JSON-LD Structured Data Schema
Markup Schema.org tertanam pada tag `<head>` untuk membantu Google memahami konteks website dan entitas pelaporan publik:
```json
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "SIRA — Sistem Informasi Ruang Aman",
  "alternateName": "SIRA",
  "url": "https://sira.local",
  "description": "Platform pengaduan publik berbasis GIS OpenMap dengan algoritma prioritas ranking Wilson Score...",
  "image": "https://sira.local/android-chrome-512x512.png"
}
```

### 5. XML Sitemap Otomatis
Sitemap dinamis yang dapat diakses di `/sitemap.xml` secara otomatis memperbarui indeks rute penting dan 500 laporan publik terbaru lengkap dengan tag `<lastmod>`, `<changefreq>`, dan nilai `<priority>`.

### 6. SEO-Friendly Semantic Markup
- Struktur hierarki heading tunggal `<h1>` pada setiap halaman.
- Atribut `alt` deskriptif pada seluruh elemen gambar.
- Tag `<link rel="canonical">` dinamis untuk mencegah penalti konten duplikat.
- Waktu muat cepat (*First Contentful Paint*) berkat optimasi Tailwind CSS v4 dan bundler modern Vite.

---

## 📄 Lisensi

Platform SIRA dirilis di bawah lisensi terbuka [MIT License](LICENSE). Anda bebas menggunakan, memodifikasi, dan mendistribusikan kode ini untuk kepentingan pengembangan fasilitas kota dan keterbukaan informasi publik.
