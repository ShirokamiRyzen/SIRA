<?php

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
