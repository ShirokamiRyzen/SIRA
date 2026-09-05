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
