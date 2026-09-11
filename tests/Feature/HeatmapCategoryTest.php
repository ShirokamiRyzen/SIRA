<?php

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('halaman heatmap menampilkan filter kategori dan icon visual', function () {
    $user = User::factory()->create();

    Report::create([
        'user_id' => $user->id,
        'title' => 'Kerusakan Jalan Berlubang Parah',
        'category' => 'jalan_jembatan',
        'description' => 'Lubang jalan sedalam 15cm membahayakan pengendara motor saat malam hari.',
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
        'category' => 'drainase_saluran',
        'description' => 'Air menggenang setinggi 40cm karena saluran air mampet tersumbat sampah.',
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
    $response->assertSee('Filter Kategori Masalah');
    $response->assertSee('Jalan & Jembatan');
    $response->assertSee('Drainase & Saluran Air');
    $response->assertSee('Sampah & Kebersihan');
    $response->assertSee('Lampu Jalan & PJU');
    $response->assertSee('Lalu Lintas & Rambu');
    $response->assertSee('Trotoar & Fasilitas Difabel');
    $response->assertSee('Fasilitas Publik & Taman');
    $response->assertSee('cat-filter-btn');
    $response->assertSee('Icon Kategori Pada Peta');
});

test('endpoint heatmap geojson menyertakan metadata kategori dan icon id', function () {
    $user = User::factory()->create();

    Report::create([
        'user_id' => $user->id,
        'title' => 'Jalan Rusak Berlubang Besar',
        'category' => 'jalan_jembatan',
        'description' => 'Aspal mengelupas dan berlubang membahayakan pengguna jalan raya.',
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
    $jalanFeature = collect($features)->firstWhere('properties.category', 'jalan_jembatan');

    expect($jalanFeature)->not->toBeNull();
    expect($jalanFeature['properties']['category_label'])->toBe('Jalan & Jembatan');
    expect($jalanFeature['properties']['category_icon_id'])->toBe('cat-icon-jalan_jembatan');
    expect($jalanFeature['properties']['category_color'])->toBe('#f97316');
});

test('endpoint heatmap geojson mendukung filter query berdasarkan kategori', function () {
    $user = User::factory()->create();

    Report::create([
        'user_id' => $user->id,
        'title' => 'Saluran Air Meluap',
        'category' => 'drainase_saluran',
        'description' => 'Drainase tidak mengalir lancar dan meluap ke permukiman warga.',
        'image_base64' => 'data:image/jpeg;base64,dummy',
        'latitude' => -6.915000,
        'longitude' => 107.615000,
        'status' => 'active',
    ]);

    Report::create([
        'user_id' => $user->id,
        'title' => 'Jalan Rusak Aspal',
        'category' => 'jalan_jembatan',
        'description' => 'Jalan berlubang dalam sedalam dua puluh sentimeter membahayakan pengendara.',
        'image_base64' => 'data:image/jpeg;base64,dummy',
        'latitude' => -6.925000,
        'longitude' => 107.625000,
        'status' => 'active',
    ]);

    $response = $this->getJson(route('api.reports.heatmap', ['category' => 'drainase_saluran']));

    $response->assertOk();
    $features = $response->json('features');

    expect($features)->toHaveCount(1);
    expect($features[0]['properties']['category'])->toBe('drainase_saluran');
    expect($features[0]['properties']['title'])->toBe('Saluran Air Meluap');
});

test('pembuatan laporan baru dapat memilih kategori pemda yang diperluas', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $dummyBase64 = 'data:image/jpeg;base64,'.base64_encode('dummy_image');

    $response = $this->post(route('reports.store'), [
        'title' => 'Jalan Berlubang di Depan Puskesmas',
        'category' => 'jalan_jembatan',
        'description' => 'Lubang jalan sedalam lima belas sentimeter membahayakan warga yang berobat ke puskesmas.',
        'image_base64' => $dummyBase64,
        'latitude' => -6.850000,
        'longitude' => 107.600000,
        'city' => 'Kota Bandung',
        'district' => 'Cidadap',
    ]);

    $this->assertDatabaseHas('reports', [
        'user_id' => $user->id,
        'title' => 'Jalan Berlubang di Depan Puskesmas',
        'category' => 'jalan_jembatan',
        'district' => 'Cidadap',
    ]);

    $report = Report::where('title', 'Jalan Berlubang di Depan Puskesmas')->first();
    $response->assertRedirect(route('reports.show', $report));
});
