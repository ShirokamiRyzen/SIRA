<?php

use App\Http\Controllers\ReportController;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

test('report description fails when containing fewer than 5 words', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('reports.store'), [
        'title' => 'Jalan Rusak Parah Sekali',
        'category' => 'jalan_jembatan',
        'description' => 'Jalanan ini rusak parah', // 4 words
        'image_base64' => 'data:image/jpeg;base64,dummyimage',
        'latitude' => -6.9175,
        'longitude' => 107.6191,
    ]);

    $response->assertSessionHasErrors('description');
});

test('report description succeeds when containing 5 or more words', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('reports.store'), [
        'title' => 'Jalan Rusak Parah Sekali',
        'category' => 'jalan_jembatan',
        'description' => 'Jalanan berlubang ini sangat berbahaya bagi pengendara motor.', // 8 words
        'image_base64' => 'data:image/jpeg;base64,dummyimage',
        'latitude' => -6.9175,
        'longitude' => 107.6191,
    ]);

    $response->assertSessionHasNoErrors();
    $report = Report::latest('id')->first();
    expect($report->category)->toBe('jalan_jembatan');
});

test('districts are pulled dynamically from existing database records without hardcoding', function () {
    $user = User::factory()->create();

    Report::create([
        'user_id' => $user->id,
        'title' => 'Laporan 1',
        'category' => 'sampah_kebersihan',
        'description' => 'Penumpukan sampah liar di tepi jalan umum.',
        'image_base64' => 'data:image/jpeg;base64,dummy',
        'latitude' => -6.9,
        'longitude' => 107.6,
        'city' => 'Kota Cimahi',
        'district' => 'Cimahi Tengah',
    ]);

    Report::create([
        'user_id' => $user->id,
        'title' => 'Laporan 2',
        'category' => 'drainase_saluran',
        'description' => 'Saluran pembuangan air tersumbat oleh sedimentasi lumpur.',
        'image_base64' => 'data:image/jpeg;base64,dummy',
        'latitude' => -6.91,
        'longitude' => 107.61,
        'city' => 'Kota Cimahi',
        'district' => 'Cimahi Selatan',
    ]);

    $districts = ReportController::getAvailableDistricts('Kota Cimahi');
    expect($districts)->toContain('Cimahi Tengah')
        ->toContain('Cimahi Selatan')
        ->not->toContain('Coblong')
        ->not->toContain('Andir');
});

test('submitting a new report via store endpoint properly saves district and updates dynamic filter', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('reports.store'), [
        'title' => 'Lampu PJU Padam di Coblong Dago',
        'category' => 'lampu_pju',
        'description' => 'Lampu penerangan jalan padam total membahayakan pejalan kaki.',
        'image_base64' => 'data:image/jpeg;base64,dummyimage',
        'latitude' => -6.885,
        'longitude' => 107.614,
        'city' => 'Kota Bandung',
        'district' => 'Coblong',
        'subdistrict' => 'Dago',
        'province' => 'Jawa Barat',
        'formatted_address' => 'Jl. Ir. H. Juanda, Dago, Coblong, Kota Bandung',
    ]);

    $response->assertSessionHasNoErrors();

    $createdReport = Report::where('title', 'Lampu PJU Padam di Coblong Dago')->first();
    expect($createdReport)->not->toBeNull()
        ->and($createdReport->district)->toBe('Coblong')
        ->and($createdReport->city)->toBe('Kota Bandung');

    // Cek dropdown filter otomatis memunculkan Coblong
    $availableDistricts = ReportController::getAvailableDistricts('Kota Bandung');
    expect($availableDistricts)->toContain('Coblong');
});

test('reward modal leaderboard excludes admin and verified users', function () {
    $admin = User::factory()->create(['username' => 'superadmin', 'is_admin' => true, 'is_verified' => true]);
    $verified = User::factory()->create(['username' => 'dinas_official', 'is_admin' => false, 'is_verified' => true]);
    $regularCitizen = User::factory()->create(['username' => 'warga_teladan', 'is_admin' => false, 'is_verified' => false]);

    // Admin and verified make reports
    Report::create([
        'user_id' => $admin->id,
        'title' => 'Laporan Admin',
        'category' => 'jalan_jembatan',
        'description' => 'Laporan oleh admin internal dinas.',
        'image_base64' => 'data:image/jpeg;base64,dummy',
        'latitude' => -6.9,
        'longitude' => 107.6,
    ]);

    Report::create([
        'user_id' => $verified->id,
        'title' => 'Laporan Akun Terverifikasi',
        'category' => 'jalan_jembatan',
        'description' => 'Laporan oleh lembaga resmi terverifikasi.',
        'image_base64' => 'data:image/jpeg;base64,dummy',
        'latitude' => -6.9,
        'longitude' => 107.6,
    ]);

    Report::create([
        'user_id' => $regularCitizen->id,
        'title' => 'Laporan Warga Biasa',
        'category' => 'sampah_kebersihan',
        'description' => 'Laporan dari warga biasa lingkungan sekitar.',
        'image_base64' => 'data:image/jpeg;base64,dummy',
        'latitude' => -6.9,
        'longitude' => 107.6,
    ]);

    Cache::forget('top_5_reporters_modal');

    $renderedModal = (string) $this->blade('<x-reward-modal />');
    expect($renderedModal)->toContain('warga_teladan')
        ->not->toContain('superadmin')
        ->not->toContain('dinas_official');
});
