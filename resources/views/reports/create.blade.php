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
                <input type="hidden" name="latitude" id="inputLatitude" value="{{ old('latitude', '') }}">
                <input type="hidden" name="longitude" id="inputLongitude" value="{{ old('longitude', '') }}">
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
                                <button type="button" id="btnTriggerCamera"
                                    class="flex flex-col items-center justify-center p-5 rounded-2xl border border-slate-200 dark:border-[#2A2A2A] bg-white dark:bg-[#141414] hover:border-emerald-500 dark:hover:border-emerald-500 hover:bg-emerald-50/40 dark:hover:bg-emerald-950/30 active:scale-[0.98] transition cursor-pointer group shadow-xs">
                                    <div
                                        class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-2.5 group-hover:scale-110 transition">
                                        <flux:icon name="camera" class="w-6 h-6" />
                                    </div>
                                    <span class="text-sm font-bold text-slate-900 dark:text-[#EDEDEC]">Gunakan Kamera</span>
                                    <span class="text-[11px] text-slate-400 dark:text-[#787774] mt-0.5">Ambil foto
                                        langsung</span>
                                </button>

                                <!-- Tombol Buka Galeri -->
                                <button type="button" id="btnTriggerGallery"
                                    class="flex flex-col items-center justify-center p-5 rounded-2xl border border-slate-200 dark:border-[#2A2A2A] bg-white dark:bg-[#141414] hover:border-emerald-500 dark:hover:border-emerald-500 hover:bg-emerald-50/40 dark:hover:bg-emerald-950/30 active:scale-[0.98] transition cursor-pointer group shadow-xs">
                                    <div
                                        class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-2.5 group-hover:scale-110 transition">
                                        <flux:icon name="photo" class="w-6 h-6" />
                                    </div>
                                    <span class="text-sm font-bold text-slate-900 dark:text-[#EDEDEC]">Pilih dari
                                        Galeri</span>
                                    <span class="text-[11px] text-slate-400 dark:text-[#787774] mt-0.5">Berkas JPG, PNG,
                                        WebP</span>
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
                                <button type="button" id="btnChangePhoto"
                                    class="px-3.5 py-1.5 text-xs font-semibold rounded-full bg-slate-200 hover:bg-slate-300 dark:bg-[#282828] dark:hover:bg-[#333333] text-slate-800 dark:text-[#EDEDEC] transition cursor-pointer inline-flex items-center gap-1.5">
                                    <flux:icon name="arrow-path" class="w-3.5 h-3.5" />
                                    <span>Ganti Foto</span>
                                </button>
                                <button type="button" id="btnRemovePhoto"
                                    class="px-3.5 py-1.5 text-xs font-semibold rounded-full bg-rose-100 hover:bg-rose-200 dark:bg-rose-950/60 dark:hover:bg-rose-900/60 text-rose-700 dark:text-rose-300 transition cursor-pointer inline-flex items-center gap-1.5">
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
                        <label
                            class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-[#CCCCCC] mb-2">
                            Kategori Masalah <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5">
                            @foreach($categories as $catKey => $cat)
                                <label
                                    class="relative flex items-center gap-2.5 p-3 rounded-2xl border border-slate-200 dark:border-[#282828] bg-white dark:bg-[#181818] hover:border-emerald-500/60 dark:hover:border-emerald-500/60 cursor-pointer transition has-checked:border-emerald-600 dark:has-checked:border-emerald-400 has-checked:bg-emerald-50/50 dark:has-checked:bg-emerald-950/30 has-checked:ring-2 has-checked:ring-emerald-500/30 shadow-2xs">
                                    <input type="radio" name="category" value="{{ $catKey }}" {{ old('category', 'infrastruktur') === $catKey ? 'checked' : '' }} class="sr-only">
                                    <div class="w-7 h-7 rounded-xl flex items-center justify-center shrink-0"
                                        style="background-color: {{ $cat['color'] }}20; color: {{ $cat['color'] }};">
                                        <flux:icon name="{{ $cat['icon'] }}" class="w-4 h-4" />
                                    </div>
                                    <span
                                        class="text-xs font-bold text-slate-800 dark:text-[#EDEDEC] truncate">{{ $cat['label'] }}</span>
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
                            placeholder="Jelaskan detail masalah, dampak, dan perkiraan sudah berapa lama terjadi... (Ketik @ untuk menandai akun instansi atau warga lain)"
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-[#282828] bg-white dark:bg-[#181818] text-slate-900 dark:text-[#EDEDEC] placeholder-slate-400 dark:placeholder-[#666666] text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">{{ old('description') }}</textarea>
                        <div class="flex items-center justify-between mt-1.5 text-[11px] text-slate-500 dark:text-[#888888]">
                            <span class="inline-flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block shrink-0"></span>
                                <span>Fitur Tag: Ketik <strong class="text-emerald-600 dark:text-emerald-400">@username</strong> untuk menandai akun instansi atau warga</span>
                            </span>
                            <span class="font-mono text-[10px] text-slate-400">Notifikasi Otomatis</span>
                        </div>
                        @error('description')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- 3. Titik Lokasi GPS Terverifikasi (GPS Only) -->
                <div class="space-y-4">
                    <!-- Label & Tombol Deteksi Lokasi GPS -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl bg-slate-50 dark:bg-[#181818] border border-slate-200 dark:border-[#282828]">
                        <div>
                            <div class="flex items-center gap-2">
                                <label class="block text-sm font-bold text-slate-900 dark:text-[#EDEDEC]">
                                    Titik Lokasi (GPS Only) <span class="text-rose-500">*</span>
                                </label>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                    Sensor Otomatis
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 dark:text-[#787774] mt-0.5">
                                Lokasi wajib diambil langsung melalui sensor GPS perangkat saat berada di tempat kejadian. Pin lokasi manual dinonaktifkan.
                            </p>
                        </div>
                        <button type="button" id="btnGeolocate"
                            class="inline-flex items-center justify-center space-x-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer shrink-0">
                            <flux:icon name="viewfinder-circle" class="w-4 h-4 text-white shrink-0" />
                            <span id="btnGeolocateText">Ambil Titik Lokasi GPS</span>
                        </button>
                    </div>

                    <!-- Kontainer Peta MapLibre (Read-Only GPS Visualization) -->
                    <div class="relative">
                        <div id="mapPicker"
                            class="w-full h-80 rounded-2xl border border-slate-300 dark:border-[#282828] shadow-inner relative overflow-hidden">
                            <div id="mapLoading"
                                class="absolute inset-0 bg-white/70 dark:bg-[#141414]/80 backdrop-blur-sm z-10 flex items-center justify-center text-xs font-bold text-slate-600 dark:text-[#CCCCCC]">
                                Memuat peta OpenFreeMap...
                            </div>
                        </div>

                        <!-- Overlay Badge: Pin Terkunci Sesuai GPS -->
                        <div class="absolute top-3 left-3 z-10 pointer-events-none">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-medium bg-slate-900/90 text-white dark:bg-black/90 shadow-md backdrop-blur-xs">
                                <flux:icon name="lock-closed" class="w-3 h-3 text-emerald-400" />
                                <span>Pin Terkunci Sesuai GPS</span>
                            </span>
                        </div>
                    </div>

                    <!-- Tampilan Alamat Terdeteksi Otomatis -->
                    <div id="gpsStatusContainer"
                        class="p-4 rounded-2xl bg-slate-50 dark:bg-[#181818] border border-slate-200 dark:border-[#282828] text-xs flex items-start space-x-3 transition">
                        <div id="gpsStatusIcon" class="mt-0.5 shrink-0 text-slate-400 dark:text-[#787774]">
                            <flux:icon name="map-pin" class="w-4 h-4" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-slate-800 dark:text-[#EDEDEC]" id="displayAddress">
                                Sinyal GPS belum diambil. Klik tombol "Ambil Titik Lokasi GPS" di atas.
                            </div>
                            <div class="text-slate-500 dark:text-[#888888] text-[11px] mt-0.5 font-mono" id="displayCoordinates">
                                Koordinat: Belum terkunci
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
            <div
                class="p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-[#222222] bg-white dark:bg-[#141414] space-y-2 shadow-xs">
                <div class="text-xs font-mono font-bold text-slate-900 dark:text-[#EDEDEC] flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>01. OpenFreeMap &amp; Reverse Geocode</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-[#9B9B97] leading-relaxed">
                    Pilih titik lokasi pada peta terbuka. Nama jalan, kelurahan, dan kecamatan teridentifikasi otomatis
                    tanpa dependensi API komersial berbayar.
                </p>
            </div>

            <!-- Bento 2 -->
            <div
                class="p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-[#222222] bg-white dark:bg-[#141414] space-y-2 shadow-xs">
                <div class="text-xs font-mono font-bold text-slate-900 dark:text-[#EDEDEC] flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-[#9F2F2D]"></span>
                    <span>02. Crowdsourced Voting Tier</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-[#9B9B97] leading-relaxed">
                    Laporan tidak diverifikasi oleh birokrat tunggal. Dukungan vote komunitas yang menentukan apakah suatu
                    masalah naik ke status Trending, Urgent, atau Critical.
                </p>
            </div>

            <!-- Bento 3 -->
            <div
                class="p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-[#222222] bg-white dark:bg-[#141414] space-y-2 shadow-xs">
                <div class="text-xs font-mono font-bold text-slate-900 dark:text-[#EDEDEC] flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-[#1F6C9F]"></span>
                    <span>03. WebGL Heatmap GPU</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-[#9B9B97] leading-relaxed">
                    Visualisasi titik-titik panas masalah kota secara menyeluruh. Semakin tinggi skor vote laporan, semakin
                    pekat intensitas warna panas yang terpancar.
                </p>
            </div>
        </div>
    </div>

    <!-- Modal Webcam Kamera Langsung (Desktop / Browser) -->
    <div id="webcamModal"
        class="fixed inset-0 z-50 hidden bg-black/75 backdrop-blur-xs flex items-center justify-center p-4">
        <div
            class="bg-white dark:bg-[#161615] border border-slate-200 dark:border-[#282828] rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl p-5 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <flux:icon name="camera" class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                    <span class="text-sm font-bold text-slate-900 dark:text-[#EDEDEC]">Ambil Foto dari Kamera</span>
                </div>
                <button type="button" id="closeWebcamBtn"
                    class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 cursor-pointer">
                    <flux:icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>

            <!-- Video Container -->
            <div class="relative bg-black rounded-2xl overflow-hidden aspect-video flex items-center justify-center">
                <video id="webcamVideo" autoplay playsinline muted class="w-full h-full object-cover"></video>
                <div id="webcamLoading"
                    class="absolute inset-0 flex items-center justify-center bg-black/60 text-white text-xs space-x-2">
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
        // 2. OpenFreeMap MapLibre GL JS GPS-Only Viewer & Lock
        // -------------------------------------------------------------
        const latInput = document.getElementById('inputLatitude');
        const lngInput = document.getElementById('inputLongitude');
        let currentLat = parseFloat(latInput?.value);
        let currentLng = parseFloat(lngInput?.value);
        let hasGpsLocation = !isNaN(currentLat) && !isNaN(currentLng) && currentLat !== 0;

        // Koordinat inisial (Gunakan posisi tersimpan atau default Bandung center sebagai titik pandang awal)
        const initialCenter = hasGpsLocation ? [currentLng, currentLat] : [107.609810, -6.914744];
        const initialZoom = hasGpsLocation ? 16 : 13;

        const map = new maplibregl.Map({
            container: 'mapPicker',
            style: 'https://tiles.openfreemap.org/styles/bright',
            center: initialCenter,
            zoom: initialZoom
        });

        // Marker TIDAK BISA DIGESER (draggable: false) - Khusus GPS Saja
        const marker = new maplibregl.Marker({
            draggable: false,
            color: '#059669'
        });

        if (hasGpsLocation) {
            marker.setLngLat([currentLng, currentLat]).addTo(map);
        }

        map.on('load', () => {
            const loading = document.getElementById('mapLoading');
            if (loading) loading.style.display = 'none';

            if (hasGpsLocation) {
                reverseGeocode(currentLat, currentLng);
                setGpsStatusLocked(currentLat, currentLng);
            } else {
                // Coba ambil GPS secara otomatis saat halaman dibuka
                requestGpsLocation(true);
            }
        });

        function setGpsStatusLocked(lat, lng) {
            const container = document.getElementById('gpsStatusContainer');
            const icon = document.getElementById('gpsStatusIcon');
            const displayCoord = document.getElementById('displayCoordinates');
            const btnText = document.getElementById('btnGeolocateText');

            if (container) {
                container.className = 'p-4 rounded-2xl bg-emerald-50/80 dark:bg-emerald-950/40 border border-emerald-300/80 dark:border-emerald-800/80 text-xs flex items-start space-x-3 transition';
            }
            if (icon) {
                icon.className = 'mt-0.5 shrink-0 text-emerald-600 dark:text-emerald-400';
            }
            if (displayCoord) {
                displayCoord.className = 'text-emerald-800 dark:text-emerald-300 text-[11px] mt-0.5 font-mono font-medium';
                displayCoord.innerText = `GPS Terkunci: Lat ${lat.toFixed(6)}, Lng ${lng.toFixed(6)}`;
            }
            if (btnText) {
                btnText.innerText = 'Perbarui Lokasi GPS';
            }
        }

        function updateCoordinates(lat, lng) {
            currentLat = lat;
            currentLng = lng;
            hasGpsLocation = true;
            if (latInput) latInput.value = lat.toFixed(8);
            if (lngInput) lngInput.value = lng.toFixed(8);
            setGpsStatusLocked(lat, lng);
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
                            document.getElementById('displayAddress').innerText = 'Alamat pada titik GPS teridentifikasi.';
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        document.getElementById('displayAddress').innerText = 'Gagal mendeteksi nama alamat secara otomatis.';
                    });
            }, 400);
        }

        // -------------------------------------------------------------
        // 4. Deteksi GPS Pengguna (Geolocation API Only)
        // -------------------------------------------------------------
        function requestGpsLocation(isAuto = false) {
            if (!navigator.geolocation) {
                if (!isAuto) alert('Browser atau perangkat Anda tidak mendukung sensor geolokasi GPS.');
                return;
            }

            const btn = document.getElementById('btnGeolocate');
            const btnText = document.getElementById('btnGeolocateText');
            if (btnText) btnText.innerText = 'Mengunci sinyal GPS...';
            if (btn) btn.disabled = true;

            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    if (btn) btn.disabled = false;
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;

                    marker.setLngLat([lng, lat]);
                    if (!marker.getElement().parentElement) {
                        marker.addTo(map);
                    }
                    map.flyTo({ center: [lng, lat], zoom: 16 });
                    updateCoordinates(lat, lng);
                },
                (err) => {
                    if (btn) btn.disabled = false;
                    if (btnText) {
                        btnText.innerText = hasGpsLocation ? 'Perbarui Lokasi GPS' : 'Ambil Titik Lokasi GPS';
                    }

                    if (!isAuto) {
                        let errorMsg = 'Gagal mengambil titik GPS: ';
                        switch (err.code) {
                            case err.PERMISSION_DENIED:
                                errorMsg += 'Izin akses lokasi ditolak. Harap izinkan akses lokasi (GPS) pada pengaturan peramban atau perangkat Anda.';
                                break;
                            case err.POSITION_UNAVAILABLE:
                                errorMsg += 'Sinyal lokasi perangkat tidak tersedia atau GPS sedang tidak aktif.';
                                break;
                            case err.TIMEOUT:
                                errorMsg += 'Waktu permintaan lokasi habis. Silakan coba kembali.';
                                break;
                            default:
                                errorMsg += err.message;
                        }
                        alert(errorMsg);
                    }
                },
                { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 }
            );
        }

        document.getElementById('btnGeolocate').addEventListener('click', function () {
            requestGpsLocation(false);
        });

        // Validasi form sebelum submit (Foto & Wajib GPS)
        document.getElementById('reportForm').addEventListener('submit', function (e) {
            if (!imageBase64Input.value) {
                e.preventDefault();
                alert('Harap unggah foto bukti laporan terlebih dahulu!');
                return;
            }

            if (!latInput.value || !lngInput.value) {
                e.preventDefault();
                alert('Titik lokasi wajib diambil menggunakan GPS perangkat Anda saat berada di lokasi kejadian. Silakan klik tombol "Ambil Titik Lokasi GPS"!');
                const btnGeolocate = document.getElementById('btnGeolocate');
                if (btnGeolocate) {
                    btnGeolocate.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    btnGeolocate.classList.add('ring-4', 'ring-emerald-400');
                    setTimeout(() => btnGeolocate.classList.remove('ring-4', 'ring-emerald-400'), 2000);
                }
                return;
            }
        });

        // -------------------------------------------------------------
        // Auto-Complete Mention (@) Sistem pada Form Laporan Baru
        // -------------------------------------------------------------
        (function initCreateMentionSystem() {
            const dropdown = document.createElement('div');
            dropdown.id = 'createMentionDropdown';
            dropdown.style.zIndex = '99999';
            dropdown.className = 'fixed hidden bg-white dark:bg-[#161615] border border-slate-200 dark:border-[#262626] rounded-2xl shadow-2xl overflow-hidden w-72 max-w-[90vw] transition-opacity duration-150 text-left font-sans';
            dropdown.innerHTML = `
                <div class="px-3 py-2 bg-slate-50 dark:bg-[#1F1F1E] border-b border-slate-100 dark:border-[#282828] text-[10px] font-bold text-slate-400 dark:text-[#888888] uppercase tracking-wider flex items-center justify-between">
                    <span>Saran Tag Akun</span>
                    <span class="text-[9px] font-normal lowercase text-slate-400 dark:text-[#777777]">↑↓ dan ↵</span>
                </div>
                <div id="createMentionDropdownList" class="p-1 max-h-56 overflow-y-auto space-y-0.5"></div>
            `;
            document.body.appendChild(dropdown);

            const dropdownList = document.getElementById('createMentionDropdownList');
            const descTextarea = document.getElementById('description');
            if (!descTextarea) return;

            let mentionStartIndex = -1;
            let mentionQuery = '';
            let currentUsers = [];
            let highlightedIndex = 0;

            function closeMentionDropdown() {
                dropdown.classList.add('hidden');
                dropdown.style.display = 'none';
                currentUsers = [];
                highlightedIndex = 0;
            }

            function positionDropdown() {
                if (dropdown.classList.contains('hidden')) return;
                const rect = descTextarea.getBoundingClientRect();
                dropdown.style.top = `${rect.bottom + 6}px`;
                dropdown.style.left = `${rect.left}px`;
            }

            function renderSuggestions() {
                if (!currentUsers || currentUsers.length === 0) {
                    closeMentionDropdown();
                    return;
                }

                dropdownList.innerHTML = '';
                currentUsers.forEach((user, index) => {
                    const item = document.createElement('div');
                    const isSelected = (index === highlightedIndex);
                    item.className = isSelected
                        ? 'px-3 py-2 rounded-xl text-xs cursor-pointer flex items-center justify-between transition bg-emerald-600 text-white font-semibold shadow-xs'
                        : 'px-3 py-2 rounded-xl text-xs cursor-pointer flex items-center justify-between transition hover:bg-slate-100 dark:hover:bg-[#222222] text-slate-800 dark:text-[#EDEDEC]';

                    const isAi = user.is_ai;
                    let badgeMarkup = '';
                    if (isAi) {
                        badgeMarkup = `<span class="ml-2 shrink-0 px-2 py-0.5 rounded-full text-[9px] font-extrabold ${isSelected ? 'bg-white/20 text-white' : 'bg-indigo-600 text-white'}">SIRA AI</span>`;
                    } else if (user.badge_type === 'admin') {
                        badgeMarkup = `<span class="ml-2 shrink-0 inline-flex items-center space-x-1 px-1.5 py-0.5 rounded text-[9px] font-bold ${isSelected ? 'bg-amber-400 text-slate-950' : 'bg-amber-100 text-amber-900 dark:bg-amber-950/80 dark:text-amber-300'}"><svg class="w-3 h-3 text-amber-500 fill-current shrink-0" viewBox="0 0 24 24"><path d="M22.5 12.5c0-1.58-.8-2.95-2-3.77.54-1.51.16-3.22-1-4.38-1.16-1.16-2.87-1.54-4.38-1-1.03-1.44-2.73-2.35-4.62-2.35s-3.59.91-4.62 2.35c-1.51-.54-3.22-.16-4.38 1-1.16 1.16-1.54 2.87-1 4.38-1.2 1.03-2 2.4-2 3.77 0 1.58.8 2.95 2 3.77-.54 1.51-.16 3.22 1 4.38 1.16 1.16 2.87 1.54 4.38 1 1.03 1.44 2.73 2.35 4.62 2.35s3.59-.91 4.62-2.35c1.51.54 3.22.16 4.38-1 1.16-1.16 1.54-2.87 1-4.38 1.2-1.03 2-2.4 2-3.77zm-12.03 4.5L6 12.53l1.41-1.41 3.06 3.06 6.06-6.06 1.41 1.41-7.47 7.47z"/></svg><span>ADMIN</span></span>`;
                    } else if (user.badge_type === 'verified') {
                        badgeMarkup = `<span class="ml-2 shrink-0 inline-flex items-center space-x-1 px-1.5 py-0.5 rounded text-[9px] font-bold ${isSelected ? 'bg-sky-400 text-slate-950' : 'bg-sky-100 text-sky-900 dark:bg-sky-950/80 dark:text-sky-300'}"><svg class="w-3 h-3 text-sky-500 fill-current shrink-0" viewBox="0 0 24 24"><path d="M22.5 12.5c0-1.58-.8-2.95-2-3.77.54-1.51.16-3.22-1-4.38-1.16-1.16-2.87-1.54-4.38-1-1.03-1.44-2.73-2.35-4.62-2.35s-3.59.91-4.62 2.35c-1.51-.54-3.22-.16-4.38 1-1.16 1.16-1.54 2.87-1 4.38-1.2 1.03-2 2.4-2 3.77 0 1.58.8 2.95 2 3.77-.54 1.51-.16 3.22 1 4.38 1.16 1.16 2.87 1.54 4.38 1 1.03 1.44 2.73 2.35 4.62 2.35s3.59-.91 4.62-2.35c1.51.54 3.22.16 4.38-1 1.16-1.16 1.54-2.87 1-4.38 1.2-1.03 2-2.4 2-3.77zm-12.03 4.5L6 12.53l1.41-1.41 3.06 3.06 6.06-6.06 1.41 1.41-7.47 7.47z"/></svg><span>VERIFIED</span></span>`;
                    }

                    item.innerHTML = `
                        <div class="flex items-center space-x-2 min-w-0">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0 ${isAi ? 'bg-white text-indigo-700 shadow-xs' : 'bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 uppercase'}">
                                ${isAi ? '<svg class="w-3.5 h-3.5 text-indigo-600" viewBox="0 0 16 16" fill="currentColor"><path d="M8 1a2 2 0 0 1 2 2v1h1a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1V3a2 2 0 0 1 2-2z"/></svg>' : (user.username ? user.username.charAt(0).toUpperCase() : 'U')}
                            </div>
                            <div class="truncate">
                                <div class="truncate text-xs ${isSelected ? 'text-white' : (isAi ? 'text-indigo-600 dark:text-indigo-400 font-bold' : 'text-slate-900 dark:text-[#EDEDEC] font-medium')}">${user.name}</div>
                                <div class="text-[11px] ${isSelected ? 'text-emerald-100' : 'text-slate-400 dark:text-[#888888]'} font-mono">@${user.username}</div>
                            </div>
                        </div>
                        ${badgeMarkup}
                    `;

                    item.addEventListener('mousedown', (e) => {
                        e.preventDefault();
                        selectUser(user);
                    });

                    dropdownList.appendChild(item);
                });

                dropdown.classList.remove('hidden');
                dropdown.style.display = 'block';
                positionDropdown();
            }

            function selectUser(user) {
                const val = descTextarea.value;
                const before = val.slice(0, mentionStartIndex);
                const after = val.slice(descTextarea.selectionStart);
                const insert = '@' + user.username + ' ';
                descTextarea.value = before + insert + after;
                const newPos = before.length + insert.length;
                descTextarea.focus();
                descTextarea.setSelectionRange(newPos, newPos);
                closeMentionDropdown();
            }

            function handleMention() {
                const cursorPos = descTextarea.selectionStart;
                const textBefore = descTextarea.value.slice(0, cursorPos);
                const match = textBefore.match(/(?:^|\s)@([a-zA-Z0-9_]*)$/);

                if (match) {
                    mentionQuery = match[1];
                    mentionStartIndex = cursorPos - mentionQuery.length - 1;

                    const mentionUrl = "{{ route('api.users.mention', [], false) }}";
                    fetch(`${mentionUrl}?q=${encodeURIComponent(mentionQuery)}`)
                        .then(res => res.json())
                        .then(data => {
                            currentUsers = data.users || [];
                            highlightedIndex = 0;
                            renderSuggestions();
                        })
                        .catch(() => closeMentionDropdown());
                } else {
                    closeMentionDropdown();
                }
            }

            descTextarea.addEventListener('input', handleMention);
            descTextarea.addEventListener('click', handleMention);
            document.addEventListener('click', (e) => {
                if (e.target !== descTextarea && !dropdown.contains(e.target)) {
                    closeMentionDropdown();
                }
            });

            descTextarea.addEventListener('keydown', (e) => {
                if (dropdown.classList.contains('hidden') || currentUsers.length === 0) return;

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    highlightedIndex = (highlightedIndex + 1) % currentUsers.length;
                    renderSuggestions();
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    highlightedIndex = (highlightedIndex - 1 + currentUsers.length) % currentUsers.length;
                    renderSuggestions();
                } else if (e.key === 'Enter' || e.key === 'Tab') {
                    if (currentUsers[highlightedIndex]) {
                        e.preventDefault();
                        selectUser(currentUsers[highlightedIndex]);
                    }
                } else if (e.key === 'Escape') {
                    closeMentionDropdown();
                }
            });

            window.addEventListener('resize', positionDropdown);
            window.addEventListener('scroll', positionDropdown, true);
        })();
    </script>
@endpush