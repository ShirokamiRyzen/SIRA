@extends('layouts.app')

@section('title', 'Buat Laporan Baru - SIRA')

@section('content')
    <div class="max-w-4xl mx-auto my-6 space-y-6">
        <div
            class="bg-white dark:bg-[#141414] p-6 sm:p-10 rounded-3xl border border-slate-200 dark:border-[#222222] shadow-sm">
            <div class="mb-8">
                <a href="{{ route('reports.index') }}"
                    class="inline-flex items-center text-xs font-semibold text-emerald-700 dark:text-emerald-400 hover:underline mb-2">
                    &larr; Kembali ke Feed Laporan
                </a>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-[#EDEDEC] tracking-tight">Laporkan
                    Masalah Baru</h1>
                <p class="text-sm text-slate-500 dark:text-[#888888] mt-1">
                    Unggah bukti foto dan tandai titik lokasi pada peta. Sistem akan otomatis mendeteksi nama daerah via
                    OpenFreeMap.
                </p>
            </div>

            <form id="reportForm" action="{{ route('reports.store') }}" method="POST" class="space-y-8">
                @csrf

                <!-- Hidden input untuk Base64 dan Data Koordinat / Wilayah -->
                <input type="hidden" name="image_base64" id="imageBase64">
                <input type="hidden" name="latitude" id="inputLatitude" value="{{ old('latitude', '-6.914744') }}">
                <input type="hidden" name="longitude" id="inputLongitude" value="{{ old('longitude', '107.609810') }}">
                <input type="hidden" name="province" id="inputProvince" value="{{ old('province') }}">
                <input type="hidden" name="city" id="inputCity" value="{{ old('city') }}">
                <input type="hidden" name="district" id="inputDistrict" value="{{ old('district') }}">
                <input type="hidden" name="subdistrict" id="inputSubdistrict" value="{{ old('subdistrict') }}">
                <input type="hidden" name="formatted_address" id="inputAddress" value="{{ old('formatted_address') }}">
                <input type="hidden" name="osm_place_id" id="inputOsmId" value="{{ old('osm_place_id') }}">

                <!-- 1. Bagian Upload Foto dengan Kompresi 80% (Kamera / Galeri) -->
                <div>
                    <label class="block text-sm font-bold text-slate-900 dark:text-[#EDEDEC] mb-1">
                        Foto Bukti Laporan <span class="text-rose-500">*</span>
                    </label>
                    <p class="text-xs text-slate-400 dark:text-[#787774] mb-3">
                        Pilih foto dari galeri berkas atau ambil langsung menggunakan kamera.
                    </p>

                    <!-- Hidden Inputs: Galeri & Kamera Langsung -->
                    <input type="file" id="galleryInput" accept="image/*" class="hidden">
                    <input type="file" id="cameraInput" accept="image/*" capture="environment" class="hidden">

                    <div class="border-2 border-dashed border-slate-300 dark:border-[#282828] hover:border-emerald-500 dark:hover:border-emerald-500 rounded-3xl p-6 sm:p-8 text-center transition relative bg-slate-50 dark:bg-[#181818]"
                        id="dropZone">

                        <!-- State 1: Tombol Pilihan Sumber Foto -->
                        <div id="uploadPlaceholder" class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-w-md mx-auto">
                                <!-- Tombol Buka Kamera -->
                                <button
                                    type="button"
                                    id="btnTriggerCamera"
                                    class="flex flex-col items-center justify-center p-5 rounded-2xl border border-slate-200 dark:border-[#2A2A2A] bg-white dark:bg-[#141414] hover:border-emerald-500 dark:hover:border-emerald-500 hover:bg-emerald-50/40 dark:hover:bg-emerald-950/30 active:scale-[0.98] transition cursor-pointer group shadow-xs"
                                >
                                    <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-2.5 group-hover:scale-110 transition">
                                        <flux:icon name="camera" class="w-6 h-6" />
                                    </div>
                                    <span class="text-sm font-bold text-slate-900 dark:text-[#EDEDEC]">Gunakan Kamera</span>
                                    <span class="text-[11px] text-slate-400 dark:text-[#787774] mt-0.5">Ambil foto langsung</span>
                                </button>

                                <!-- Tombol Buka Galeri -->
                                <button
                                    type="button"
                                    id="btnTriggerGallery"
                                    class="flex flex-col items-center justify-center p-5 rounded-2xl border border-slate-200 dark:border-[#2A2A2A] bg-white dark:bg-[#141414] hover:border-emerald-500 dark:hover:border-emerald-500 hover:bg-emerald-50/40 dark:hover:bg-emerald-950/30 active:scale-[0.98] transition cursor-pointer group shadow-xs"
                                >
                                    <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-2.5 group-hover:scale-110 transition">
                                        <flux:icon name="photo" class="w-6 h-6" />
                                    </div>
                                    <span class="text-sm font-bold text-slate-900 dark:text-[#EDEDEC]">Pilih dari Galeri</span>
                                    <span class="text-[11px] text-slate-400 dark:text-[#787774] mt-0.5">Berkas JPG, PNG, WebP</span>
                                </button>
                            </div>

                            <p class="text-xs text-slate-400 dark:text-[#787774] pt-1">
                                Atau seret dan letakkan berkas foto langsung ke area ini
                            </p>
                        </div>

                        <!-- State 2: Preview Foto Terkompresi -->
                        <div id="previewContainer" class="hidden space-y-3">
                            <div class="relative inline-block max-w-full">
                                <img id="imagePreview" src="" alt="Preview Bukti Laporan"
                                    class="max-h-72 mx-auto rounded-2xl shadow-sm border border-slate-200 dark:border-[#282828] object-cover">
                            </div>

                            <div class="flex flex-wrap items-center justify-center gap-2 pt-1">
                                <div class="text-xs font-semibold text-emerald-700 dark:text-emerald-300 bg-emerald-100/80 dark:bg-emerald-950/60 px-3 py-1.5 rounded-full"
                                    id="compressionInfo">
                                    Kompresi 80% Berhasil
                                </div>
                                <button type="button" id="btnChangePhoto"
                                    class="px-3 py-1.5 text-xs font-semibold rounded-full bg-slate-200 hover:bg-slate-300 dark:bg-[#282828] dark:hover:bg-[#333333] text-slate-800 dark:text-[#EDEDEC] transition cursor-pointer inline-flex items-center gap-1.5">
                                    <flux:icon name="arrow-path" class="w-3.5 h-3.5" />
                                    <span>Ganti Foto</span>
                                </button>
                                <button type="button" id="btnRemovePhoto"
                                    class="px-3 py-1.5 text-xs font-semibold rounded-full bg-rose-100 hover:bg-rose-200 dark:bg-rose-950/60 dark:hover:bg-rose-900/60 text-rose-700 dark:text-rose-300 transition cursor-pointer inline-flex items-center gap-1.5">
                                    <flux:icon name="trash" class="w-3.5 h-3.5" />
                                    <span>Hapus</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    @error('image_base64')
                        <p class="text-xs text-rose-600 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 2. Informasi Pokok Masalah -->
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-[#CCCCCC] mb-2">
                            Kategori Masalah <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5">
                            @foreach($categories as $catKey => $cat)
                                <label class="relative flex items-center gap-2.5 p-3 rounded-2xl border border-slate-200 dark:border-[#282828] bg-white dark:bg-[#181818] hover:border-emerald-500/60 dark:hover:border-emerald-500/60 cursor-pointer transition has-checked:border-emerald-600 dark:has-checked:border-emerald-400 has-checked:bg-emerald-50/50 dark:has-checked:bg-emerald-950/30 has-checked:ring-2 has-checked:ring-emerald-500/30 shadow-2xs">
                                    <input type="radio" name="category" value="{{ $catKey }}" {{ old('category', 'infrastruktur') === $catKey ? 'checked' : '' }} class="sr-only">
                                    <div class="w-7 h-7 rounded-xl flex items-center justify-center shrink-0" style="background-color: {{ $cat['color'] }}20; color: {{ $cat['color'] }};">
                                        <flux:icon name="{{ $cat['icon'] }}" class="w-4 h-4" />
                                    </div>
                                    <span class="text-xs font-bold text-slate-800 dark:text-[#EDEDEC] truncate">{{ $cat['label'] }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('category')
                            <p class="text-xs text-rose-600 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="title"
                            class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-[#CCCCCC] mb-1.5">
                            Judul Masalah <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" required
                            placeholder="Contoh: Lubang Jalan Dalam di Depan Halte Dago"
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-[#282828] bg-white dark:bg-[#181818] text-slate-900 dark:text-[#EDEDEC] placeholder-slate-400 dark:placeholder-[#666666] text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        @error('title')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description"
                            class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-[#CCCCCC] mb-1.5">
                            Deskripsi Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="description" name="description" rows="4" required
                            placeholder="Jelaskan detail masalah, dampak, dan perkiraan sudah berapa lama terjadi..."
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-[#282828] bg-white dark:bg-[#181818] text-slate-900 dark:text-[#EDEDEC] placeholder-slate-400 dark:placeholder-[#666666] text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- 3. Peta Interaktif OpenFreeMap & Koordinat -->
                <div class="space-y-4">
                    <!-- Label & Tombol Deteksi Lokasi -->
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="block text-sm font-bold text-slate-900 dark:text-[#EDEDEC]">
                                Titik Lokasi <span class="text-rose-500">*</span>
                            </label>
                            <p class="text-xs text-slate-400 dark:text-[#787774]">Cari lokasi, geser pin pada peta, atau klik deteksi GPS.</p>
                        </div>
                        <button type="button" id="btnGeolocate"
                            class="inline-flex items-center space-x-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-[#222222] dark:hover:bg-[#282828] active:bg-slate-300 text-slate-700 dark:text-[#EDEDEC] font-semibold text-xs rounded-xl transition cursor-pointer">
                            <flux:icon name="viewfinder-circle"
                                class="w-3.5 h-3.5 text-slate-600 dark:text-[#EDEDEC] shrink-0" />
                            <span id="btnGeolocateText">Deteksi Lokasi Saya (GPS)</span>
                        </button>
                    </div>

                    <!-- Input Pencarian Lokasi Cepat -->
                    <div class="relative">
                        <div class="flex items-center bg-white dark:bg-[#1C1C1B] border border-slate-300 dark:border-[#2E2E2E] rounded-xl px-3 py-2 shadow-2xs focus-within:ring-2 focus-within:ring-emerald-500/20 focus-within:border-emerald-500 transition">
                            <flux:icon name="magnifying-glass" class="w-4 h-4 text-slate-400 dark:text-[#787774] shrink-0 mr-2" />
                            <input type="text" id="reportLocationSearch"
                                placeholder="Cari nama tempat, kampus, jalan, atau daerah (contoh: STT Wastukancana)..."
                                class="w-full bg-transparent text-xs text-slate-900 dark:text-[#EDEDEC] placeholder-slate-400 dark:placeholder-[#787774] focus:outline-hidden"
                                autocomplete="off" />
                            <button type="button" id="clearReportSearchBtn" class="hidden text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-0.5 cursor-pointer">
                                <flux:icon name="x-mark" class="w-3.5 h-3.5" />
                            </button>
                            <div id="reportSearchSpinner" class="hidden shrink-0 ml-1.5">
                                <div class="w-3.5 h-3.5 border-2 border-emerald-500 border-t-transparent rounded-full animate-spin"></div>
                            </div>
                        </div>
                        <!-- Dropdown Hasil Pencarian Lokasi -->
                        <div id="reportSearchResultsDropdown"
                            class="absolute top-full left-0 right-0 mt-1 max-h-56 overflow-y-auto bg-white dark:bg-[#1A1A19] border border-slate-200 dark:border-[#282828] rounded-xl shadow-xl z-30 hidden divide-y divide-slate-100 dark:divide-[#242424]">
                        </div>
                    </div>

                    <!-- Kontainer Peta MapLibre -->
                    <div id="mapPicker"
                        class="w-full h-80 rounded-2xl border border-slate-300 dark:border-[#282828] shadow-inner relative overflow-hidden">
                        <div id="mapLoading"
                            class="absolute inset-0 bg-white/70 dark:bg-[#141414]/80 backdrop-blur-sm z-10 flex items-center justify-center text-xs font-bold text-slate-600 dark:text-[#CCCCCC]">
                            Memuat peta OpenFreeMap...
                        </div>
                    </div>

                    <!-- Tampilan Alamat Terdeteksi Otomatis -->
                    <div
                        class="mt-3 p-4 rounded-2xl bg-emerald-50/70 dark:bg-emerald-950/40 border border-emerald-200/80 dark:border-emerald-800/60 text-xs flex items-start space-x-3">
                        <flux:icon name="map-pin" class="w-4 h-4 text-emerald-700 dark:text-emerald-400 shrink-0 mt-0.5" />
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-emerald-950 dark:text-emerald-200" id="displayAddress">
                                Mengambil data wilayah...
                            </div>
                            <div class="text-emerald-800 dark:text-emerald-300 text-[11px] mt-0.5" id="displayCoordinates">
                                Lat: -6.914744, Lng: 107.609810
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Kirim Laporan -->
                <div class="pt-4 border-t border-slate-200 dark:border-[#222222] flex items-center justify-end space-x-3">
                    <a href="{{ route('reports.index') }}"
                        class="px-5 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 dark:text-[#888888] dark:hover:bg-[#222222] text-sm font-semibold transition">
                        Batal
                    </a>
                    <button type="submit" id="submitBtn"
                        class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-bold rounded-xl shadow-sm transition">
                        Publikasikan Laporan
                    </button>
                </div>
            </form>
        </div>

        <!-- 3 Card Tutorial Singkat Alur Pelaporan SIRA -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 font-sans">
            <!-- Bento 1 -->
            <div class="p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-[#222222] bg-white dark:bg-[#141414] space-y-2 shadow-xs">
                <div class="text-xs font-mono font-bold text-slate-900 dark:text-[#EDEDEC] flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>01. OpenFreeMap &amp; Reverse Geocode</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-[#9B9B97] leading-relaxed">
                    Pilih titik lokasi pada peta terbuka. Nama jalan, kelurahan, dan kecamatan teridentifikasi otomatis tanpa dependensi API komersial berbayar.
                </p>
            </div>

            <!-- Bento 2 -->
            <div class="p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-[#222222] bg-white dark:bg-[#141414] space-y-2 shadow-xs">
                <div class="text-xs font-mono font-bold text-slate-900 dark:text-[#EDEDEC] flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-[#9F2F2D]"></span>
                    <span>02. Crowdsourced Voting Tier</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-[#9B9B97] leading-relaxed">
                    Laporan tidak diverifikasi oleh birokrat tunggal. Dukungan vote komunitas yang menentukan apakah suatu masalah naik ke status Trending, Urgent, atau Critical.
                </p>
            </div>

            <!-- Bento 3 -->
            <div class="p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-[#222222] bg-white dark:bg-[#141414] space-y-2 shadow-xs">
                <div class="text-xs font-mono font-bold text-slate-900 dark:text-[#EDEDEC] flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-[#1F6C9F]"></span>
                    <span>03. WebGL Heatmap GPU</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-[#9B9B97] leading-relaxed">
                    Visualisasi titik-titik panas masalah kota secara menyeluruh. Semakin tinggi skor vote laporan, semakin pekat intensitas warna panas yang terpancar.
                </p>
            </div>
        </div>
    </div>

    <!-- Modal Webcam Kamera Langsung (Desktop / Browser) -->
    <div id="webcamModal" class="fixed inset-0 z-50 hidden bg-black/75 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-[#161615] border border-slate-200 dark:border-[#282828] rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl p-5 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <flux:icon name="camera" class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                    <span class="text-sm font-bold text-slate-900 dark:text-[#EDEDEC]">Ambil Foto dari Kamera</span>
                </div>
                <button type="button" id="closeWebcamBtn" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 cursor-pointer">
                    <flux:icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>

            <!-- Video Container -->
            <div class="relative bg-black rounded-2xl overflow-hidden aspect-video flex items-center justify-center">
                <video id="webcamVideo" autoplay playsinline muted class="w-full h-full object-cover"></video>
                <div id="webcamLoading" class="absolute inset-0 flex items-center justify-center bg-black/60 text-white text-xs space-x-2">
                    <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                    <span>Menghubungkan ke kamera...</span>
                </div>
            </div>

            <!-- Shutter & Controls -->
            <div class="flex items-center justify-center gap-3 pt-1">
                <button type="button" id="shutterBtn"
                    class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs font-bold rounded-xl flex items-center gap-2 shadow-sm transition cursor-pointer">
                    <flux:icon name="camera" class="w-4 h-4" />
                    <span>Jepret Foto</span>
                </button>
                <button type="button" id="cancelWebcamBtn"
                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-[#222222] dark:hover:bg-[#282828] text-slate-700 dark:text-[#CCCCCC] text-xs font-semibold rounded-xl transition cursor-pointer">
                    Batal
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // -------------------------------------------------------------
        // 1. Client-Side Image Compression & Multiple Source Handler (Kamera / Galeri)
        // -------------------------------------------------------------
        const galleryInput = document.getElementById('galleryInput');
        const cameraInput = document.getElementById('cameraInput');
        const btnTriggerCamera = document.getElementById('btnTriggerCamera');
        const btnTriggerGallery = document.getElementById('btnTriggerGallery');
        const btnChangePhoto = document.getElementById('btnChangePhoto');
        const btnRemovePhoto = document.getElementById('btnRemovePhoto');
        const dropZone = document.getElementById('dropZone');

        const imagePreview = document.getElementById('imagePreview');
        const imageBase64Input = document.getElementById('imageBase64');
        const uploadPlaceholder = document.getElementById('uploadPlaceholder');
        const previewContainer = document.getElementById('previewContainer');
        const compressionInfo = document.getElementById('compressionInfo');

        // Modal Webcam Elements
        const webcamModal = document.getElementById('webcamModal');
        const webcamVideo = document.getElementById('webcamVideo');
        const webcamLoading = document.getElementById('webcamLoading');
        const closeWebcamBtn = document.getElementById('closeWebcamBtn');
        const cancelWebcamBtn = document.getElementById('cancelWebcamBtn');
        const shutterBtn = document.getElementById('shutterBtn');
        let webcamStream = null;

        function processImageFile(file) {
            if (!file || !file.type.startsWith('image/')) {
                alert('Silakan pilih berkas gambar yang valid (JPG, PNG, WebP).');
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                const img = new Image();
                img.onload = function () {
                    // Resize bila melebihi 1600px untuk menghemat ukuran
                    const maxDimension = 1600;
                    let width = img.width;
                    let height = img.height;

                    if (width > height && width > maxDimension) {
                        height = Math.round((height * maxDimension) / width);
                        width = maxDimension;
                    } else if (height > maxDimension) {
                        width = Math.round((width * maxDimension) / height);
                        height = maxDimension;
                    }

                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;

                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    // Kompresi 80% (0.8 quality JPEG)
                    const compressedBase64 = canvas.toDataURL('image/jpeg', 0.8);
                    imageBase64Input.value = compressedBase64;

                    // Tampilkan preview
                    imagePreview.src = compressedBase64;
                    uploadPlaceholder.classList.add('hidden');
                    previewContainer.classList.remove('hidden');

                    const origKb = (file.size / 1024).toFixed(1);
                    const compKb = (compressedBase64.length * 0.75 / 1024).toFixed(1);
                    compressionInfo.innerText = `Kompresi 80% Berhasil: ${origKb} KB → ~${compKb} KB`;
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }

        // Buka Galeri
        if (btnTriggerGallery) {
            btnTriggerGallery.addEventListener('click', () => {
                galleryInput.click();
            });
        }

        if (galleryInput) {
            galleryInput.addEventListener('change', (e) => {
                if (e.target.files && e.target.files[0]) {
                    processImageFile(e.target.files[0]);
                }
            });
        }

        // Buka Kamera (Deteksi Mobile vs Desktop Webcam)
        function isMobileDevice() {
            return /Android|iPhone|iPad|iPod/i.test(navigator.userAgent) ||
                   (navigator.maxTouchPoints > 0 && window.innerWidth < 768);
        }

        function stopWebcam() {
            if (webcamStream) {
                webcamStream.getTracks().forEach(track => track.stop());
                webcamStream = null;
            }
            if (webcamModal) {
                webcamModal.classList.add('hidden');
            }
        }

        async function openWebcamModal() {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                cameraInput.click();
                return;
            }

            webcamModal.classList.remove('hidden');
            webcamLoading.classList.remove('hidden');

            try {
                webcamStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: { ideal: 'environment' },
                        width: { ideal: 1920 },
                        height: { ideal: 1080 }
                    },
                    audio: false
                });

                webcamVideo.srcObject = webcamStream;
                webcamVideo.onloadedmetadata = () => {
                    webcamLoading.classList.add('hidden');
                };
            } catch (err) {
                console.warn('Gagal mengakses kamera langsung:', err);
                stopWebcam();
                cameraInput.click();
            }
        }

        if (btnTriggerCamera) {
            btnTriggerCamera.addEventListener('click', () => {
                if (isMobileDevice()) {
                    cameraInput.click();
                } else {
                    openWebcamModal();
                }
            });
        }

        if (cameraInput) {
            cameraInput.addEventListener('change', (e) => {
                if (e.target.files && e.target.files[0]) {
                    processImageFile(e.target.files[0]);
                }
            });
        }

        // Jepret foto dari webcam modal
        if (shutterBtn) {
            shutterBtn.addEventListener('click', () => {
                if (!webcamVideo.videoWidth) return;

                const canvas = document.createElement('canvas');
                canvas.width = webcamVideo.videoWidth;
                canvas.height = webcamVideo.videoHeight;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(webcamVideo, 0, 0, canvas.width, canvas.height);

                canvas.toBlob((blob) => {
                    if (blob) {
                        const file = new File([blob], 'camera-capture.jpg', { type: 'image/jpeg' });
                        processImageFile(file);
                    }
                    stopWebcam();
                }, 'image/jpeg', 0.9);
            });
        }

        if (closeWebcamBtn) closeWebcamBtn.addEventListener('click', stopWebcam);
        if (cancelWebcamBtn) cancelWebcamBtn.addEventListener('click', stopWebcam);

        // Ganti foto
        if (btnChangePhoto) {
            btnChangePhoto.addEventListener('click', () => {
                galleryInput.click();
            });
        }

        // Hapus foto
        if (btnRemovePhoto) {
            btnRemovePhoto.addEventListener('click', () => {
                imageBase64Input.value = '';
                imagePreview.src = '';
                if (galleryInput) galleryInput.value = '';
                if (cameraInput) cameraInput.value = '';
                previewContainer.classList.add('hidden');
                uploadPlaceholder.classList.remove('hidden');
            });
        }

        // Drag & Drop pada area dropZone
        if (dropZone) {
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropZone.classList.add('border-emerald-500', 'bg-emerald-50/20');
                });
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropZone.classList.remove('border-emerald-500', 'bg-emerald-50/20');
                });
            });

            dropZone.addEventListener('drop', (e) => {
                const files = e.dataTransfer?.files;
                if (files && files.length > 0) {
                    processImageFile(files[0]);
                }
            });
        }

        // -------------------------------------------------------------
        // 2. OpenFreeMap MapLibre GL JS Interactive Picker
        // -------------------------------------------------------------
        let currentLat = parseFloat(document.getElementById('inputLatitude').value) || -6.914744;
        let currentLng = parseFloat(document.getElementById('inputLongitude').value) || 107.609810;

        const map = new maplibregl.Map({
            container: 'mapPicker',
            style: 'https://tiles.openfreemap.org/styles/bright',
            center: [currentLng, currentLat],
            zoom: 14
        });

        // Marker yang dapat digeser
        const marker = new maplibregl.Marker({
            draggable: true,
            color: '#059669'
        })
            .setLngLat([currentLng, currentLat])
            .addTo(map);

        map.on('load', () => {
            const loading = document.getElementById('mapLoading');
            if (loading) loading.style.display = 'none';
            reverseGeocode(currentLat, currentLng);
        });

        // Event ketika marker selesai digeser
        marker.on('dragend', () => {
            const lngLat = marker.getLngLat();
            updateCoordinates(lngLat.lat, lngLat.lng);
        });

        // Event klik di area peta untuk memindahkan marker
        map.on('click', (e) => {
            marker.setLngLat(e.lngLat);
            updateCoordinates(e.lngLat.lat, e.lngLat.lng);
        });

        function updateCoordinates(lat, lng) {
            currentLat = lat;
            currentLng = lng;
            document.getElementById('inputLatitude').value = lat.toFixed(8);
            document.getElementById('inputLongitude').value = lng.toFixed(8);
            document.getElementById('displayCoordinates').innerText = `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`;
            reverseGeocode(lat, lng);
        }

        // -------------------------------------------------------------
        // 3. Reverse Geocoding via Nominatim OpenStreetMap
        // -------------------------------------------------------------
        let debounceTimer;
        function reverseGeocode(lat, lng) {
            clearTimeout(debounceTimer);
            document.getElementById('displayAddress').innerText = 'Mendeteksi alamat dan wilayah...';

            debounceTimer = setTimeout(() => {
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`)
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.address) {
                            const addr = data.address;
                            const road = addr.road || addr.pedestrian || addr.footway || addr.path || addr.residential || '';
                            const subdistrict = addr.village || addr.quarter || addr.subdistrict || addr.neighbourhood || '';
                            let district = addr.city_district || addr.district || addr.municipality || addr.suburb || addr.county || '';
                            if (!district) {
                                district = subdistrict || addr.town || addr.city || '';
                            }
                            if (district.toLowerCase().startsWith('kecamatan ')) {
                                district = district.substring(10).trim();
                            } else if (district.toLowerCase().startsWith('kabupaten ')) {
                                district = district.substring(10).trim();
                            }
                            let city = addr.city || addr.town || addr.county || addr.regency || '';
                            if (city.toLowerCase().startsWith('kabupaten ')) {
                                city = city.substring(10).trim();
                            }
                            const province = addr.state || '';

                            document.getElementById('inputProvince').value = province;
                            document.getElementById('inputCity').value = city;
                            document.getElementById('inputDistrict').value = district;
                            document.getElementById('inputSubdistrict').value = subdistrict;
                            document.getElementById('inputAddress').value = data.display_name || '';
                            document.getElementById('inputOsmId').value = data.osm_id ? String(data.osm_id) : '';

                            const formattedText = [road, subdistrict, district, city].filter(Boolean).join(', ');
                            document.getElementById('displayAddress').innerText = formattedText || data.display_name;
                        } else {
                            document.getElementById('displayAddress').innerText = 'Alamat tidak ditemukan, silakan gunakan titik peta.';
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        document.getElementById('displayAddress').innerText = 'Gagal mendeteksi nama alamat secara otomatis.';
                    });
            }, 400);
        }

        // -------------------------------------------------------------
        // 4. Deteksi GPS Pengguna (Geolocation API)
        // -------------------------------------------------------------
        document.getElementById('btnGeolocate').addEventListener('click', function () {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung geolokasi.');
                return;
            }

            const btnText = document.getElementById('btnGeolocateText');
            if (btnText) btnText.innerText = 'Mendeteksi lokasi GPS...';
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    if (btnText) btnText.innerText = 'Deteksi Lokasi Saya (GPS)';
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    map.flyTo({ center: [lng, lat], zoom: 16 });
                    marker.setLngLat([lng, lat]);
                    updateCoordinates(lat, lng);
                },
                (err) => {
                    if (btnText) btnText.innerText = 'Deteksi Lokasi Saya (GPS)';
                    alert('Gagal mengambil titik GPS: ' + err.message);
                },
                { enableHighAccuracy: true, timeout: 8000 }
            );
        });

        // -------------------------------------------------------------
        // 5. Pencarian Lokasi Cepat untuk Penempatan Pin
        // -------------------------------------------------------------
        const reportLocationSearch = document.getElementById('reportLocationSearch');
        const clearReportSearchBtn = document.getElementById('clearReportSearchBtn');
        const reportSearchSpinner = document.getElementById('reportSearchSpinner');
        const reportSearchResultsDropdown = document.getElementById('reportSearchResultsDropdown');
        let reportSearchDebounce = null;

        function closeReportSearchDropdown() {
            if (reportSearchResultsDropdown) {
                reportSearchResultsDropdown.classList.add('hidden');
                reportSearchResultsDropdown.innerHTML = '';
            }
        }

        function escapeReportHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function executeReportLocationSearch(query) {
            if (!query || query.trim().length < 2) {
                closeReportSearchDropdown();
                return;
            }

            if (reportSearchSpinner) reportSearchSpinner.classList.remove('hidden');

            fetch(`{{ route('api.geocode.search') }}?q=${encodeURIComponent(query.trim())}`)
                .then(res => res.json())
                .then(results => {
                    if (reportSearchSpinner) reportSearchSpinner.classList.add('hidden');

                    if (!results || results.length === 0) {
                        reportSearchResultsDropdown.innerHTML = `
                            <div class="p-3 text-center text-xs text-slate-500 dark:text-[#888888]">
                                Lokasi tidak ditemukan. Coba gunakan nama kota, kampus, jalan, atau daerah lain.
                            </div>
                        `;
                        reportSearchResultsDropdown.classList.remove('hidden');
                        return;
                    }

                    reportSearchResultsDropdown.innerHTML = '';
                    results.forEach(item => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'w-full text-left p-2.5 sm:p-3 hover:bg-slate-50 dark:hover:bg-[#1E1E1E] transition flex items-start gap-2.5 cursor-pointer';
                        btn.innerHTML = `
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 21s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 7.2c0 7.3-8 11.8-8 11.8z" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs font-bold text-slate-900 dark:text-[#EDEDEC] truncate">${escapeReportHtml(item.name)}</div>
                                <div class="text-[11px] text-slate-500 dark:text-[#888888] truncate">${escapeReportHtml(item.display_name)}</div>
                            </div>
                        `;
                        btn.addEventListener('click', () => {
                            closeReportSearchDropdown();
                            reportLocationSearch.value = item.name;
                            if (clearReportSearchBtn) clearReportSearchBtn.classList.remove('hidden');

                            const lat = parseFloat(item.lat);
                            const lng = parseFloat(item.lng);
                            if (!isNaN(lat) && !isNaN(lng)) {
                                map.flyTo({ center: [lng, lat], zoom: 16 });
                                marker.setLngLat([lng, lat]);
                                updateCoordinates(lat, lng);
                            }
                        });
                        reportSearchResultsDropdown.appendChild(btn);
                    });

                    reportSearchResultsDropdown.classList.remove('hidden');
                })
                .catch(err => {
                    console.error('Pencarian lokasi gagal:', err);
                    if (reportSearchSpinner) reportSearchSpinner.classList.add('hidden');
                });
        }

        if (reportLocationSearch) {
            reportLocationSearch.addEventListener('input', (e) => {
                const query = e.target.value;
                if (clearReportSearchBtn) {
                    if (query.trim().length > 0) {
                        clearReportSearchBtn.classList.remove('hidden');
                    } else {
                        clearReportSearchBtn.classList.add('hidden');
                    }
                }

                clearTimeout(reportSearchDebounce);
                reportSearchDebounce = setTimeout(() => {
                    executeReportLocationSearch(query);
                }, 350);
            });

            reportLocationSearch.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(reportSearchDebounce);
                    executeReportLocationSearch(reportLocationSearch.value);
                } else if (e.key === 'Escape') {
                    closeReportSearchDropdown();
                }
            });
        }

        if (clearReportSearchBtn) {
            clearReportSearchBtn.addEventListener('click', () => {
                if (reportLocationSearch) reportLocationSearch.value = '';
                clearReportSearchBtn.classList.add('hidden');
                closeReportSearchDropdown();
            });
        }

        document.addEventListener('click', (e) => {
            if (!reportLocationSearch || !reportSearchResultsDropdown) return;
            if (!reportLocationSearch.contains(e.target) && !reportSearchResultsDropdown.contains(e.target)) {
                closeReportSearchDropdown();
            }
        });

        // Validasi form sebelum submit
        document.getElementById('reportForm').addEventListener('submit', function (e) {
            if (!imageBase64Input.value) {
                e.preventDefault();
                alert('Harap unggah foto bukti laporan terlebih dahulu!');
            }
        });
    </script>
@endpush