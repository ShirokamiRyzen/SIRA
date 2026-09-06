<?php

use App\Models\Report;
use App\Models\ReportComment;
use App\Models\ReportVote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('pengguna dapat mendaftar dan login menggunakan username dan password', function () {
    // 1. Registrasi
    $registerResponse = $this->post('/register', [
        'name' => 'Warga Bandung',
        'username' => 'wargabandung',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $registerResponse->assertRedirect(route('reports.index'));
    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'username' => 'wargabandung',
    ]);

    // 2. Logout
    $this->post('/logout')->assertRedirect(route('reports.index'));
    $this->assertGuest();

    // 3. Login kembali
    $loginResponse = $this->post('/login', [
        'username' => 'wargabandung',
        'password' => 'password123',
    ]);

    $loginResponse->assertRedirect(route('reports.index'));
    $this->assertAuthenticated();
});

test('halaman registrasi menampilkan input nama lengkap, username, dan password tanpa email', function () {
    $response = $this->get('/register');

    $response->assertSuccessful();
    $response->assertSee('Nama Lengkap');
    $response->assertSee('Username');
    $response->assertSee('Kata Sandi');
    $response->assertSee('Konfirmasi Kata Sandi');
    $response->assertDontSee('Alamat Email');
});

test('pengguna terautentikasi dapat membuat laporan baru dengan foto base64 dan koordinat', function () {
    $user = User::factory()->create([
        'username' => 'pelapor_01',
    ]);

    $this->actingAs($user);

    $dummyBase64 = 'data:image/jpeg;base64,'.base64_encode('dummy_compressed_image_bytes_80_percent');

    $response = $this->post(route('reports.store'), [
        'title' => 'Jalan Berlubang di Dekat Simpang Dago',
        'description' => 'Lubang sedalam 15cm sangat membahayakan pengendara motor di malam hari.',
        'image_base64' => $dummyBase64,
        'latitude' => -6.885123,
        'longitude' => 107.613456,
        'city' => 'Kota Bandung',
        'district' => 'Coblong',
        'subdistrict' => 'Lebakgede',
        'formatted_address' => 'Jl. Ir. H. Juanda No. 100, Coblong, Kota Bandung',
    ]);

    $this->assertDatabaseHas('reports', [
        'user_id' => $user->id,
        'title' => 'Jalan Berlubang di Dekat Simpang Dago',
        'district' => 'Coblong',
        'city' => 'Kota Bandung',
        'rank_tier' => 'normal',
        'status' => 'active',
    ]);

    $report = Report::where('title', 'Jalan Berlubang di Dekat Simpang Dago')->first();
    $response->assertRedirect(route('reports.show', $report));
});

test('voting like dan dislike menaikkan skor dan rank tier laporan', function () {
    $author = User::factory()->create(['username' => 'author_report']);
    $voter = User::factory()->create(['username' => 'voter_01']);

    $report = Report::create([
        'user_id' => $author->id,
        'title' => 'Tumpukan Sampah Liar',
        'description' => 'Sampah menumpuk di pinggir jalan selama 3 hari.',
        'image_base64' => 'data:image/jpeg;base64,dummy',
        'latitude' => -6.914744,
        'longitude' => 107.609810,
        'city' => 'Kota Bandung',
        'district' => 'Sumur Bandung',
        'rank_tier' => 'normal',
        'status' => 'active',
    ]);

    $this->actingAs($voter);

    // Berikan Like (+1) via AJAX request
    $response = $this->postJson(route('reports.vote', $report), [
        'value' => 1,
    ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'user_vote' => 1,
            'upvotes_count' => 1,
            'vote_score' => 1,
        ]);

    $this->assertDatabaseHas('report_votes', [
        'report_id' => $report->id,
        'user_id' => $voter->id,
        'value' => 1,
    ]);

    $report->refresh();
    expect($report->vote_score)->toBe(1);

    // Klik tombol like lagi -> unvote (batalkan vote)
    $responseUnvote = $this->postJson(route('reports.vote', $report), [
        'value' => 1,
    ]);

    $responseUnvote->assertOk()
        ->assertJson([
            'success' => true,
            'user_vote' => 0,
            'vote_score' => 0,
        ]);

    $this->assertDatabaseMissing('report_votes', [
        'report_id' => $report->id,
        'user_id' => $voter->id,
    ]);

    // Test eskalasi rank_tier otomatis saat vote mencapai threshold
    // Simulasikan 12 upvotes
    for ($i = 0; $i < 12; $i++) {
        $u = User::factory()->create(['username' => "user_voter_{$i}"]);
        ReportVote::create([
            'report_id' => $report->id,
            'user_id' => $u->id,
            'value' => 1,
        ]);
    }

    $report->recalculateVoteStatsAndTier();
    $report->refresh();

    expect($report->vote_score)->toBe(12);
    expect($report->rank_tier)->toBe('trending');
});

