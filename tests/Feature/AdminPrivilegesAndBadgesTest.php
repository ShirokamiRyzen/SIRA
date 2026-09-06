<?php

use App\Models\Report;
use App\Models\ReportComment;
use App\Models\User;

test('admin can update status of any report', function () {
    $admin = User::factory()->create([
        'username' => 'superadmin',
        'is_admin' => true,
    ]);

    $author = User::factory()->create(['username' => 'citizen1']);
    $report = Report::create([
        'user_id' => $author->id,
        'title' => 'Jalan Rusak Parah',
        'category' => 'infrastruktur',
        'description' => 'Aspal hancur berlubang',
        'image_base64' => 'data:image/jpeg;base64,sample',
        'latitude' => -6.9175,
        'longitude' => 107.6191,
        'status' => 'active',
        'rank_tier' => 'normal',
    ]);

    $response = $this->actingAs($admin)->patchJson(route('reports.updateStatus', $report), [
        'status' => 'resolved',
    ]);

    $response->assertOk()
        ->assertJson(['success' => true, 'status' => 'resolved']);

    expect($report->fresh()->status)->toBe('resolved');
});

test('admin can delete any report', function () {
    $admin = User::factory()->create([
        'username' => 'superadmin2',
        'is_admin' => true,
    ]);

    $author = User::factory()->create(['username' => 'citizen2']);
    $report = Report::create([
        'user_id' => $author->id,
        'title' => 'Pohon Tumbang',
        'category' => 'infrastruktur',
        'description' => 'Menghalangi jalan raya',
        'image_base64' => 'data:image/jpeg;base64,sample',
        'latitude' => -6.9175,
        'longitude' => 107.6191,
        'status' => 'active',
        'rank_tier' => 'normal',
    ]);

    $response = $this->actingAs($admin)->delete(route('reports.destroy', $report));

    $response->assertRedirect(route('reports.index'));
    expect(Report::find($report->id))->toBeNull();
});

test('admin can delete any comment', function () {
    $admin = User::factory()->create([
        'username' => 'superadmin3',
        'is_admin' => true,
    ]);

    $author = User::factory()->create(['username' => 'citizen3']);
    $commenter = User::factory()->create(['username' => 'commenter1']);

    $report = Report::create([
        'user_id' => $author->id,
        'title' => 'Lampu Padam',
        'category' => 'infrastruktur',
        'description' => 'Gelap di malam hari',
        'image_base64' => 'data:image/jpeg;base64,sample',
        'latitude' => -6.9175,
        'longitude' => 107.6191,
        'status' => 'active',
        'rank_tier' => 'normal',
    ]);

    $comment = ReportComment::create([
        'report_id' => $report->id,
        'user_id' => $commenter->id,
        'content' => 'Komentar yang melanggar aturan',
    ]);

    $response = $this->actingAs($admin)->deleteJson(route('comments.destroy', $comment));

    $response->assertOk()
        ->assertJson(['success' => true]);

    expect(ReportComment::find($comment->id))->toBeNull();
});

test('regular non-author user cannot update status or delete others reports and comments', function () {
    $stranger = User::factory()->create([
        'username' => 'stranger1',
        'is_admin' => false,
    ]);

    $author = User::factory()->create(['username' => 'author1']);
    $report = Report::create([
        'user_id' => $author->id,
        'title' => 'Saluran Air Mampet',
        'category' => 'drainase',
        'description' => 'Genangan air setinggi mata kaki',
        'image_base64' => 'data:image/jpeg;base64,sample',
        'latitude' => -6.9175,
        'longitude' => 107.6191,
        'status' => 'active',
        'rank_tier' => 'normal',
    ]);

    $comment = ReportComment::create([
        'report_id' => $report->id,
        'user_id' => $author->id,
        'content' => 'Komentar asli pembuat laporan',
    ]);

    // Stranger coba ubah status laporan
    $this->actingAs($stranger)->patchJson(route('reports.updateStatus', $report), [
        'status' => 'resolved',
    ])->assertStatus(403);

    // Stranger coba hapus laporan
    $this->actingAs($stranger)->delete(route('reports.destroy', $report))
        ->assertStatus(403);

    // Stranger coba hapus komentar orang lain
    $this->actingAs($stranger)->delete(route('comments.destroy', $comment))
        ->assertStatus(403);
});

test('admin can grant and revoke verified badge for other users', function () {
    $admin = User::factory()->create([
        'username' => 'admin_daerah',
        'is_admin' => true,
    ]);

    $targetUser = User::factory()->create([
        'username' => 'dishub_kotabandung',
        'is_verified' => false,
    ]);

    expect($targetUser->isVerified())->toBeFalse()
        ->and($targetUser->badgeType())->toBeNull();

    // 1. Berikan verifikasi
    $resGrant = $this->actingAs($admin)->postJson(route('admin.users.toggleVerify', $targetUser));
    $resGrant->assertOk()
        ->assertJson([
            'success' => true,
            'is_verified' => true,
            'badge_type' => 'verified',
        ]);

    $targetUser->refresh();
    expect($targetUser->isVerified())->toBeTrue()
        ->and($targetUser->badgeType())->toBe('verified');

    // 2. Cabut verifikasi
    $resRevoke = $this->actingAs($admin)->postJson(route('admin.users.toggleVerify', $targetUser));
    $resRevoke->assertOk()
        ->assertJson([
            'success' => true,
            'is_verified' => false,
            'badge_type' => null,
        ]);

    $targetUser->refresh();
    expect($targetUser->isVerified())->toBeFalse()
        ->and($targetUser->badgeType())->toBeNull();
});

test('admin has gold badge automatically and permanently', function () {
    $admin = User::factory()->create([
        'username' => 'admin_official',
        'is_admin' => true,
        'is_verified' => false,
    ]);

    expect($admin->isAdmin())->toBeTrue()
        ->and($admin->isVerified())->toBeTrue()
        ->and($admin->badgeType())->toBe('admin');

    // Admin tidak bisa dicabut lencana adminnya melalui toggle-verify
    $this->actingAs($admin)->postJson(route('admin.users.toggleVerify', $admin))
        ->assertStatus(422);
});

test('non-admin cannot toggle verification badges', function () {
    $regularUser = User::factory()->create([
        'username' => 'regular_user',
        'is_admin' => false,
    ]);

    $target = User::factory()->create(['username' => 'target_user']);

    $this->actingAs($regularUser)->post(route('admin.users.toggleVerify', $target))
        ->assertStatus(403);
});
