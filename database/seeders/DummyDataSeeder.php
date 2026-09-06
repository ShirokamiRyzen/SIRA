<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\ReportComment;
use App\Models\ReportVote;
use App\Models\User;
use App\Notifications\ReportMentionNotification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    /**
     * Jalankan seeder 100 data laporan publik bahasa sehari-hari,
     * penuh dinamika perseteruan warga vs akun buzzer, sindir-menyindir,
     * respon formal instansi pemda, dan tanpa ada komentar kosong.
     */
    public function run(): void
    {
        $now = now();
        $defaultPassword = Hash::make('password123');

        // Bersihkan data laporan lama agar tepat 100 laporan
        ReportComment::query()->delete();
        ReportVote::query()->delete();
        Report::query()->delete();

        // -------------------------------------------------------------
        // 1. Akun Admin Pusat (admin / admin)
        // -------------------------------------------------------------
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator SIRA Pusat',
                'email' => 'admin@sira.local',
                'password' => Hash::make('admin'),
                'is_admin' => true,
                'is_verified' => true,
            ]
        );
        $admin->update(['is_admin' => true, 'is_verified' => true]);

        // -------------------------------------------------------------
        // 2. Akun Pengguna Biasa (user / user)
        // -------------------------------------------------------------
        $userAccount = User::firstOrCreate(
            ['username' => 'user'],
            [
                'name' => 'Pengguna Warga',
                'email' => 'user@sira.local',
                'password' => Hash::make('user'),
                'is_admin' => false,
                'is_verified' => false,
            ]
        );

        // -------------------------------------------------------------
        // 3. Akun Resmi Pemerintah Daerah (Verified Badge Biru)
        // -------------------------------------------------------------
        $pemdaJabar = User::firstOrCreate(
            ['username' => 'pemda_jabar'],
            [
                'name' => 'Pemerintah Provinsi Jawa Barat',
                'email' => 'pemda_jabar@sira.local',
                'password' => Hash::make('pemda_jabar'),
                'is_admin' => false,
                'is_verified' => true,
            ]
        );
        $pemdaJabar->update(['is_verified' => true]);

        $pemdaJateng = User::firstOrCreate(
            ['username' => 'pemda_jateng'],
            [
                'name' => 'Pemerintah Provinsi Jawa Tengah',
                'email' => 'pemda_jateng@sira.local',
                'password' => Hash::make('pemda_jateng'),
                'is_admin' => false,
                'is_verified' => true,
            ]
        );
        $pemdaJateng->update(['is_verified' => true]);

        $pemdaJatim = User::firstOrCreate(
            ['username' => 'pemda_jatim'],
            [
                'name' => 'Pemerintah Provinsi Jawa Timur',
                'email' => 'pemda_jatim@sira.local',
                'password' => Hash::make('pemda_jatim'),
                'is_admin' => false,
                'is_verified' => true,
            ]
        );
        $pemdaJatim->update(['is_verified' => true]);

        // -------------------------------------------------------------
        // 4. Akun AI Assistant SIRA
        // -------------------------------------------------------------
        $siraBot = User::firstOrCreate(
            ['username' => 'Sira'],
            [
                'name' => 'SIRA AI Assistant',
                'email' => 'ai@sira.local',
                'password' => Hash::make('sira-bot-secure-'.config('app.key')),
                'is_admin' => false,
                'is_verified' => true,
            ]
        );

        // -------------------------------------------------------------
        // 5. Akun Warga Lapangan (Ceplas-ceplos, Kritis, Sarkas)
        // -------------------------------------------------------------
        $citizenPersonas = [
            ['name' => 'Bambang Velg Peyang', 'username' => 'bambang_velg_peyang'],
            ['name' => 'Kang Ojol Keringat Dingin', 'username' => 'kang_ojol_sabar'],
            ['name' => 'Ibu RT Taat Pajak', 'username' => 'bu_rt_kritis'],
            ['name' => 'Dimas Korban Aspal', 'username' => 'dimas_shockbreaker'],
            ['name' => 'Pak Yanto Naik Beat', 'username' => 'pak_yanto_beat'],
            ['name' => 'Pejalan Kaki Nyawa Cadangan', 'username' => 'pejalan_kaki_terzalimi'],
            ['name' => 'Siti Anti Birokrasi', 'username' => 'siti_pembayar_pbb'],
            ['name' => 'Indra Shock Depan Mati', 'username' => 'indra_tambal_ban'],
            ['name' => 'Anisa Masker Tiga Lapis', 'username' => 'anisa_kolektor_debu'],
            ['name' => 'Eko Mancing di Jalan', 'username' => 'eko_kolam_lele'],
            ['name' => 'Fajar Pejuang Jalan Rusak', 'username' => 'fajar_warga_lapangan'],
            ['name' => 'Ratna Korban Proyek Gali', 'username' => 'ratna_jemuran_debu'],
            ['name' => 'Wahyu Suara Rakyat', 'username' => 'wahyu_anti_template'],
            ['name' => 'Gilang Pencari Keadilan', 'username' => 'gilang_suara_warga'],
            ['name' => 'Rina Spion Patah', 'username' => 'rina_korban_lobang'],
            ['name' => 'Dedi Warga Biasa', 'username' => 'dedi_warga_biasa'],
            ['name' => 'Teguh Suara Kebon', 'username' => 'teguh_komplain_keras'],
            ['name' => 'Bayu Anti Anggaran Gaib', 'username' => 'bayu_audit_rakyat'],
        ];

        $citizenUsers = collect();
        foreach ($citizenPersonas as $persona) {
            $u = User::firstOrCreate(
                ['username' => $persona['username']],
                [
                    'name' => $persona['name'],
                    'email' => $persona['username'].'@sira.local',
                    'password' => $defaultPassword,
                    'is_verified' => false,
                ]
            );
            $citizenUsers->push($u);
        }

        // -------------------------------------------------------------
        // 6. Akun Buzzer & Pembela Narasi Pemda (Suka menyalahkan warga / cuaca)
        // -------------------------------------------------------------
        $buzzerPersonas = [
            ['name' => 'Patroli Siber Positif', 'username' => 'patroli_narasi_positif'],
            ['name' => 'Kang Syukur Warga Bijak', 'username' => 'warga_tetap_bersyukur'],
            ['name' => 'Relawan Pembangunan Nyata', 'username' => 'relawan_gerak_cepat'],
            ['name' => 'Paham Alur Birokrasi', 'username' => 'pejuang_tahapan_apbd'],
            ['name' => 'Humas Sukarela Cyber', 'username' => 'pemberantas_nyinyir_net'],
            ['name' => 'Duta Proyek Mulus', 'username' => 'bela_kebijakan_pemda'],
        ];

        $buzzerUsers = collect();
        foreach ($buzzerPersonas as $buzzer) {
            $u = User::firstOrCreate(
                ['username' => $buzzer['username']],
                [
                    'name' => $buzzer['name'],
                    'email' => $buzzer['username'].'@sira.local',
                    'password' => $defaultPassword,
                    'is_verified' => false,
                ]
            );
            $buzzerUsers->push($u);
        }

        // Kumpulan pemilih untuk voting
        $voterUsers = collect([$userAccount])->merge($citizenUsers)->merge($buzzerUsers);
        for ($i = 1; $i <= 60; $i++) {
            $un = "warga_voter_{$i}";
            $u = User::firstOrCreate(
                ['username' => $un],
                [
                    'name' => "Warga Wilayah {$i}",
                    'email' => "{$un}@sira.local",
                    'password' => $defaultPassword,
                    'is_verified' => false,
                ]
            );
            $voterUsers->push($u);
        }

        // -------------------------------------------------------------
        // 7. Ambil 100 Dataset Laporan Alami Berdasarkan Wilayah
        // -------------------------------------------------------------
        $reportsData = $this->get100NaturalReports();

        $this->command?->info('Mempersiapkan 100 laporan bahasa sehari-hari, debat warga vs buzzer, dan respon pemda...');

        $createdCount = 0;

        foreach ($reportsData as $idx => $item) {
            $author = $citizenUsers->random();
            $tagPemda = $item['pemda_tag'];
            $pemdaUser = match ($tagPemda) {
                '@pemda_jabar' => $pemdaJabar,
                '@pemda_jateng' => $pemdaJateng,
                '@pemda_jatim' => $pemdaJatim,
                default => $pemdaJabar,
            };

            $geohash = $this->quickGeohash($item['lat'], $item['lng']);

            $imageBase64 = $this->generateReportSvg(
                $item['category'],
                $item['title'],
                $item['color'] ?? '#f97316'
            );

            $upvotes = mt_rand(18, 140);
            $downvotes = mt_rand(1, 6);
            $status = $item['status'] ?? 'active';

            $report = Report::create([
                'user_id' => $author->id,
                'title' => $item['title'],
                'category' => $item['category'],
                'description' => $item['description'],
                'image_base64' => $imageBase64,
                'latitude' => $item['lat'],
                'longitude' => $item['lng'],
                'geohash' => $geohash,
                'province' => $item['province'],
                'city' => $item['city'],
                'status' => $status,
                'upvotes_count' => $upvotes,
                'downvotes_count' => $downvotes,
                'created_at' => $now->copy()->subMinutes(mt_rand(25, 25000)),
                'updated_at' => $now,
            ]);

            // Vote records
            $sampledUsers = $voterUsers->where('id', '!=', $author->id)->random(min($upvotes, $voterUsers->count() - 1));
            $voteRows = [];
            foreach ($sampledUsers as $voter) {
                $voteRows[] = [
                    'report_id' => $report->id,
                    'user_id' => $voter->id,
                    'value' => 1,
                    'created_at' => $report->created_at->copy()->addMinutes(mt_rand(2, 60)),
                    'updated_at' => $report->created_at->copy()->addMinutes(mt_rand(2, 60)),
                ];
            }
            if (! empty($voteRows)) {
                ReportVote::insertOrIgnore($voteRows);
            }

            $report->recalculateVoteStatsAndTier();

            // Notifikasi ke Pemda karena di-tag
            $snippet = Str::limit(strip_tags($report->description), 120);
            $pemdaUser->notify(new ReportMentionNotification(
                $author->username,
                $author->name,
                $report->id,
                $report->title,
                $snippet
            ));

            // ---------------------------------------------------------
            // Buat Komentar Berisi Pertikaian Warga vs Buzzer & Respon Pemda
            // ---------------------------------------------------------
            $thread = $this->buildLivelyCommentThread(
                $item['category'],
                $tagPemda,
                $pemdaUser,
                $citizenUsers,
                $buzzerUsers,
                $author
            );

            $rootCommentModel = null;
            foreach ($thread as $cIndex => $cData) {
                $parentId = ($cIndex > 0 && $rootCommentModel) ? $rootCommentModel->id : null;

                $comment = ReportComment::create([
                    'report_id' => $report->id,
                    'user_id' => $cData['user_id'],
                    'parent_id' => $parentId,
                    'content' => $cData['content'],
                    'created_at' => $report->created_at->copy()->addMinutes(mt_rand(15, 240) + ($cIndex * 15)),
                ]);

                if ($cIndex === 0) {
                    $rootCommentModel = $comment;
                }
            }

            // Tambahkan SIRA AI pada 15% laporan
            if ($idx % 7 === 0) {
                $userAiAuthor = $citizenUsers->random();
                $userAiPrompt = ReportComment::create([
                    'report_id' => $report->id,
                    'user_id' => $userAiAuthor->id,
                    'parent_id' => null,
                    'content' => 'Tolong @Sira buatkan rangkuman dan poin inti dari keluhan di laporan ini.',
                    'created_at' => $report->created_at->copy()->addMinutes(300),
                ]);

                ReportComment::create([
                    'report_id' => $report->id,
                    'user_id' => $siraBot->id,
                    'parent_id' => $userAiPrompt->id,
                    'content' => "@{$userAiAuthor->username} **Ringkasan SIRA AI:**\n\n• **Keluhan Warga:** Terjadi masalah {$report->category} pada lokasi {$report->title} ({$report->city}).\n• **Dinamika Aspirasi:** Warga mengeluhkan dampak keselamatan dan kerugian kendaraan, sementara ada masukan agar penanganan segera diprioritaskan oleh {$tagPemda}.\n• **Rekomendasi:** Diperlukan tindak lanjut nyata dan inspeksi lapangan dari pihak terkait guna menjamin keselamatan bersama.",
                    'created_at' => $userAiPrompt->created_at->copy()->addMinutes(2),
                ]);
            }

            $actualComments = ReportComment::where('report_id', $report->id)->count();
            $report->update(['comments_count' => $actualComments]);

            $createdCount++;
        }

        $this->command?->info("Selesai! Berhasil merombak {$createdCount} data laporan natural dengan pertikaian warga vs buzzer dan respon pemda.");
    }

    /**
     * Membangun rangkaian komentar pertikaian warga vs buzzer vs instansi pemda.
     */
    private function buildLivelyCommentThread(
        string $category,
        string $tagPemda,
        User $pemdaUser,
        $citizenUsers,
        $buzzerUsers,
        User $author
    ): array {
        $citizenA = $citizenUsers->where('id', '!=', $author->id)->random();
        $citizenB = $citizenUsers->where('id', '!=', $author->id)->where('id', '!=', $citizenA->id)->random();
        $buzzer = $buzzerUsers->random();

        $pemdaGreeting = match ($tagPemda) {
            '@pemda_jabar' => 'Sampurasun wargi Jabar.',
            '@pemda_jateng' => 'Sugeng siang / ndalu sedulur Jateng.',
            '@pemda_jatim' => 'Halo rek warga Jawa Timur.',
            default => 'Halo warga.',
        };

        // Bank percakapan perseteruan berdasarkan kategori masalah
        $scenarios = [
            'infrastruktur' => [
                [
                    'c1' => 'Ini lubang aspal udah dari jaman kapan dibiarin aja! Tadi malem tetangga gue balik kerja ngehajar lubang ini langsung velg motornya peyang. Mau nunggu ada korban nyawa dulu baru kalian melek?',
                    'buzz' => 'Jangan cuma modal nyinyir di sosmed! Lu kira nambal aspal kayak beli gorengan di pinggir jalan tinggal tunjuk? Ada tahapan lelang anggaran di dinas, sabar dikit napa jangan provokatif mulu!',
                    'c2' => 'Nasi bungkus lu berapa rebu sih min belain aspal hancur begini? Warga bayar pajak kendaraan tiap tahun lancar, giliran nuntut aspal layak dibilang ga sabaran. Otak lu dipake dong!',
                    'pemda' => "{$pemdaGreeting} Laporan mengenai kerusakan jalan telah kami catat dengan nomor aduan #INF-".mt_rand(1000, 9999).'. Informasi ini telah diteruskan ke Dinas Bina Marga & Penataan Ruang untuk dijadwalkan penanganan teknis.',
                    'c3' => 'Nah kan keluar lagi jurus sakti template humas! Kemarin bilangnya dijadwalkan, sampe ganti tahun cuma diliat doang sama petugas yang dateng foto-foto sebentar.',
                ],
                [
                    'c1' => 'Gila ya, baru sebulan lalu diaspal tipis-tipis buat pencitraan kunjungan pejabat, sekarang udah ambles dan bolong-bolong lagi kayak rempeyek remuk.',
                    'buzz' => 'Itu karena kendaraan truk muatan yang lewat over tonase melebihi kapasitas jalan! Jangan salahin kualitas aspal kontraktornya terus, introspeksi juga pengusaha ekspedisi yang nakal!',
                    'c2' => 'Ya kalau tau truk over kapasitas, tugas jembatan timbang sama dishub ngapain aja malih? Molor? Jangan lempar tanggung jawab mulu.',
                    'pemda' => "{$pemdaGreeting} Terima kasih atas perhatiannya. Kami berkoordinasi dengan petugas pengawas lapangan untuk melakukan kaji ulang beban jalan dan perbaikan titik ambles.",
                ],
            ],
            'kelistrikan' => [
                [
                    'c1' => 'Gelap gulita kayak kuburan! Sepanjang jalan ini lampu PJU mati total udah tiga minggu. Tiap bayar token listrik kan ditarik Pajak Penerangan Jalan (PPJ), larinya kemana tuh duit kalau jalanan tetep remang-remang?',
                    'buzz' => 'Mungkin kabel bawah tanahnya ada yang putus kena galian utilitas lain atau kena petir. Petugas dinas juga manusia, punya antrean kerjaan se-provinsi. Positif thinking dikit kenapa sih.',
                    'c2' => 'Positif thinking pala lu! Kemarin lusa anak kuliahan dibegal persis di bawah tiang lampu yang mati ini! Kalau nunggu ada yang tewas baru kalian gerak ya?',
                    'pemda' => "{$pemdaGreeting} Aduan terkait penerangan jalan umum telah kami terima. Tim teknis pemeliharaan jaringan PJU segera meluncur ke lokasi untuk pengecekan gardu dan penggantian lampu.",
                    'c3' => 'Tolong cepetan ya min, jangan cuma janji manis doang. Warga sini udah was-was tiap mau pulang malam.',
                ],
            ],
            'lingkungan' => [
                [
                    'c1' => 'Aroma sedap menyengat menusuk hidung! Gunung sampah liar udah makan separuh jalan raya. Lalat ijo beterbangan masuk ke warung makan warga sekitar. Jorok dan memalukan!',
                    'buzz' => 'Yang buang sampah sembarangan di situ kan warga sendiri yang ga punya etika kebersihan! Giliran numpuk teriak-teriak nyalahin dinas lingkungan. Sadar diri dulu dong baru nuntut fasilitas!',
                    'c2' => 'Woi buzzer cerdas, warga sini udah bayar retribusi sampah tiap bulan! TPS resmi yang lama ditutup sepihak sama dinas tanpa ada gantinya, terus warga suruh makan sampahnya sendiri apa gimana?',
                    'pemda' => "{$pemdaGreeting} Laporan timbunan sampah liar telah diteruskan ke Dinas Lingkungan Hidup. Armada truk dan petugas kebersihan akan segera dijadwalkan untuk pengangkutan ke TPA.",
                ],
            ],
            'bencana_alam' => [
                [
                    'c1' => 'Hujan gerimis 20 menit doang langsung air meluap setinggi lutut masuk ke teras rumah! Gorong-gorong drainase kemarin ternyata ditutup semen sama ruko baru tapi didiemin sama satpol pp.',
                    'buzz' => 'Tolong dipahami ya, ini faktor anomali cuaca ekstrem hidrometeorologi! Di luar negeri kota metropolitan juga banjir kalau curah hujan lagi tinggi. Jangan dikit-dikit politisasi bencana!',
                    'c2' => 'Politisasi gundulmu bau kencur, ini jelas-jelas saluran airnya disemen dan mampet sisa proyek! Lu dapet jatah fee apa gimana sih getol banget belain pejabat lelet?',
                    'pemda' => "{$pemdaGreeting} Tim satgas penanggulangan banjir Dinas Sumber Daya Air telah disiagakan dengan pompa portabel untuk menyedot genangan dan memeriksa sumbatan saluran.",
                ],
            ],
            'fasilitas_umum' => [
                [
                    'c1' => 'Halte bus mewah anggaran milyaran tapi bangkunya pada copot dan atapnya bolong. Pejalan kaki kehujanan, yang nongkrong malah orang pacaran sama gerobak mangkal.',
                    'buzz' => 'Itu karena mental masyarakat kita yang belum siap fasilitas modern, sukanya ngerusak dan vandalisme! Pemerintah udah capek bangun bagus-bagus tapi ga dijaga.',
                    'c2' => 'Kalo tau ga dijaga ya dirawat dan dipasang cctv dong bambang! Masa habis potong pita langsung ditinggal mangkrak bertahun-tahun. Anggaran pemeliharaan kemana larinya?',
                    'pemda' => "{$pemdaGreeting} Terima kasih masukannya wargi. Data halte rusak sudah kami rekap untuk masuk agenda program rehabilitasi sarana dan prasarana transportasi publik.",
                ],
            ],
            'kebakaran' => [
                [
                    'c1' => 'Tolong pemadam merapat cepat! Semak belukar kering kebakar dari siang asapnya udah bikin sesak nafas warga satu RT. Takut nyamber ke kabel trafo atasnya!',
                    'buzz' => 'Warga jangan panik berlebihan sambil bikin narasi liar di medsos. Bantu padamkan pakai ember dulu sambil nunggu damkar yang pasti kejebak macet.',
                    'c2' => 'Matiin api lahan segede lapangan bola pake ember cucian lu kata sirkus? Jangan banyak bacot di medsos kalau lu cuma rebahan di kamar ber-AC!',
                    'pemda' => "{$pemdaGreeting} Laporan kebakaran telah diteruskan ke Pos Pemadam Kebakaran terdekat. Armada damkar sedang meluncur ke titik koordinat. Harap warga menjaga jarak aman.",
                ],
            ],
        ];

        $pool = $scenarios[$category] ?? $scenarios['infrastruktur'];
        $chosen = $pool[array_rand($pool)];

        $thread = [];

        // 1. Komentar pertama dari warga yang marah/kecewa
        $thread[] = [
            'user_id' => $citizenA->id,
            'content' => $chosen['c1'],
        ];

        // 2. Reply dari buzzer yang nyalahin warga / bela pemerintah (reply ke citizenA)
        $thread[] = [
            'user_id' => $buzzer->id,
            'content' => "@{$citizenA->username} ".$chosen['buzz'],
        ];

        // 3. Counter-attack dari warga kedua yang ngegas (reply ke buzzer)
        $thread[] = [
            'user_id' => $citizenB->id,
            'content' => "@{$buzzer->username} ".$chosen['c2'],
        ];

        // 4. Balasan resmi khas akun Pemda (reply ke citizenA)
        $thread[] = [
            'user_id' => $pemdaUser->id,
            'content' => "@{$citizenA->username} ".$chosen['pemda'],
        ];

        // 5. Opsi warga skeptis terhadap respon pemda (reply ke pemdaUser)
        if (isset($chosen['c3']) && mt_rand(1, 10) <= 7) {
            $thread[] = [
                'user_id' => $citizenA->id,
                'content' => "@{$pemdaUser->username} ".$chosen['c3'],
            ];
        }

        return $thread;
    }

    /**
     * 100 Data Laporan Natural dengan Bahasa Sehari-hari Warga Netizen.
     * Tidak kaku, tanpa emoji, ceplas-ceplos, dan tetap memuat tag pemda wajib.
     */
    private function get100NaturalReports(): array
    {
        $data = [];

        // -------------------------------------------------------------
        // JAWA BARAT (34 Laporan) - @pemda_jabar
        // -------------------------------------------------------------
        $jabarList = [
            [
                'title' => 'Lubang Aspal Kawah Meteor Dekat Flyover Kiaracondong',
                'category' => 'infrastruktur',
                'description' => 'Ini aspal di turunan flyover Kiaracondong bolongnya udah kayak kawah meteor, dalem banget ada 15 cm lebih. Kemarin malem ada anak sekolah ngehajar lobang ini sampe nyungsep stang motornya bengkok. Tolong ditambal woy @pemda_jabar, jangan nunggu ada nyawa melayang baru sibuk pasang spanduk belasungkawa!',
                'city' => 'Kota Bandung',
                'province' => 'Jawa Barat',
                'lat' => -6.9156,
                'lng' => 107.6441,
                'status' => 'active',
            ],
            [
                'title' => 'Lampu Jalan Padam Total Jalur Lingkar Bogor Bikin Was-Was Begal',
                'category' => 'kelistrikan',
                'description' => 'Lapor @pemda_jabar, jalan lingkar luar Bogor gelap gulita berminggu-minggu tanpa penerangan. Tiap malam orang yang pulang kerja shift malam was-was takut kena begal atau tabrak pembatas jalan. Tiang lampunya cuma jadi hiasan doang apa gimana nih?',
                'city' => 'Kota Bogor',
                'province' => 'Jawa Barat',
                'lat' => -6.5612,
                'lng' => 106.7789,
                'status' => 'active',
            ],
            [
                'title' => 'Gunung Sampah Liar Kalimalang Bau Busuk Bikin Enek',
                'category' => 'lingkungan',
                'description' => 'Bantaran Kali Kalimalang Bekasi Barat dipenuhi tumpukan kantong sampah liar yang udah membusuk dan berbelatung. Kalau siang baunya masuk ke jendela rumah warga. Tolong angkut dan pasang kawat berduri @pemda_jabar, jangan dicuekin mulu!',
                'city' => 'Kota Bekasi',
                'province' => 'Jawa Barat',
                'lat' => -6.2415,
                'lng' => 106.9924,
                'status' => 'in_progress',
            ],
            [
                'title' => 'Hujan Dikit Langsung Waterboom Cileuncang di Margonda',
                'category' => 'bencana_alam',
                'description' => 'Margonda Raya Depok kembali jadi wahana waterboom alami setiap kali mendung tebal. Air comberan meluap ke trotoar setinggi betis, motor pada mogok masal dorong berjamaah. Dinas terkait @pemda_jabar tolong dibongkar itu got mampetnya!',
                'city' => 'Kota Depok',
                'province' => 'Jawa Barat',
                'lat' => -6.3728,
                'lng' => 106.8319,
                'status' => 'active',
            ],
            [
                'title' => 'Kabel Optik Menjuntai Setinggi Leher di Kesambi Cirebon',
                'category' => 'kelistrikan',
                'description' => 'Ada bentangan kabel optik kendor turun hampir kena leher pengendara motor di Jalan Kesambi. Udah mirip jebakan batman di jalan raya. Mohon ditertibkan kabel liar ini @pemda_jabar sebelum ada pemotor yang lehernya terjerat!',
                'city' => 'Kota Cirebon',
                'province' => 'Jawa Barat',
                'lat' => -6.7265,
                'lng' => 108.5521,
                'status' => 'resolved',
            ],
            [
                'title' => 'Tutup Gorong-gorong Lenyap Diganti Ranting Pohon di Cimahi',
                'category' => 'infrastruktur',
                'description' => 'Tutup besi manhole di depan ruko Cimahi Tengah hilang dicuri orang entah kemana. Sebagai gantinya warga terpaksa tancepin ranting pohon kresek merah. Tolong @pemda_jabar pasang penutup beton permanen biar pejalan kaki ga nyungsep ke dalem selokan comberan.',
                'city' => 'Kota Cimahi',
                'province' => 'Jawa Barat',
                'lat' => -6.8723,
                'lng' => 107.5422,
                'status' => 'active',
            ],
            [
                'title' => 'Semak Belukar Kering Kebakar Dekat Pemukiman Cikole',
                'category' => 'kebakaran',
                'description' => 'Asap putih tebal membubung dari lahan ilalang kering di perbukitan Cikole Lembang. Angin kencang bikin apinya cepat merembet ke arah kebun warga. Tolong damkar @pemda_jabar segera luncurkan armada pemadam!',
                'city' => 'Kabupaten Bandung Barat',
                'province' => 'Jawa Barat',
                'lat' => -6.7821,
                'lng' => 107.6255,
                'status' => 'in_progress',
            ],
            [
                'title' => 'Jembatan Gantung Desa Goyang Parah dan Pondasi Retak di Sukabumi',
                'category' => 'infrastruktur',
                'description' => 'Jembatan gantung penghubung kampung di Cikembar Sukabumi pondasi semennya udah rontok kena arus sungai. Pas dilewati jembatan ngayun parah kayak mau putus. Tolong @pemda_jabar audit jembatan ini, anak-anak sekolah lewat sini tiap pagi taruhan nyawa.',
                'city' => 'Kabupaten Sukabumi',
                'province' => 'Jawa Barat',
                'lat' => -6.9812,
                'lng' => 106.8211,
                'status' => 'active',
            ],
            [
                'title' => 'Lampu Merah Error Nyala Kuning Semua di Asia Afrika',
                'category' => 'kelistrikan',
                'description' => 'Traffic light simpang Asia Afrika - Otista Bandung error kedip kuning doang dari jam 7 pagi. Mobil motor saling serobot ga ada yang mau ngalah sampai bikin macet mengular panjang. Mohon teknisi @pemda_jabar benerin modul kontrolnya.',
                'city' => 'Kota Bandung',
                'province' => 'Jawa Barat',
                'lat' => -6.9214,
                'lng' => 107.6075,
                'status' => 'resolved',
            ],
            [
                'title' => 'Atap Seng Halte Pantura Subang Terbang Ditiup Angin',
                'category' => 'fasilitas_umum',
                'description' => 'Kondisi halte bus di jalur Pantura Pamanukan Subang mengenaskan banget. Atap sengnya copot beterbangan waktu badai kemarin, besi penyangga karatan. Tolong renovasi @pemda_jabar biar warga yang nunggu bus antar kota ga kepanasan.',
                'city' => 'Kabupaten Subang',
                'province' => 'Jawa Barat',
                'lat' => -6.2815,
                'lng' => 107.8105,
                'status' => 'active',
            ],
            [
                'title' => 'Tanah Longsor Tebing Nutup Separuh Jalur Puncak Pass',
                'category' => 'bencana_alam',
                'description' => 'Tebing tanah longsor menimpa badan jalan di kawasan Puncak Pass Cianjur sehabis diguyur hujan badai. Lumpur tebal bikin aspal luar biasa licin, jalanan diberlakukan buka tutup. Butuh alat berat loader @pemda_jabar buat beresin material tanah.',
                'city' => 'Kabupaten Cianjur',
                'province' => 'Jawa Barat',
                'lat' => -6.7022,
                'lng' => 106.9912,
                'status' => 'in_progress',
            ],
            [
                'title' => 'Saluran Irigasi Sawah Karawang Berbusa Hitam Bau Kimia',
                'category' => 'lingkungan',
                'description' => 'Air irigasi sawah warga Karawang warnanya berubah hitam pekat dan berbusa bau bahan kimia menyengat. Padi warga terancam puso mati menguning. Tolong razia pabrik yang buang limbah siluman ini @pemda_jabar, jangan tutup mata!',
                'city' => 'Kabupaten Karawang',
                'province' => 'Jawa Barat',
                'lat' => -6.3015,
                'lng' => 107.3012,
                'status' => 'active',
            ],
            [
                'title' => 'Rambu Blind Spot Tikungan Maut Garut Ilang Ketutup Ranting',
                'category' => 'fasilitas_umum',
                'description' => 'Rambu peringatan jurang dan jalur penyelamat di turunan Cangar Garut ketutup rimbun semak belukar liar. Sopir truk luar kota yang ga hafal medan sering tekor rem. Mohon pemeliharaan @pemda_jabar potong itu ranting pohon.',
                'city' => 'Kabupaten Garut',
                'province' => 'Jawa Barat',
                'lat' => -7.2144,
                'lng' => 107.9011,
                'status' => 'active',
            ],
            [
                'title' => 'Percikan Api Meletup di Tiang Trafo Depan Pasar Cicalengka',
                'category' => 'kelistrikan',
                'description' => 'Kabel sambungan tiang trafo PLN depan pasar Cicalengka meletup-letup keluar kembang api kecil dari tadi siang. Pedagang pasar panik takut meledak merembet ke kios terpal. Tolong koordinasi @pemda_jabar ke PLN panggil petugas darurat.',
                'city' => 'Kabupaten Bandung',
                'province' => 'Jawa Barat',
                'lat' => -6.9814,
                'lng' => 107.8341,
                'status' => 'resolved',
            ],
            [
                'title' => 'Aspal Bergelombang Mirip Ombak Pantai di Tasik-Ciamis',
                'category' => 'infrastruktur',
                'description' => 'Jalur provinsi Tasikmalaya menuju Ciamis aspalnya bergelombang ekstrem gara-gara truk tambang pasir over tonase. Mobil ceper pasti mentok gasruk bawahnya. Tolong gelar razia jembatan timbang dan aspal ulang @pemda_jabar.',
                'city' => 'Kota Tasikmalaya',
                'province' => 'Jawa Barat',
                'lat' => -7.3274,
                'lng' => 108.2201,
                'status' => 'active',
            ],
            [
                'title' => 'Pohon Trembesi Raksasa Tumbang Melintang di Sumedang',
                'category' => 'bencana_alam',
                'description' => 'Pohon tua pinggir jalan tumbang menutup total akses dua arah di Sumedang Selatan. Kendaraan roda empat macet total ga bisa gerak. Warga butuh bantuan senso gergaji mesin BPBD @pemda_jabar buat potong batang kayu besar.',
                'city' => 'Kabupaten Sumedang',
                'province' => 'Jawa Barat',
                'lat' => -6.8589,
                'lng' => 107.9278,
                'status' => 'resolved',
            ],
            [
                'title' => 'Asap Bakaran Jerami Sawah Tol Cipali KM 115 Halangi Pandangan',
                'category' => 'lingkungan',
                'description' => 'Warga petani bakar jerami di pinggir jalan tol Cipali KM 115 Majalengka bikin kabut asap tebal pekat. Jarak pandang ga sampai 5 meter, bahaya banget memicu tabrakan beruntun. Mohon ditindak tegas pemilik lahannya @pemda_jabar!',
                'city' => 'Kabupaten Majalengka',
                'province' => 'Jawa Barat',
                'lat' => -6.7412,
                'lng' => 108.2014,
                'status' => 'active',
            ],
            [
                'title' => 'Guiding Block Tunanetra Terkelupas Rusak di Stasiun Purwakarta',
                'category' => 'fasilitas_umum',
                'description' => 'Jalur pemandu kuning tunanetra di trotoar luar stasiun Purwakarta banyak yang lepas copot dan berlubang. Bukannya ngebantu malah bikin penyandang disabilitas kesandung jatuh. Tolong perbaiki ubinnya @pemda_jabar.',
                'city' => 'Kabupaten Purwakarta',
                'province' => 'Jawa Barat',
                'lat' => -6.5541,
                'lng' => 107.4422,
                'status' => 'active',
            ],
            [
                'title' => 'Semburan Air Pipa PDAM Bocor Bikin Aspal Ciumbuleuit Licin',
                'category' => 'infrastruktur',
                'description' => 'Pipa transmisi air PDAM di tanjakan Ciumbuleuit Bandung pecah, air nyembur kayak air mancur ke tengah jalan. Aspal jadi licin banyak pemotor tergelincir. Tolong tutup katupnya dan tambal pipa bocor ini @pemda_jabar.',
                'city' => 'Kota Bandung',
                'province' => 'Jawa Barat',
                'lat' => -6.8789,
                'lng' => 107.6056,
                'status' => 'resolved',
            ],
            [
                'title' => 'Gazebo Taman Pandapa Kuningan Rusak Penuh Vandalisme Corat-Coret',
                'category' => 'fasilitas_umum',
                'description' => 'Fasilitas umum di Taman Kota Kuningan memprihatinkan, bangku semen patah, lampu hias dipecahin anak nongkrong, dinding penuh coretan pilox kasar. Tolong satpol pp @pemda_jabar razia malam dan cat ulang fasilitasnya.',
                'city' => 'Kabupaten Kuningan',
                'province' => 'Jawa Barat',
                'lat' => -6.9781,
                'lng' => 108.4844,
                'status' => 'active',
            ],
            // Tambahan 14 Laporan Jabar
            ['title' => 'Tebing Penahan Tanah Ambles di Tikungan Cisarua', 'category' => 'infrastruktur', 'description' => 'Dinding penahan tebing jalan Cisarua Bogor retak menganga dan miring ke arah jurang. Warga was-was jalan amblas kebawa longsor. Tolong tinjau @pemda_jabar.', 'city' => 'Kabupaten Bogor', 'province' => 'Jawa Barat', 'lat' => -6.6912, 'lng' => 106.9321, 'status' => 'active'],
            ['title' => 'Kabel Trafo Terbakar di Pintu Masuk Kawasan MM2100', 'category' => 'kebakaran', 'description' => 'Gardu listrik MM2100 Cikarang meletup keras mengeluarkan api dan asap hitam. Listrik mati sebagian area industri. Mohon bantuan koordinasi @pemda_jabar.', 'city' => 'Kabupaten Bekasi', 'province' => 'Jawa Barat', 'lat' => -6.3102, 'lng' => 107.0988, 'status' => 'resolved'],
            ['title' => 'Banjir Luapan Citarum Rendam Jalan Utama Dayeuhkolot', 'category' => 'bencana_alam', 'description' => 'Banjir tahunan kembali terulang di Dayeuhkolot setinggi paha orang dewasa. Akses jalan putus total cuma delman sama perahu yang bisa jalan. Tolong pompa penyedotnya @pemda_jabar.', 'city' => 'Kabupaten Bandung', 'province' => 'Jawa Barat', 'lat' => -6.9856, 'lng' => 107.6212, 'status' => 'active'],
            ['title' => 'Pembuangan Limbah Medis Jarum Suntik Liar di Tambun', 'category' => 'lingkungan', 'description' => 'Gila bener ada yang buang limbah medis plastik jarum suntik sama kantong darah di semak kosong Tambun Bekasi. Sangat berbahaya menular penyakit! Tolong diusut pelakunya @pemda_jabar.', 'city' => 'Kabupaten Bekasi', 'province' => 'Jawa Barat', 'lat' => -6.2612, 'lng' => 107.0654, 'status' => 'active'],
            ['title' => 'Lampu PJU Padam di Tanjakan Maut Emen Subang', 'category' => 'kelistrikan', 'description' => 'Tanjakan Emen jalur wisata Ciater gelap gulita ga ada lampu jalan yang hidup. Padahal tanjakannya curam dan banyak tikungan patah. Lapor @pemda_jabar segera ganti lampunya.', 'city' => 'Kabupaten Subang', 'province' => 'Jawa Barat', 'lat' => -6.7412, 'lng' => 107.6412, 'status' => 'active'],
            ['title' => 'Pelat Sambungan Jembatan Ciamis-Banjar Lepas', 'category' => 'infrastruktur', 'description' => 'Besi pelat expansion joint jembatan perbatasan Ciamis lepas timbul bunyi jedug keras tiap dilewati mobil. Kalau dibiarin bisa bikin ban meledak. Mohon dilas lagi @pemda_jabar.', 'city' => 'Kabupaten Ciamis', 'province' => 'Jawa Barat', 'lat' => -7.3312, 'lng' => 108.3512, 'status' => 'active'],
            ['title' => 'Pohon Pinus Dago Atas Tumbang Timpa Jaringan Kabel', 'category' => 'bencana_alam', 'description' => 'Hujan angin di Dago Atas menumbangkan pohon pinus besar menimpa kabel optik sampai tiang listriknya doyong 30 derajat. Bahaya roboh ke jalan raya, tolong evakuasi @pemda_jabar.', 'city' => 'Kota Bandung', 'province' => 'Jawa Barat', 'lat' => -6.8612, 'lng' => 107.6189, 'status' => 'resolved'],
            ['title' => 'Debu Hitam Pembakaran Pabrik Kapur Masuk Rumah Warga Sukabumi', 'category' => 'lingkungan', 'description' => 'Asap pekat dari cerobong pabrik kapur tanpa filter turun ke atap dan jemuran warga. Anak-anak kecil pada batuk sesak nafas. Tolong dinas lingkungan @pemda_jabar tindak tegas pabriknya!', 'city' => 'Kota Sukabumi', 'province' => 'Jawa Barat', 'lat' => -6.9244, 'lng' => 106.9288, 'status' => 'active'],
            ['title' => 'Tiang Listrik Beton Retak Dihantam Truk di Cianjur', 'category' => 'kelistrikan', 'description' => 'Tiang beton PLN retak parah di bagian bawah setelah ditabrak truk tronton mundur di Cianjur. Kabel di atasnya ketarik tegang. Tolong diganti tiang baru @pemda_jabar sebelum roboh.', 'city' => 'Kabupaten Cianjur', 'province' => 'Jawa Barat', 'lat' => -6.8212, 'lng' => 107.1412, 'status' => 'resolved'],
            ['title' => 'Papan Penunjuk Arah Keluar Tol Baros Cimahi Roboh', 'category' => 'fasilitas_umum', 'description' => 'Tiang rambu penunjuk arah roboh menimpa trotoar jalan keluar tol Baros. Bikin pengendara luar kota kebingungan ambil lajur. Tolong pasang tiang baru @pemda_jabar.', 'city' => 'Kota Cimahi', 'province' => 'Jawa Barat', 'lat' => -6.8912, 'lng' => 107.5341, 'status' => 'active'],
            ['title' => 'Genangan Air Comberan Depan Sekolah Sawangan Depok', 'category' => 'infrastruktur', 'description' => 'Air comberan mampet menggenangi gerbang sekolah dasar di Sawangan Depok. Anak-anak terpaksa buka sepatu loncat-loncatan di atas batu bata. Tolong sedot comberannya @pemda_jabar.', 'city' => 'Kota Depok', 'province' => 'Jawa Barat', 'lat' => -6.4012, 'lng' => 106.7712, 'status' => 'active'],
            ['title' => 'Kebakaran Lahan Kosong Dekat Gardu Induk Cirebon', 'category' => 'kebakaran', 'description' => 'Kobaran api lahan rumput alang-alang mendekat ke pagar kawat gardu induk PLN Cirebon Barat. Tolong damkar meluncur @pemda_jabar, kalau trafo gardu kena bisa mati listrik sekota!', 'city' => 'Kabupaten Cirebon', 'province' => 'Jawa Barat', 'lat' => -6.7112, 'lng' => 108.5112, 'status' => 'resolved'],
            ['title' => 'Guardrail Besi Pembatas Jalan Tol Jagorawi Menganga Tajam', 'category' => 'fasilitas_umum', 'description' => 'Besi pembatas guardrail di tikungan KM 42 tol arah Ciawi patah dan ujungnya mencuat ke arah badan jalan. Sangat berbahaya kalau ada mobil tersenggol bisa robek bodinya. Lapor @pemda_jabar.', 'city' => 'Kota Bogor', 'province' => 'Jawa Barat', 'lat' => -6.5912, 'lng' => 106.8112, 'status' => 'active'],
            ['title' => 'Aspal Ambles di Perlintasan Rel Kereta Cicalengka', 'category' => 'infrastruktur', 'description' => 'Karet peredam perlintasan sebidang kereta Cicalengka hancur, aspalnya bolong dalem bikin ban motor sering nyelip di rel. Tolong perbaiki bersama PT KAI @pemda_jabar.', 'city' => 'Kabupaten Bandung', 'province' => 'Jawa Barat', 'lat' => -6.9789, 'lng' => 107.8412, 'status' => 'in_progress'],
        ];

        foreach ($jabarList as $item) {
            $item['pemda_tag'] = '@pemda_jabar';
            $data[] = $item;
        }

        // -------------------------------------------------------------
        // JAWA TENGAH (33 Laporan) - @pemda_jateng
        // -------------------------------------------------------------
        $jatengList = [
            [
                'title' => 'Banjir Rob Kaligawe Bikin Mesin Motor Ratusan Buruh Ambrol',
                'category' => 'bencana_alam',
                'description' => 'Jalur Pantura Kaligawe Semarang tenggelam lagi kena banjir rob setinggi 45 cm. Air asin bikin knalpot motor berkarat dan mesin pada mati mogok. Truk-truk besar jalan pelan bikin macet belasan kilo. Mana janji pompa air anti rob kalian @pemda_jateng?',
                'city' => 'Kota Semarang',
                'province' => 'Jawa Tengah',
                'lat' => -6.9589,
                'lng' => 110.4512,
                'status' => 'active',
            ],
            [
                'title' => 'Lubang Aspal Jebakan Batman di Jalan Slamet Riyadi Solo',
                'category' => 'infrastruktur',
                'description' => 'Jalan protokol kebanggaan wong Solo Slamet Riyadi dekat simpang Nonongan aspalnya retak pecah dan ada lubang tajam. Pesepeda sama pengendara motor matic sering oleng kaget. Tolong segera ditambal mulus @pemda_jateng, malu dilihat turis luar kota!',
                'city' => 'Kota Surakarta',
                'province' => 'Jawa Tengah',
                'lat' => -7.5698,
                'lng' => 110.8244,
                'status' => 'resolved',
            ],
            [
                'title' => 'Penerangan Jalan Muntilan Magelang Mati Berhari-hari',
                'category' => 'kelistrikan',
                'description' => 'Lapor @pemda_jateng, jalur utama Magelang menuju perbatasan DIY gelap total lampu PJU ga nyala. Mana jalurnya ramai dilewati truk pasir merapi yang kencang-kencang. Jangan nunggu ada kecelakaan maut baru sibuk ganti trafo!',
                'city' => 'Kabupaten Magelang',
                'province' => 'Jawa Tengah',
                'lat' => -7.5812,
                'lng' => 110.2912,
                'status' => 'active',
            ],
            [
                'title' => 'Tumpukan Sampah Bau Meluap di Bahu Jalan Pasar Kliwon Kudus',
                'category' => 'lingkungan',
                'description' => 'Kontainer penampungan sampah di Pasar Kliwon Kudus udah luber berceceran sampai ke trotoar jalan raya. Baunya busuk menyengat dan air lindi hitam menggenangi jalan. Tolong kirim truk sampah ekstra @pemda_jateng!',
                'city' => 'Kabupaten Kudus',
                'province' => 'Jawa Tengah',
                'lat' => -6.8122,
                'lng' => 110.8412,
                'status' => 'in_progress',
            ],
            [
                'title' => 'Tanggul Tanah Kali Bodri Kendal Rontok Kena Arus Deras',
                'category' => 'bencana_alam',
                'description' => 'Tanggul pembatas Sungai Bodri Kendal terkikis longsor sepanjang 15 meter setelah hujan deras di hulu. Warga dusun sekitar was-was kalau debit air naik lagi tanggul jebol menerjang kampung. Butuh bronjong kawat darurat @pemda_jateng!',
                'city' => 'Kabupaten Kendal',
                'province' => 'Jawa Tengah',
                'lat' => -6.9214,
                'lng' => 110.2014,
                'status' => 'active',
            ],
            [
                'title' => 'Lampu Merah Simpang Lima Purwokerto Mati Total Bikin Macet Semrawut',
                'category' => 'kelistrikan',
                'description' => 'Traffic light persimpangan padat Purwokerto padam total ga ada petugas yang ngatur di jam pulang kerja. Motor mobil angkot saling serobot klakson bersahut-sahutan bikin emosi warga memuncak. Tolong teknisi dishub @pemda_jateng gerak cepat!',
                'city' => 'Kabupaten Banyumas',
                'province' => 'Jawa Tengah',
                'lat' => -7.4244,
                'lng' => 109.2312,
                'status' => 'resolved',
            ],
            [
                'title' => 'Papan Kayu Jembatan Lereng Gunung Slamet Lapuk Berlubang',
                'category' => 'infrastruktur',
                'description' => 'Jembatan kayu penghubung desa di Purbalingga papannya udah pada patah dan paku karatan mencuat. Mobil pick up sayur ga berani lewat takut jeblos masuk sungai. Tolong bangun jembatan beton permanen @pemda_jateng.',
                'city' => 'Kabupaten Purbalingga',
                'province' => 'Jawa Tengah',
                'lat' => -7.3891,
                'lng' => 109.3612,
                'status' => 'active',
            ],
            [
                'title' => 'Kebakaran TPA Putri Cempo Asap Tebal Masuk Pemukiman Mojosongo',
                'category' => 'kebakaran',
                'description' => 'Tumpukan sampah plastik di zona barat TPA Putri Cempo Solo kembali terbakar keluar kepulan asap putih pekat. Warga Mojosongo sampai Jebres sesak nafas matanya perih. Tolong damkar dan water cannon @pemda_jateng siram titik api gas metana!',
                'city' => 'Kota Surakarta',
                'province' => 'Jawa Tengah',
                'lat' => -7.5312,
                'lng' => 110.8512,
                'status' => 'in_progress',
            ],
            [
                'title' => 'Paving Trotoar Simpang Tujuh Kudus Hancur Berantakan',
                'category' => 'infrastruktur',
                'description' => 'Keramik paving trotoar pejalan kaki dekat Alun-alun Simpang Tujuh Kudus ambles dan pecah-pecah. Banyak ibu-ibu bawa stroller anak kesusahan lewat. Tolong perbaiki pavingnya @pemda_jateng.',
                'city' => 'Kabupaten Kudus',
                'province' => 'Jawa Tengah',
                'lat' => -6.8056,
                'lng' => 110.8402,
                'status' => 'active',
            ],
            [
                'title' => 'Kabel Listrik Ketimpa Dahan Pohon Nggantung Rendah di Salatiga',
                'category' => 'kelistrikan',
                'description' => 'Kabel listrik tegangan menengah di Jl. Diponegoro Salatiga tertimpa dahan pohon rindang sampai menggelantung cuma 2 meter di atas jalan. Takut nyamber atap truk boks. Lapor @pemda_jateng tolong pangkas dahannya.',
                'city' => 'Kota Salatiga',
                'province' => 'Jawa Tengah',
                'lat' => -7.3312,
                'lng' => 110.5012,
                'status' => 'resolved',
            ],
            // 23 Laporan Tambahan Jateng
            ['title' => 'Bahu Jalan Longsor Masuk Jurang di Jalur Tawangmangu', 'category' => 'infrastruktur', 'description' => 'Separuh badan jalan aspal amblas ke jurang di jalur wisata Tawangmangu Karanganyar. Cuma dipasang tali rafia doang sebagai pengaman. Bahaya sekali tolong tangani @pemda_jateng.', 'city' => 'Kabupaten Karanganyar', 'province' => 'Jawa Tengah', 'lat' => -7.6612, 'lng' => 111.1312, 'status' => 'active'],
            ['title' => 'Limbah Ciu Bau Alkohol Busuk Cemari Bengawan Solo', 'category' => 'lingkungan', 'description' => 'Air Sungai Bengawan Solo wilayah Sukoharjo menghitam pekat dan berbau alkohol ciu menyengat. Ikan-ikan mabuk pada ngambang mati. Tolong tindak pabriknya @pemda_jateng!', 'city' => 'Kabupaten Sukoharjo', 'province' => 'Jawa Tengah', 'lat' => -7.6812, 'lng' => 110.8412, 'status' => 'active'],
            ['title' => 'Kaca Panel Halte BRT Trans Jateng Bawen Pecah Berhamburan', 'category' => 'fasilitas_umum', 'description' => 'Kaca pelindung halte bus Trans Jateng di Bawen dipecahin oknum vandalisme, serpihan kaca tajam berceceran di lantai tunggu. Tolong bersihkan dan pasang baru @pemda_jateng.', 'city' => 'Kabupaten Semarang', 'province' => 'Jawa Tengah', 'lat' => -7.2512, 'lng' => 110.4212, 'status' => 'active'],
            ['title' => 'Jalan Pantura Batang-Pekalongan Berlubang Bikin Velg Truk Pecah', 'category' => 'infrastruktur', 'description' => 'Jalan Pantura Batang hancur berlubang banyak jebakan air waktu hujan. Truk kontainer sering oleng dan motor banyak jatuh. Tolong tambal aspal hotmix @pemda_jateng.', 'city' => 'Kabupaten Batang', 'province' => 'Jawa Tengah', 'lat' => -6.9112, 'lng' => 109.7312, 'status' => 'in_progress'],
            ['title' => 'Sawah Siap Panen di Kebumen Terendam Luapan Kali Ijo', 'category' => 'bencana_alam', 'description' => 'Ratusan hektar tanaman padi siap panen terendam banjir luapan sungai Kali Ijo. Petani terancam gagal panen total rugi puluhan juta. Butuh pompa buang @pemda_jateng.', 'city' => 'Kabupaten Kebumen', 'province' => 'Jawa Tengah', 'lat' => -7.6712, 'lng' => 109.6512, 'status' => 'active'],
            ['title' => 'Flyover Palur Karanganyar Gelap Gulita Tanpa PJU', 'category' => 'kelistrikan', 'description' => 'Jembatan layang flyover Palur lampunya mati total dari ujung ke ujung. Jalur cepat kendaraan melesat kencang rawan senggolan di malam hari. Tolong hidupkan lampunya @pemda_jateng.', 'city' => 'Kabupaten Karanganyar', 'province' => 'Jawa Tengah', 'lat' => -7.5612, 'lng' => 110.8712, 'status' => 'resolved'],
            ['title' => 'Sampah Bambu Rumpun Sumbat Pintu Air Saluran Klaten', 'category' => 'lingkungan', 'description' => 'Rumpun bambu tumbang menyumbat pintu air irigasi di Klaten bikin air meluap ke pemukiman warga. Butuh alat berat buat ngangkat batang bambu @pemda_jateng.', 'city' => 'Kabupaten Klaten', 'province' => 'Jawa Tengah', 'lat' => -7.7012, 'lng' => 110.6012, 'status' => 'active'],
            ['title' => 'Pohon Jati Tumbang ke Arah Rel Kereta Kroya Cilacap', 'category' => 'bencana_alam', 'description' => 'Pohon jati tumbang melintang menimpa jaringan sinyal dan mendekati bantalan rel kereta api dekat stasiun Kroya. Tolong evakuasi darurat @pemda_jateng.', 'city' => 'Kabupaten Cilacap', 'province' => 'Jawa Tengah', 'lat' => -7.6312, 'lng' => 109.2512, 'status' => 'resolved'],
            ['title' => 'Rambu Mata Kucing Turunan Bayeman Purbalingga Ga Nyala', 'category' => 'fasilitas_umum', 'description' => 'Jalur tengkorak turunan Bayeman marka scotchlite mata kucingnya udah pada lepas dan lampu kedip kuning mati. Sopir ga bisa liat tikungan jurang waktu malam. Lapor @pemda_jateng.', 'city' => 'Kabupaten Purbalingga', 'province' => 'Jawa Tengah', 'lat' => -7.2912, 'lng' => 109.3112, 'status' => 'active'],
            ['title' => 'Kebakaran Hutan Rakyat Lereng Gunung Merbabu Selo Boyolali', 'category' => 'kebakaran', 'description' => 'Api lahan rumput membakar lereng perbukitan Selo Boyolali merembet cepat ditiup angin kencang gunung. Butuh tambahan selang pemadam dan relawan @pemda_jateng.', 'city' => 'Kabupaten Boyolali', 'province' => 'Jawa Tengah', 'lat' => -7.5112, 'lng' => 110.4612, 'status' => 'in_progress'],
            ['title' => 'Aspal Ambles Melingkar Akibat Pipa Bocor di Kota Tegal', 'category' => 'infrastruktur', 'description' => 'Di Jalan Martoloyo Tegal ada aspal ambles melingkar diameter 2 meter akibat tanah di bawahnya tergerus semburan pipa bocor. Bahaya jeblos tolong atensi @pemda_jateng.', 'city' => 'Kota Tegal', 'province' => 'Jawa Tengah', 'lat' => -6.8612, 'lng' => 109.1312, 'status' => 'active'],
            ['title' => 'Selokan Pekalongan Berwarna Ungu Pekat Bau Kimia Obat Batik', 'category' => 'lingkungan', 'description' => 'Air comberan parit pemukiman warga berwarna ungu gelap dan berbau obat pewarna tekstil menyengat. Sumur warga takut tercemar. Tolong tertibkan limbah liar @pemda_jateng.', 'city' => 'Kota Pekalongan', 'province' => 'Jawa Tengah', 'lat' => -6.8912, 'lng' => 109.6712, 'status' => 'active'],
            ['title' => 'Tiang Besi Listrik Keropos Karatan Diterpa Angin Laut Jepara', 'category' => 'kelistrikan', 'description' => 'Tiang listrik di pinggir pantai Jepara pangkal bawahnya udah bolong karatan kena uap garam air laut. Goyang ditiup angin kencang. Tolong ganti tiang beton @pemda_jateng.', 'city' => 'Kabupaten Jepara', 'province' => 'Jawa Tengah', 'lat' => -6.5812, 'lng' => 110.6612, 'status' => 'active'],
            ['title' => 'Oprit Jembatan Antar Kecamatan Wonogiri Anjlok 30 CM', 'category' => 'infrastruktur', 'description' => 'Sambungan aspal naik ke jembatan anjlok sedalam 30 cm setelah dilindas truk molen. Mobil avanza xenia pasti nyangkut bumpernya. Tolong ratakan aspalnya @pemda_jateng.', 'city' => 'Kabupaten Wonogiri', 'province' => 'Jawa Tengah', 'lat' => -7.8112, 'lng' => 110.9212, 'status' => 'in_progress'],
            ['title' => 'Tebing Tanah Longsor Timpa Rumah Warga Garung Wonosobo', 'category' => 'bencana_alam', 'description' => 'Tebing setinggi 7 meter ambrol menimpa bagian belakang dapur warga di Garung Wonosobo sehabis hujan lebat semalam. Warga butuh bantuan logistik terpal @pemda_jateng.', 'city' => 'Kabupaten Wonosobo', 'province' => 'Jawa Tengah', 'lat' => -7.3212, 'lng' => 109.9112, 'status' => 'resolved'],
            ['title' => 'Marka Garis Putih Jalur Lingkar Salatiga Lenyap Terhapus', 'category' => 'fasilitas_umum', 'description' => 'Garis pembatas marka jalan lingkar Salatiga udah pudar ga keliatan sama sekali. Waktu kabut sore sopir sering makan lajur lawan arah. Tolong cat ulang @pemda_jateng.', 'city' => 'Kota Salatiga', 'province' => 'Jawa Tengah', 'lat' => -7.3512, 'lng' => 110.5112, 'status' => 'active'],
            ['title' => 'Tumpukan Ban Bekas Bengkel Terbakar di Lingkar Demak', 'category' => 'kebakaran', 'description' => 'Ratusan ban truk bekas di bengkel pinggir jalan lingkar Demak terbakar hebat, asap hitam pekat menutupi jalan utama. Tolong damkar meluncur @pemda_jateng.', 'city' => 'Kabupaten Demak', 'province' => 'Jawa Tengah', 'lat' => -6.8912, 'lng' => 110.6312, 'status' => 'resolved'],
            ['title' => 'Saringan Got Mampet Plastik di Kawasan Simpang Lima Semarang', 'category' => 'lingkungan', 'description' => 'Tutup besi got saringan trotoar Simpang Lima penuh sampah gelas plastik air mineral bikin air meluber ke aspal. Tolong bersihkan sedimentasinya @pemda_jateng.', 'city' => 'Kota Semarang', 'province' => 'Jawa Tengah', 'lat' => -6.9912, 'lng' => 110.4212, 'status' => 'resolved'],
            ['title' => 'Jalan Antar Kota Purworejo-Magelang Penuh Kubangan Air Hujan', 'category' => 'infrastruktur', 'description' => 'Jalanan provinsi banyak lubang dalam yang ketutup genangan air kayak kolam ikan lele. Pemotor ga bisa bedain jalan mulus sama lubang maut. Tolong ditambal @pemda_jateng.', 'city' => 'Kabupaten Purworejo', 'province' => 'Jawa Tengah', 'lat' => -7.7112, 'lng' => 110.0112, 'status' => 'active'],
            ['title' => 'Kabel Telepon Putus Menjuntai di Depan SD Negeri Temanggung', 'category' => 'kelistrikan', 'description' => 'Kabel hitam putus menggantung pas di depan pintu gerbang sekolah dasar Temanggung. Takut ada anak kecil iseng narik kabel. Tolong rapikan @pemda_jateng.', 'city' => 'Kabupaten Temanggung', 'province' => 'Jawa Tengah', 'lat' => -7.3112, 'lng' => 110.1712, 'status' => 'active'],
            ['title' => 'Plafon Gypsum GOR Rembang Ambruk Kena Rembesan Atap', 'category' => 'fasilitas_umum', 'description' => 'Plafon ruang tribun GOR Rembang jebol ambruk ke lantai lapangan bulutangkis gara-gara atap seng bocor ga pernah dirawat. Tolong perbaiki @pemda_jateng.', 'city' => 'Kabupaten Rembang', 'province' => 'Jawa Tengah', 'lat' => -6.7012, 'lng' => 111.3412, 'status' => 'active'],
            ['title' => 'Bantaran Sungai Serayu Banyumas Meluap Rendam Padi', 'category' => 'bencana_alam', 'description' => 'Debit air Sungai Serayu meluap merendam puluhan hektar sawah dan pemukiman pinggir kali di Banyumas. Mohon bantuan evakuasi ternak @pemda_jateng.', 'city' => 'Kabupaten Banyumas', 'province' => 'Jawa Tengah', 'lat' => -7.5112, 'lng' => 109.2812, 'status' => 'active'],
            ['title' => 'Batu Krikil Berserakan dari Aspal Rusak Tanjakan Ketep Pass', 'category' => 'infrastruktur', 'description' => 'Aspal mengelupas di tanjakan terjal Ketep Pass Magelang bikin batu kerikil tajam berceceran. Ban motor sering selip licin ga kuat nanjak. Tolong aspal hotmix @pemda_jateng.', 'city' => 'Kabupaten Magelang', 'province' => 'Jawa Tengah', 'lat' => -7.4912, 'lng' => 110.3812, 'status' => 'in_progress'],
        ];

        foreach ($jatengList as $item) {
            $item['pemda_tag'] = '@pemda_jateng';
            $data[] = $item;
        }

        // -------------------------------------------------------------
        // JAWA TIMUR (33 Laporan) - @pemda_jatim
        // -------------------------------------------------------------
        $jatimList = [
            [
                'title' => 'Underpass Mayjend Sungkono Tenggelam Jadi Kolam Renang',
                'category' => 'bencana_alam',
                'description' => 'Hujan deres sejam rek, underpass Mayjend Sungkono Surabaya langsung kelelep banjir sak ban mobil! Mobil sedan ga wani liwat macet total sak kutho. Pompa banyu Darmo Park mosok ga murup blas? Tolong sedot banyune @pemda_jatim!',
                'city' => 'Kota Surabaya',
                'province' => 'Jawa Timur',
                'lat' => -7.2912,
                'lng' => 112.7112,
                'status' => 'in_progress',
            ],
            [
                'title' => 'Lubang Aspal Ajur Turunan Dau Arah Kota Batu',
                'category' => 'infrastruktur',
                'description' => 'Turunan tajam Dau arah Batu aspalnya ajur mumur rek, lubangnya dalem banget ada sak meter lebarnya. Wisatawan luar kota sing ga apal dalan sering rem mendadak hampir jungkir balik. Tambalen ndang @pemda_jatim sebelum ono korban jiwa!',
                'city' => 'Kota Batu',
                'province' => 'Jawa Timur',
                'lat' => -7.8812,
                'lng' => 112.5212,
                'status' => 'resolved',
            ],
            [
                'title' => 'Lampu PJU Jembatan Suramadu Padam Peteng Dedet',
                'category' => 'kelistrikan',
                'description' => 'Bentang tengah Jembatan Suramadu arah Madura lampune mati peteng ndedet rek. Angin laut kenceng banget, motoran rasane koyo uji nyali horor. Opo ga bahaya iki? Tolong dibenerin kabel e @pemda_jatim.',
                'city' => 'Kota Surabaya',
                'province' => 'Jawa Timur',
                'lat' => -7.1812,
                'lng' => 112.7812,
                'status' => 'active',
            ],
            [
                'title' => 'Lumpur Lapindo Sidoarjo Dibuati Sampah Liar Numpuk sak Gunung',
                'category' => 'lingkungan',
                'description' => 'Bahu jalan tanggul luar Porong Sidoarjo dipenuhi karung sampah buangan liar. Baune bosok nganti nyesekno dodo. Satpol PP tolong tangkepi wong sing buang sampah bengi-bengi @pemda_jatim!',
                'city' => 'Kabupaten Sidoarjo',
                'province' => 'Jawa Timur',
                'lat' => -7.5212,
                'lng' => 112.7112,
                'status' => 'active',
            ],
            [
                'title' => 'Kebakaran Padang Savana Kaldera Gunung Bromo',
                'category' => 'kebakaran',
                'description' => 'Titik geni murup maneh ndek bukit teletubbies padang savana Bromo mergo suket garing kena angin kenceng. Asep kandel ketok jelas teko Cemoro Lawang. Tulung kirim pemadam water bombing @pemda_jatim!',
                'city' => 'Kabupaten Probolinggo',
                'province' => 'Jawa Timur',
                'lat' => -7.9412,
                'lng' => 112.9512,
                'status' => 'in_progress',
            ],
            [
                'title' => 'Pilar Jembatan Glodok Madiun Retak Kena Banjir Bandang',
                'category' => 'infrastruktur',
                'description' => 'Pilar penyangga beton Jembatan Glodok jalur Magetan-Madiun retak amblas mergo katerak banyu banjir bandang bengi mau. Truk tronton abot tolong dialihno rek, wedi ambruk jembatane! Tolong sidak @pemda_jatim.',
                'city' => 'Kota Madiun',
                'province' => 'Jawa Timur',
                'lat' => -7.6312,
                'lng' => 111.5212,
                'status' => 'active',
            ],
            [
                'title' => 'Tiang Internet Miring Meh Ambruk Ndek Alun-alun Jember',
                'category' => 'kelistrikan',
                'description' => 'Tiang wesi kabel optik ndek ngarep kantor pos Jember miring 45 derajat kabel e bergelantungan medeni uwong liwat. Lek ketabrak mobil box iso ambyar sekampung. Lapor @pemda_jatim ndang ditoto maneh!',
                'city' => 'Kabupaten Jember',
                'province' => 'Jawa Timur',
                'lat' => -8.1712,
                'lng' => 113.7012,
                'status' => 'resolved',
            ],
            [
                'title' => 'Watu Gede Longsor Nutup Dalan Lintas Selatan Pacitan',
                'category' => 'bencana_alam',
                'description' => 'Watu segede gajah ambrol teko tebing nutup total dalan JLS Pacitan. Kendaraan roda papat macet total ga iso liwat blas. Butuh ekskavator pemecah watu teko dinas bina marga @pemda_jatim ndang dibukak dalane!',
                'city' => 'Kabupaten Pacitan',
                'province' => 'Jawa Timur',
                'lat' => -8.2012,
                'lng' => 111.0912,
                'status' => 'in_progress',
            ],
            [
                'title' => 'Banyu Kali Surabaya Berbusa Putih Tebal Mambu Kimia',
                'category' => 'lingkungan',
                'description' => 'Banyu baku PDAM Kali Surabaya ndek pintu air Karangpilang metuk busa putih kandel sak meter dhuwure rek. Mambu obat kimia nyengak, iwak-iwak podo semaput. Tolong gerebek pabrik sing buang limbah siluman iki @pemda_jatim!',
                'city' => 'Kota Surabaya',
                'province' => 'Jawa Timur',
                'lat' => -7.3312,
                'lng' => 112.6912,
                'status' => 'active',
            ],
            [
                'title' => 'Kaca Halte Bus Trans Semanggi Ngarep ITS Ambyar Pecah',
                'category' => 'fasilitas_umum',
                'description' => 'Kaca pelindung halte bus Trans Semanggi ngarep gerbang kampus ITS Surabaya ambyar pecah dibalang watu wong ga tanggung jawab. Beling tajem berserakan di trotoar. Tulung ganti panel anyar @pemda_jatim mesakno arek-arek kuliah nunggu bus.',
                'city' => 'Kota Surabaya',
                'province' => 'Jawa Timur',
                'lat' => -7.2812,
                'lng' => 112.7912,
                'status' => 'resolved',
            ],
            // 23 Laporan Tambahan Jatim
            ['title' => 'Dalan Pantura Tuban Gelombang Parah Truk Semen Podo Ngguling', 'category' => 'infrastruktur', 'description' => 'Bahu dalan Pantura Tuban amblas mblenduk koyo ombak segoro. Truk muatan semen sering oleng meh ngguling. Tolong aspal ulang @pemda_jatim.', 'city' => 'Kabupaten Tuban', 'province' => 'Jawa Timur', 'lat' => -6.8912, 'lng' => 112.0612, 'status' => 'active'],
            ['title' => 'Banjir Kali Lamong Rendam Omah Warga Benjeng Gresik', 'category' => 'bencana_alam', 'description' => 'Banjir kiriman Kali Lamong teko maneh ngrendem atusan omah sak dengkul ndek Benjeng Gresik. Pompa banjir e tolong dimaksimalno @pemda_jatim.', 'city' => 'Kabupaten Gresik', 'province' => 'Jawa Timur', 'lat' => -7.2612, 'lng' => 112.4912, 'status' => 'active'],
            ['title' => 'Lampu PJU Jalur Wisata Pasir Putih Situbondo Mati Kabeh', 'category' => 'kelistrikan', 'description' => 'Penerangan dalan wisata Pasir Putih Situbondo peteng dedet, rawan begal karo kacilakan tabrak wit. Tulung ganti bohlam e @pemda_jatim.', 'city' => 'Kabupaten Situbondo', 'province' => 'Jawa Timur', 'lat' => -7.6912, 'lng' => 113.8212, 'status' => 'active'],
            ['title' => 'Wit Beringin Tuwek Ambruk Nimpa Lampu Alun-alun Kediri', 'category' => 'bencana_alam', 'description' => 'Wit beringin kesamber petir ambruk nindih tiang lampu taman kutho Kediri nganti remuk. Tulung gergaji mesin senso damkar @pemda_jatim dievakuasi.', 'city' => 'Kota Kediri', 'province' => 'Jawa Timur', 'lat' => -7.8212, 'lng' => 112.0112, 'status' => 'resolved'],
            ['title' => 'Limbah Kulit Pabrik Dibakar Sembarangan ndek Magetan', 'category' => 'lingkungan', 'description' => 'Asep gosong sisa kulit pabrik sepatu dibakar awan-awan nggawe sesek napas arek cilik sak desa Magetan. Tulung sidak dinas lingkungan @pemda_jatim!', 'city' => 'Kabupaten Magetan', 'province' => 'Jawa Timur', 'lat' => -7.6512, 'lng' => 111.3212, 'status' => 'active'],
            ['title' => 'Tanggul Pemecah Ombak Pantai Boom Banyuwangi Jebol', 'category' => 'infrastruktur', 'description' => 'Watu bronjong penahan gelombang pantai Boom Banyuwangi ambrol dihantem ombak segoro kidul. Tolong didandani @pemda_jatim mumpung durung rob gedhe.', 'city' => 'Kabupaten Banyuwangi', 'province' => 'Jawa Timur', 'lat' => -8.2112, 'lng' => 114.3812, 'status' => 'active'],
            ['title' => 'Suket Garing Terbakar Pinggir Tol Surabaya-Mojokerto', 'category' => 'kebakaran', 'description' => 'Asep kandel kebakaran ilalang pinggir tol Sumo KM 720 nutupi dalan cepet, mbebayani pengguna tol. Damkar tolong meluncur @pemda_jatim.', 'city' => 'Kabupaten Mojokerto', 'province' => 'Jawa Timur', 'lat' => -7.4212, 'lng' => 112.4412, 'status' => 'resolved'],
            ['title' => 'Kawat Seling Jembatan Gantung Lumajang Karatan Pedot', 'category' => 'infrastruktur', 'description' => 'Tali sling wesi jembatan gantung antar dusun Lumajang pedot siji, jembatane miring medeni. Tolong dandani @pemda_jatim sakdurunge ambruk.', 'city' => 'Kabupaten Lumajang', 'province' => 'Jawa Timur', 'lat' => -8.1312, 'lng' => 113.2212, 'status' => 'active'],
            ['title' => 'Tanjakan Ekstrem Cangar-Pacet Ga Ono Marka Garis Putih', 'category' => 'fasilitas_umum', 'description' => 'Tanjakan curam tikungan tajem Pacet Mojokerto ga ono garis marka dalane rek, lek pas pedut kabut kandel rawan tabrakan adu banteng. Lapor @pemda_jatim.', 'city' => 'Kabupaten Mojokerto', 'province' => 'Jawa Timur', 'lat' => -7.7112, 'lng' => 112.5412, 'status' => 'active'],
            ['title' => 'Lampu Abang Ijo Murup Bareng ndek Simpang Kletek Sidoarjo', 'category' => 'kelistrikan', 'description' => 'Traffic light persimpangan Kletek error lampu abang karo ijo murup bareng, supir truk karo pemotor bingung tabrakan meh kedaden. Benerno @pemda_jatim!', 'city' => 'Kabupaten Sidoarjo', 'province' => 'Jawa Timur', 'lat' => -7.3612, 'lng' => 112.6712, 'status' => 'resolved'],
            ['title' => 'Sampah Kresek Sumbat Saluran Irigasi Sawah Blitar', 'category' => 'lingkungan', 'description' => 'Atusan kresek sampah numpuk nyumpel pintu banyu irigasi Blitar bikin banyu mbludak ngrendem palawija. Tulung keruk sampohe @pemda_jatim.', 'city' => 'Kota Blitar', 'province' => 'Jawa Timur', 'lat' => -8.0912, 'lng' => 112.1612, 'status' => 'active'],
            ['title' => 'Tebing Jurang Jalur Trenggalek-Ponorogo Ono Retakan Lemah', 'category' => 'bencana_alam', 'description' => 'Ono retakan lemah sak kilan ndek tebing dalan nasional Trenggalek-Ponorogo. Lek udan deres iso longsor nutup dalan. Tolong antisipasi @pemda_jatim.', 'city' => 'Kabupaten Trenggalek', 'province' => 'Jawa Timur', 'lat' => -8.0112, 'lng' => 111.6112, 'status' => 'in_progress'],
            ['title' => 'Dalan Ngarep Terminal Arjosari Malang Rusak Parah Ono Jeglongan', 'category' => 'infrastruktur', 'description' => 'Pintu metu bus terminal Arjosari Malang dalane ajur jeglongan banyu koyo kubangan kebo. Bus karo angkot goyang miring. Ndang tambal aspal @pemda_jatim!', 'city' => 'Kota Malang', 'province' => 'Jawa Timur', 'lat' => -7.9312, 'lng' => 112.6512, 'status' => 'in_progress'],
            ['title' => 'Trafo Listrik Pasar Besar Malang Ngetokno Percikan Geni', 'category' => 'kelistrikan', 'description' => 'Tiang trafo gardu pasar gedhe Malang meletup metu geni cilik-cilik. Pedagang terpal podo panik wedi kebakar. Tolong PLN @pemda_jatim moro rene!', 'city' => 'Kota Malang', 'province' => 'Jawa Timur', 'lat' => -7.9812, 'lng' => 112.6312, 'status' => 'resolved'],
            ['title' => 'Ayunan Karatan Taman Hiburan Pasuruan Ketutup Rungkut Semak', 'category' => 'fasilitas_umum', 'description' => 'Taman bermain anak kutho Pasuruan ga tau dirawat, ayunan jungkat-jungkit karatan tajem ketutup suket rungkut. Tolong dibersihkan @pemda_jatim.', 'city' => 'Kota Pasuruan', 'province' => 'Jawa Timur', 'lat' => -7.6412, 'lng' => 112.9112, 'status' => 'active'],
            ['title' => 'Gudang Kayu Palet Bekas Terbakar ndek Margomulyo Surabaya', 'category' => 'kebakaran', 'description' => 'Geni gedhe mumbul dhuwur teko tumpukan kayu palet ndek Margomulyo Surabaya. Damkar tolong meluncur @pemda_jatim wedi nyamber gudang liyane.', 'city' => 'Kota Surabaya', 'province' => 'Jawa Timur', 'lat' => -7.2412, 'lng' => 112.6612, 'status' => 'in_progress'],
            ['title' => 'Banjir Rendam Dalan Utama Kawasan Industri PIER Pasuruan', 'category' => 'bencana_alam', 'description' => 'Kawasan industri PIER Pasuruan kelelep banyu sak dhengkul, buruh pabrik sing mulih kerja montore podo mogok kabeh. Tulung sedot banyune @pemda_jatim.', 'city' => 'Kabupaten Pasuruan', 'province' => 'Jawa Timur', 'lat' => -7.6112, 'lng' => 112.8712, 'status' => 'active'],
            ['title' => 'Ubin Pemandu Difabel Rusak Copot ndek Dalan Ijen Malang', 'category' => 'fasilitas_umum', 'description' => 'Guiding block kuning kanggo tunanetra ndek trotoar Ijen Malang podo copot ucul ga karuan. Pejalan kaki difabel kesandung. Tulung pasang anyar @pemda_jatim.', 'city' => 'Kota Malang', 'province' => 'Jawa Timur', 'lat' => -7.9712, 'lng' => 112.6212, 'status' => 'active'],
            ['title' => 'Aspal Jeglong ndek Perlintasan Kereta Api Ngawi', 'category' => 'infrastruktur', 'description' => 'Karet bantalan rel sebidang Ngawi ucul, aspal bolong gawe velg motor monting. Tolong koordinasi tambal aspal @pemda_jatim.', 'city' => 'Kabupaten Ngawi', 'province' => 'Jawa Timur', 'lat' => -7.4012, 'lng' => 111.4412, 'status' => 'active'],
            ['title' => 'Pipa PDAM Bocor Sembur Banyu Bersih ndek Basuki Rahmat Surabaya', 'category' => 'infrastruktur', 'description' => 'Pipa banyu resik pecah ndek trotoar Basuki Rahmat Surabaya nyembur banyu mubazir nganti ngembong aspal. Tulung klem pipa @pemda_jatim.', 'city' => 'Kota Surabaya', 'province' => 'Jawa Timur', 'lat' => -7.2654, 'lng' => 112.7412, 'status' => 'resolved'],
            ['title' => 'Lampu Jembatan Kali Porong Sidoarjo Mati Total', 'category' => 'kelistrikan', 'description' => 'Jembatan Porong penghubung Sidoarjo-Pasuruan peteng ndedet, lampune ga murup blas. Akeh montor liwat banter rawan tabrakan. Lapor @pemda_jatim.', 'city' => 'Kabupaten Sidoarjo', 'province' => 'Jawa Timur', 'lat' => -7.5412, 'lng' => 112.7212, 'status' => 'active'],
            ['title' => 'Asep Bakaran Ban Bekas ndek Driyorejo Gresik Mlebu Omah', 'category' => 'lingkungan', 'description' => 'Pembakaran ban bekas liar tengah wengi ndek Driyorejo Gresik gawe asep beracun ireng mlebu kamar turu warga. Tulung tangkepi pelakune @pemda_jatim!', 'city' => 'Kabupaten Gresik', 'province' => 'Jawa Timur', 'lat' => -7.3412, 'lng' => 112.6112, 'status' => 'active'],
            ['title' => 'Plang Jeneng Dalan Nganjuk Bengkok Ketabrak Truk', 'category' => 'fasilitas_umum', 'description' => 'Tiang wesi plang penunjuk arah kutho Nganjuk bengkok ampir nyemplung got bar ketabrak truk mundur. Tulung pasang anyar @pemda_jatim.', 'city' => 'Kabupaten Nganjuk', 'province' => 'Jawa Timur', 'lat' => -7.6012, 'lng' => 111.9012, 'status' => 'active'],
        ];

        foreach ($jatimList as $item) {
            $item['pemda_tag'] = '@pemda_jatim';
            $data[] = $item;
        }

        return $data;
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

    private function generateReportSvg(string $category, string $title, string $accentColor = '#f97316'): string
    {
        $vectorArt = match ($category) {
            'infrastruktur' => '<path d="M100 350 L260 80 L340 80 L500 350" stroke="#374151" stroke-width="6" fill="none"/>
                <line x1="300" y1="80" x2="300" y2="160" stroke="#f59e0b" stroke-width="8" stroke-dasharray="16,14"/>
                <line x1="300" y1="220" x2="300" y2="350" stroke="#f59e0b" stroke-width="8" stroke-dasharray="16,14"/>
                <ellipse cx="300" cy="275" rx="90" ry="38" fill="#090a0f" stroke="'.$accentColor.'" stroke-width="5"/>
                <path d="M240 275 Q280 255 360 280" stroke="'.$accentColor.'" stroke-width="3" fill="none" opacity="0.8"/>',
            'bencana_alam' => '<rect x="0" y="240" width="600" height="160" fill="#0c4a6e" opacity="0.6"/>
                <path d="M0 260 Q75 230 150 260 T300 260 T450 260 T600 260 L600 400 L0 400 Z" fill="#0284c7" opacity="0.5"/>
                <path d="M0 290 Q75 270 150 290 T300 290 T450 290 T600 290" stroke="#38bdf8" stroke-width="6" fill="none"/>
                <rect x="230" y="120" width="140" height="90" rx="8" fill="#1e293b" stroke="#64748b" stroke-width="4"/>
                <line x1="260" y1="120" x2="260" y2="210" stroke="#64748b" stroke-width="4"/>
                <line x1="300" y1="120" x2="300" y2="210" stroke="#64748b" stroke-width="4"/>
                <line x1="340" y1="120" x2="340" y2="210" stroke="#64748b" stroke-width="4"/>',
            'kelistrikan' => '<line x1="300" y1="60" x2="300" y2="330" stroke="#475569" stroke-width="8"/>
                <path d="M260 100 Q300 50 340 100" stroke="#475569" stroke-width="6" fill="none"/>
                <circle cx="300" cy="115" r="22" fill="#1e293b" stroke="#eab308" stroke-width="4" stroke-dasharray="6,4"/>
                <polygon points="275,130 325,130 380,330 220,330" fill="#eab308" opacity="0.06"/>
                <circle cx="300" cy="330" r="40" fill="#0f172a" stroke="#334155" stroke-width="4"/>',
            'lingkungan' => '<polygon points="200,340 230,220 370,220 400,340" fill="#1c1917" stroke="#78716c" stroke-width="4"/>
                <path d="M190 220 L410 220 L380 190 L220 190 Z" fill="#292524" stroke="#a8a29e" stroke-width="3"/>
                <polygon points="240,320 270,250 330,250 360,320" fill="#15803d" opacity="0.7"/>
                <circle cx="280" cy="200" r="16" fill="#ca8a04" opacity="0.8"/>
                <polygon points="310,230 345,180 370,230" fill="#b91c1c" opacity="0.8"/>',
            'kebakaran' => '<path d="M300 80 Q350 180 300 240 Q400 220 370 330 Q330 360 300 360 Q270 360 230 330 Q200 220 300 240 Q250 180 300 80 Z" fill="#ef4444" opacity="0.85"/>
                <path d="M300 160 Q330 220 300 260 Q360 250 340 330 Q320 350 300 350 Q280 350 260 330 Q240 250 300 260 Q270 220 300 160 Z" fill="#f59e0b" opacity="0.9"/>',
            default => '<line x1="50" y1="320" x2="550" y2="320" stroke="#3f3f46" stroke-width="4"/>
                <line x1="120" y1="280" x2="170" y2="340" stroke="#f8fafc" stroke-width="16"/>
                <line x1="200" y1="280" x2="250" y2="340" stroke="#f8fafc" stroke-width="16"/>
                <line x1="280" y1="280" x2="330" y2="340" stroke="#f8fafc" stroke-width="16"/>
                <line x1="360" y1="280" x2="410" y2="340" stroke="#f8fafc" stroke-width="16"/>
                <polygon points="300,90 350,180 250,180" fill="#18181b" stroke="'.$accentColor.'" stroke-width="5"/>
                <line x1="300" y1="125" x2="300" y2="150" stroke="'.$accentColor.'" stroke-width="4"/>
                <circle cx="300" cy="165" r="2.5" fill="'.$accentColor.'"/>',
        };

        $escapedTitle = htmlspecialchars(Str::limit($title, 45), ENT_QUOTES, 'UTF-8');
        $escapedCategory = htmlspecialchars(strtoupper(str_replace('_', ' ', $category)), ENT_QUOTES, 'UTF-8');
        $pillWidth = strlen($escapedCategory) * 9 + 24;

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 400" width="100%" height="100%">
            <defs>
                <pattern id="grid" width="30" height="30" patternUnits="userSpaceOnUse">
                    <path d="M 30 0 L 0 0 0 30" fill="none" stroke="#ffffff" stroke-width="1" opacity="0.04"/>
                </pattern>
            </defs>
            <rect width="600" height="400" fill="#121316"/>
            <rect width="600" height="400" fill="url(#grid)"/>
            '.$vectorArt.'
            <rect x="25" y="25" width="'.$pillWidth.'" height="26" rx="5" fill="#18181b" stroke="'.$accentColor.'" stroke-width="1.5"/>
            <text x="37" y="42" fill="'.$accentColor.'" font-family="ui-monospace, monospace" font-size="11" font-weight="700" letter-spacing="1">'.$escapedCategory.'</text>
            <rect x="20" y="340" width="560" height="38" rx="6" fill="#09090b" opacity="0.85"/>
            <text x="300" y="364" fill="#f4f4f5" font-family="system-ui, -apple-system, sans-serif" font-size="14" font-weight="600" text-anchor="middle">'.$escapedTitle.'</text>
        </svg>';

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
