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
});
