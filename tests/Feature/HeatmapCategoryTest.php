<?php

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('halaman heatmap menampilkan filter kategori dan icon visual', function () {
    $user = User::factory()->create();

    Report::create([
        'user_id' => $user->id,
        'title' => 'Insiden Kebakaran Lahan Semak Kering',
        'category' => 'kebakaran',
        'description' => 'Api berkobar di semak kering dekat perumahan.',
        'image_base64' => 'data:image/jpeg;base64,dummy',
        'latitude' => -6.914744,
        'longitude' => 107.609810,
        'city' => 'Kota Bandung',
        'district' => 'Coblong',
        'rank_tier' => 'critical',
        'status' => 'active',
    ]);

    Report::create([
        'user_id' => $user->id,
        'title' => 'Banjir Luapan Drainase',
        'category' => 'bencana_alam',
        'description' => 'Air menggenang setinggi 40cm.',
        'image_base64' => 'data:image/jpeg;base64,dummy',
        'latitude' => -6.920000,
        'longitude' => 107.610000,
        'city' => 'Kota Bandung',
        'district' => 'Regol',
        'rank_tier' => 'urgent',
        'status' => 'active',
    ]);

    $response = $this->get(route('heatmap.index'));

    $response->assertOk();
    $response->assertSee('Filter Kategori Laporan');
    $response->assertSee('Kebakaran');
    $response->assertSee('Infrastruktur Rusak');
    $response->assertSee('Bencana Alam');
    $response->assertSee('Lampu & Kelistrikan');
    $response->assertSee('Sampah & Lingkungan');
    $response->assertSee('Fasilitas Umum');
    $response->assertSee('cat-filter-btn');
    $response->assertSee('Icon Kategori Pada Peta');
});

test('endpoint heatmap geojson menyertakan metadata kategori dan icon id', function () {
    $user = User::factory()->create();

    Report::create([
        'user_id' => $user->id,
        'title' => 'Kebakaran Gudang Rongsok',
        'category' => 'kebakaran',
        'description' => 'Api membesar di area gudang.',
        'image_base64' => 'data:image/jpeg;base64,dummy',
        'latitude' => -6.915000,
        'longitude' => 107.615000,
        'city' => 'Kota Bandung',
        'district' => 'Sumur Bandung',
        'rank_tier' => 'critical',
        'vote_score' => 110,
        'status' => 'active',
    ]);

    $response = $this->getJson(route('api.reports.heatmap'));

    $response->assertOk();
    $response->assertJsonStructure([
        'type',
        'features' => [
            '*' => [
                'type',
                'geometry' => ['type', 'coordinates'],
                'properties' => [
                    'id',
                    'title',
                    'category',
                    'category_label',
                    'category_symbol',
                    'category_color',
                    'category_icon_id',
                    'category_badge_class',
                    'weight',
                    'rank_tier',
                ],
            ],
        ],
    ]);

    $features = $response->json('features');
    $kebakaranFeature = collect($features)->firstWhere('properties.category', 'kebakaran');

    expect($kebakaranFeature)->not->toBeNull();
    expect($kebakaranFeature['properties']['category_label'])->toBe('Kebakaran');
    expect($kebakaranFeature['properties']['category_icon_id'])->toBe('cat-icon-kebakaran');
    expect($kebakaranFeature['properties']['category_color'])->toBe('#ef4444');
});

test('endpoint heatmap geojson mendukung filter query berdasarkan kategori', function () {
    $user = User::factory()->create();

    Report::create([
        'user_id' => $user->id,
        'title' => 'Kebakaran Lahan',
        'category' => 'kebakaran',
        'description' => 'Kebakaran di kebun warga.',
        'image_base64' => 'data:image/jpeg;base64,dummy',
        'latitude' => -6.915000,
        'longitude' => 107.615000,
        'status' => 'active',
    ]);

    Report::create([
        'user_id' => $user->id,
        'title' => 'Jalan Rusak Aspal',
        'category' => 'infrastruktur',
        'description' => 'Jalan berlubang dalam.',
        'image_base64' => 'data:image/jpeg;base64,dummy',
        'latitude' => -6.925000,
        'longitude' => 107.625000,
        'status' => 'active',
    ]);

    $response = $this->getJson(route('api.reports.heatmap', ['category' => 'kebakaran']));

    $response->assertOk();
    $features = $response->json('features');

    expect($features)->toHaveCount(1);
    expect($features[0]['properties']['category'])->toBe('kebakaran');
    expect($features[0]['properties']['title'])->toBe('Kebakaran Lahan');
});

test('pembuatan laporan baru dapat memilih kategori seperti kebakaran atau bencana alam', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $dummyBase64 = 'data:image/jpeg;base64,'.base64_encode('dummy_image');

    $response = $this->post(route('reports.store'), [
        'title' => 'Tanah Longsor Menutup Badan Jalan',
        'category' => 'bencana_alam',
        'description' => 'Longsor tebing menutupi separuh jalan raya saat hujan deras.',
        'image_base64' => $dummyBase64,
        'latitude' => -6.850000,
        'longitude' => 107.600000,
        'city' => 'Kota Bandung',
        'district' => 'Cidadap',
    ]);

    $this->assertDatabaseHas('reports', [
        'user_id' => $user->id,
        'title' => 'Tanah Longsor Menutup Badan Jalan',
        'category' => 'bencana_alam',
        'district' => 'Cidadap',
    ]);

    $report = Report::where('title', 'Tanah Longsor Menutup Badan Jalan')->first();
    $response->assertRedirect(route('reports.show', $report));
});
