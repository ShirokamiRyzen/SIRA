@extends('layouts.app')

@section('title', 'Buat Laporan Baru - SIRA')

@section('content')
    <div class="max-w-4xl mx-auto my-6">
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

                <!-- 1. Bagian Upload Foto dengan Kompresi 80% -->
                <div>
                    <label class="block text-sm font-bold text-slate-900 dark:text-[#EDEDEC] mb-2">
                        Foto Bukti Laporan <span class="text-rose-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-slate-300 dark:border-[#282828] hover:border-emerald-500 dark:hover:border-emerald-500 rounded-3xl p-6 text-center transition cursor-pointer relative bg-slate-50 dark:bg-[#181818] hover:bg-emerald-50/20 dark:hover:bg-emerald-950/20"
                        id="dropZone">
                        <input type="file" id="photoInput" accept="image/*"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" required>

                        <div id="uploadPlaceholder" class="space-y-2">
                            <div
                                class="w-12 h-12 rounded-2xl bg-white dark:bg-[#222222] shadow-sm flex items-center justify-center mx-auto text-emerald-600 dark:text-emerald-400 border border-slate-200 dark:border-[#333333]">
                                <flux:icon name="camera" class="w-6 h-6" />
                            </div>
                            <div class="text-sm font-semibold text-slate-800 dark:text-[#EDEDEC]">
                                Klik atau seret foto ke sini untuk mengunggah
                            </div>
                            <p class="text-xs text-slate-400 dark:text-[#787774]">
                                Mendukung JPG, PNG, WebP.
                            </p>
                        </div>

                        <!-- Preview Foto Terkompresi -->
                        <div id="previewContainer" class="hidden space-y-3">
                            <img id="imagePreview" src="" alt="Preview"
                                class="max-h-64 mx-auto rounded-2xl shadow-sm border border-slate-200 dark:border-[#282828] object-cover">
                            <div class="text-xs font-semibold text-emerald-700 dark:text-emerald-300 bg-emerald-100/80 dark:bg-emerald-950/60 inline-block px-3 py-1 rounded-full"
                                id="compressionInfo">
                                Kompresi 80% Berhasil
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
                <div>
                    <div class="flex flex-wrap items-center justify-between mb-3 gap-2">
                        <div>
                            <label class="block text-sm font-bold text-slate-900 dark:text-[#EDEDEC]">
                                Titik Lokasi <span class="text-rose-500">*</span>
                            </label>
                            <p class="text-xs text-slate-400 dark:text-[#787774]">Geser pin pada peta atau klik tombol
                                deteksi GPS di bawah.</p>
                        </div>
                        <button type="button" id="btnGeolocate"
                            class="inline-flex items-center space-x-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-[#222222] dark:hover:bg-[#282828] active:bg-slate-300 text-slate-700 dark:text-[#EDEDEC] font-semibold text-xs rounded-xl transition">
                            <flux:icon name="viewfinder-circle"
                                class="w-3.5 h-3.5 text-slate-600 dark:text-[#EDEDEC] shrink-0" />
                            <span id="btnGeolocateText">Deteksi Lokasi Saya (GPS)</span>
                        </button>
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
    </div>
@endsection

@push('scripts')
    <script>
        // -------------------------------------------------------------
        // 1. Client-Side Image Compression (80% Quality to Base64)
        // -------------------------------------------------------------
        const photoInput = document.getElementById('photoInput');
        const imagePreview = document.getElementById('imagePreview');
        const imageBase64Input = document.getElementById('imageBase64');
        const uploadPlaceholder = document.getElementById('uploadPlaceholder');
        const previewContainer = document.getElementById('previewContainer');
        const compressionInfo = document.getElementById('compressionInfo');

        photoInput.addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (!file) return;

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
        });

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
                            const road = addr.road || addr.pedestrian || addr.footway || addr.suburb || '';
                            const subdistrict = addr.village || addr.subdistrict || addr.neighbourhood || '';
                            const district = addr.city_district || addr.municipality || addr.district || '';
                            const city = addr.city || addr.town || addr.county || addr.regency || '';
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

        // Validasi form sebelum submit
        document.getElementById('reportForm').addEventListener('submit', function (e) {
            if (!imageBase64Input.value) {
                e.preventDefault();
                alert('Harap unggah foto bukti laporan terlebih dahulu!');
            }
        });
    </script>
@endpush