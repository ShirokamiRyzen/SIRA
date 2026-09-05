<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\ReportComment;
use App\Models\ReportVote;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    /**
     * Jalankan seeder 1.000 data dummy bernuansa satir, menyindir realitas sosial,
     * serta perseteruan warga vs buzzer di kolom komentar.
     */
    public function run(): void
    {
        // -------------------------------------------------------------
        // 1. Akun Admin (username: admin, password: admin)
        // -------------------------------------------------------------
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator SIRA Pusat',
                'email' => 'admin@sira.local',
                'password' => Hash::make('admin'),
            ]
        );

        $this->command?->info('Akun Admin disiapkan: username [admin], password [admin]');

        // -------------------------------------------------------------
        // 2. Akun Warga Kritis & Akun Buzzer / Pembela Proyek
        // -------------------------------------------------------------
        $citizenPersonas = [
            // Warga Kritis, Sinis, dan Korban Lapangan
            ['name' => 'Slamet Velg Peyang', 'username' => 'korban_lubang_aspal'],
            ['name' => 'Bambang Overthinking', 'username' => 'warga_overthinking'],
            ['name' => 'Ibu RT Ikhlas Bayar Pajak', 'username' => 'pembayar_pajak_ikhlas'],
            ['name' => 'Pejalan Kaki Nyawa Cadangan', 'username' => 'pejalan_kaki_teraniaya'],
            ['name' => 'Kang Ojol Sabar Menanti', 'username' => 'kang_ojol_sabar'],
            ['name' => 'Duta Kolam Lele Aspal', 'username' => 'duta_kolam_lele'],
            ['name' => 'Kolektor Debu Proyek', 'username' => 'warga_kolektor_debu'],
            ['name' => 'Insinyur Otodidak Komplek', 'username' => 'insinyur_otodidak_rt'],
            ['name' => 'Alumni Janji Kampanye', 'username' => 'korban_janji_kampanye'],
            ['name' => 'Penyelam Gorong-Gorong', 'username' => 'bocah_petualang_got'],
            ['name' => 'Akuntan Warung Kopi', 'username' => 'pengamat_anggaran_dadakan'],
            ['name' => 'Juragan Shockbreaker', 'username' => 'bengkel_langganan_warga'],

            // Akun Buzzer, Pembela Proyek, & Penjaga Narasi Positif
            ['name' => 'Klarifikasi Cepat Cyber', 'username' => 'buzzer_pemberantas_hoaks'],
            ['name' => 'Relawan Pembangunan Abadi', 'username' => 'duta_pembangunan_abadi'],
            ['name' => 'Warga Tetap Bersyukur', 'username' => 'warga_tetap_bersyukur'],
            ['name' => 'Patroli Pembela Aspal', 'username' => 'aparat_pujian_cyber'],
            ['name' => 'Penyebar Energi Positif', 'username' => 'pejuang_narasi_positif'],
            ['name' => 'Loyalis Trotoar Estetik', 'username' => 'simpatisan_garis_keras'],
            ['name' => 'Satgas Anti Nyinyir', 'username' => 'buzzer_anti_nyinyir'],
            ['name' => 'Mitra Humas Tanpa Gaji', 'username' => 'mitra_humas_sukarela'],
        ];

        $users = collect([$admin]);
        $defaultPassword = Hash::make('password123');

        foreach ($citizenPersonas as $persona) {
            $user = User::firstOrCreate(
                ['username' => $persona['username']],
                [
                    'name' => $persona['name'],
                    'email' => $persona['username'].'@sira.test',
                    'password' => $defaultPassword,
                ]
            );
            $users->push($user);
        }

        // Generate tambahan user pelengkap hingga 50 pengguna
        for ($i = 21; $i <= 50; $i++) {
            $un = "warga_pantau_{$i}";
            $user = User::firstOrCreate(
                ['username' => $un],
                [
                    'name' => "Warga Pemerhati RT {$i}",
                    'email' => "{$un}@sira.test",
                    'password' => $defaultPassword,
                ]
            );
            $users->push($user);
        }

        $userIds = $users->pluck('id')->toArray();

        // -------------------------------------------------------------
        // 3. Dataset Masalah Satir & Sindiran Fasilitas Publik
        // -------------------------------------------------------------
        $satiricalTemplates = [
            [
                'title' => 'Wahana Wisata Kolam Lele Baru di Tengah Aspal Jalan',
                'desc' => 'Pemerintah sangat visioner! Lubang jalan berdiameter 1 meter ini sengaja dibiarkan berbulan-bulan tanpa tambalan agar warga bisa budidaya ikan lele mandiri demi ketahanan pangan lokal. Terima kasih atas wahana rekreasi air gratisnya.',
                'tag' => 'Wisata Aspal',
            ],
            [
                'title' => 'Monumen Tiang Lampu Estetik Futuristik Tanpa Aliran Listrik',
                'desc' => 'Desain tiang lampunya sangat artistik bergaya victoria eropa, sayangnya tidak pernah menyala sekalipun sejak seremoni gunting pita 8 bulan lalu. Sangat cocok untuk menguji nyali uji keberanian warga di malam hari.',
                'tag' => 'Monumen Gelap',
            ],
            [
                'title' => 'Trotoar Khusus Parkir Mobil Mewah dan Gerobak Gorengan',
                'desc' => 'Trotoar yang dibangun dengan anggaran miliaran rupiah ini sangat ramah pejalan kaki, asalkan pejalan kakinya mampu melompati kap mobil dan uap minyak panas gorengan. Pejalan kaki mohon tahu diri jangan mengganggu lapak parkir.',
                'tag' => 'Hak Pejalan Kaki',
            ],
            [
                'title' => 'Wahana Waterboom Alami Setiap Hujan Turun Lebih dari 15 Menit',
                'desc' => 'Sistem drainase dirancang istimewa agar air tidak lekas surut ke laut, melainkan memanjakan warga dengan sensasi arung jeram setinggi lutut di ruang tamu masing-masing.',
                'tag' => 'Arung Jeram Gratis',
            ],
            [
                'title' => 'Proyek Gali Tutup Gali Aspal Abadi Tiada Henti',
                'desc' => 'Minggu lalu baru selesai diaspal mulus dengan bangga, minggu ini digali lagi buat kabel, minggu depan rencana digali pipa air. Sebuah siklus ekonomi sirkular tiada tara demi kelangsungan hidup para vendor tersayang.',
                'tag' => 'Proyek Abadi',
            ],
            [
                'title' => 'Zebra Cross Khusus Pejalan Kaki yang Memiliki Nyawa Cadangan',
                'desc' => 'Marka penyeberangan diletakkan tepat di tikungan buta tanpa rambu peringatan. Didedikasikan khusus untuk warga yang sudah siap menjemput ajal dan tidak takut ditabrak truk tronton.',
                'tag' => 'Uji Adrenalin',
            ],
            [
                'title' => 'Pohon Rindang Penjaga Tradisi Belum Dipangkas Sejak Musim Lalu',
                'desc' => 'Dahan pohon tua sudah melengkung anggun menyentuh kabel optik dan kepala sopir pick-up. Warga sengaja tidak memangkas karena masih menunggu dinas terkait menyelesaikan rapat koordinasi lintas sektoral jilid ke-4.',
                'tag' => 'Pohon Keramat',
            ],
            [
                'title' => 'Tutup Manhole Gorong-Gorong Diganti Ranting Pohon Beringin Estetik',
                'desc' => 'Tutup besi gorong-gorong lenyap entah ke mana. Sebagai gantinya, warga gotong royong menancapkan ranting pohon lengkap dengan plastik kresek merah berkibar sebagai sensor peringatan ultra-canggih.',
                'tag' => 'Kearifan Lokal',
            ],
            [
                'title' => 'Jembatan Penyeberangan Orang (JPO) Konsep Sauna Tropis Terbuka',
                'desc' => 'Atap jembatan penyeberangan sengaja dibiarkan bolong-bolong agar warga bisa berjemur menyerap vitamin D saat siang bolong dan menikmati mandi air hujan alami saat petang.',
                'tag' => 'Sauna Alam',
            ],
            [
                'title' => 'Taman Bermain Anak Berkonsep Survival Gladiator Extrim',
                'desc' => 'Perosotan berkarat dengan ujung seng tajam dan ayunan rantai putus satu. Sangat mendidik karakter anak-anak sejak usia dini agar tangguh menghadapi kejamnya dunia nyata.',
                'tag' => 'Latihan Survival',
            ],
            [
                'title' => 'Halte Bus Mewah Tempat Tidur Siang Nyaman Tanpa Ada Bus yang Lewat',
                'desc' => 'Sebuah mahakarya infrastruktur transportasi publik: halte megah ber-AC alami tanpa ada satupun trayek bus yang melintas selama 2 tahun terakhir.',
                'tag' => 'Halte Ghaib',
            ],
            [
                'title' => 'Timbunan Debu Vulkanik Proyek yang Tak Kunjung Dibereskan',
                'desc' => 'Warga sekitar kini tidak perlu jauh-jauh liburan ke Gunung Bromo untuk merasakan sensasi badai debu pasir. Cukup duduk manis di teras rumah sambil batuk berjamaah.',
                'tag' => 'Sensasi Bromo',
            ],
        ];

        // Kluster Wilayah Perkotaan (Padat untuk visualisasi Heatmap)
        $clusters = [
            // Kluster Kota Bandung
            [
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Coblong',
                'subdistrict' => 'Dago',
                'address_prefix' => 'Jl. Ir. H. Juanda No. ',
                'base_lat' => -6.885000,
                'base_lng' => 107.613000,
                'spread' => 0.015,
            ],
            [
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Coblong',
                'subdistrict' => 'Lebakgede',
                'address_prefix' => 'Jl. Dipati Ukur No. ',
                'base_lat' => -6.892000,
                'base_lng' => 107.618000,
                'spread' => 0.012,
            ],
            [
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Sumur Bandung',
                'subdistrict' => 'Braga',
                'address_prefix' => 'Jl. Braga No. ',
                'base_lat' => -6.917000,
                'base_lng' => 107.609000,
                'spread' => 0.010,
            ],
            [
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Cicendo',
                'subdistrict' => 'Pasirkaliki',
                'address_prefix' => 'Jl. Pasirkaliki No. ',
                'base_lat' => -6.908000,
                'base_lng' => 107.601000,
                'spread' => 0.014,
            ],
            [
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Lengkong',
                'subdistrict' => 'Malabar',
                'address_prefix' => 'Jl. Burangrang No. ',
                'base_lat' => -6.928000,
                'base_lng' => 107.621000,
                'spread' => 0.015,
            ],
            // Kluster DKI Jakarta
            [
                'province' => 'DKI Jakarta',
                'city' => 'Jakarta Pusat',
                'district' => 'Menteng',
                'subdistrict' => 'Cikini',
                'address_prefix' => 'Jl. Cikini Raya No. ',
                'base_lat' => -6.191000,
                'base_lng' => 106.841000,
                'spread' => 0.015,
            ],
            [
                'province' => 'DKI Jakarta',
                'city' => 'Jakarta Selatan',
                'district' => 'Kebayoran Baru',
                'subdistrict' => 'Senayan',
                'address_prefix' => 'Jl. Senopati No. ',
                'base_lat' => -6.230000,
                'base_lng' => 106.808000,
                'spread' => 0.018,
            ],
            // Kluster Surabaya & Yogya
            [
                'province' => 'Jawa Timur',
                'city' => 'Kota Surabaya',
                'district' => 'Gubeng',
                'subdistrict' => 'Airlangga',
                'address_prefix' => 'Jl. Dharmawangsa No. ',
                'base_lat' => -7.272000,
                'base_lng' => 112.756000,
                'spread' => 0.018,
            ],
            [
                'province' => 'D.I. Yogyakarta',
                'city' => 'Kota Yogyakarta',
                'district' => 'Danurejan',
                'subdistrict' => 'Bausasran',
                'address_prefix' => 'Jl. Malioboro No. ',
                'base_lat' => -7.792000,
                'base_lng' => 110.366000,
                'spread' => 0.012,
            ],
        ];

        // -------------------------------------------------------------
        // 4. Palette Gambar Base64 Ringan Bergaya Editorial
        // -------------------------------------------------------------
        $base64Images = [
            'data:image/svg+xml;base64,'.base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 400" width="100%" height="100%"><rect width="600" height="400" fill="#1e293b"/><path d="M50 300 Q150 250 300 290 T550 310" stroke="#334155" stroke-width="14" fill="none"/><circle cx="280" cy="270" r="50" fill="#0f172a" stroke="#ef4444" stroke-width="6"/><text x="280" y="370" fill="#f8fafc" font-family="monospace" font-size="18" font-weight="bold" text-anchor="middle">Wisata Kolam Aspal Bersejarah</text></svg>'),
            'data:image/svg+xml;base64,'.base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 400" width="100%" height="100%"><rect width="600" height="400" fill="#14532d"/><polygon points="200,320 250,220 350,220 400,320" fill="#166534"/><circle cx="300" cy="180" r="45" fill="#f59e0b"/><text x="300" y="370" fill="#ffffff" font-family="monospace" font-size="18" font-weight="bold" text-anchor="middle">Sampah Estetik Pembatas Jalan</text></svg>'),
            'data:image/svg+xml;base64,'.base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 400" width="100%" height="100%"><rect width="600" height="400" fill="#0f172a"/><line x1="300" y1="50" x2="300" y2="300" stroke="#475569" stroke-width="10"/><circle cx="300" cy="70" r="28" fill="#38bdf8" opacity="0.2"/><text x="300" y="370" fill="#cbd5e1" font-family="monospace" font-size="18" font-weight="bold" text-anchor="middle">Monumen Tiang Gelap Gulita</text></svg>'),
            'data:image/svg+xml;base64,'.base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 400" width="100%" height="100%"><rect width="600" height="400" fill="#1e3a8a"/><rect x="0" y="220" width="600" height="180" fill="#1d4ed8"/><text x="300" y="150" fill="#ffffff" font-family="monospace" font-size="22" font-weight="bold" text-anchor="middle">Waterboom Alami Depan Teras</text></svg>'),
            'data:image/svg+xml;base64,'.base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 400" width="100%" height="100%"><rect width="600" height="400" fill="#3b0764"/><path d="M100 350 L300 120 L500 350 Z" fill="#581c87"/><rect x="280" y="280" width="40" height="100" fill="#713f12"/><text x="300" y="80" fill="#f3e8ff" font-family="monospace" font-size="18" font-weight="bold" text-anchor="middle">Pohon Keramat Penunggu Kabel</text></svg>'),
        ];

        // -------------------------------------------------------------
        // 5. Generate 1.000 Laporan Dummy Satir
        // -------------------------------------------------------------
        $this->command?->info('Menyiapkan 1.000 data laporan dummy satir...');

        $reportsData = [];
        $now = now();
        $targetCount = 1000;

        for ($i = 1; $i <= $targetCount; $i++) {
            $cluster = $clusters[array_rand($clusters)];
            $template = $satiricalTemplates[array_rand($satiricalTemplates)];
            $authorId = $userIds[array_rand($userIds)];
            $image = $base64Images[array_rand($base64Images)];

            // Random Jitter koordinat di sekitar kluster
            $latJitter = (mt_rand(-1000, 1000) / 1000) * $cluster['spread'];
            $lngJitter = (mt_rand(-1000, 1000) / 1000) * $cluster['spread'];
            $latitude = round($cluster['base_lat'] + $latJitter, 8);
            $longitude = round($cluster['base_lng'] + $lngJitter, 8);

            // Distribusi Vote & Tier:
            // ~5% Critical Tier (100 - 280 votes)
            // ~15% Urgent Tier (50 - 99 votes)
            // ~35% Trending Tier (10 - 49 votes)
            // ~45% Normal Tier (0 - 9 votes)
            $randTierChance = mt_rand(1, 100);
            if ($randTierChance <= 5) {
                $upvotes = mt_rand(102, 280);
                $downvotes = mt_rand(1, 20);
                $tier = 'critical';
            } elseif ($randTierChance <= 20) {
                $upvotes = mt_rand(52, 98);
                $downvotes = mt_rand(1, 12);
                $tier = 'urgent';
            } elseif ($randTierChance <= 55) {
                $upvotes = mt_rand(12, 48);
                $downvotes = mt_rand(0, 6);
                $tier = 'trending';
            } else {
                $upvotes = mt_rand(0, 9);
                $downvotes = mt_rand(0, 3);
                $tier = 'normal';
            }

            $voteScore = max(0, $upvotes - $downvotes);
            $houseNum = mt_rand(1, 250);
            $address = "{$cluster['address_prefix']}{$houseNum}, {$cluster['subdistrict']}, {$cluster['district']}, {$cluster['city']}, {$cluster['province']}";

            $createdAt = $now->copy()->subMinutes(mt_rand(15, 43200));
            $statuses = ['active', 'active', 'active', 'in_progress', 'resolved'];
            $status = $statuses[array_rand($statuses)];

            $reportsData[] = [
                'user_id' => $authorId,
                'title' => "{$template['title']} #{$i}",
                'description' => "{$template['desc']} Titik koordinat terpantau di sekitar area {$cluster['subdistrict']}.",
                'image_base64' => $image,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'geohash' => $this->quickGeohash($latitude, $longitude),
                'province' => $cluster['province'],
                'city' => $cluster['city'],
                'district' => $cluster['district'],
                'subdistrict' => $cluster['subdistrict'],
                'formatted_address' => $address,
                'osm_place_id' => (string) mt_rand(10000000, 99999999),
                'rank_tier' => $tier,
                'upvotes_count' => $upvotes,
                'downvotes_count' => $downvotes,
                'vote_score' => $voteScore,
                'comments_count' => 0,
                'status' => $status,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
        }

        // Simpan dalam batch 200 data
        foreach (array_chunk($reportsData, 200) as $chunk) {
            DB::table('reports')->insert($chunk);
        }

        $this->command?->info('1.000 Laporan berhasil di-generate.');

        // -------------------------------------------------------------
        // 6. Komentar Satir: Perseteruan Warga Kritis vs Akun Buzzer
        // -------------------------------------------------------------
        $this->command?->info('Menyiapkan debat komentar satir warga vs buzzer...');

        $critics = User::whereIn('username', [
            'korban_lubang_aspal',
            'warga_overthinking',
            'pembayar_pajak_ikhlas',
            'pejalan_kaki_teraniaya',
            'kang_ojol_sabar',
            'duta_kolam_lele',
            'korban_janji_kampanye',
        ])->get();

        $buzzers = User::whereIn('username', [
            'buzzer_pemberantas_hoaks',
            'duta_pembangunan_abadi',
            'warga_tetap_bersyukur',
            'aparat_pujian_cyber',
            'pejuang_narasi_positif',
            'simpatisan_garis_keras',
            'buzzer_anti_nyinyir',
        ])->get();

        $debateScenarios = [
            [
                'root' => 'Ini lubang dari jaman bupati periode pertama masih awet aja, minimal diresmikan ulang dong potong tumpeng!',
                'buzzer' => 'Jangan cuma bisa nyinyir di medsos! Apa kontribusi nyata kamu buat daerah?! Pemda lagi bekerja keras dalam senyap, anggaran sudah dialokasikan bertahap di APBD perubahan, tolong hargai prosesnya!',
                'rebuttal' => 'Bekerja dalam senyap sampai lampunya padam beneran ya min? Tiap bayar pajak tepat waktu giliran nuntut hak malah dibilang ga ada kontribusi wkwk.',
            ],
            [
                'root' => 'Banjirnya udah sepinggang, perahu karet dinas mana ya? Apa nunggu viral di TikTok dulu baru petugasnya pada dateng bikin konten?',
                'buzzer' => 'Tolong dipahami, curah hujan kali ini adalah fenomena anomali iklim global! Di kota maju luar negeri kayak New York juga banjir! Jangan semua-semua salahin pemerintah, introspeksi diri kalian yang suka buang puntung rokok!',
                'rebuttal' => 'Mantap logikanya, gorong-gorong ditutup semen ruko dibilang anomali iklim global. Sertifikasi buzzer tier Mythic Glory nih.',
            ],
            [
                'root' => 'Trotoar jalan udah kayak showroom motor bekas dan lapak pecel lele. Pejalan kaki disuruh terbang pake baling-baling bambu apa gimana?',
                'buzzer' => 'Kalian tidak paham sosiologi perkotaan! Itu bagian dari denyut pemberdayaan UMKM ekonomi kerakyatan! Jangan mematikan rezeki rakyat kecil demi ego kaum borjuis yang mau jalan kaki doang!',
                'rebuttal' => 'UMKM punya mobil Fortuner 2 biji diparkir di trotoar dibilang rakyat kecil. Lawak bener abang buzzer satu ini, makan nasi bungkusnya jangan lupa.',
            ],
            [
                'root' => 'Tiang lampu estetik gaya eropa dipasang puluhan biji tapi kabelnya ga ada yang nyambung. Jadi monumen sarang laba-laba doang.',
                'buzzer' => 'Lho itu namanya tahapan estetika ruang publik fase 1! Penyambungan daya PLN itu masuk fase 2 tahun anggaran depan. Kalian yang awam teknis jangan sok tau mengkritik kinerja tim ahli!',
                'rebuttal' => 'Fase 1 tiang, fase 2 kabel, fase 3 lampu, fase 4 roboh kena angin. Proyek tiada akhir emang paling gurih!',
            ],
            [
                'root' => 'Baru kemarin diaspal mulus difoto-foto pejabat, sekarang udah dibongkar lagi digali pipa. Kompak banget antar dinas ya!',
                'buzzer' => 'Itu koordinasi taktis terpadu! Pemasangan pipa air bersih itu kebutuhan primer warga juga! Kalau pipa ga dipasang kalian protes, dipasang juga protes. Maunya apa sih netizen?',
                'rebuttal' => 'Maunya ya gali dulu baru diaspal malih, bukan aspal baru sehari diacak-acak kayak bubur ayam diaduk!',
            ],
        ];

        // Sematkan debat satir ke 60 laporan teratas
        $topReports = Report::orderByDesc('vote_score')->take(60)->get();

        foreach ($topReports as $rep) {
            $scenario = $debateScenarios[array_rand($debateScenarios)];
            $criticUser = $critics->random();
            $buzzerUser = $buzzers->random();
            $rebuttalUser = $critics->where('id', '!=', $criticUser->id)->random();

            // 1. Komentar Utama (Warga Kritis)
            $root = ReportComment::create([
                'report_id' => $rep->id,
                'user_id' => $criticUser->id,
                'parent_id' => null,
                'content' => $scenario['root'],
                'created_at' => $rep->created_at->copy()->addMinutes(mt_rand(10, 120)),
            ]);

            // 2. Balasan Buzzer (Membela Proyek / Menyalahkan Warga)
            $buzzerReply = ReportComment::create([
                'report_id' => $rep->id,
                'user_id' => $buzzerUser->id,
                'parent_id' => $root->id,
                'content' => $scenario['buzzer'],
                'created_at' => $root->created_at->copy()->addMinutes(mt_rand(5, 45)),
            ]);

            // 3. Balasan Balik Warga (Menyindir Buzzer)
            ReportComment::create([
                'report_id' => $rep->id,
                'user_id' => $rebuttalUser->id,
                'parent_id' => $buzzerReply->id,
                'content' => $scenario['rebuttal'],
                'created_at' => $buzzerReply->created_at->copy()->addMinutes(mt_rand(5, 30)),
            ]);

            // Perbarui counter comments_count
            $actualComments = ReportComment::where('report_id', $rep->id)->count();
            $rep->update(['comments_count' => $actualComments]);
        }

        // -------------------------------------------------------------
        // 7. Berikan Vote Nyata
        // -------------------------------------------------------------
        $highPriorityReports = Report::take(40)->get();
        foreach ($highPriorityReports as $hpr) {
            ReportVote::firstOrCreate(
                ['report_id' => $hpr->id, 'user_id' => $admin->id],
                ['value' => 1]
            );
        }

        $this->command?->info('Seeding 1.000 data dummy satir, buzzer, dan warga kritis sukses dijalankan!');
    }

    private function quickGeohash(float $lat, float $lon, int $precision = 8): string
    {
        $base32 = '0123456789bcdefghjkmnpqrstuvwxyz';
        $minLat = -90.0;
        $maxLat = 90.0;
        $minLon = -180.0;
        $maxLon = 180.0;

        $geohash = '';
        $isEven = true;
        $bit = 0;
        $ch = 0;

        while (strlen($geohash) < $precision) {
            if ($isEven) {
                $mid = ($minLon + $maxLon) / 2;
                if ($lon >= $mid) {
                    $ch |= (1 << (4 - $bit));
                    $minLon = $mid;
                } else {
                    $maxLon = $mid;
                }
            } else {
                $mid = ($minLat + $maxLat) / 2;
                if ($lat >= $mid) {
                    $ch |= (1 << (4 - $bit));
                    $minLat = $mid;
                } else {
                    $maxLat = $mid;
                }
            }

            $isEven = ! $isEven;
            if ($bit < 4) {
                $bit++;
            } else {
                $geohash .= $base32[$ch];
                $bit = 0;
                $ch = 0;
            }
        }

        return $geohash;
    }
}
