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
            [
                'tag' => 'Wisata Aspal',
                'title' => 'Wahana Wisata Kolam Lele Baru di Tengah Aspal Jalan',
                'desc' => 'Pemerintah sangat visioner! Lubang jalan berdiameter 1 meter ini sengaja dibiarkan berbulan-bulan tanpa tambalan agar warga bisa budidaya ikan lele mandiri demi ketahanan pangan lokal. Terima kasih atas wahana rekreasi air gratisnya.',
            ],
            [
                'tag' => 'Monumen Gelap',
                'title' => 'Monumen Tiang Lampu Estetik Futuristik Tanpa Aliran Listrik',
                'desc' => 'Desain tiang lampunya sangat artistik bergaya victoria eropa, sayangnya tidak pernah menyala sekalipun sejak seremoni gunting pita 8 bulan lalu. Sangat cocok untuk menguji nyali uji keberanian warga di malam hari.',
            ],
            [
                'tag' => 'Hak Pejalan Kaki',
                'title' => 'Trotoar Khusus Parkir Mobil Mewah dan Gerobak Gorengan',
                'desc' => 'Trotoar yang dibangun dengan anggaran miliaran rupiah ini sangat ramah pejalan kaki, asalkan pejalan kakinya mampu melompati kap mobil dan uap minyak panas gorengan. Pejalan kaki mohon tahu diri jangan mengganggu lapak parkir.',
            ],
            [
                'tag' => 'Arung Jeram Gratis',
                'title' => 'Wahana Waterboom Alami Setiap Hujan Turun Lebih dari 15 Menit',
                'desc' => 'Sistem drainase dirancang istimewa agar air tidak lekas surut ke laut, melainkan memanjakan warga dengan sensasi arung jeram setinggi lutut di ruang tamu masing-masing.',
            ],
            [
                'tag' => 'Proyek Abadi',
                'title' => 'Proyek Gali Tutup Gali Aspal Abadi Tiada Henti',
                'desc' => 'Minggu lalu baru selesai diaspal mulus dengan bangga, minggu ini digali lagi buat kabel, minggu depan rencana digali pipa air. Sebuah siklus ekonomi sirkular tiada tara demi kelangsungan hidup para vendor tersayang.',
            ],
            [
                'tag' => 'Uji Adrenalin',
                'title' => 'Zebra Cross Khusus Pejalan Kaki yang Memiliki Nyawa Cadangan',
                'desc' => 'Marka penyeberangan diletakkan tepat di tikungan buta tanpa rambu peringatan. Didedikasikan khusus untuk warga yang sudah siap menjemput ajal dan tidak takut ditabrak truk tronton.',
            ],
            [
                'tag' => 'Pohon Keramat',
                'title' => 'Pohon Rindang Penjaga Tradisi Belum Dipangkas Sejak Musim Lalu',
                'desc' => 'Dahan pohon tua sudah melengkung anggun menyentuh kabel optik dan kepala sopir pick-up. Warga sengaja tidak memangkas karena masih menunggu dinas terkait menyelesaikan rapat koordinasi lintas sektoral jilid ke-4.',
            ],
            [
                'tag' => 'Kearifan Lokal',
                'title' => 'Tutup Manhole Gorong-Gorong Diganti Ranting Pohon Beringin Estetik',
                'desc' => 'Tutup besi gorong-gorong lenyap entah ke mana. Sebagai gantinya, warga gotong royong menancapkan ranting pohon lengkap dengan plastik kresek merah berkibar sebagai sensor peringatan ultra-canggih.',
            ],
            [
                'tag' => 'Sauna Alam',
                'title' => 'Jembatan Penyeberangan Orang (JPO) Konsep Sauna Tropis Terbuka',
                'desc' => 'Atap jembatan penyeberangan sengaja dibiarkan bolong-bolong agar warga bisa berjemur menyerap vitamin D saat siang bolong dan menikmati mandi air hujan alami saat petang.',
            ],
            [
                'tag' => 'Latihan Survival',
                'title' => 'Taman Bermain Anak Berkonsep Survival Gladiator Extrim',
                'desc' => 'Perosotan berkarat dengan ujung seng tajam dan ayunan rantai putus satu. Sangat mendidik karakter anak-anak sejak usia dini agar tangguh menghadapi kejamnya dunia nyata.',
            ],
            [
                'tag' => 'Halte Ghaib',
                'title' => 'Halte Bus Mewah Tempat Tidur Siang Nyaman Tanpa Ada Bus yang Lewat',
                'desc' => 'Sebuah mahakarya infrastruktur transportasi publik: halte megah ber-AC alami tanpa ada satupun trayek bus yang melintas selama 2 tahun terakhir.',
            ],
            [
                'tag' => 'Sensasi Bromo',
                'title' => 'Timbunan Debu Vulkanik Proyek yang Tak Kunjung Dibereskan',
                'desc' => 'Warga sekitar kini tidak perlu jauh-jauh liburan ke Gunung Bromo untuk merasakan sensasi badai debu pasir. Cukup duduk manis di teras rumah sambil batuk berjamaah.',
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
        // 6. Palette Gambar Base64
        // -------------------------------------------------------------
        $base64Images = [
            'data:image/svg+xml;base64,'.base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 400" width="100%" height="100%"><rect width="600" height="400" fill="#1e293b"/><path d="M50 300 Q150 250 300 290 T550 310" stroke="#334155" stroke-width="14" fill="none"/><circle cx="280" cy="270" r="50" fill="#0f172a" stroke="#ef4444" stroke-width="6"/><text x="280" y="370" fill="#f8fafc" font-family="monospace" font-size="18" font-weight="bold" text-anchor="middle">Wisata Kolam Aspal Bersejarah</text></svg>'),
            'data:image/svg+xml;base64,'.base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 400" width="100%" height="100%"><rect width="600" height="400" fill="#14532d"/><polygon points="200,320 250,220 350,220 400,320" fill="#166534"/><circle cx="300" cy="180" r="45" fill="#f59e0b"/><text x="300" y="370" fill="#ffffff" font-family="monospace" font-size="18" font-weight="bold" text-anchor="middle">Sampah Estetik Pembatas Jalan</text></svg>'),
            'data:image/svg+xml;base64,'.base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 400" width="100%" height="100%"><rect width="600" height="400" fill="#0f172a"/><line x1="300" y1="50" x2="300" y2="300" stroke="#475569" stroke-width="10"/><circle cx="300" cy="70" r="28" fill="#38bdf8" opacity="0.2"/><text x="300" y="370" fill="#cbd5e1" font-family="monospace" font-size="18" font-weight="bold" text-anchor="middle">Monumen Tiang Gelap Gulita</text></svg>'),
            'data:image/svg+xml;base64,'.base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 400" width="100%" height="100%"><rect width="600" height="400" fill="#1e3a8a"/><rect x="0" y="220" width="600" height="180" fill="#1d4ed8"/><text x="300" y="150" fill="#ffffff" font-family="monospace" font-size="22" font-weight="bold" text-anchor="middle">Waterboom Alami Depan Teras</text></svg>'),
            'data:image/svg+xml;base64,'.base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 400" width="100%" height="100%"><rect width="600" height="400" fill="#3b0764"/><path d="M100 350 L300 120 L500 350 Z" fill="#581c87"/><rect x="280" y="280" width="40" height="100" fill="#713f12"/><text x="300" y="80" fill="#f3e8ff" font-family="monospace" font-size="18" font-weight="bold" text-anchor="middle">Pohon Keramat Penunggu Kabel</text></svg>'),
        ];

        // -------------------------------------------------------------
        // 7. Generate 1.000 Laporan Dummy Satir
        // -------------------------------------------------------------
        $this->command?->info('Menyiapkan 1.000 data laporan dummy satir...');

        $reportsData = [];
        $reportMeta = [];
        $targetCount = 1000;

        for ($i = 1; $i <= $targetCount; $i++) {
            $cluster = $clusters[array_rand($clusters)];
            $template = $satiricalTemplates[array_rand($satiricalTemplates)];
            $authorId = $allUserIds[array_rand($allUserIds)];
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
                    'content' => "🤖 **Ringkasan SIRA AI:**\n\n• **Masalah Utama:** {$rep->title}.\n• **Status Aspirasi:** Masuk dalam kategori **{$rep->rank_tier} tier** dengan total **{$rep->upvotes_count} dukungan warga**.\n• **Sentimen Diskusi:** Warga menyoroti dampak langsung di lapangan dan lambatnya respon dinas terkait, sementara klarifikasi formal meminta warga bersabar menunggu siklus anggaran.\n• **Rekomendasi:** Perlu percepatan audit lapangan oleh dinas terkait serta transparansi jadwal pengerjaan perbaikan fisik di lokasi.",
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
}
