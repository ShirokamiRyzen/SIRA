<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\ReportComment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    /**
     * Jalankan seeder 1.000 data dummy bernuansa satir, menyindir realitas sosial,
     * serta perseteruan warga vs buzzer di kolom komentar yang sekonteks dengan judul laporan.
     */
    public function run(): void
    {
        $now = now();
        $defaultPassword = Hash::make('password123');

        // -------------------------------------------------------------
        // 1. Akun Admin (username: admin, password: admin) & User (username: user, password: user)
        // -------------------------------------------------------------
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

        $this->command?->info('Akun Admin disiapkan: username [admin], password [admin]');
        $this->command?->info('Akun User disiapkan: username [user], password [user]');

        // -------------------------------------------------------------
        // 2. Akun Bot SIRA AI Assistant
        // -------------------------------------------------------------
        $siraBot = User::firstOrCreate(
            ['username' => 'Sira'],
            [
                'name' => 'SIRA AI Assistant',
                'email' => 'ai@sira.local',
                'password' => Hash::make('sira-bot-secure-'.config('app.key')),
            ]
        );

        // -------------------------------------------------------------
        // 3. Akun Warga Kritis & Akun Buzzer / Pembela Proyek
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

        $users = collect([$admin, $siraBot]);

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

        // Generate tambahan user dummy hingga 350 pengguna (agar vote unik bisa mencapai 280+)
        $extraUsers = [];
        for ($i = 21; $i <= 350; $i++) {
            $un = "warga_pantau_{$i}";
            $extraUsers[] = [
                'name' => "Warga Pemerhati RT {$i}",
                'username' => $un,
                'email' => "{$un}@sira.test",
                'password' => $defaultPassword,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($extraUsers, 100) as $chunk) {
            DB::table('users')->insertOrIgnore($chunk);
        }

        // Kumpulan semua ID user pembuat laporan (termasuk persona)
        $allUserIds = User::where('id', '!=', $siraBot->id)->pluck('id')->toArray();
        // User voter (kecuali admin & bot) agar admin bisa vote secara bebas di web
        $voterUserIds = User::where('id', '!=', $admin->id)->where('id', '!=', $siraBot->id)->pluck('id')->toArray();

        // -------------------------------------------------------------
        // 4. Dataset 12 Template Masalah Satir
        // -------------------------------------------------------------
        $satiricalTemplates = [
            // Kategori: Jalan Berlubang
            [
                'category' => 'Jalan Berlubang',
                'tag' => 'Wisata Aspal',
                'title' => 'Wahana Wisata Kolam Lele Baru di Tengah Aspal Jalan',
                'desc' => 'Pemerintah sangat visioner! Lubang jalan berdiameter 1 meter ini sengaja dibiarkan berbulan-bulan tanpa tambalan agar warga bisa budidaya ikan lele mandiri demi ketahanan pangan lokal. Terima kasih atas wahana rekreasi air gratisnya.',
                'short_label' => 'Wahana Kolam Lele Aspal',
                'theme' => '#ef4444',
                'bg_dark' => '#18181b',
            ],
            [
                'category' => 'Jalan Berlubang',
                'tag' => 'Proyek Abadi',
                'title' => 'Proyek Gali Tutup Gali Aspal Abadi Tiada Henti',
                'desc' => 'Minggu lalu baru selesai diaspal mulus dengan bangga, minggu ini digali lagi buat kabel, minggu depan rencana digali pipa air. Sebuah siklus ekonomi sirkular tiada tara demi kelangsungan hidup para vendor tersayang.',
                'short_label' => 'Proyek Gali Tutup Aspal',
                'theme' => '#f97316',
                'bg_dark' => '#18181b',
            ],
            [
                'category' => 'Jalan Berlubang',
                'tag' => 'Kearifan Lokal',
                'title' => 'Tutup Manhole Gorong-Gorong Diganti Ranting Pohon Beringin Estetik',
                'desc' => 'Tutup besi gorong-gorong lenyap entah ke mana. Sebagai gantinya, warga gotong royong menancapkan ranting pohon lengkap dengan plastik kresek merah berkibar sebagai sensor peringatan ultra-canggih.',
                'short_label' => 'Manhole Ditutup Ranting',
                'theme' => '#eab308',
                'bg_dark' => '#18181b',
            ],
            // Kategori: Drainase & Banjir
            [
                'category' => 'Drainase & Banjir',
                'tag' => 'Arung Jeram Gratis',
                'title' => 'Wahana Waterboom Alami Setiap Hujan Turun Lebih dari 15 Menit',
                'desc' => 'Sistem drainase dirancang istimewa agar air tidak lekas surut ke laut, melainkan memanjakan warga dengan sensasi arung jeram setinggi lutut di ruang tamu masing-masing.',
                'short_label' => 'Waterboom Alami Depan Teras',
                'theme' => '#0284c7',
                'bg_dark' => '#0f172a',
            ],
            // Kategori: Lampu Jalan Padam
            [
                'category' => 'Lampu Jalan Padam',
                'tag' => 'Monumen Gelap',
                'title' => 'Monumen Tiang Lampu Estetik Futuristik Tanpa Aliran Listrik',
                'desc' => 'Desain tiang lampunya sangat artistik bergaya victoria eropa, sayangnya tidak pernah menyala sekalipun sejak seremoni gunting pita 8 bulan lalu. Sangat cocok untuk menguji nyali uji keberanian warga di malam hari.',
                'short_label' => 'Penerangan Jalan Padam Total',
                'theme' => '#eab308',
                'bg_dark' => '#0b0f19',
            ],
            // Kategori: Trotoar Rusak
            [
                'category' => 'Trotoar Rusak',
                'tag' => 'Hak Pejalan Kaki',
                'title' => 'Trotoar Khusus Parkir Mobil Mewah dan Gerobak Gorengan',
                'desc' => 'Trotoar yang dibangun dengan anggaran miliaran rupiah ini sangat ramah pejalan kaki, asalkan pejalan kakinya mampu melompati kap mobil dan uap minyak panas gorengan. Pejalan kaki mohon tahu diri jangan mengganggu lapak parkir.',
                'short_label' => 'Trotoar Beralih Fungsi Parkir',
                'theme' => '#f97316',
                'bg_dark' => '#1c1917',
            ],
            // Kategori: Sampah Liar
            [
                'category' => 'Sampah Liar',
                'tag' => 'Sensasi Bromo',
                'title' => 'Timbunan Debu Vulkanik Proyek yang Tak Kunjung Dibereskan',
                'desc' => 'Warga sekitar kini tidak perlu jauh-jauh liburan ke Gunung Bromo untuk merasakan sensasi badai debu pasir. Cukup duduk manis di teras rumah sambil batuk berjamaah.',
                'short_label' => 'Debu & Limbah Proyek Mangkrak',
                'theme' => '#a8a29e',
                'bg_dark' => '#141a15',
            ],
            [
                'category' => 'Sampah Liar',
                'tag' => 'Sampah Liar',
                'title' => 'Gunung Sampah Liar Estetik Menghiasi Bahu Jalan Protokol',
                'desc' => 'Tumpukan sampah plastik dan limbah rumah tangga setinggi 2 meter dibiarkan menumpuk berhari-hari hingga memakan separuh badan jalan utama dan menyebarkan bau busuk.',
                'short_label' => 'Tumpukan Sampah Bahu Jalan',
                'theme' => '#16a34a',
                'bg_dark' => '#141a15',
            ],
            // Kategori: Fasilitas Umum
            [
                'category' => 'Fasilitas Umum',
                'tag' => 'Uji Adrenalin',
                'title' => 'Zebra Cross Khusus Pejalan Kaki yang Memiliki Nyawa Cadangan',
                'desc' => 'Marka penyeberangan diletakkan tepat di tikungan buta tanpa rambu peringatan. Didedikasikan khusus untuk warga yang sudah siap menjemput ajal dan tidak takut ditabrak truk tronton.',
                'short_label' => 'Zebra Cross Blind Spot',
                'theme' => '#ef4444',
                'bg_dark' => '#18181b',
            ],
            [
                'category' => 'Fasilitas Umum',
                'tag' => 'Pohon Keramat',
                'title' => 'Pohon Rindang Penjaga Tradisi Belum Dipangkas Sejak Musim Lalu',
                'desc' => 'Dahan pohon tua sudah melengkung anggun menyentuh kabel optik dan kepala sopir pick-up. Warga sengaja tidak memangkas karena masih menunggu dinas terkait menyelesaikan rapat koordinasi lintas sektoral jilid ke-4.',
                'short_label' => 'Pohon Rindang Halangi Kabel',
                'theme' => '#10b981',
                'bg_dark' => '#141c16',
            ],
            [
                'category' => 'Fasilitas Umum',
                'tag' => 'Sauna Alam',
                'title' => 'Jembatan Penyeberangan Orang (JPO) Konsep Sauna Tropis Terbuka',
                'desc' => 'Atap jembatan penyeberangan sengaja dibiarkan bolong-bolong agar warga bisa berjemur menyerap vitamin D saat siang bolong dan menikmati mandi air hujan alami saat petang.',
                'short_label' => 'JPO Rusak Tanpa Atap',
                'theme' => '#f59e0b',
                'bg_dark' => '#18181b',
            ],
            [
                'category' => 'Fasilitas Umum',
                'tag' => 'Latihan Survival',
                'title' => 'Taman Bermain Anak Berkonsep Survival Gladiator Extrim',
                'desc' => 'Perosotan berkarat dengan ujung seng tajam dan ayunan rantai putus satu. Sangat mendidik karakter anak-anak sejak usia dini agar tangguh menghadapi kejamnya dunia nyata.',
                'short_label' => 'Fasilitas Bermain Rusak',
                'theme' => '#ec4899',
                'bg_dark' => '#1b141a',
            ],
            [
                'category' => 'Fasilitas Umum',
                'tag' => 'Halte Ghaib',
                'title' => 'Halte Bus Mewah Tempat Tidur Siang Nyaman Tanpa Ada Bus yang Lewat',
                'desc' => 'Sebuah mahakarya infrastruktur transportasi publik: halte megah ber-AC alami tanpa ada satupun trayek bus yang melintas selama 2 tahun terakhir.',
                'short_label' => 'Halte Bus Mangkrak',
                'theme' => '#6366f1',
                'bg_dark' => '#141624',
            ],
        ];

        // -------------------------------------------------------------
        // 5. Skenario Debat yang 100% Sekonteks Berdasarkan Tag Template
        // -------------------------------------------------------------
        $debateScenariosByTag = [
            'Wisata Aspal' => [
                [
                    'root' => 'Ini lubang aspal dari jaman bupati periode pertama masih awet aja, dalemnya udah 15cm minimal diresmikan ulang dong potong tumpeng!',
                    'buzzer' => 'Jangan cuma bisa nyinyir di medsos! Apa kontribusi nyata kamu buat perbaikan jalan?! Pemda lagi bekerja keras dalam senyap, anggaran tambal aspal sudah dialokasikan bertahap di APBD perubahan!',
                    'rebuttal' => 'Bekerja dalam senyap sampai velg motor warga pada peyang berjamaah ya min? Tiap bayar pajak kendaraan tepat waktu giliran nuntut hak aspal mulus malah dibilang ga ada kontribusi wkwk.',
                ],
                [
                    'root' => 'Kemarin ada lele beneran lepas di lubang ini waktu hujan. Lumayan warga ga perlu belanja lauk ke pasar, tinggal bawa pancingan ke tengah jalan.',
                    'buzzer' => 'Itu bukti kepedulian warga yang kreatif memanfaatkan genangan air! Lagipula dinas bina marga sudah survei lokasi minggu lalu, tunggu jadwal antrean alat berat dong!',
                    'rebuttal' => 'Survei doang dari tahun jebot min, cuma difoto pake hp terus ditinggal pulang. Lubangnya makin lebar kayak kawah meteor!',
                ],
            ],
            'Monumen Gelap' => [
                [
                    'root' => 'Tiang lampu estetik gaya eropa dipasang puluhan biji tapi kabelnya ga ada yang nyambung. Jadi monumen sarang laba-laba dan spot uji nyali warga.',
                    'buzzer' => 'Lho itu namanya tahapan estetika ruang publik fase 1! Penyambungan daya PLN itu masuk fase 2 tahun anggaran depan. Kalian yang awam teknis jangan sok tau mengkritik kinerja tim ahli!',
                    'rebuttal' => 'Fase 1 tiang, fase 2 kabel, fase 3 lampu, fase 4 roboh kena angin. Proyek tiada akhir emang paling gurih anggarannya!',
                ],
                [
                    'root' => 'Tiap malam jalanan ini gelap gulita padahal tiang lampunya mewah banget. Warga terpaksa pasang senter di stang motor biar ga dibegal.',
                    'buzzer' => 'Pemerintah sudah menyediakan infrastruktur terbaik! Masalah penerangan itu sedang proses integrasi smart city! Bersabarlah sedikit, jangan menyebar pesimisme!',
                    'rebuttal' => 'Smart city apa kota gelap gulita min? Bayar Pajak Penerangan Jalan (PPJ) di struk listrik tiap bulan jalan terus, giliran lampunya mati ga pernah diurus!',
                ],
            ],
            'Hak Pejalan Kaki' => [
                [
                    'root' => 'Trotoar jalan udah kayak showroom mobil bekas dan lapak tenda pecel lele. Pejalan kaki disuruh terbang pake baling-baling bambu apa gimana?',
                    'buzzer' => 'Kalian tidak paham sosiologi perkotaan! Itu bagian dari denyut pemberdayaan UMKM ekonomi kerakyatan! Jangan mematikan rezeki rakyat kecil demi ego kaum borjuis yang mau jalan kaki doang!',
                    'rebuttal' => 'UMKM punya mobil Fortuner 2 biji diparkir di trotoar dibilang rakyat kecil. Lawak bener abang buzzer satu ini, makan nasi bungkusnya jangan lupa.',
                ],
                [
                    'root' => 'Guiding block kuning buat tuna netra malah nabrak tiang listrik sama terhalang motor parkir. Sangat membahayakan penyandang disabilitas!',
                    'buzzer' => 'Petugas Satpol PP rutin melakukan penertiban humanis! Penataan ruang publik butuh sinergi semua pihak, jangan selalu sudutkan petugas lapangan!',
                    'rebuttal' => 'Penertiban humanis pas ada kunjungan gubernur doang, besoknya trotoar udah penuh lapak lagi. Giliran ditagih perda saling lempar dinas.',
                ],
            ],
            'Arung Jeram Gratis' => [
                [
                    'root' => 'Banjirnya udah sepinggang, perahu karet dinas mana ya? Apa nunggu viral di TikTok dulu baru petugasnya pada dateng bikin konten?',
                    'buzzer' => 'Tolong dipahami, curah hujan kali ini adalah fenomena anomali iklim global! Di kota maju luar negeri kayak New York juga banjir! Jangan semua-semua salahin pemerintah, introspeksi diri kalian yang suka buang puntung rokok!',
                    'rebuttal' => 'Mantap logikanya, gorong-gorong ditutup semen ruko dibilang anomali iklim global. Sertifikasi buzzer tier Mythic Glory nih.',
                ],
                [
                    'root' => 'Hujan gerimis 20 menit langsung air meluap masuk rumah warga. Pompa air portabel yang dibeli miliaran kemarin disimpan di mana?',
                    'buzzer' => 'Pompa air dalam status siaga optimal! Petugas SDA sedang standby 24 jam memantau debit pintu air. Netizen jangan memprovokasi kepanikan warga!',
                    'rebuttal' => 'Standby sambil ngopi di posko ya min? Rumah kami udah kelelep sampai kasur ngapung, airnya ga surut-surut dari sore.',
                ],
            ],
            'Proyek Abadi' => [
                [
                    'root' => 'Baru kemarin diaspal mulus difoto-foto pejabat, sekarang udah dibongkar lagi digali pipa. Kompak banget antar dinas ya!',
                    'buzzer' => 'Itu koordinasi taktis terpadu! Pemasangan pipa air bersih itu kebutuhan primer warga juga! Kalau pipa ga dipasang kalian protes, dipasang juga protes. Maunya apa sih netizen?',
                    'rebuttal' => 'Maunya ya gali dulu baru diaspal malih, bukan aspal baru sehari diacak-acak kayak bubur ayam diaduk!',
                ],
                [
                    'root' => 'Minggu lalu galian kabel optik, minggu ini galian gas, bulan depan rencana drainase. Jalan ini ga pernah mulus lebih dari seminggu.',
                    'buzzer' => 'Ini tanda percepatan transformasi digital dan modernisasi utilitas kota! Negara maju selalu membangun infrastrukturnya tanpa henti!',
                    'rebuttal' => 'Membangun tanpa henti atau bancakan proyek tanpa koordinasi? Tiap kali nambal lubang galian cuma diuruk tanah merah becek licin!',
                ],
            ],
            'Uji Adrenalin' => [
                [
                    'root' => 'Zebra cross ditaruh pas di tikungan buta tanpa lampu peringatan. Tiap mau nyebrang rasanya kayak gladi resik ketemu malaikat maut.',
                    'buzzer' => 'Lokasi penyeberangan sudah melalui kajian lalu lintas komprehensif! Pengendara dan penyeberang wajib saling mengedepankan etika berkendara!',
                    'rebuttal' => 'Kajian komprehensif mata lu peyang, truk muatan pasir mana keliatan orang nyebrang di tikungan begitu! Minimal pasang pita kejut atau tombol lampu nyebrang!',
                ],
                [
                    'root' => 'Cat marka jalan baru 2 minggu udah luntur total ga keliatan lagi. Pake cat kiloan apa kapur tulis ini kontraktornya?',
                    'buzzer' => 'Gesekan ribuan ban kendaraan per hari tentu mempengaruhi ketahanan cat! Dishub sudah mengagendakan pengecatan ulang berkala!',
                    'rebuttal' => 'Anggaran tender cat marka termoplastik miliaran tapi kualitasnya setara kapur papan tulis sekolah dasar.',
                ],
            ],
            'Pohon Keramat' => [
                [
                    'root' => 'Dahan pohon tua udah melengkung nindih kabel PLN dan optik, tinggal nunggu angin kencang langsung roboh timpa pengendara.',
                    'buzzer' => 'Penebangan atau perampingan pohon lindung ada regulasi amdalnya! Dinas Pertamanan menjaga kelestarian ruang terbuka hijau agar udara tetap sejuk!',
                    'rebuttal' => 'Nunggu nimpa anak sekolah dulu baru amdalnya selesai ya? Udah dilaporin dari bulan lalu ga ada satu petugas pun yang bawa gergaji mesin.',
                ],
                [
                    'root' => 'Ranting pohon rimbun nutupi rambu petunjuk jalan sama lampu merah. Pengendara dari luar kota sering nyasar dan kebablasan.',
                    'buzzer' => 'Petugas rutin memangkas dahan prioritas sesuai laporan berjenjang melalui kelurahan! Gunakan aplikasi peta digital jika kesulitan navigasi!',
                    'rebuttal' => 'Disuruh liat google maps sambil nyetir motor, solutif sekali admin dinas ini. Pangkas dahan setengah jam aja birokrasinya 3 bulan.',
                ],
            ],
            'Kearifan Lokal' => [
                [
                    'root' => 'Tutup manhole gorong-gorong hilang, ditancepin ranting pohon sama kresek merah. Sensor bahaya kearifan lokal paling canggih di dunia.',
                    'buzzer' => 'Itu tindakan antisipasi swadaya masyarakat yang terpuji! Suku cadang penutup besi sedang dipesan khusus dengan material anti-maling!',
                    'rebuttal' => 'Pesan khusus anti maling atau kontraktornya belum bayar vendor? Udah 4 motor terperosok ke got gara-gara rantingnya patah pas malem.',
                ],
                [
                    'root' => 'Lubang got menganga tanpa penutup di jalur cepat, bau limbahnya semerbak bikin pusing kepala saat macet.',
                    'buzzer' => 'Pemeliharaan saluran primer sedang berjalan serentak di 5 zona! Harap kurangi kecepatan dan patuhi rambu pengalihan!',
                    'rebuttal' => 'Rambu pengalihan aja ga ada, cuma ada kresek merah melambai-lambai ditiup angin!',
                ],
            ],
            'Sauna Alam' => [
                [
                    'root' => 'Atap JPO sengaja dibiarkan bolong biar pejalan kaki bisa berjemur matahari gratis sambil sauna alami.',
                    'buzzer' => 'Desain JPO modern memang mengadopsi sirkulasi udara terbuka demi kesehatan pernapasan dan efisiensi pencahayaan alami!',
                    'rebuttal' => 'Sirkulasi udara terbuka kepala lu botak, pas hujan lebat tangga JPO-nya jadi air terjun grojogan sewu licin berlumut!',
                ],
                [
                    'root' => 'Anak tangga JPO udah karatan bolong-bolong, diinjak goyang kayak jembatan gantung indiana jones.',
                    'buzzer' => 'Kekuatan struktur baja utama JPO telah diuji beban dan dinyatakan layak! Anggaran revitalisasi fasilitas penyeberangan sudah diajukan!',
                    'rebuttal' => 'Layak apanya, baut pengikatnya aja udah copot tinggal 2 biji. Mending nyebrang di aspal daripada jatuh dari atas JPO.',
                ],
            ],
            'Latihan Survival' => [
                [
                    'root' => 'Taman bermain anak fasilitasnya ekstrem banget, perosotan seng berkarat ujungnya tajam siap memicu infeksi tetanus.',
                    'buzzer' => 'Fasilitas umum butuh partisipasi warga untuk merawat bersama! Jangan hanya menyalahkan pengelola ketika ada kerusakan fasilitas!',
                    'rebuttal' => 'Warga bayar retribusi buat apa min kalau disuruh ngelas perosotan sendiri? Anggaran pemeliharaan taman miliaran larinya ke mana?',
                ],
                [
                    'root' => 'Ayunan rantai putus satu, jungkat-jungkit papannya patah. Ini taman ramah anak apa lokasi shooting film horor?',
                    'buzzer' => 'Tim sarana prasarana taman kota telah menjadwalkan peremajaan berkala di kuartal berikutnya. Mohon awasi anak saat bermain!',
                    'rebuttal' => 'Nunggu kuartal depan keburu anak-anak komplek pada kapok main ke taman. Padahal baru diresmikan setahun lalu.',
                ],
            ],
            'Halte Ghaib' => [
                [
                    'root' => 'Halte megah cat kinclong tapi ga pernah ada bus lewat selama 2 tahun. Malah jadi tempat jemur kasur dan nongkrong ayam tetangga.',
                    'buzzer' => 'Itu persiapan koridor rute baru dalam masterplan transportasi terpadu jangka panjang! Pembangunan harus mendahului kebutuhan armada!',
                    'rebuttal' => 'Masterplan jangka panjang tapi armada busnya aja ga pernah dibeli pemda. Haltenya udah karatan sebelum bus perdananya nongol.',
                ],
                [
                    'root' => 'Papan informasi rute di halte isinya iklan caleg kadaluarsa, rute busnya gaib ga ada jadwal kepastian.',
                    'buzzer' => 'Integrasi sistem transportasi publik berbasis aplikasi sedang dalam tahap finalisasi beta testing dengan konsorsium penyedia!',
                    'rebuttal' => 'Beta testing dari jaman smartphone 3G belum kelar-kelar. Warga udah keburu pensiun nunggu bus di halte ini.',
                ],
            ],
            'Sensasi Bromo' => [
                [
                    'root' => 'Debu proyek galian beterbangan tebal banget, jemuran tetangga putih berubah jadi cokelat tanah. Berasa tinggal di lereng Bromo.',
                    'buzzer' => 'Pihak kontraktor telah diinstruksikan menyiram air berkala sesuai SOP lingkungan! Debu adalah konsekuensi wajar dari dinamika pembangunan!',
                    'rebuttal' => 'Nyiram airnya cuma pas mandor lewat doang, sisanya warga satu RW batuk pilek ISPA kena debu tanah galian!',
                ],
                [
                    'root' => 'Truk pengangkut tanah proyek ga pernah ditutup terpal, tanahnya ceceran di jalan bikin aspal licin berlumpur kalau hujan.',
                    'buzzer' => 'Pengawasan armada angkutan material dilakukan ketat di pos check-point! Jika ada pelanggaran vendor akan disanksi administratif!',
                    'rebuttal' => 'Disanksi administratif tapi tiap malam puluhan truk tanah tanpa terpal konvoi bebas di depan kantor dishub.',
                ],
            ],
            'Sampah Liar' => [
                [
                    'root' => 'Bau sampah menyengat sudah seminggu belum diangkut truk dinas kebersihan, lalat dan belatung sampai masuk warung makan warga.',
                    'buzzer' => 'Warga jangan cuma bisa buang sampah tapi ga mau bayar retribusi! Armada truk sampah terbatas dan sudah dijadwalkan bergilir!',
                    'rebuttal' => 'Retribusi iuran sampah tiap bulan ditarik bendahara RT/RW kok min! Giliran sampahnya numpuk berhari-hari malah nyalahin warga.',
                ],
                [
                    'root' => 'Tumpukan sampah liar di bahu jalan udah makan separuh jalan raya, pengendara motor harus ngalah masuk ke jalur lawan arah.',
                    'buzzer' => 'Satgas Kebersihan sudah memasang spanduk larangan membuang sampah! Perilaku oknum warga yang buang sampah sembarangan yang harus diperbaiki!',
                    'rebuttal' => 'Spanduk larangannya malah ketimbun karung sampah min. Sediakan kontainer bak sampah di sini, jangan cuma modal spanduk!',
                ],
            ],
        ];

        // Kluster Wilayah Perkotaan (Padat untuk visualisasi Heatmap)
        $clusters = [
            // Kluster Lengkap 30 Kecamatan Kota Bandung
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
            [
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Andir',
                'subdistrict' => 'Kebon Jeruk',
                'address_prefix' => 'Jl. Jend. Sudirman No. ',
                'base_lat' => -6.914200,
                'base_lng' => 107.585700,
                'spread' => 0.012,
            ],
            [
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Antapani',
                'subdistrict' => 'Antapani Wetan',
                'address_prefix' => 'Jl. Purwakarta No. ',
                'base_lat' => -6.916700,
                'base_lng' => 107.658600,
                'spread' => 0.012,
            ],
            [
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Arcamanik',
                'subdistrict' => 'Sukamiskin',
                'address_prefix' => 'Jl. Pacuan Kuda No. ',
                'base_lat' => -6.924200,
                'base_lng' => 107.674400,
                'spread' => 0.014,
            ],
            [
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Astanaanyar',
                'subdistrict' => 'Panjunan',
                'address_prefix' => 'Jl. Astana Anyar No. ',
                'base_lat' => -6.932800,
                'base_lng' => 107.602200,
                'spread' => 0.011,
            ],
            [
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Babakan Ciparay',
                'subdistrict' => 'Sukahaji',
                'address_prefix' => 'Jl. Soekarno-Hatta No. ',
                'base_lat' => -6.945800,
                'base_lng' => 107.579400,
                'spread' => 0.015,
            ],
            [
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Bandung Kidul',
                'subdistrict' => 'Mengger',
                'address_prefix' => 'Jl. Batununggal Indah No. ',
                'base_lat' => -6.958300,
                'base_lng' => 107.633300,
                'spread' => 0.013,
            ],
            [
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Bandung Kulon',
                'subdistrict' => 'Caringin',
                'address_prefix' => 'Jl. Holis No. ',
                'base_lat' => -6.925000,
                'base_lng' => 107.566700,
                'spread' => 0.014,
            ],
            [
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Bandung Wetan',
                'subdistrict' => 'Citarum',
                'address_prefix' => 'Jl. R.E. Martadinata No. ',
                'base_lat' => -6.905600,
                'base_lng' => 107.618600,
                'spread' => 0.010,
            ],
            [
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Batununggal',
                'subdistrict' => 'Gumuruh',
                'address_prefix' => 'Jl. Gatot Subroto No. ',
                'base_lat' => -6.941700,
                'base_lng' => 107.633300,
                'spread' => 0.012,
            ],
            [
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Bojongloa Kaler',
                'subdistrict' => 'Kopo',
                'address_prefix' => 'Jl. Kopo No. ',
                'base_lat' => -6.933300,
                'base_lng' => 107.588900,
                'spread' => 0.013,
            ],
            [
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Bojongloa Kidul',
                'subdistrict' => 'Cibaduyut',
                'address_prefix' => 'Jl. Cibaduyut Raya No. ',
                'base_lat' => -6.955600,
                'base_lng' => 107.594400,
                'spread' => 0.014,
            ],
            [
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Buahbatu',
                'subdistrict' => 'Margasari',
                'address_prefix' => 'Jl. Ciwastra No. ',
                'base_lat' => -6.963900,
                'base_lng' => 107.655600,
                'spread' => 0.015,
            ],
            [
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Cibeunying Kaler',
                'subdistrict' => 'Cihaurgeulis',
                'address_prefix' => 'Jl. Surapati No. ',
                'base_lat' => -6.897200,
                'base_lng' => 107.633300,
                'spread' => 0.012,
            ],
            [
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Cibeunying Kidul',
                'subdistrict' => 'Padasuka',
                'address_prefix' => 'Jl. PH.H. Mustofa No. ',
                'base_lat' => -6.908300,
                'base_lng' => 107.641700,
                'spread' => 0.013,
            ],
            [
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Cibiru',
                'subdistrict' => 'Cipadung',
                'address_prefix' => 'Jl. A.H. Nasution No. ',
                'base_lat' => -6.927800,
                'base_lng' => 107.716700,
                'spread' => 0.015,
            ],
            [
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Cidadap',
                'subdistrict' => 'Hegarmanah',
                'address_prefix' => 'Jl. Setiabudi No. ',
                'base_lat' => -6.861100,
                'base_lng' => 107.597200,
                'spread' => 0.016,
            ],
            [
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Cinambo',
                'subdistrict' => 'Babakan Penghulu',
                'address_prefix' => 'Jl. Rumah Sakit No. ',
                'base_lat' => -6.936100,
                'base_lng' => 107.691700,
                'spread' => 0.014,
            ],
            [
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Gedebage',
                'subdistrict' => 'Rancabolang',
                'address_prefix' => 'Jl. Gedebage Selatan No. ',
                'base_lat' => -6.958300,
                'base_lng' => 107.697200,
                'spread' => 0.016,
            ],
            [
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Kiaracondong',
                'subdistrict' => 'Babakansari',
                'address_prefix' => 'Jl. Ibrahim Adjie No. ',
                'base_lat' => -6.927800,
                'base_lng' => 107.647200,
                'spread' => 0.013,
            ],
            [
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Mandalajati',
                'subdistrict' => 'Sindangjaya',
                'address_prefix' => 'Jl. Pasir Impun No. ',
                'base_lat' => -6.905600,
                'base_lng' => 107.677800,
                'spread' => 0.014,
            ],
            [
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Panyileukan',
                'subdistrict' => 'Cipadung Kulon',
                'address_prefix' => 'Jl. Soekarno-Hatta No. ',
                'base_lat' => -6.944400,
                'base_lng' => 107.711100,
                'spread' => 0.014,
            ],
            [
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Rancasari',
                'subdistrict' => 'Derwati',
                'address_prefix' => 'Jl. Rancabolang No. ',
                'base_lat' => -6.955600,
                'base_lng' => 107.669400,
                'spread' => 0.013,
            ],
            [
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Regol',
                'subdistrict' => 'Balonggede',
                'address_prefix' => 'Jl. Pungkur No. ',
                'base_lat' => -6.938900,
                'base_lng' => 107.611100,
                'spread' => 0.012,
            ],
            [
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Sukajadi',
                'subdistrict' => 'Pasteur',
                'address_prefix' => 'Jl. Dr. Djunjunan No. ',
                'base_lat' => -6.888900,
                'base_lng' => 107.588900,
                'spread' => 0.014,
            ],
            [
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Sukasari',
                'subdistrict' => 'Sarijadi',
                'address_prefix' => 'Jl. Gegerkalong Hilir No. ',
                'base_lat' => -6.869400,
                'base_lng' => 107.586100,
                'spread' => 0.013,
            ],
            [
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Ujungberung',
                'subdistrict' => 'Pasanggrahan',
                'address_prefix' => 'Jl. A.H. Nasution No. ',
                'base_lat' => -6.916700,
                'base_lng' => 107.702800,
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
                'province' => 'DI Yogyakarta',
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
        // 6. Generate 1.000 Laporan Dummy Satir Sinkron
        // -------------------------------------------------------------
        $this->command?->info('Menyiapkan 1.000 data laporan dummy satir sinkron...');

        $reportsData = [];
        $reportMeta = [];
        $targetCount = 1000;

        for ($i = 1; $i <= $targetCount; $i++) {
            $cluster = $clusters[array_rand($clusters)];
            $template = $satiricalTemplates[array_rand($satiricalTemplates)];
            $authorId = $allUserIds[array_rand($allUserIds)];

            // Gambar SVG dibuat 100% sinkron dan sesuai dengan kategori serta tema masalah
            $image = $this->generateReportSvg(
                $template['category'],
                $template['short_label'],
                $template['theme'],
                $template['bg_dark']
            );

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
                $downvotes = mt_rand(1, 15);
                $tier = 'critical';
            } elseif ($randTierChance <= 20) {
                $upvotes = mt_rand(52, 98);
                $downvotes = mt_rand(1, 10);
                $tier = 'urgent';
            } elseif ($randTierChance <= 55) {
                $upvotes = mt_rand(12, 48);
                $downvotes = mt_rand(0, 5);
                $tier = 'trending';
            } else {
                $upvotes = mt_rand(0, 9);
                $downvotes = mt_rand(0, 2);
                $tier = 'normal';
            }

            $voteScore = max(0, $upvotes - $downvotes);
            $houseNum = mt_rand(1, 250);
            $address = "{$cluster['address_prefix']}{$houseNum}, {$cluster['subdistrict']}, {$cluster['district']}, {$cluster['city']}, {$cluster['province']}";

            $createdAt = $now->copy()->subMinutes(mt_rand(15, 43200));
            $statuses = ['active', 'active', 'active', 'in_progress', 'resolved'];
            $status = $statuses[array_rand($statuses)];

            // Judul bervariasi secara natural berdasarkan area/wilayah tanpa repetisi #angka
            $titleVariants = [
                "{$template['title']} di {$cluster['subdistrict']}",
                "{$template['title']} ({$cluster['district']})",
                "{$template['title']} Area {$cluster['city']}",
                "{$template['title']}",
            ];
            $title = $titleVariants[array_rand($titleVariants)];

            $reportsData[] = [
                'user_id' => $authorId,
                'title' => $title,
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

            $reportMeta[$i - 1] = [
                'template_tag' => $template['tag'],
                'upvotes' => $upvotes,
                'downvotes' => $downvotes,
                'created_at' => $createdAt,
            ];
        }

        // Simpan dalam batch 200 data
        foreach (array_chunk($reportsData, 200) as $chunk) {
            DB::table('reports')->insert($chunk);
        }

        $this->command?->info('1.000 Laporan berhasil di-generate.');

        // -------------------------------------------------------------
        // 8. Berikan Real Votes di Tabel report_votes (Perbaiki Bug 1: Voting reset ke 0)
        // -------------------------------------------------------------
        $this->command?->info('Menyinkronkan dan menyisipkan data vote nyata ke report_votes...');

        // Ambil ID semua laporan yang baru saja dibuat berurutan
        $insertedReports = DB::table('reports')->select('id')->orderBy('id')->get();
        $votesToInsert = [];
        $totalVotersAvailable = count($voterUserIds);

        foreach ($insertedReports as $index => $repRow) {
            $meta = $reportMeta[$index] ?? null;
            if (! $meta) {
                continue;
            }

            $reqUpvotes = min($meta['upvotes'], $totalVotersAvailable);
            $reqDownvotes = min($meta['downvotes'], $totalVotersAvailable - $reqUpvotes);

            // Ambil voter unik acak
            $shuffledVoters = $voterUserIds;
            shuffle($shuffledVoters);

            $upvoters = array_slice($shuffledVoters, 0, $reqUpvotes);
            $downvoters = array_slice($shuffledVoters, $reqUpvotes, $reqDownvotes);
            $createdAt = $meta['created_at'];

            foreach ($upvoters as $uId) {
                $votesToInsert[] = [
                    'report_id' => $repRow->id,
                    'user_id' => $uId,
                    'value' => 1,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            }

            foreach ($downvoters as $dId) {
                $votesToInsert[] = [
                    'report_id' => $repRow->id,
                    'user_id' => $dId,
                    'value' => -1,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            }

            // Flush dalam batch 2.000 row agar efisien di memori dan cepat di database
            if (count($votesToInsert) >= 2000) {
                DB::table('report_votes')->insert($votesToInsert);
                $votesToInsert = [];
            }
        }

        if (! empty($votesToInsert)) {
            DB::table('report_votes')->insert($votesToInsert);
            $votesToInsert = [];
        }

        $this->command?->info('Semua data vote nyata berhasil dimasukkan ke tabel report_votes.');

        // -------------------------------------------------------------
        // 9. Komentar Satir Sekonteks (Perbaiki Bug 5: Komentar tidak sekonteks)
        // -------------------------------------------------------------
        $this->command?->info('Menyiapkan debat komentar satir warga vs buzzer yang 100% sekonteks...');

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

        // Sematkan debat satir ke 75 laporan teratas
        $topReports = Report::orderByDesc('vote_score')->take(75)->get();

        foreach ($topReports as $loopIdx => $rep) {
            // Deteksi tag dari judul atau deskripsi laporan untuk mencocokkan skenario secara akurat
            $matchedTag = 'Wisata Aspal';
            foreach ($satiricalTemplates as $tpl) {
                if (str_contains($rep->title, $tpl['title']) || str_contains($rep->description, $tpl['tag'])) {
                    $matchedTag = $tpl['tag'];
                    break;
                }
            }

            $scenarios = $debateScenariosByTag[$matchedTag] ?? $debateScenariosByTag['Wisata Aspal'];
            $scenario = $scenarios[array_rand($scenarios)];

            $criticUser = $critics->random();
            $buzzerUser = $buzzers->random();
            $rebuttalUser = $critics->where('id', '!=', $criticUser->id)->random();

            // 1. Komentar Utama (Warga Kritis - Sesuai konteks masalah)
            $root = ReportComment::create([
                'report_id' => $rep->id,
                'user_id' => $criticUser->id,
                'parent_id' => null,
                'content' => $scenario['root'],
                'created_at' => $rep->created_at->copy()->addMinutes(mt_rand(10, 60)),
            ]);

            // 2. Balasan Buzzer (Membela Proyek / Mengkritik Warga)
            $buzzerReply = ReportComment::create([
                'report_id' => $rep->id,
                'user_id' => $buzzerUser->id,
                'parent_id' => $root->id,
                'content' => $scenario['buzzer'],
                'created_at' => $root->created_at->copy()->addMinutes(mt_rand(5, 30)),
            ]);

            // 3. Balasan Balik Warga (Menyindir Buzzer)
            ReportComment::create([
                'report_id' => $rep->id,
                'user_id' => $rebuttalUser->id,
                'parent_id' => $buzzerReply->id,
                'content' => $scenario['rebuttal'],
                'created_at' => $buzzerReply->created_at->copy()->addMinutes(mt_rand(5, 25)),
            ]);

            // Setiap laporan ke-3, buatkan interaksi contoh fitur AI Summary @Sira
            if ($loopIdx % 3 === 0) {
                $userAiPrompt = ReportComment::create([
                    'report_id' => $rep->id,
                    'user_id' => $criticUser->id,
                    'parent_id' => null,
                    'content' => '@Sira tolong buatkan ringkasan masalah pada laporan ini dan respon perdebatan warganya.',
                    'created_at' => $rep->created_at->copy()->addMinutes(mt_rand(70, 150)),
                ]);

                ReportComment::create([
                    'report_id' => $rep->id,
                    'user_id' => $siraBot->id,
                    'parent_id' => $userAiPrompt->id,
                    'content' => "**Ringkasan SIRA AI:**\n\n• **Masalah Utama:** {$rep->title}.\n• **Status Aspirasi:** Masuk dalam kategori **{$rep->rank_tier} tier** dengan total **{$rep->upvotes_count} dukungan warga**.\n• **Sentimen Diskusi:** Warga menyoroti dampak langsung di lapangan dan lambatnya respon dinas terkait, sementara klarifikasi formal meminta warga bersabar menunggu siklus anggaran.\n• **Rekomendasi:** Perlu percepatan audit lapangan oleh dinas terkait serta transparansi jadwal pengerjaan perbaikan fisik di lokasi.",
                    'created_at' => $userAiPrompt->created_at->copy()->addMinutes(1),
                ]);
            }

            // Perbarui counter comments_count
            $actualComments = ReportComment::where('report_id', $rep->id)->count();
            $rep->update(['comments_count' => $actualComments]);
        }

        $this->command?->info('Seeding 1.000 data dummy satir, vote nyata, dan komentar sekonteks berhasil!');
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

    private function generateReportSvg(string $category, string $shortLabel, string $accentColor, string $bgDark = '#121316'): string
    {
        $vectorArt = match ($category) {
            'Jalan Berlubang' => '<path d="M100 350 L260 80 L340 80 L500 350" stroke="#374151" stroke-width="6" fill="none"/>
                <line x1="300" y1="80" x2="300" y2="160" stroke="#f59e0b" stroke-width="8" stroke-dasharray="16,14"/>
                <line x1="300" y1="220" x2="300" y2="350" stroke="#f59e0b" stroke-width="8" stroke-dasharray="16,14"/>
                <ellipse cx="300" cy="275" rx="90" ry="38" fill="#090a0f" stroke="'.$accentColor.'" stroke-width="5"/>
                <path d="M240 275 Q280 255 360 280" stroke="'.$accentColor.'" stroke-width="3" fill="none" opacity="0.8"/>',
            'Drainase & Banjir' => '<rect x="0" y="240" width="600" height="160" fill="#0c4a6e" opacity="0.6"/>
                <path d="M0 260 Q75 230 150 260 T300 260 T450 260 T600 260 L600 400 L0 400 Z" fill="#0284c7" opacity="0.5"/>
                <path d="M0 290 Q75 270 150 290 T300 290 T450 290 T600 290" stroke="#38bdf8" stroke-width="6" fill="none"/>
                <rect x="230" y="120" width="140" height="90" rx="8" fill="#1e293b" stroke="#64748b" stroke-width="4"/>
                <line x1="260" y1="120" x2="260" y2="210" stroke="#64748b" stroke-width="4"/>
                <line x1="300" y1="120" x2="300" y2="210" stroke="#64748b" stroke-width="4"/>
                <line x1="340" y1="120" x2="340" y2="210" stroke="#64748b" stroke-width="4"/>',
            'Lampu Jalan Padam' => '<line x1="300" y1="60" x2="300" y2="330" stroke="#475569" stroke-width="8"/>
                <path d="M260 100 Q300 50 340 100" stroke="#475569" stroke-width="6" fill="none"/>
                <circle cx="300" cy="115" r="22" fill="#1e293b" stroke="#eab308" stroke-width="4" stroke-dasharray="6,4"/>
                <polygon points="275,130 325,130 380,330 220,330" fill="#eab308" opacity="0.06"/>
                <circle cx="300" cy="330" r="40" fill="#0f172a" stroke="#334155" stroke-width="4"/>',
            'Sampah Liar' => '<polygon points="200,340 230,220 370,220 400,340" fill="#1c1917" stroke="#78716c" stroke-width="4"/>
                <path d="M190 220 L410 220 L380 190 L220 190 Z" fill="#292524" stroke="#a8a29e" stroke-width="3"/>
                <polygon points="240,320 270,250 330,250 360,320" fill="#15803d" opacity="0.7"/>
                <circle cx="280" cy="200" r="16" fill="#ca8a04" opacity="0.8"/>
                <polygon points="310,230 345,180 370,230" fill="#b91c1c" opacity="0.8"/>',
            'Trotoar Rusak' => '<polygon points="80,350 200,100 400,100 520,350" fill="#1e1e24" stroke="#3f3f46" stroke-width="4"/>
                <line x1="200" y1="180" x2="400" y2="180" stroke="#71717a" stroke-width="2"/>
                <line x1="160" y1="240" x2="440" y2="240" stroke="#71717a" stroke-width="3"/>
                <line x1="120" y1="300" x2="480" y2="300" stroke="#71717a" stroke-width="4"/>
                <polygon points="260,250 320,220 360,270 310,310" fill="#09090b" stroke="#f97316" stroke-width="4"/>',
            default => '<line x1="50" y1="320" x2="550" y2="320" stroke="#3f3f46" stroke-width="4"/>
                <line x1="120" y1="280" x2="170" y2="340" stroke="#f8fafc" stroke-width="16"/>
                <line x1="200" y1="280" x2="250" y2="340" stroke="#f8fafc" stroke-width="16"/>
                <line x1="280" y1="280" x2="330" y2="340" stroke="#f8fafc" stroke-width="16"/>
                <line x1="360" y1="280" x2="410" y2="340" stroke="#f8fafc" stroke-width="16"/>
                <polygon points="300,90 350,180 250,180" fill="#18181b" stroke="'.$accentColor.'" stroke-width="5"/>
                <line x1="300" y1="125" x2="300" y2="150" stroke="'.$accentColor.'" stroke-width="4"/>
                <circle cx="300" cy="165" r="2.5" fill="'.$accentColor.'"/>',
        };

        $escapedLabel = htmlspecialchars($shortLabel, ENT_QUOTES, 'UTF-8');
        $escapedCategory = htmlspecialchars(strtoupper($category), ENT_QUOTES, 'UTF-8');
        $pillWidth = strlen($escapedCategory) * 9 + 24;

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 400" width="100%" height="100%">
            <defs>
                <pattern id="grid" width="30" height="30" patternUnits="userSpaceOnUse">
                    <path d="M 30 0 L 0 0 0 30" fill="none" stroke="#ffffff" stroke-width="1" opacity="0.04"/>
                </pattern>
            </defs>
            <rect width="600" height="400" fill="'.$bgDark.'"/>
            <rect width="600" height="400" fill="url(#grid)"/>
            '.$vectorArt.'
            <rect x="25" y="25" width="'.$pillWidth.'" height="26" rx="5" fill="#18181b" stroke="'.$accentColor.'" stroke-width="1.5"/>
            <text x="37" y="42" fill="'.$accentColor.'" font-family="ui-monospace, monospace" font-size="11" font-weight="700" letter-spacing="1">'.$escapedCategory.'</text>
            <rect x="20" y="340" width="560" height="38" rx="6" fill="#09090b" opacity="0.85"/>
            <text x="300" y="364" fill="#f4f4f5" font-family="system-ui, -apple-system, sans-serif" font-size="14" font-weight="600" text-anchor="middle">'.$escapedLabel.'</text>
        </svg>';

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
