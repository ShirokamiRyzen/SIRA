<?php

use App\Models\User;

test('report create page renders camera and gallery options', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('reports.create'));

    $response->assertOk();
    $response->assertSee('btnTriggerCamera');
    $response->assertSee('btnTriggerGallery');
    $response->assertSee('galleryInput');
    $response->assertSee('cameraInput');
    $response->assertSee('webcamModal');
    $response->assertSee('Agar laporan cepat ditangani');
    $response->assertSee('currentWordCount');
    $response->assertSee('gpsStatusContainer');
});

test('landing hero in index page no longer renders tutorial bento grid cards', function () {
    $response = $this->get(route('reports.index'));
    $response->assertOk();
    $response->assertDontSee('01. OpenFreeMap &amp; Reverse Geocode', false);
});