test('fitur nested comment mendukung komentar utama dan balasan berjenjang', function () {
    $author = User::factory()->create(['username' => 'pelapor_top']);
    $commenter1 = User::factory()->create(['username' => 'warga_1']);
    $commenter2 = User::factory()->create(['username' => 'warga_2']);

    $report = Report::create([
        'user_id' => $author->id,
        'title' => 'Pohon Tumbang',
        'description' => 'Pohon tumbang menghalangi jalan raya.',
        'image_base64' => 'data:image/jpeg;base64,dummy',
        'latitude' => -6.914744,
        'longitude' => 107.609810,
        'rank_tier' => 'normal',
    ]);

    // 1. Komentar Utama (Parent)
    $this->actingAs($commenter1);
    $this->post(route('comments.store', $report), [
        'content' => 'Sudah saya laporkan juga ke dinas pertamanan setempat.',
    ])->assertRedirect();

    $rootComment = ReportComment::where('report_id', $report->id)->whereNull('parent_id')->first();
    expect($rootComment)->not->toBeNull();
    expect($rootComment->content)->toBe('Sudah saya laporkan juga ke dinas pertamanan setempat.');

    // 2. Balasan (Nested Reply)
    $this->actingAs($commenter2);
    $this->post(route('comments.store', $report), [
        'parent_id' => $rootComment->id,
        'content' => 'Terima kasih informasinya! Petugas sedang meluncur.',
    ])->assertRedirect();

    $replyComment = ReportComment::where('parent_id', $rootComment->id)->first();
    expect($replyComment)->not->toBeNull();
    expect($replyComment->user_id)->toBe($commenter2->id);

    // Cek relasi nested comment di model
    expect($rootComment->replies)->toHaveCount(1);
    expect($rootComment->replies->first()->content)->toBe('Terima kasih informasinya! Petugas sedang meluncur.');

    // Cek counter cache comments_count pada laporan
    $report->refresh();
    expect($report->comments_count)->toBe(2);
});

test('endpoint heatmap geojson mengembalikan data titik dan bobot untuk openfreemap', function () {
    $user = User::factory()->create(['username' => 'tester_map']);

    Report::create([
        'user_id' => $user->id,
        'title' => 'Banjir Cileuncang',
        'description' => 'Genangan air setinggi 30cm.',
        'image_base64' => 'data:image/jpeg;base64,dummy',
        'latitude' => -6.920000,
        'longitude' => 107.610000,
        'city' => 'Kota Bandung',
        'district' => 'Regol',
        'rank_tier' => 'critical',
        'vote_score' => 120,
        'status' => 'active',
    ]);

    $response = $this->getJson(route('api.reports.heatmap'));

    $response->assertOk()
        ->assertJsonStructure([
            'type',
            'features' => [
                '*' => [
                    'type',
                    'geometry' => [
                        'type',
                        'coordinates',
                    ],
                    'properties' => [
                        'id',
                        'title',
                        'weight',
                        'vote_score',
                        'rank_tier',
                        'city',
                        'district',
                        'url',
                    ],
                ],
            ],
        ]);

    $features = $response->json('features');
    expect($features)->not->toBeEmpty();
    // GeoJSON: coordinates = [lng, lat]
    expect($features[0]['geometry']['coordinates'][0])->toEqualWithDelta(107.610000, 0.0001);
    expect($features[0]['geometry']['coordinates'][1])->toEqualWithDelta(-6.920000, 0.0001);
});

