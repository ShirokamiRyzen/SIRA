<?php

use App\Models\User;
use App\Notifications\ReportMentionNotification;
use Illuminate\Support\Facades\Notification;

test('tagging a user in a new report automatically sends a notification', function () {
    Notification::fake();

    $author = User::factory()->create(['username' => 'citizen_andi', 'name' => 'Andi']);
    $taggedUser = User::factory()->create(['username' => 'dishub_bandung', 'name' => 'Dishub Kota Bandung']);

    $response = $this->actingAs($author)->post(route('reports.store'), [
        'title' => 'Lampu lalu lintas mati di simpang lima',
        'category' => 'kelistrikan',
        'description' => 'Mohon perhatian @dishub_bandung untuk segera memperbaiki lampu lalu lintas yang mati.',
        'image_base64' => 'data:image/jpeg;base64,sampleimage123',
        'latitude' => -6.9175,
        'longitude' => 107.6191,
    ]);

    $response->assertSessionHasNoErrors();

    Notification::assertSentTo(
        $taggedUser,
        ReportMentionNotification::class,
        function (ReportMentionNotification $notification) use ($author) {
            $payload = $notification->toArray(new stdClass);

            return $notification->senderUsername === $author->username &&
                   $payload['type'] === 'post_mention' &&
                   str_contains($payload['message'], "@{$author->username}") &&
                   str_contains($payload['snippet'], '@dishub_bandung');
        }
    );
});

test('tagging multiple users in a report notifies all of them except self', function () {
    Notification::fake();

    $author = User::factory()->create(['username' => 'pelapor_utama']);
    $user1 = User::factory()->create(['username' => 'dinas_pupr']);
    $user2 = User::factory()->create(['username' => 'kecamatan_sukajadi']);

    $response = $this->actingAs($author)->post(route('reports.store'), [
        'title' => 'Jalan amblas di pasteur',
        'category' => 'infrastruktur',
        'description' => 'Laporan ke @dinas_pupr dan tembusan ke @kecamatan_sukajadi, dilaporkan oleh @pelapor_utama',
        'image_base64' => 'data:image/jpeg;base64,sampleimage123',
        'latitude' => -6.9175,
        'longitude' => 107.6191,
    ]);

    $response->assertSessionHasNoErrors();

    Notification::assertSentTo($user1, ReportMentionNotification::class);
    Notification::assertSentTo($user2, ReportMentionNotification::class);
    Notification::assertNotSentTo($author, ReportMentionNotification::class);
});

test('notification is saved to database and accessible by recipient', function () {
    $author = User::factory()->create(['username' => 'warga_budi']);
    $recipient = User::factory()->create(['username' => 'satpol_pp']);

    $this->actingAs($author)->post(route('reports.store'), [
        'title' => 'Penertiban PKL liar di trotoar',
        'category' => 'fasilitas_umum',
        'description' => 'Tolong @satpol_pp ditertibkan gerobak yang menutupi akses difabel.',
        'image_base64' => 'data:image/jpeg;base64,sampleimage123',
        'latitude' => -6.9175,
        'longitude' => 107.6191,
    ]);

    $recipient->refresh();
    expect($recipient->unreadNotifications)->toHaveCount(1);

    $notif = $recipient->unreadNotifications->first();
    expect($notif->data['type'])->toBe('post_mention')
        ->and($notif->data['sender_username'])->toBe('warga_budi')
        ->and($notif->data['report_title'])->toBe('Penertiban PKL liar di trotoar')
        ->and($notif->data['url'])->toContain('/reports/');

    // Penerima dapat menandai sebagai dibaca
    $this->actingAs($recipient)->postJson(route('notifications.markAsRead', $notif->id))
        ->assertOk()
        ->assertJson(['success' => true, 'unread_count' => 0]);

    expect($recipient->fresh()->unreadNotifications)->toHaveCount(0);
});
