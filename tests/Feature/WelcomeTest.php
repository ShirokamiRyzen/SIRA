<?php

use App\Models\Report;
use App\Models\User;
use Livewire\Volt\Volt;

test('welcome page renders successfully with sira context and telemetry', function () {
    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee('SIRA');
    $response->assertSee('Sistem Informasi &amp; Rekomendasi Aspirasi Publik', false);
    $response->assertSee('Kawal fasilitas publik dengan bukti nyata dan suara warga.');
    $response->assertSee('theme-toggle');
    $response->assertSee('Dasbor Laporan');
    $response->assertSee('Peta Sebaran');
    $response->assertSee('localStorage.getItem(\'theme\')', false);
});

test('stack status volt component functions reactively', function () {
    Volt::test('stack-status')
        ->assertSee('01. Alur Konsensus &amp; Vote', false)
        ->assertSee('Sistem Siap')
        ->call('selectTab', 'ai')
        ->assertSee('Asisten Cerdas @Sira')
        ->call('selectTab', 'geo')
        ->assertSee('Pemetaan &amp; Lokasi', false);
});

test('welcome page contains responsive mobile navigation and menu drawer', function () {
    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee('mobileMenuBtn');
    $response->assertSee('mobileMenu');
    $response->assertSee('mobileMenuOpenIcon');
    $response->assertSee('mobileMenuCloseIcon');
});

test('welcome page renders report card components with flux icons when reports exist', function () {
    $user = User::factory()->create();
    Report::create([
        'user_id' => $user->id,
        'title' => 'Kerusakan Trotoar Kritis',
        'description' => 'Trotoar berlubang dan berbahaya bagi pejalan kaki.',
        'image_base64' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        'latitude' => -6.914744,
        'longitude' => 107.609810,
        'province' => 'Jawa Barat',
        'city' => 'Kota Bandung',
        'district' => 'Coblong',
        'subdistrict' => 'Dago',
        'formatted_address' => 'Jl. Ir. H. Juanda, Dago, Coblong, Kota Bandung',
        'rank_tier' => 'critical',
        'vote_score' => 15,
        'status' => 'active',
    ]);

    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee('Kerusakan Trotoar Kritis');
});