test('hanya pembuat post yang dapat mengubah status laporan menjadi resolved', function () {
    $author = User::factory()->create(['username' => 'pembuat_post_01']);
    $otherUser = User::factory()->create(['username' => 'orang_lain_01']);

    $report = Report::create([
        'user_id' => $author->id,
        'title' => 'Lampu Merah Mati',
        'description' => 'Lampu lalu lintas padam.',
        'image_base64' => 'data:image/jpeg;base64,dummy',
        'latitude' => -6.900000,
        'longitude' => 107.600000,
        'status' => 'active',
        'rank_tier' => 'normal',
    ]);

    // 1. Orang lain mencoba mengubah status -> 403 Forbidden
    $this->actingAs($otherUser);
    $forbiddenResponse = $this->patchJson(route('reports.updateStatus', $report), [
        'status' => 'resolved',
    ]);
    $forbiddenResponse->assertForbidden();
    expect($report->fresh()->status)->toBe('active');

    // 2. Pembuat post mengubah status menjadi resolved -> Berhasil
    $this->actingAs($author);
    $successResponse = $this->patchJson(route('reports.updateStatus', $report), [
        'status' => 'resolved',
    ]);
    $successResponse->assertOk()
        ->assertJson([
            'success' => true,
            'status' => 'resolved',
        ]);
    expect($report->fresh()->status)->toBe('resolved');

    // 3. Pembuat post dapat membuka kembali laporan (reopen ke active)
    $reopenResponse = $this->patchJson(route('reports.updateStatus', $report), [
        'status' => 'active',
    ]);
    $reopenResponse->assertOk()
        ->assertJson([
            'success' => true,
            'status' => 'active',
        ]);
    expect($report->fresh()->status)->toBe('active');
});

test('komentar dapat dikirim melalui AJAX tanpa reload halaman', function () {
    $author = User::factory()->create(['username' => 'creator_ajax']);
    $commenter = User::factory()->create(['username' => 'commenter_ajax']);

    $report = Report::create([
        'user_id' => $author->id,
        'title' => 'Jalan Rusak Parah',
        'description' => 'Aspal mengelupas.',
        'image_base64' => 'data:image/jpeg;base64,dummy',
        'latitude' => -6.900000,
        'longitude' => 107.600000,
        'status' => 'active',
        'rank_tier' => 'normal',
    ]);

    $this->actingAs($commenter);

    $response = $this->postJson(route('comments.store', $report), [
        'content' => 'Komentar via AJAX tanpa refresh.',
    ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'comments_count' => 1,
        ])
        ->assertJsonStructure([
            'comment_html',
            'comment_id',
        ]);

    expect($report->fresh()->comments_count)->toBe(1);
    $this->assertDatabaseHas('report_comments', [
        'report_id' => $report->id,
        'user_id' => $commenter->id,
        'content' => 'Komentar via AJAX tanpa refresh.',
    ]);
});

test('menandai bot @Sira di komentar memicu respon otomatis AI', function () {
    // Mock OpenAI API Response
    Http::fake([
        '*/chat/completions' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'content' => 'Ini adalah ringkasan resmi dari SIRA AI Assistant terkait laporan ini.',
                    ],
                ],
            ],
        ], 200),
    ]);

    $author = User::factory()->create(['username' => 'author_ai_test']);
    $commenter = User::factory()->create(['username' => 'warga_ai_tester']);

    $report = Report::create([
        'user_id' => $author->id,
        'title' => 'Jembatan Ambrol',
        'description' => 'Jembatan retak dan ambrol di bagian pondasi.',
        'image_base64' => 'data:image/jpeg;base64,dummy',
        'latitude' => -6.900000,
        'longitude' => 107.600000,
        'status' => 'active',
        'rank_tier' => 'urgent',
    ]);

    $this->actingAs($commenter);

    // 1. User mengirim komentar: langsung terkirim secara instan (tidak menunggu AI, no-lag)
    $response = $this->postJson(route('comments.store', $report), [
        'content' => 'Halo @Sira tolong hitung estimasi biaya dengan formula $f(x) = x^2$ terkait laporan ini.',
    ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'has_ai_mention' => true,
        ])
        ->assertJsonStructure([
            'comment_html',
            'comment_id',
        ]);

    $userCommentId = $response->json('comment_id');

    // Komentar user langsung tersimpan di database
    $this->assertDatabaseHas('report_comments', [
        'id' => $userCommentId,
        'report_id' => $report->id,
        'user_id' => $commenter->id,
    ]);

    // 2. Client secara asynchronous memanggil endpoint AI reply
    $userComment = ReportComment::find($userCommentId);
    $aiResponse = $this->postJson(route('comments.aiReply', [$report, $userComment]));

    $aiResponse->assertOk()
        ->assertJson([
            'success' => true,
        ])
        ->assertJsonStructure([
            'ai_comment_html',
            'ai_comment_id',
            'comments_count',
        ]);

    // Verifikasi bot @Sira dibuat dan membalas komentar
    $siraBot = User::where('username', 'Sira')->first();
    expect($siraBot)->not->toBeNull();

    $this->assertDatabaseHas('report_comments', [
        'report_id' => $report->id,
        'user_id' => $siraBot->id,
        'content' => 'Ini adalah ringkasan resmi dari SIRA AI Assistant terkait laporan ini.',
    ]);

    expect($report->fresh()->comments_count)->toBe(2);
});

