<?php

use App\Models\Report;
use App\Models\User;

test('default og-image route returns a valid 1200x630 png', function () {
    $response = $this->get(route('og.default'));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'image/png');

    $imageSize = getimagesizefromstring($response->getContent());
    expect($imageSize[0])->toBe(1200);
    expect($imageSize[1])->toBe(630);
});

test('report og-image route returns a valid 1200x630 png for report', function () {
    $user = User::factory()->create();

    // 1x1 transparent PNG base64
    $dummyPngBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

    $report = Report::create([
        'user_id' => $user->id,
        'title' => 'Jalan Berlubang Parah di Daerah Sukajadi',
        'description' => 'Aspal amblas sedalam 30cm membahayakan pengendara motor saat malam hari.',
        'image_base64' => $dummyPngBase64,
        'latitude' => -6.890000,
        'longitude' => 107.600000,
        'city' => 'Kota Bandung',
        'district' => 'Sukajadi',
        'rank_tier' => 'critical',
        'status' => 'active',
        'vote_score' => 45,
    ]);

    $response = $this->get(route('reports.ogImage', $report));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'image/png');

    $imageSize = getimagesizefromstring($response->getContent());
    expect($imageSize[0])->toBe(1200);
    expect($imageSize[1])->toBe(630);
});

test('homepage renders default opengraph meta tags', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('property="og:site_name"', false);
    $response->assertSee('property="og:image"', false);
    $response->assertSee(route('og.default'), false);
    $response->assertSee('name="twitter:card" content="summary_large_image"', false);
});

test('report detail page renders dynamic opengraph meta tags and canvas modal triggers', function () {
    $user = User::factory()->create();

    $report = Report::create([
        'user_id' => $user->id,
        'title' => 'Lampu PJU Padam di Jalan Riau',
        'description' => 'Sudah 1 minggu lampu penerangan jalan umum mati total sepanjang 500 meter.',
        'image_base64' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        'latitude' => -6.908000,
        'longitude' => 107.620000,
        'city' => 'Kota Bandung',
        'district' => 'Bandung Wetan',
        'rank_tier' => 'urgent',
        'status' => 'active',
        'vote_score' => 20,
    ]);

    $response = $this->get(route('reports.show', $report));

    $response->assertOk();
    $response->assertSee('property="og:title" content="[MENUNGGU RESPON] Lampu PJU Padam di Jalan Riau — SIRA"', false);
    $response->assertSee('property="og:description"', false);
    $response->assertSee('Sudah 1 minggu lampu penerangan jalan umum mati total', false);
    $response->assertSee('Status: Menunggu Respon', false);
    $response->assertSee('Lokasi: Bandung Wetan, Kota Bandung', false);
    $response->assertSee('Dukungan: +20 Suara', false);
    $response->assertSee('property="og:type" content="article"', false);
    $response->assertSee('property="og:image" content="'.route('reports.ogImage', $report).'"', false);
    $response->assertSee('name="twitter:image" content="'.route('reports.ogImage', $report).'"', false);
    $response->assertSee('id="ogCanvasModal"', false);
    $response->assertSee('id="ogCardCanvas"', false);
    $response->assertSee('openOgCanvasModal()', false);
});
