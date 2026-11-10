<?php

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

test('guest can view forgot password page', function () {
    $this->get(route('password.request'))->assertOk();
});

test('reset password email renders with branded content', function () {
    $user = User::factory()->make([
        'name' => 'Warga Teladan',
        'email' => 'warga@gmail.com',
    ]);

    $html = (new ResetPasswordNotification('token-uji'))->toMail($user)->render();

    expect($html)
        ->toContain('Atur Ulang Kata Sandi')
        ->toContain('Warga Teladan')
        ->toContain(route('password.reset', ['token' => 'token-uji', 'email' => 'warga@gmail.com']));
});

test('google bound user receives reset password notification', function () {
    Notification::fake();

    $user = User::factory()->create([
        'email' => 'warga@gmail.com',
        'google_id' => 'google-123',
    ]);

    $response = $this->post(route('password.email'), ['email' => 'warga@gmail.com']);

    $response->assertSessionHasNoErrors();
    $response->assertSessionHas('success');

    Notification::assertSentTo($user, ResetPasswordNotification::class);
});

test('user without linked google account cannot request password reset', function () {
    Notification::fake();

    $user = User::factory()->create([
        'email' => 'warga@gmail.com',
        'google_id' => null,
    ]);

    $response = $this->post(route('password.email'), ['email' => 'warga@gmail.com']);

    $response->assertSessionHasErrors('email');
    Notification::assertNotSentTo($user, ResetPasswordNotification::class);
});

test('unregistered email cannot request password reset', function () {
    Notification::fake();

    $response = $this->post(route('password.email'), ['email' => 'tidakada@gmail.com']);

    $response->assertSessionHasErrors('email');
    Notification::assertNothingSent();
});

test('user can reset password with a valid token', function () {
    $user = User::factory()->create([
        'email' => 'warga@gmail.com',
        'google_id' => 'google-123',
        'password' => Hash::make('password-lama'),
    ]);

    $token = Password::createToken($user);

    $response = $this->post(route('password.update'), [
        'token' => $token,
        'email' => 'warga@gmail.com',
        'password' => 'password-baru',
        'password_confirmation' => 'password-baru',
    ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('success');

    expect(Hash::check('password-baru', $user->fresh()->password))->toBeTrue();
});

test('reset password fails with an invalid token', function () {
    $user = User::factory()->create([
        'email' => 'warga@gmail.com',
        'google_id' => 'google-123',
        'password' => Hash::make('password-lama'),
    ]);

    $response = $this->post(route('password.update'), [
        'token' => 'token-tidak-valid',
        'email' => 'warga@gmail.com',
        'password' => 'password-baru',
        'password_confirmation' => 'password-baru',
    ]);

    $response->assertSessionHasErrors('email');

    expect(Hash::check('password-lama', $user->fresh()->password))->toBeTrue();
});

test('account page warns when google account is not linked', function () {
    $user = User::factory()->create(['google_id' => null]);

    $this->actingAs($user)
        ->get(route('account.index'))
        ->assertOk()
        ->assertSee('Reset Kata Sandi Tidak Tersedia');
});

test('account page does not warn when google account is linked', function () {
    $user = User::factory()->create(['google_id' => 'google-123']);

    $this->actingAs($user)
        ->get(route('account.index'))
        ->assertOk()
        ->assertDontSee('Reset Kata Sandi Tidak Tersedia');
});