test('voting mempertahankan skor yang sudah ada di database dan tidak mereset ke 0', function () {
    $author = User::factory()->create(['username' => 'post_author']);
    $voter1 = User::factory()->create(['username' => 'seeded_voter_1']);
    $voter2 = User::factory()->create(['username' => 'seeded_voter_2']);
    $newUser = User::factory()->create(['username' => 'new_voter_user']);

    $report = Report::create([
        'user_id' => $author->id,
        'title' => 'Laporan Populer dari Seeder',
        'description' => 'Laporan dengan banyak dukungan warga.',
        'image_base64' => 'data:image/jpeg;base64,dummy',
        'latitude' => -6.900000,
        'longitude' => 107.600000,
        'status' => 'active',
        'rank_tier' => 'normal',
    ]);

    // Simulasikan data dari seeder: 2 vote upvote ada di report_votes
    ReportVote::create(['report_id' => $report->id, 'user_id' => $voter1->id, 'value' => 1]);
    ReportVote::create(['report_id' => $report->id, 'user_id' => $voter2->id, 'value' => 1]);
    $report->recalculateVoteStatsAndTier();
    $report->refresh();

    expect($report->upvotes_count)->toBe(2);
    expect($report->vote_score)->toBe(2);

    // User baru melakukan upvote via AJAX
    $this->actingAs($newUser);
    $response = $this->postJson(route('reports.vote', $report), [
        'value' => 1,
    ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'upvotes_count' => 3,
            'vote_score' => 3,
        ]);

    $report->refresh();
    expect($report->upvotes_count)->toBe(3);
    expect($report->vote_score)->toBe(3);
});

test('endpoint mention suggestion mengembalikan saran akun termasuk bot @Sira saat mengetik @S', function () {
    User::firstOrCreate(
        ['username' => 'Sira'],
        [
            'name' => 'SIRA AI Assistant',
            'email' => 'ai@sira.local',
            'password' => bcrypt('password'),
        ]
    );

    User::factory()->create([
        'username' => 'slamet_asphalt',
        'name' => 'Slamet Permadi',
    ]);

    // 1. Query @ kosong (hanya @) hanya memunculkan bot @Sira
    $responseEmpty = $this->getJson(route('api.users.mention', ['q' => '']));
    $responseEmpty->assertOk();
    $usersEmpty = $responseEmpty->json('users');
    expect($usersEmpty)->toHaveCount(1);
    expect($usersEmpty[0]['username'])->toBe('Sira');
    expect($usersEmpty[0]['is_ai'])->toBeTrue();

    // 2. Query < 3 karakter yang bukan Sira (misal 'sl') tidak memunculkan user lain
    $responseShort = $this->getJson(route('api.users.mention', ['q' => 'sl']));
    $responseShort->assertOk();
    expect($responseShort->json('users'))->toBeEmpty();

    // 3. Query < 3 karakter yang cocok dengan Sira (misal 'S') hanya memunculkan Sira
    $responseS = $this->getJson(route('api.users.mention', ['q' => 'S']));
    $responseS->assertOk();
    $usersS = $responseS->json('users');
    expect($usersS)->toHaveCount(1);
    expect($usersS[0]['username'])->toBe('Sira');

    // 4. Query >= 3 karakter (misal 'slamet') menemukan pengguna lain (slamet_asphalt)
    $responseSlamet = $this->getJson(route('api.users.mention', ['q' => 'slamet']));
    $responseSlamet->assertOk();
    $usernames = array_column($responseSlamet->json('users'), 'username');
    expect($usernames)->toContain('slamet_asphalt');
});

