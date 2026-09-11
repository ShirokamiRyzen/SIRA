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
        <em>(Browser Tab)</em>
      </td>
      <td align="center">
        <img src="public/favicon-16x16.png" width="16" height="16" alt="16x16px Icon" /><br>
        <strong>16 x 16 px</strong><br>
        <code>favicon-16x16.png</code><br>
        <em>(Taskbar / Tab)</em>
      </td>
    </tr>
  </table>
</div>

- **Ukuran Master**: 512 × 512 piksel (format PNG, maskable, latar transparan dengan rasio kontras editorial).
- **Lokasi Berkas**: [`public/android-chrome-512x512.png`](file:///c:/Users/xredz/Projects/SIRA/public/android-chrome-512x512.png)
- **Manifest PWA**: Terdaftar di [`public/site.webmanifest`](file:///c:/Users/xredz/Projects/SIRA/public/site.webmanifest) dengan konfigurasi `display: standalone` dan `orientation: portrait-primary`.

---

## ⚡ Fitur Unggulan

- **📸 Multi-mode Capture & Visual Proof**  
  Unggah bukti foto dari penyimpanan perangkat atau rekam langsung melalui kamera/webcam di peramban secara *real-time*. Kompresi otomatis berbasis Canvas untuk efisiensi transfer data.
- **⚖️ Algoritma Perangkingan Wilson Score**  
  Menghitung tingkat urgensi perbaikan secara matematis menggunakan *Wilson Score Interval* berdasarkan rasio suara dukung (*upvotes*), penolakan (*downvotes*), dan volume partisipasi. Mengeliminasi bias manipulasi suara dan otomatis mengelompokkan laporan ke dalam tier **Critical**, **Urgent**, **Trending**, atau **Normal**.
- **🗺️ Pemetaan Geospasial & Heatmap Bebas Biaya (OpenFreeMap + MapLibre GL)**  
  Integrasi visualisasi peta interaktif berbasis OpenFreeMap dan MapLibre GL JS tanpa ketergantungan API key berbayar. Menyediakan *Geocoding reverse address*, radar deteksi radius multi-isu sejenis di sekitar lokasi, serta layer *density heatmap*.
- **🤖 Asisten Cerdas Terintegrasi (@Sira)**  
  Bot asisten AI `@Sira` yang dapat di-mention pada komentar laporan untuk memberikan analisis kelayakan teknis, rujukan regulasi keselamatan, maupun ringkasan eksekutif secara otomatis. Dilengkapi mekanisme *auto-fallback model pipeline*.
- **💬 Diskusi Warga & Real-Time SSE Notification**  
  Komentar bertingkat (*nested replies*), pelengkapan otomatis mention (`@username`), dan streaming notifikasi langsung di bilah navigasi tanpa perlu *reload* halaman melalui Server-Sent Events (SSE).
- **🛡️ Moderasi Terbuka & Verifikasi Lencana Warga**  
  Panel admin untuk memverifikasi akun warga terpercaya (*Verified Badge*), memantau siklus hidup status laporan (`Pending`, `In Progress`, `Resolved`, `Archived`), serta sistem audit publik yang transparan.

---

## 🛠️ Tech Stack

### Backend
- **Bahasa**: PHP 8.3+ (Fitur PHP 8 modern, Typed properties, Match expressions)
- **Framework**: [Laravel 13.x](https://laravel.com)
- **Reaktivitas**: [Livewire 4.x](https://livewire.laravel.com), [Livewire Volt](https://livewire.laravel.com/docs/volt), [Flux UI](https://flux.laravel.com)
- **Database**: SQLite (default untuk fleksibilitas lokal) / MySQL / PostgreSQL dengan pengindeksan geospasial (`latitude`, `longitude`, `geohash`)
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
- **PHP** >= 8.3
- **Ekstensi PHP**: `ext-gd`, `ext-mbstring`, `ext-curl`, `ext-sqlite3`, `ext-pdo`, `ext-xml`, `ext-zip`
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
   # Untuk SQLite (default)
   touch database/database.sqlite
   
   # Jalankan migrasi tabel
   php artisan migrate --seed
   ```

6. **Pasang Dependensi Frontend & Build Aset**
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

Proyek ini memiliki cakupan pengujian komprehensif (fitur pembuatan laporan, webcam, perankingan Wilson Score, komentar bertingkat, dan notifikasi).

Jalankan pengujian menggunakan runner Pest atau Artisan:
```bash
# Menjalankan suite pengujian ringkas
php artisan test --compact

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
APP_URL=http://127.0.0.1:8000

# Pengaturan Database (Default SQLite)
DB_CONNECTION=sqlite

# Pengaturan AI Assistant (@Sira)
OPENAI_API_KEY=your_api_key_here
OPENAI_BASE_URL=https://api.deepseek.com/v1   # atau provider OpenAI-compatible lainnya
OPENAI_MODEL=deepseek-chat
```

---

### Struktur Direktori Utama

```
SIRA/
├── app/
│   ├── Http/Controllers/       # Logika controller (Report, Comment, Heatmap, OgImage, Admin, Auth)
│   ├── Models/                 # Model Eloquent (Report, Comment, ReportVote, User, Notification)
│   ├── Services/               # Layanan bisnis (AiSummaryService, dsb.)
│   └── View/Components/        # Blade Component classes
├── config/                     # Konfigurasi aplikasi Laravel
├── database/
│   ├── factories/              # Factory data uji
│   ├── migrations/             # Skema tabel database (Reports, Votes, Comments, Geohash Indexes)
│   └── seeders/                # Pengisian data dummy awal
├── public/                     # Aset publik, favicon, manifest, dan master icon (512x512px)
├── resources/
│   ├── css/                    # Tailwind CSS v4 entrypoint
│   ├── fonts/                  # TrueType Fonts (Plus Jakarta Sans, Roboto untuk banner OG)
│   ├── js/                     # Skrip interaktif & inisialisasi MapLibre GL
│   └── views/                  # Tampilan Blade (reports, heatmap, admin, layouts, components)
├── routes/
│   ├── web.php                 # Rute web utama & sitemap XML
│   └── console.php             # Rute artisan command
└── tests/                      # Suite pengujian Pest PHP (Unit & Feature tests)
```

---

### Endpoint API & Integrasi

SIRA menyediakan beberapa endpoint REST & GeoJSON publik untuk integrasi eksternal:

| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
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
Dikonfigurasi pada [`public/site.webmanifest`](file:///c:/Users/xredz/Projects/SIRA/public/site.webmanifest) dan termuat otomatis pada layout utama:
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
