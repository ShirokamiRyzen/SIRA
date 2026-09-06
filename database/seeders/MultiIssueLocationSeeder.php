<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\ReportComment;
use App\Models\ReportVote;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MultiIssueLocationSeeder extends Seeder
{
    /**
     * Seeder untuk mensimulasikan skenario di mana satu titik koordinat/lokasi yang sama persis
     * memiliki beberapa permasalahan publik dari berbagai kategori berbeda sekaligus.
     *
     * Sangat berguna untuk pengujian:
     * 1. Akumulasi heatmap pada koordinat identik.
     * 2. Pengelompokan (clustering) dan tumpang-tindih marker laporan pada peta.
     * 3. Pemantauan analitik multi-masalah di satu titik rawan (hotspot) perkotaan.
     */
    public function run(): void
    {
        $now = now();
        $defaultPassword = Hash::make('password123');

        // Pastikan akun bot SIRA tersedia untuk ringkasan AI
        $siraBot = User::firstOrCreate(
            ['username' => 'Sira'],
            [
                'name' => 'SIRA AI Assistant',
                'email' => 'ai@sira.local',
                'password' => Hash::make('sira-bot-secure-'.config('app.key')),
            ]
        );

        // Pastikan akun admin & user tersedia jika seeder dijalankan mandiri
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator SIRA Pusat',
                'email' => 'admin@sira.local',
                'password' => Hash::make('admin'),
            ]
        );

        $userAccount = User::firstOrCreate(
            ['username' => 'user'],
            [
                'name' => 'Pengguna Warga',
                'email' => 'user@sira.local',
                'password' => Hash::make('user'),
            ]
        );

        // Persona warga pelapor & komentator
        $personas = [
            ['name' => 'Bambang Kritis Lapangan', 'username' => 'bambang_kritis_lapangan'],
            ['name' => 'Rina Pemerhati Pedestrian', 'username' => 'rina_pedestrian_bdg'],
            ['name' => 'Hendra Ojol Siaga', 'username' => 'kang_ojol_simpangan'],
            ['name' => 'Dewi Ibu Rumah Tangga', 'username' => 'dewi_warga_setempat'],
            ['name' => 'Ahmad Teknisi Kabel', 'username' => 'ahmad_pemerhati_utilitas'],
            ['name' => 'Klarifikasi Dinas Simpatisan', 'username' => 'mitra_penjelas_lapangan'],
            ['name' => 'Satgas Cepat Tanggap Cyber', 'username' => 'satgas_pantau_lapangan'],
        ];

        $voterUsers = collect([$userAccount]);

        foreach ($personas as $p) {
            $createdUser = User::firstOrCreate(
                ['username' => $p['username']],
                [
                    'name' => $p['name'],
                    'email' => $p['username'].'@sira.test',
                    'password' => $defaultPassword,
                ]
            );
            $voterUsers->push($createdUser);
        }

        // Tambahan akun dummy pemilih jika belum ada untuk mengisi vote
        $existingUserCount = User::where('id', '!=', $admin->id)->where('id', '!=', $siraBot->id)->count();
        if ($existingUserCount < 160) {
            $extraToMake = 160 - $existingUserCount;
            $bulkUsers = [];
            for ($k = 1; $k <= $extraToMake; $k++) {
                $uName = "warga_co_voter_{$k}";
                $bulkUsers[] = [
                    'name' => "Warga Partisipan #{$k}",
                    'username' => $uName,
                    'email' => "{$uName}@sira.test",
                    'password' => $defaultPassword,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            DB::table('users')->insertOrIgnore($bulkUsers);
        }

        $allVoterIds = User::where('id', '!=', $admin->id)
            ->where('id', '!=', $siraBot->id)
            ->pluck('id')
            ->toArray();

        // ---------------------------------------------------------------------
        // Definisi Hotspot: Koordinat SAMA PERSIS dengan Berbagai Masalah Berbeda
        // ---------------------------------------------------------------------
        $hotspots = [
            [
                'spot_name' => 'Simpang Lima Asia Afrika - Tamblong',
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Sumur Bandung',
                'subdistrict' => 'Braga',
                'formatted_address' => 'Simpang Lima, Jl. Asia Afrika, Braga, Kec. Sumur Bandung, Kota Bandung, Jawa Barat',
                'latitude' => -6.92147100,
                'longitude' => 107.61114200,
                'reports' => [
                    [
                        'category' => 'infrastruktur',
                        'title' => 'Lubang Jalan Amblas Berdiameter Besar di Jalur Belok Simpang Lima',
                        'description' => 'Tepat di titik temu belokan Simpang Lima menuju Tamblong, terdapat aspal amblas menganga sedalam 15 cm berdiameter hampir 1 meter. Menjadi jebakan maut bagi pengendara roda dua terutama saat malam hari dan jam sibuk pulang kerja.',
                        'short_label' => 'Jalan Amblas Menganga',
                        'accent_color' => '#f97316',
                        'target_upvotes' => 135,
                        'target_downvotes' => 3,
                        'comments' => [
                            [
                                'user' => 'bambang_kritis_lapangan',
                                'text' => 'Sudah 3 motor velg-nya peyang gara-gara lubang ini pas belok dari arah Asia Afrika. Posisi persis di blind spot lampu merah!',
                            ],
                            [
                                'user' => 'mitra_penjelas_lapangan',
                                'text' => 'Laporan sudah diteruskan ke Unit Reaksi Cepat Bina Marga, sedang dijadwalkan overlay aspal dalam siklus pemeliharaan berkala.',
                            ],
                            [
                                'user' => 'kang_ojol_simpangan',
                                'text' => 'Kalau nunggu siklus keburu ada korban patah tulang min, minimal kasih drum atau cat semprot putih dulu!',
                            ],
                        ],
                    ],
                    [
                        'category' => 'kelistrikan',
                        'title' => 'Lampu PJU Utama Padam Total & Kabel Utilitas Rendah Nyaris Mengenai Bus',
                        'description' => 'Penerangan tiang utama di simpang lima ini padam total selama lebih dari dua pekan. Selain gelap gulita, kabel fiber dan listrik menjuntai ke bawah hingga ketinggian 2,6 meter yang rawan tersangkut atap bus dan truk barang.',
                        'short_label' => 'PJU Padam & Kabel Menjuntai',
                        'accent_color' => '#eab308',
                        'target_upvotes' => 84,
                        'target_downvotes' => 2,
                        'comments' => [
                            [
                                'user' => 'ahmad_pemerhati_utilitas',
                                'text' => 'Tiang PJU-nya padam dari gardu simpang, ditambah kabel liar saling tumpang tindih kayak jaring laba-laba.',
                            ],
                            [
                                'user' => 'bambang_kritis_lapangan',
                                'text' => 'Malam hari gelap banget, rawan tindak kejahatan dan bikin jalan amblas di bawahnya jadi tidak terlihat sama sekali.',
                            ],
                        ],
                    ],
                    [
                        'category' => 'lingkungan',
                        'title' => 'Tumpukan Sampah Liar dan Bau Menyengat Meluber ke Badan Jalan Simpang Lima',
                        'description' => 'Sudut pembatas pulau jalan di simpang lima beralih fungsi menjadi tempat pembuangan sampah liar dadakan. Karung plastik, limbah basah, dan sisa makanan menumpuk hingga meluber ke aspal dan mengeluarkan bau busuk menyengat bagi pengendara yang antre di lampu merah.',
                        'short_label' => 'TPS Liar Median Jalan',
                        'accent_color' => '#16a34a',
                        'target_upvotes' => 67,
                        'target_downvotes' => 1,
                        'comments' => [
                            [
                                'user' => 'dewi_warga_setempat',
                                'text' => 'Tiap pagi aromanya luar biasa menyengat, lalat hijau berterbangan sampai ke warung makan sekitar.',
                            ],
                            [
                                'user' => 'satgas_pantau_lapangan',
                                'text' => 'Akan segera dikoordinasikan dengan petugas DLH dan kewilayahan untuk pengangkutan pagi serta pemasangan spanduk larangan buang sampah.',
                            ],
                        ],
                    ],
                    [
                        'category' => 'bencana_alam',
                        'title' => 'Banjir Limpasan Cileuncang Menggenangi Persimpangan Akibat Saluran Mampet',
                        'description' => 'Setiap kali hujan deras turun lebih dari 15 menit, air dari saluran drainase meluap dan menggenangi seluruh area simpang lima setinggi 30–40 cm. Arus lalu lintas lumpuh dan banyak sepeda motor mengalami mati mesin (water hammer).',
                        'short_label' => 'Genangan Banjir Cileuncang',
                        'accent_color' => '#0284c7',
                        'target_upvotes' => 112,
                        'target_downvotes' => 4,
                        'comments' => [
                            [
                                'user' => 'kang_ojol_simpangan',
                                'text' => 'Tiap hujan lebat pasti jadi kolam renang dadakan. Filter gorong-gorongnya mampet total ketutup sampah.',
                            ],
                            [
                                'user' => 'rina_pedestrian_bdg',
                                'text' => 'Pejalan kaki ga bisa lewat sama sekali, airnya kecokelatan bercampur comberan meluap ke trotoar.',
                            ],
                        ],
                    ],
                    [
                        'category' => 'fasilitas_umum',
                        'title' => 'Guiding Block Tunanetra Hancur & Trotoar Pejalan Kaki Ditimbun Sisa Material',
                        'description' => 'Trotoar pedestrian di persimpangan ini mengalami kerusakan berat; tegel pemandu tunanetra (guiding block) lepas berantakan dan sisa galian semen dibiarkan menggunung tanpa tanda peringatan, menghalangi mobilitas penyandang disabilitas dan pejalan kaki.',
                        'short_label' => 'Trotoar Disabilitas Rusak',
                        'accent_color' => '#8b5cf6',
                        'target_upvotes' => 45,
                        'target_downvotes' => 1,
                        'comments' => [
                            [
                                'user' => 'rina_pedestrian_bdg',
                                'text' => 'Hak pejalan kaki dan disabilitas terabaikan total di sini. Guiding block malah mengarah lurus ke tumpukan puing batu!',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'spot_name' => 'Perempatan Flyover Kiaracondong - Soekarno Hatta',
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Kiaracondong',
                'subdistrict' => 'Babakan Surabaya',
                'formatted_address' => 'Jl. Soekarno Hatta No. 580, Babakan Surabaya, Kec. Kiaracondong, Kota Bandung, Jawa Barat',
                'latitude' => -6.94278000,
                'longitude' => 107.64417000,
                'reports' => [
                    [
                        'category' => 'infrastruktur',
                        'title' => 'Aspal Merekah dan Bergelombang Ekstrem Membentuk Tanggul di Kolong Flyover',
                        'description' => 'Akibat sering dilalui truk tronton bermuatan melebihi tonase, aspal di persimpangan bawah flyover Kiaracondong terdorong hingga membentuk tonjolan gelombang setinggi 20 cm. Kendaraan sedan sering kandas dan pengendara motor mudah tergelincir.',
                        'short_label' => 'Aspal Bergelombang Tinggi',
                        'accent_color' => '#f97316',
                        'target_upvotes' => 125,
                        'target_downvotes' => 5,
                        'comments' => [
                            [
                                'user' => 'kang_ojol_simpangan',
                                'text' => 'Tanggul aspal ini parah banget, kalau malam ga kelihatan sering bikin pemotor lompat hampir jatuh.',
                            ],
                        ],
                    ],
                    [
                        'category' => 'kelistrikan',
                        'title' => 'Tiang Lampu Sorot Kolong Flyover Condong 30 Derajat Nyaris Roboh',
                        'description' => 'Baut angkur pondasi tiang lampu penerangan kolong jembatan terlepas sebagian setelah tersenggol bak kontainer. Tiang saat ini miring ke arah jalur kendaraan padat dan bergetar kencang setiap kali ada kendaraan berat melintas di flyover atas.',
                        'short_label' => 'Tiang PJU Condong Rawan Roboh',
                        'accent_color' => '#eab308',
                        'target_upvotes' => 96,
                        'target_downvotes' => 0,
                        'comments' => [
                            [
                                'user' => 'ahmad_pemerhati_utilitas',
                                'text' => 'Sangat membahayakan! Kalau ada angin kencang atau getaran truk kontainer bisa langsung roboh menimpa antrean lampu merah.',
                            ],
                        ],
                    ],
                    [
                        'category' => 'lingkungan',
                        'title' => 'Saluran Air Utama Tersumbat Limbah Oli Hitam Pekat dan Endapan Lumpur',
                        'description' => 'Saluran terbuka di sisi bawah jembatan tertutup lapisan oli hitam pekat dan lumpur tebal yang mengeluarkan gas berbau belerang menyengat. Tidak pernah ada pengerukan berkala selama musim kemarau maupun penghujan.',
                        'short_label' => 'Saluran Limbah Tersumbat Oli',
                        'accent_color' => '#16a34a',
                        'target_upvotes' => 58,
                        'target_downvotes' => 2,
                        'comments' => [
                            [
                                'user' => 'bambang_kritis_lapangan',
                                'text' => 'Limbah oli bengkel liar dibuang langsung ke selokan tanpa treatment. Mohon satpol PP dan DLH segera sidak.',
                            ],
                        ],
                    ],
                    [
                        'category' => 'kebakaran',
                        'title' => 'Aktivitas Pembakaran Sampah Kabel dan Ban Bekas di Bawah Kolong Jembatan',
                        'description' => 'Oknum pemulung liar rutin membakar tumpukan kabel dan ban bekas di kolong flyover setiap menjelang petang demi mengambil tembaga. Asap hitam tebal membubung masuk ke jalur lalu lintas dan mencekik pernapasan pengguna jalan.',
                        'short_label' => 'Pembakaran Liar Kolong Jembatan',
                        'accent_color' => '#ef4444',
                        'target_upvotes' => 89,
                        'target_downvotes' => 1,
                        'comments' => [
                            [
                                'user' => 'dewi_warga_setempat',
                                'text' => 'Asapnya pekat banget bikin mata perih dan sesak napas. Bahaya juga kalau apinya merambat ke tiang kabel utilitas!',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'spot_name' => 'Kawasan Pintu Keluar Stasiun Hall Bandung - Jl. Kebon Kawung',
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Cicendo',
                'subdistrict' => 'Pasirkaliki',
                'formatted_address' => 'Jl. Kebon Kawung No. 43, Pasirkaliki, Kec. Cicendo, Kota Bandung, Jawa Barat',
                'latitude' => -6.91444000,
                'longitude' => 107.60250000,
                'reports' => [
                    [
                        'category' => 'fasilitas_umum',
                        'title' => 'Shelter Halte Bus Rusak Parah Tanpa Atap dan Dikuasai Parkir Liar',
                        'description' => 'Halte penumpang umum di depan gerbang keluar stasiun mengalami kerusakan struktur: atap seng bolong, bangku tunggu patah, dan seluruh area pedestrian halte beralih menjadi lahan parkir liar motor serta lapak pangkalan tidak resmi.',
                        'short_label' => 'Halte Rusak Diserobot Parkir Liar',
                        'accent_color' => '#8b5cf6',
                        'target_upvotes' => 72,
                        'target_downvotes' => 3,
                        'comments' => [
                            [
                                'user' => 'rina_pedestrian_bdg',
                                'text' => 'Wisatawan baru turun dari kereta eksekutif langsung disambut halte hancur dan trotoar penuh motor parkir. Memalukan wajah kota.',
                            ],
                        ],
                    ],
                    [
                        'category' => 'infrastruktur',
                        'title' => 'Pelat Besi Grill Penutup Drainase Hilang di Akses Utama Masuk/Keluar Stasiun',
                        'description' => 'Tiga lembar grill besi penutup selokan jalan tepat di depan gate stasiun hilang dicuri orang. Saluran terbuka sedalam 80 cm kini menganga lebar tanpa penutup dan hanya dipasangi ranting kayu seadanya oleh warga sekitar.',
                        'short_label' => 'Grill Penutup Selokan Raib',
                        'accent_color' => '#f97316',
                        'target_upvotes' => 88,
                        'target_downvotes' => 0,
                        'comments' => [
                            [
                                'user' => 'kang_ojol_simpangan',
                                'text' => 'Minggu lalu ada taksi online ban kirinya kejeblos sampai as roda patah pas jemput penumpang subuh-subuh.',
                            ],
                        ],
                    ],
                    [
                        'category' => 'lingkungan',
                        'title' => 'Penumpukan Sampah Makanan Sisa di Sepanjang Koridor Pejalan Kaki Stasiun',
                        'description' => 'Ketiadaan tempat sampah terpilah yang memadai menyebabkan gelas plastik, sisa styrofoam makanan, dan puntung rokok berserakan di pot-pot tanaman hias trotoar stasiun hingga memicu serbuan tikus got pada malam hari.',
                        'short_label' => 'Sampah Koridor Stasiun',
                        'accent_color' => '#16a34a',
                        'target_upvotes' => 41,
                        'target_downvotes' => 2,
                        'comments' => [
                            [
                                'user' => 'dewi_warga_setempat',
                                'text' => 'Perlu penambahan tong sampah berkapasitas besar dan sanksi tegas buat yang buang sampah sembarangan di area gerbang stasiun.',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $totalReportsCreated = 0;

        foreach ($hotspots as $hotspot) {
            $this->command?->info("Menyuntikkan laporan multi-masalah di hotspot: {$hotspot['spot_name']} ({$hotspot['latitude']}, {$hotspot['longitude']})");

            foreach ($hotspot['reports'] as $reportDef) {
                // Pilih author secara acak dari persona
                $author = $voterUsers->random();

                // Generate visual SVG representation
                $imageSvg = $this->generateSvg(
                    $reportDef['category'],
                    $reportDef['short_label'],
                    $reportDef['accent_color'],
                    $hotspot['spot_name']
                );

                // Buat report dengan koordinat IDENTIK dengan hotspot
                $report = Report::create([
                    'user_id' => $author->id,
                    'title' => $reportDef['title'],
                    'category' => $reportDef['category'],
                    'description' => $reportDef['description']." [Lokasi Hotspot: {$hotspot['spot_name']}]",
                    'image_base64' => $imageSvg,
                    'latitude' => $hotspot['latitude'],
                    'longitude' => $hotspot['longitude'],
                    'geohash' => $this->quickGeohash($hotspot['latitude'], $hotspot['longitude']),
                    'province' => $hotspot['province'],
                    'city' => $hotspot['city'],
                    'district' => $hotspot['district'],
                    'subdistrict' => $hotspot['subdistrict'],
                    'formatted_address' => $hotspot['formatted_address'],
                    'osm_place_id' => (string) mt_rand(10000000, 99999999),
                    'rank_tier' => 'normal',
                    'upvotes_count' => 0,
                    'downvotes_count' => 0,
                    'vote_score' => 0,
                    'comments_count' => 0,
                    'status' => 'active',
                    'created_at' => $now->copy()->subHours(mt_rand(2, 72)),
                    'updated_at' => $now,
                ]);

                // Berikan Real Votes ke ReportVote agar sinkron dengan model
                $shuffledVoters = $allVoterIds;
                shuffle($shuffledVoters);

                $neededUpvotes = min($reportDef['target_upvotes'], count($shuffledVoters));
                $upvoterIds = array_slice($shuffledVoters, 0, $neededUpvotes);
                $remainingVoters = array_slice($shuffledVoters, $neededUpvotes);

                $neededDownvotes = min($reportDef['target_downvotes'], count($remainingVoters));
                $downvoterIds = array_slice($remainingVoters, 0, $neededDownvotes);

                $votesToInsert = [];
                foreach ($upvoterIds as $uId) {
                    $votesToInsert[] = [
                        'report_id' => $report->id,
                        'user_id' => $uId,
                        'value' => 1,
                        'created_at' => $report->created_at->copy()->addMinutes(mt_rand(1, 120)),
                        'updated_at' => $now,
                    ];
                }

                foreach ($downvoterIds as $dId) {
                    $votesToInsert[] = [
                        'report_id' => $report->id,
                        'user_id' => $dId,
                        'value' => -1,
                        'created_at' => $report->created_at->copy()->addMinutes(mt_rand(5, 120)),
                        'updated_at' => $now,
                    ];
                }

                if (! empty($votesToInsert)) {
                    ReportVote::insert($votesToInsert);
                }

                // Recalculate score & tier
                $report->recalculateVoteStatsAndTier();

                // Sisipkan komentar dialog warga
                $lastCommentTime = $report->created_at->copy();
                foreach ($reportDef['comments'] as $commDef) {
                    $cUser = User::where('username', $commDef['user'])->first() ?? $userAccount;
                    $lastCommentTime = $lastCommentTime->copy()->addMinutes(mt_rand(10, 45));

                    ReportComment::create([
                        'report_id' => $report->id,
                        'user_id' => $cUser->id,
                        'parent_id' => null,
                        'content' => $commDef['text'],
                        'created_at' => $lastCommentTime,
                        'updated_at' => $lastCommentTime,
                    ]);
                }

                // Tambahkan ringkasan dari @Sira Bot
                ReportComment::create([
                    'report_id' => $report->id,
                    'user_id' => $siraBot->id,
                    'parent_id' => null,
                    'content' => "**Ringkasan SIRA AI:**\n\n• **Lokasi Bersama:** Terdeteksi berada di hotspot yang sama dengan masalah publik lain ({$hotspot['spot_name']}).\n• **Masalah Terkait:** {$report->title}.\n• **Kategori:** {$report->category_label}.\n• **Status Aspirasi:** Masuk dalam tier **{$report->rank_tier}** dengan total **{$report->upvotes_count} dukungan warga**.\n• **Rekomendasi Penanganan Terpadu:** Disarankan penanganan lintas dinas terintegrasi karena lokasi ini memiliki akumulasi permasalahan multi-sektoral secara simultan.",
                    'created_at' => $lastCommentTime->copy()->addMinutes(2),
                    'updated_at' => $lastCommentTime->copy()->addMinutes(2),
                ]);

                // Update actual comments count
                $report->update(['comments_count' => ReportComment::where('report_id', $report->id)->count()]);

                $totalReportsCreated++;
            }
        }

        $this->command?->info("Sukses: Telah dibuat {$totalReportsCreated} laporan multi-masalah pada 3 titik hotspot koordinat identik.");
    }

    /**
     * Hitung geohash sederhana presisi 8 karakter.
     */
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

    /**
     * Buat visual SVG ringkas berkualitas tinggi untuk laporan co-located.
     */
    private function generateSvg(string $category, string $shortLabel, string $accentColor, string $hotspotName): string
    {
        $vectorArt = match ($category) {
            'infrastruktur' => '<path d="M100 350 L260 80 L340 80 L500 350" stroke="#374151" stroke-width="6" fill="none"/>
                <line x1="300" y1="80" x2="300" y2="160" stroke="#f59e0b" stroke-width="8" stroke-dasharray="16,14"/>
                <line x1="300" y1="220" x2="300" y2="350" stroke="#f59e0b" stroke-width="8" stroke-dasharray="16,14"/>
                <ellipse cx="300" cy="275" rx="90" ry="38" fill="#090a0f" stroke="'.$accentColor.'" stroke-width="5"/>
                <path d="M240 275 Q280 255 360 280" stroke="'.$accentColor.'" stroke-width="3" fill="none" opacity="0.8"/>',
            'kelistrikan' => '<line x1="300" y1="60" x2="300" y2="330" stroke="#475569" stroke-width="8"/>
                <path d="M260 100 Q300 50 340 100" stroke="#475569" stroke-width="6" fill="none"/>
                <circle cx="300" cy="115" r="22" fill="#1e293b" stroke="'.$accentColor.'" stroke-width="4" stroke-dasharray="6,4"/>
                <polygon points="275,130 325,130 380,330 220,330" fill="'.$accentColor.'" opacity="0.12"/>
                <circle cx="300" cy="330" r="40" fill="#0f172a" stroke="#334155" stroke-width="4"/>',
            'lingkungan' => '<polygon points="200,340 230,220 370,220 400,340" fill="#1c1917" stroke="#78716c" stroke-width="4"/>
                <path d="M190 220 L410 220 L380 190 L220 190 Z" fill="#292524" stroke="#a8a29e" stroke-width="3"/>
                <polygon points="240,320 270,250 330,250 360,320" fill="'.$accentColor.'" opacity="0.7"/>
                <circle cx="280" cy="200" r="16" fill="#ca8a04" opacity="0.8"/>
                <polygon points="310,230 345,180 370,230" fill="#b91c1c" opacity="0.8"/>',
            'bencana_alam' => '<rect x="0" y="240" width="600" height="160" fill="#0c4a6e" opacity="0.6"/>
                <path d="M0 260 Q75 230 150 260 T300 260 T450 260 T600 260 L600 400 L0 400 Z" fill="'.$accentColor.'" opacity="0.5"/>
                <path d="M0 290 Q75 270 150 290 T300 290 T450 290 T600 290" stroke="#38bdf8" stroke-width="6" fill="none"/>
                <rect x="230" y="120" width="140" height="90" rx="8" fill="#1e293b" stroke="#64748b" stroke-width="4"/>',
            'kebakaran' => '<path d="M300 80 Q350 180 310 230 Q370 200 360 280 Q350 340 300 350 Q250 340 240 280 Q230 200 290 230 Q250 180 300 80 Z" fill="'.$accentColor.'" opacity="0.85"/>
                <path d="M300 160 Q330 220 310 260 Q340 240 330 300 Q320 330 300 335 Q280 330 270 300 Q260 240 290 260 Q270 220 300 160 Z" fill="#fef08a"/>',
            default => '<polygon points="80,350 200,100 400,100 520,350" fill="#1e1e24" stroke="#3f3f46" stroke-width="4"/>
                <line x1="200" y1="180" x2="400" y2="180" stroke="#71717a" stroke-width="2"/>
                <line x1="160" y1="240" x2="440" y2="240" stroke="#71717a" stroke-width="3"/>
                <line x1="120" y1="300" x2="480" y2="300" stroke="#71717a" stroke-width="4"/>
                <circle cx="300" cy="240" r="30" fill="'.$accentColor.'" opacity="0.8"/>',
        };

        $escapedLabel = htmlspecialchars($shortLabel, ENT_QUOTES, 'UTF-8');
        $escapedCategory = htmlspecialchars(strtoupper($category), ENT_QUOTES, 'UTF-8');
        $escapedHotspot = htmlspecialchars($hotspotName, ENT_QUOTES, 'UTF-8');
        $pillWidth = strlen($escapedCategory) * 9 + 24;

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 400" width="100%" height="100%">
            <defs>
                <pattern id="grid" width="30" height="30" patternUnits="userSpaceOnUse">
                    <path d="M 30 0 L 0 0 0 30" fill="none" stroke="#ffffff" stroke-width="1" opacity="0.04"/>
                </pattern>
            </defs>
            <rect width="600" height="400" fill="#0f1117"/>
            <rect width="600" height="400" fill="url(#grid)"/>
            '.$vectorArt.'
            <rect x="25" y="25" width="'.$pillWidth.'" height="26" rx="5" fill="#18181b" stroke="'.$accentColor.'" stroke-width="1.5"/>
            <text x="37" y="42" fill="'.$accentColor.'" font-family="ui-monospace, monospace" font-size="11" font-weight="700" letter-spacing="1">'.$escapedCategory.'</text>
            <rect x="20" y="325" width="560" height="55" rx="8" fill="#09090b" opacity="0.92"/>
            <text x="300" y="348" fill="#f4f4f5" font-family="system-ui, -apple-system, sans-serif" font-size="14" font-weight="700" text-anchor="middle">'.$escapedLabel.'</text>
            <text x="300" y="367" fill="#94a3b8" font-family="system-ui, -apple-system, sans-serif" font-size="11" font-weight="500" text-anchor="middle">📍 '.$escapedHotspot.'</text>
        </svg>';

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