test('pengguna menerima notifikasi saat dimention atau dibalas oleh pengguna lain dan melihat badge di header', function () {
    $userA = User::factory()->create(['username' => 'budi_santoso']);
    $userB = User::factory()->create(['username' => 'andi_wijaya']);

    $report = Report::create([
        'user_id' => $userA->id,
        'title' => 'Tumpukan Sampah Liar',
        'description' => 'Sampah menumpuk di pinggir jalan.',
        'image_base64' => 'data:image/jpeg;base64,dummy',
        'latitude' => -6.914744,
        'longitude' => 107.609810,
        'city' => 'Kota Bandung',
        'district' => 'Sumur Bandung',
        'rank_tier' => 'normal',
        'status' => 'active',
    ]);

    // 1. User B menulis komentar yang menyebut @budi_santoso
    $this->actingAs($userB)->postJson(route('comments.store', $report), [
        'content' => 'Halo @budi_santoso tolong dicek kondisi jalan ini!',
    ])->assertOk();

    // Pastikan user A menerima notifikasi mention
    expect($userA->unreadNotifications()->count())->toBe(1);
    $notification = $userA->unreadNotifications()->first();
    expect($notification->data['type'])->toBe('mention');
    expect($notification->data['sender_username'])->toBe('andi_wijaya');

    // 2. User A melihat badge notifikasi di header
    $responseHeader = $this->actingAs($userA)->get(route('reports.index'));
    $responseHeader->assertOk();
    $responseHeader->assertSee('notificationBadge');
    $responseHeader->assertSee('notificationBellBtn');

    // 3. User A membalas komentar user B (reply)
    $commentB = ReportComment::where('user_id', $userB->id)->first();
    $this->actingAs($userA)->postJson(route('comments.store', $report), [
        'content' => 'Siap, segera saya cek.',
        'parent_id' => $commentB->id,
    ])->assertOk();

    // Pastikan user B menerima notifikasi reply
    expect($userB->unreadNotifications()->count())->toBe(1);
    $replyNotif = $userB->unreadNotifications()->first();
    expect($replyNotif->data['type'])->toBe('reply');

    // 4. Endpoint list notifikasi JSON & tandai dibaca
    $notifListResponse = $this->actingAs($userA)->getJson(route('notifications.index'));
    $notifListResponse->assertOk()
        ->assertJsonPath('unread_count', 1);

    // Tandai semua dibaca
    $this->actingAs($userA)->postJson(route('notifications.markAllAsRead'))->assertOk();
    expect($userA->fresh()->unreadNotifications()->count())->toBe(0);

    // 5. User B mengklik notifikasi via GET link (mark as read & redirect ke laporan)
    $notificationB = $userB->unreadNotifications()->first();
    expect($notificationB)->not->toBeNull();
    $readResponse = $this->actingAs($userB)->get(route('notifications.markAsRead', ['id' => $notificationB->id, 'redirect' => 1]));
    $readResponse->assertRedirect();
    expect($userB->fresh()->unreadNotifications()->count())->toBe(0);

    // 6. Test delete per notifikasi (DELETE /notifications/{id})
    $deleteResponse = $this->actingAs($userB)->deleteJson(route('notifications.destroy', $notificationB->id));
    $deleteResponse->assertOk()
        ->assertJson(['success' => true, 'total_count' => 0]);
    expect($userB->fresh()->notifications()->count())->toBe(0);

    // 7. Test clear-all notifikasi (POST /notifications/clear-all)
    expect($userA->notifications()->count())->toBeGreaterThan(0);
    $clearResponse = $this->actingAs($userA)->postJson(route('notifications.clearAll'));
    $clearResponse->assertOk()
        ->assertJson(['success' => true, 'total_count' => 0, 'unread_count' => 0]);
    expect($userA->fresh()->notifications()->count())->toBe(0);
});

