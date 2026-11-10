@extends('layouts.app')

@section('title', 'Buat Laporan Baru - SIRA')

@section('content')
    @php
        // Bahasa visual form: satu kartu per langkah, label kalimat biasa, isian setinggi sama
        $stepCard = 'bg-white dark:bg-sand-975 p-4 sm:p-6 rounded-2xl border border-sand-200 dark:border-sand-900 shadow-2xs space-y-4';
        $stepNum = 'w-7 h-7 rounded-full bg-emerald-600 text-white text-sm font-bold flex items-center justify-center shrink-0';
        $stepTitle = 'text-base sm:text-lg font-bold tracking-tight text-sand-975 dark:text-sand-100';
        $label = 'block text-sm font-semibold text-sand-800 dark:text-sand-200 mb-1.5';
        $field = 'w-full px-3.5 py-2.5 rounded-lg border border-sand-300 dark:border-sand-850 bg-white dark:bg-sand-950 text-sand-975 dark:text-sand-100 placeholder-sand-400 dark:placeholder-sand-600 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none';
        $error = 'text-sm text-danger dark:text-danger-soft mt-1.5';
        $sourceBtn = 'flex flex-col items-center justify-center p-4 sm:p-5 rounded-xl border border-sand-200 dark:border-sand-850 bg-white dark:bg-sand-975 hover:border-emerald-500 hover:bg-emerald-50/40 dark:hover:bg-emerald-950/30 active:scale-[0.98] transition cursor-pointer';
    @endphp

    <div class="max-w-3xl mx-auto py-2 sm:py-4 space-y-5 sm:space-y-6">
        <!-- Judul halaman -->
        <div class="space-y-1">
            <a href="{{ route('reports.index') }}"
                class="inline-flex items-center gap-1 text-sm font-semibold text-sand-600 hover:text-sand-900 dark:text-sand-400 dark:hover:text-sand-100 transition mb-1">
                &larr; Semua laporan
            </a>
            <h1 class="font-sans text-[1.75rem] leading-[1.15] sm:text-4xl font-extrabold tracking-tight text-sand-975 dark:text-sand-100">
                Laporkan Masalah Baru
            </h1>
            <p class="text-sm sm:text-base text-sand-600 dark:text-sand-400 leading-relaxed">
                Tiga langkah singkat: foto, ceritakan masalahnya, lalu kunci lokasinya dari tempat kejadian.
            </p>
        </div>

        <form id="reportForm" action="{{ route('reports.store') }}" method="POST" class="space-y-4 sm:space-y-5">
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

            <!-- Langkah 1: Foto bukti (dikompresi otomatis) -->
            <section class="{{ $stepCard }}">
                <div class="flex items-start gap-3">
                    <span class="{{ $stepNum }}">1</span>
                    <div>
                        <h2 class="{{ $stepTitle }}">Foto bukti <span class="text-danger">*</span></h2>
                        <p class="text-sm text-sand-600 dark:text-sand-500">Ambil langsung dengan kamera atau pilih dari galeri.</p>
                    </div>
                </div>

                <!-- Hidden Inputs: Galeri & Kamera Langsung -->
                <input type="file" id="galleryInput" accept="image/*" class="hidden">
                <input type="file" id="cameraInput" accept="image/*" capture="environment" class="hidden">

                <div class="border-2 border-dashed border-sand-300 dark:border-sand-850 hover:border-emerald-500 dark:hover:border-emerald-500 rounded-xl p-4 sm:p-6 text-center transition relative bg-sand-25 dark:bg-sand-950"
                    id="dropZone">

                    <!-- State 1: Tombol Pilihan Sumber Foto -->
                    <div id="uploadPlaceholder" class="space-y-3">
                        <div class="grid grid-cols-2 gap-3 max-w-md mx-auto">
                            <button type="button" id="btnTriggerCamera" class="{{ $sourceBtn }}">
                                <span class="w-11 h-11 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-2">
                                    <flux:icon name="camera" class="w-5 h-5" />
                                </span>
                                <span class="text-sm font-semibold text-sand-900 dark:text-sand-100">Kamera</span>
                                <span class="text-xs text-sand-500 dark:text-sand-600 mt-0.5">Foto langsung</span>
                            </button>

                            <button type="button" id="btnTriggerGallery" class="{{ $sourceBtn }}">
                                <span class="w-11 h-11 rounded-full bg-sand-100 dark:bg-sand-900 text-sand-700 dark:text-sand-300 flex items-center justify-center mb-2">
                                    <flux:icon name="photo" class="w-5 h-5" />
                                </span>
                                <span class="text-sm font-semibold text-sand-900 dark:text-sand-100">Galeri</span>
                                <span class="text-xs text-sand-500 dark:text-sand-600 mt-0.5">JPG, PNG, WebP</span>
                            </button>
                        </div>

                        <p class="hidden sm:block text-xs text-sand-500 dark:text-sand-600">
                            Atau seret dan letakkan foto ke area ini
                        </p>
                    </div>

                    <!-- State 2: Preview Foto Terkompresi -->
                    <div id="previewContainer" class="hidden space-y-3">
                        <div class="relative inline-block max-w-full">
                            <img id="imagePreview" src="" alt="Preview Bukti Laporan"
                                class="max-h-72 mx-auto rounded-xl border border-sand-200 dark:border-sand-850 object-cover">
                        </div>

                        <div class="flex flex-wrap items-center justify-center gap-2">
                            <button type="button" id="btnChangePhoto"
                                class="px-3.5 py-1.5 text-sm font-semibold rounded-lg border border-sand-200 dark:border-sand-850 bg-white dark:bg-sand-975 hover:bg-sand-50 dark:hover:bg-sand-925 text-sand-800 dark:text-sand-100 transition cursor-pointer inline-flex items-center gap-1.5">
                                <flux:icon name="arrow-path" class="w-4 h-4" />
                                <span>Ganti</span>
                            </button>
                            <button type="button" id="btnRemovePhoto"
                                class="px-3.5 py-1.5 text-sm font-semibold rounded-lg text-danger dark:text-danger-soft hover:bg-danger-tint dark:hover:bg-danger-deep/30 transition cursor-pointer inline-flex items-center gap-1.5">
                                <flux:icon name="trash" class="w-4 h-4" />
                                <span>Hapus</span>
                            </button>
                        </div>
                    </div>
                </div>
                @error('image_base64')
                    <p class="{{ $error }}">{{ $message }}</p>
                @enderror
            </section>

            <!-- Langkah 2: Informasi pokok masalah -->
            <section class="{{ $stepCard }}">
                <div class="flex items-start gap-3">
                    <span class="{{ $stepNum }}">2</span>
                    <div>
                        <h2 class="{{ $stepTitle }}">Apa masalahnya?</h2>
                        <p class="text-sm text-sand-600 dark:text-sand-500">Pilih kategori dan jelaskan singkat apa yang terjadi.</p>
                    </div>
                </div>

                <div>
                    <span class="{{ $label }}">Kategori <span class="text-danger">*</span></span>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach($categories as $catKey => $cat)
                            <label
                                class="relative flex items-center gap-2 p-2.5 rounded-lg border border-sand-200 dark:border-sand-850 bg-white dark:bg-sand-950 hover:border-emerald-500/60 cursor-pointer transition has-checked:border-emerald-600 dark:has-checked:border-emerald-500 has-checked:bg-emerald-50 dark:has-checked:bg-emerald-950/40 has-checked:ring-1 has-checked:ring-emerald-600 min-w-0">
                                <input type="radio" name="category" value="{{ $catKey }}" {{ old('category', 'jalan_jembatan') === $catKey ? 'checked' : '' }} class="sr-only">
                                <span class="w-7 h-7 rounded-full flex items-center justify-center shrink-0"
                                    style="background-color: {{ $cat['color'] }}20; color: {{ $cat['color'] }};">
                                    <flux:icon name="{{ $cat['icon'] }}" class="w-4 h-4" />
                                </span>
                                <span class="min-w-0 flex-1 text-xs sm:text-sm font-medium text-sand-800 dark:text-sand-100 leading-tight break-words">{{ $cat['label'] }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('category')
                        <p class="{{ $error }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="title" class="{{ $label }}">Judul <span class="text-danger">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required
                        placeholder="Contoh: Lubang jalan di Jl. Dago depan halte"
                        class="{{ $field }}">
                    @error('title')
                        <p class="{{ $error }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex items-baseline justify-between gap-2 mb-1.5">
                        <label for="description" class="text-sm font-semibold text-sand-800 dark:text-sand-200">Ceritakan detailnya <span class="text-danger">*</span></label>
                        <span id="descWordCounter" class="text-xs text-sand-500 dark:text-sand-500 tabular-nums shrink-0">
                            <span id="currentWordCount" data-ok="false" class="font-semibold text-danger data-[ok=true]:text-emerald-600 dark:text-danger-soft dark:data-[ok=true]:text-emerald-400">0</span> / min. 5 kata
                        </span>
                    </div>
                    <textarea id="description" name="description" rows="4" required
                        placeholder="Apa yang rusak, sejak kapan, dan apa dampaknya bagi warga?"
                        class="{{ $field }}">{{ old('description') }}</textarea>
                    <p class="mt-1.5 text-xs text-sand-500 dark:text-sand-500">
                        Ketik <strong class="font-semibold text-emerald-700 dark:text-emerald-400">@username</strong> untuk menandai dinas atau warga, mereka akan mendapat notifikasi.
                    </p>
                    @error('description')
                        <p class="{{ $error }}">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            <!-- Langkah 3: Titik lokasi GPS terverifikasi (GPS only) -->
            <section class="{{ $stepCard }}">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <span class="{{ $stepNum }}">3</span>
                        <div>
                            <h2 class="{{ $stepTitle }}">Di mana lokasinya? <span class="text-danger">*</span></h2>
                            <p class="text-sm text-sand-600 dark:text-sand-500">Lokasi diambil dari GPS perangkat, jadi lakukan saat Anda berada di tempat.</p>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2 shrink-0">
                        <button type="button" id="btnGeolocate"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 btn-primary font-semibold text-sm rounded-lg transition cursor-pointer">
                            <flux:icon name="viewfinder-circle" class="w-4 h-4 shrink-0" />
                            <span id="btnGeolocateText">Ambil Titik Lokasi GPS</span>
                        </button>
                        @env('local')
                            <!-- Khusus mode test (APP_ENV=local): isi titik acak tanpa sensor GPS -->
                            <button type="button" id="btnRandomLocation"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-dashed border-warning text-warning dark:text-warning-soft hover:bg-warning-tint dark:hover:bg-warning-deep/30 font-semibold text-sm rounded-lg transition cursor-pointer">
                                <flux:icon name="arrows-right-left" class="w-4 h-4 shrink-0" />
                                <span>Acak Lokasi (Test)</span>
                            </button>
                        @endenv
                    </div>
                </div>

                <!-- Kontainer Peta MapLibre (Read-Only GPS Visualization) -->
                <div class="relative">
                    <div id="mapPicker"
                        class="w-full h-64 sm:h-80 rounded-xl border border-sand-200 dark:border-sand-850 relative overflow-hidden">
                        <div id="mapLoading"
                            class="absolute inset-0 bg-white/70 dark:bg-sand-975/80 backdrop-blur-sm z-10 flex items-center justify-center text-sm font-medium text-sand-600 dark:text-sand-300">
                            Memuat peta...
                        </div>
                    </div>

                    <!-- Overlay: pin mengikuti GPS, tidak bisa digeser manual -->
                    <div class="absolute top-3 left-3 z-10 pointer-events-none">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-white/95 dark:bg-sand-975/95 text-sand-800 dark:text-sand-200 border border-sand-200 dark:border-sand-850 shadow-xs">
                            <flux:icon name="lock-closed" class="w-3 h-3 text-emerald-600 dark:text-emerald-400" />
                            <span>Pin mengikuti GPS</span>
                        </span>
                    </div>
                </div>

                <!-- Alamat terdeteksi otomatis. Gaya ditentukan data-locked; JS hanya mengubah atribut itu. -->
                <div id="gpsStatusContainer" data-locked="false"
                    class="group p-3.5 rounded-xl border text-sm flex items-start gap-3 transition bg-sand-25 border-sand-200 dark:bg-sand-950 dark:border-sand-850 data-[locked=true]:bg-emerald-50 data-[locked=true]:border-emerald-200 dark:data-[locked=true]:bg-emerald-950/40 dark:data-[locked=true]:border-emerald-900">
                    <div id="gpsStatusIcon" class="mt-0.5 shrink-0 text-sand-400 dark:text-sand-600 group-data-[locked=true]:text-emerald-600 dark:group-data-[locked=true]:text-emerald-400">
                        <flux:icon name="map-pin" class="w-4 h-4" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-sand-800 dark:text-sand-100" id="displayAddress">
                            Lokasi belum diambil. Tekan "Ambil Titik Lokasi GPS".
                        </div>
                        <div class="text-xs mt-0.5 tabular-nums text-sand-500 dark:text-sand-500 group-data-[locked=true]:text-emerald-800 dark:group-data-[locked=true]:text-emerald-300" id="displayCoordinates">
                            Koordinat: belum terkunci
                        </div>
                    </div>
                </div>
            </section>

            <!-- Kirim laporan -->
            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2 sm:gap-3 pt-1">
                <a href="{{ route('reports.index') }}"
                    class="text-center px-5 py-3 rounded-xl text-sand-600 hover:bg-sand-100 dark:text-sand-400 dark:hover:bg-sand-900 text-sm font-semibold transition">
                    Batal
                </a>
                <button type="submit" id="submitBtn"
                    class="px-6 py-3 btn-primary text-sm font-semibold rounded-xl transition">
                    Publikasikan Laporan
                </button>
            </div>
        </form>

        <!-- Tips agar laporan cepat ditindaklanjuti -->
        <section class="rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900/50 p-4 sm:p-6 space-y-3">
            <h2 class="text-base font-bold text-sand-975 dark:text-sand-100">Agar laporan cepat ditangani</h2>
            <ul class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 text-sm text-sand-700 dark:text-sand-300">
                <li class="flex gap-2.5">
                    <flux:icon name="camera" class="w-4 h-4 mt-0.5 text-emerald-600 dark:text-emerald-400 shrink-0" />
                    <span>Foto dari jarak yang memperlihatkan kerusakan <em>dan</em> sekitarnya.</span>
                </li>
                <li class="flex gap-2.5">
                    <flux:icon name="map-pin" class="w-4 h-4 mt-0.5 text-emerald-600 dark:text-emerald-400 shrink-0" />
                    <span>Kunci lokasi di tempat. Nama jalan dan kelurahan terisi otomatis.</span>
                </li>
                <li class="flex gap-2.5">
                    <flux:icon name="hand-thumb-up" class="w-4 h-4 mt-0.5 text-emerald-600 dark:text-emerald-400 shrink-0" />
                    <span>Ajak warga lain mendukung. Semakin banyak dukungan, semakin tinggi prioritasnya.</span>
                </li>
            </ul>
        </section>
    </div>

    <!-- Modal Webcam Kamera Langsung (Desktop / Browser) -->
    <div id="webcamModal"
        class="fixed inset-0 z-50 hidden bg-black/75 backdrop-blur-xs flex items-center justify-center p-4">
        <div
            class="bg-white dark:bg-sand-950 border border-sand-200 dark:border-sand-850 rounded-2xl max-w-lg w-full overflow-hidden shadow-2xl p-5 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <flux:icon name="camera" class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                    <span class="text-base font-bold text-sand-975 dark:text-sand-100">Ambil foto</span>
                </div>
                <button type="button" id="closeWebcamBtn"
                    class="text-sand-400 hover:text-sand-700 dark:hover:text-sand-200 p-1 cursor-pointer">
                    <flux:icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>

            <!-- Video Container -->
            <div class="relative bg-black rounded-xl overflow-hidden aspect-video flex items-center justify-center">
                <video id="webcamVideo" autoplay playsinline muted class="w-full h-full object-cover"></video>
                <div id="webcamLoading"
                    class="absolute inset-0 flex items-center justify-center bg-black/60 text-white text-sm gap-2">
                    <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                    <span>Menghubungkan ke kamera...</span>
                </div>
            </div>

            <!-- Shutter & Controls -->
            <div class="flex items-center justify-center gap-3 pt-1">
                <button type="button" id="shutterBtn"
                    class="px-5 py-2.5 btn-primary text-sm font-semibold rounded-lg flex items-center gap-2 transition cursor-pointer">
                    <flux:icon name="camera" class="w-4 h-4" />
                    <span>Jepret</span>
                </button>
                <button type="button" id="cancelWebcamBtn"
                    class="px-4 py-2.5 bg-sand-100 hover:bg-sand-200 dark:bg-sand-900 dark:hover:bg-sand-850 text-sand-700 dark:text-sand-300 text-sm font-semibold rounded-lg transition cursor-pointer">
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

            // Gaya terkunci ditentukan data-locked (lihat markup #gpsStatusContainer)
            if (container) {
                container.dataset.locked = 'true';
            }
            if (displayCoord) {
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

        // Mode test: titik acak dalam radius ~5 km dari pusat Bandung
        document.getElementById('btnRandomLocation')?.addEventListener('click', function () {
            const lat = -6.914744 + (Math.random() - 0.5) * 0.09;
            const lng = 107.609810 + (Math.random() - 0.5) * 0.09;

            marker.setLngLat([lng, lat]);
            if (!marker.getElement().parentElement) {
                marker.addTo(map);
            }
            map.flyTo({ center: [lng, lat], zoom: 16 });
            updateCoordinates(lat, lng);
        });

        // Live counter kata untuk deskripsi
        const descInput = document.getElementById('description');
        const wordCountDisplay = document.getElementById('currentWordCount');
        function updateWordCount() {
            if (!descInput || !wordCountDisplay) return;
            const words = descInput.value.trim().split(/\s+/).filter(Boolean);
            const count = words.length;
            wordCountDisplay.innerText = count;
            wordCountDisplay.dataset.ok = String(count >= 5);
        }
        if (descInput) {
            descInput.addEventListener('input', updateWordCount);
            updateWordCount();
        }

        // Validasi form sebelum submit (Foto, Deskripsi min 5 kata, & Wajib GPS)
        document.getElementById('reportForm').addEventListener('submit', function (e) {
            if (!imageBase64Input.value) {
                e.preventDefault();
                alert('Harap unggah foto bukti laporan terlebih dahulu!');
                return;
            }

            const descVal = (document.getElementById('description')?.value || '').trim();
            const words = descVal.split(/\s+/).filter(Boolean);
            if (words.length < 5) {
                e.preventDefault();
                alert('Deskripsi laporan minimal harus terdiri dari 5 kata agar informasi masalah lengkap dan jelas!');
                document.getElementById('description')?.focus();
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
            dropdown.className = 'fixed hidden bg-white dark:bg-sand-950 border border-slate-200 dark:border-sand-900 rounded-2xl shadow-2xl overflow-hidden w-72 max-w-[90vw] transition-opacity duration-150 text-left font-sans';
            dropdown.innerHTML = `
                <div class="px-3 py-2 bg-slate-50 dark:bg-sand-925 border-b border-slate-100 dark:border-sand-850 text-[10px] font-bold text-slate-400 dark:text-sand-500 uppercase tracking-wider flex items-center justify-between">
                    <span>Saran Tag Akun</span>
                    <span class="text-[9px] font-normal lowercase text-slate-400 dark:text-sand-600">↑↓ dan ↵</span>
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
                        : 'px-3 py-2 rounded-xl text-xs cursor-pointer flex items-center justify-between transition hover:bg-slate-100 dark:hover:bg-sand-900 text-slate-800 dark:text-sand-100';

                    const isAi = user.is_ai;
                    let badgeMarkup = '';
                    if (isAi) {
                        badgeMarkup = `<span class="ml-2 shrink-0 px-2 py-0.5 rounded-full text-[9px] font-extrabold ${isSelected ? 'bg-white/20 text-white' : 'bg-indigo-600 text-white'}">SIRA AI</span>`;
                    } else if (user.badge_type === 'admin') {
                        badgeMarkup = `<span class="ml-2 shrink-0 inline-flex items-center space-x-1 px-1.5 py-0.5 rounded text-[9px] font-bold ${isSelected ? 'bg-amber-400 text-slate-950' : 'bg-amber-100 text-amber-900 dark:bg-amber-950/80 dark:text-amber-300'}"><svg class="w-3 h-3 text-amber-500 fill-current shrink-0" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.516 2.17a.75.75 0 0 0-1.032 0 11.209 11.209 0 0 1-7.877 3.08.75.75 0 0 0-.722.515A12.74 12.74 0 0 0 2.25 9.75c0 5.942 4.064 10.933 9.563 12.348a.749.749 0 0 0 .374 0c5.499-1.415 9.563-6.406 9.563-12.348 0-1.39-.223-2.73-.635-3.985a.75.75 0 0 0-.722-.516l-.143.001c-2.996 0-5.717-1.17-7.734-3.08Zm3.094 8.016a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd"/></svg><span>ADMIN</span></span>`;
                    } else if (user.badge_type === 'verified') {
                        badgeMarkup = `<span class="ml-2 shrink-0 inline-flex items-center space-x-1 px-1.5 py-0.5 rounded text-[9px] font-bold ${isSelected ? 'bg-sky-400 text-slate-950' : 'bg-sky-100 text-sky-900 dark:bg-sky-950/80 dark:text-sky-300'}"><svg class="w-3 h-3 text-sky-500 fill-current shrink-0" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0 1 12 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 0 1 3.498 1.307 4.491 4.491 0 0 1 1.307 3.497A4.49 4.49 0 0 1 21.75 12a4.49 4.49 0 0 1-1.549 3.397 4.491 4.491 0 0 1-1.307 3.497 4.491 4.491 0 0 1-3.497 1.307A4.49 4.49 0 0 1 12 21.75a4.49 4.49 0 0 1-3.397-1.549 4.49 4.49 0 0 1-3.498-1.306 4.491 4.491 0 0 1-1.307-3.498A4.49 4.49 0 0 1 2.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 0 1 1.307-3.497 4.49 4.49 0 0 1 3.497-1.307Zm7.007 6.387a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd"/></svg><span>VERIFIED</span></span>`;
                    }

                    item.innerHTML = `
                        <div class="flex items-center space-x-2 min-w-0">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0 ${isAi ? 'overflow-hidden ring-1 ring-indigo-300' : 'bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 uppercase'}">
                                ${isAi ? '<img src="{{ asset('android-chrome-192x192.png') }}" alt="SIRA AI" class="w-6 h-6 rounded-full object-cover shrink-0">' : (user.username ? user.username.charAt(0).toUpperCase() : 'U')}
                            </div>
                            <div class="truncate">
                                <div class="truncate text-xs ${isSelected ? 'text-white' : (isAi ? 'text-indigo-600 dark:text-indigo-400 font-bold' : 'text-slate-900 dark:text-sand-100 font-medium')}">${user.name}</div>
                                <div class="text-[11px] ${isSelected ? 'text-emerald-100' : 'text-slate-400 dark:text-sand-500'} font-mono">@${user.username}</div>
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