test('endpoint stream notifikasi realtime mengembalikan response text/event-stream untuk pengguna login', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('notifications.stream'));
    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toContain('text/event-stream');
});

test('laporan yang masih aktif menampilkan badge waktu berapa lama belum diproses dari awal upload', function () {
    $user = User::factory()->create();

    $activeReport = Report::create([
        'user_id' => $user->id,
        'title' => 'Jalan Rusak Berlubang Parah',
        'description' => 'Kerusakan jalan aspal di persimpangan utama.',
        'image_base64' => 'data:image/jpeg;base64,dummy',
        'latitude' => -6.914744,
        'longitude' => 107.609810,
        'city' => 'Kota Bandung',
        'district' => 'Sumur Bandung',
        'rank_tier' => 'normal',
        'status' => 'active',
    ]);
    $activeReport->created_at = now()->subDays(3);
    $activeReport->save();

    expect($activeReport->pending_duration)->toBe('3 hari');

    // Cek di halaman index/dashboard
    $indexResponse = $this->get(route('reports.index'));
    $indexResponse->assertOk();
    $indexResponse->assertSee('3 hari belum diproses');

    // Cek di halaman detail laporan
    $showResponse = $this->get(route('reports.show', $activeReport));
    $showResponse->assertOk();
    $showResponse->assertSee('3 hari belum diproses');
});

test('dasbor laporan menampilkan pagination di bagian atas dan bawah daftar laporan ketika data melebihi batas per halaman', function () {
    $user = User::factory()->create();

    for ($i = 1; $i <= 12; $i++) {
        Report::create([
            'user_id' => $user->id,
            'title' => 'Laporan Fasilitas Ke-'.$i,
            'description' => 'Deskripsi laporan uji fasilitas ke-'.$i,
            'image_base64' => 'data:image/jpeg;base64,dummy',
            'latitude' => -6.914744,
            'longitude' => 107.609810,
            'city' => 'Kota Bandung',
            'district' => 'Sumur Bandung',
            'rank_tier' => 'normal',
            'status' => 'active',
        ]);
    }

    $response = $this->get(route('reports.index'));

    $response->assertOk();
    $response->assertSee('Navigasi Halaman');

    // Memastikan bar navigasi pagination muncul di bagian atas dan bawah (2 kali)
    $content = $response->getContent();
    $matchesCount = substr_count($content, 'aria-label="Navigasi Halaman"');
    expect($matchesCount)->toBe(2);
});

test('halaman detail laporan menyediakan tautan untuk membuka titik koordinat di heatmap', function () {
    $user = User::factory()->create();

    $report = Report::create([
        'user_id' => $user->id,
        'title' => 'Pipa PDAM Bocor di Jalan Merdeka',
        'description' => 'Genangan air akibat kebocoran pipa air bersih.',
        'image_base64' => 'data:image/jpeg;base64,dummy',
        'latitude' => -6.914744,
        'longitude' => 107.609810,
        'city' => 'Kota Bandung',
        'district' => 'Sumur Bandung',
        'rank_tier' => 'normal',
        'status' => 'active',
    ]);

    $response = $this->get(route('reports.show', $report));

    $response->assertOk();
    $response->assertSee('Titik Koordinat Peta');
    $response->assertSee('Buka di Heatmap');
    $response->assertSee('/heatmap?lat='.$report->latitude);
    $response->assertSee('report_id='.$report->id);
});

test('halaman heatmap memproses parameter koordinat dan menampilkan elemen fokus titik laporan', function () {
    $user = User::factory()->create();

    $report = Report::create([
        'user_id' => $user->id,
        'title' => 'Lampu Jalan Padam di Coblong',
        'description' => 'Lampu penerangan jalan padam saat malam hari.',
        'image_base64' => 'data:image/jpeg;base64,dummy',
        'latitude' => -6.890123,
        'longitude' => 107.612345,
        'city' => 'Kota Bandung',
        'district' => 'Coblong',
        'rank_tier' => 'trending',
        'status' => 'active',
    ]);

    $response = $this->get(route('heatmap.index', [
        'lat' => $report->latitude,
        'lng' => $report->longitude,
        'report_id' => $report->id,
    ]));

    $response->assertOk();
    $response->assertSee('focusReportBanner');
    $response->assertSee('btnResetFocus');
});
