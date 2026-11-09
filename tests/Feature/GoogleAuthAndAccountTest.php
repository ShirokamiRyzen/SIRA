<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

function mockSocialiteUser(string $id = 'google-12345', string $email = 'warga@gmail.com', string $name = 'Warga Teladan', ?string $avatar = 'https://example.com/avatar.jpg'): void
{
    $socialiteUser = Mockery::mock(SocialiteUser::class);
    $socialiteUser->shouldReceive('getId')->andReturn($id);
    $socialiteUser->shouldReceive('getEmail')->andReturn($email);
    $socialiteUser->shouldReceive('getName')->andReturn($name);
    $socialiteUser->shouldReceive('getNickname')->andReturn('warga');
    $socialiteUser->shouldReceive('getAvatar')->andReturn($avatar);

    $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
    $provider->shouldReceive('user')->andReturn($socialiteUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
}

test('guest can redirect to google auth', function () {
    $response = $this->get(route('auth.google.redirect'));
    $response->assertRedirect();
});

test('guest with bound google_id can login directly', function () {
    $user = User::factory()->create([
        'username' => 'wargabound',
        'google_id' => 'google-99999',
    ]);

    mockSocialiteUser(id: 'google-99999', email: 'bound@gmail.com', name: 'Warga Bound');

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('reports.index'));
    $this->assertAuthenticatedAs($user);
});

test('guest with matching email automatically links google_id and logs in', function () {
    $user = User::factory()->create([
        'username' => 'existingemail',
        'email' => 'existing@gmail.com',
        'google_id' => null,
    ]);

    mockSocialiteUser(id: 'google-new-id', email: 'existing@gmail.com');

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('reports.index'));
    $this->assertAuthenticatedAs($user);

    expect($user->fresh()->google_id)->toBe('google-new-id');
});

test('new guest is redirected to registration completion form', function () {
    mockSocialiteUser(id: 'google-unregistered', email: 'newcitizen@gmail.com', name: 'Citizen Baru');

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('auth.google.register'));
    $this->assertGuest();

    $responseForm = $this->get(route('auth.google.register'));
    $responseForm->assertOk();
    $responseForm->assertSee('Citizen Baru');
});

test('new guest can complete registration via google without password', function () {
    $this->withSession([
        'google_registration' => [
            'google_id' => 'google-unregistered-nopass',
            'email' => 'nopass@gmail.com',
            'name' => 'Warga Tanpa Password',
            'avatar' => 'https://example.com/avatar.jpg',
            'suggested_username' => 'warga_nopass',
        ],
    ]);

    $response = $this->post(route('auth.google.complete'), [
        'name' => 'Warga Tanpa Password',
        'username' => 'warga_nopass',
        'password' => '',
        'password_confirmation' => '',
    ]);

    $response->assertRedirect(route('reports.index'));
    $this->assertAuthenticated();

    $user = User::where('username', 'warga_nopass')->first();
    expect($user)->not->toBeNull()
        ->and($user->google_id)->toBe('google-unregistered-nopass')
        ->and($user->hasPassword())->toBeFalse();
});

test('new guest can complete registration via google with optional password', function () {
    $this->withSession([
        'google_registration' => [
            'google_id' => 'google-unregistered-withpass',
            'email' => 'withpass@gmail.com',
            'name' => 'Warga With Password',
            'avatar' => null,
            'suggested_username' => 'warga_withpass',
        ],
    ]);

    $response = $this->post(route('auth.google.complete'), [
        'name' => 'Warga With Password',
        'username' => 'warga_withpass',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ]);

    $response->assertRedirect(route('reports.index'));
    $this->assertAuthenticated();

    $user = User::where('username', 'warga_withpass')->first();
    expect($user)->not->toBeNull()
        ->and($user->hasPassword())->toBeTrue()
        ->and(Hash::check('secret123', $user->password))->toBeTrue();
});

test('authenticated user can bind google account', function () {
    $user = User::factory()->create([
        'username' => 'user_to_bind',
        'google_id' => null,
    ]);

    mockSocialiteUser(id: 'google-bind-123', email: 'bindme@gmail.com');

    $response = $this->actingAs($user)->get(route('auth.google.callback'));

    $response->assertRedirect(route('account.index'));
    $response->assertSessionHas('success');

    expect($user->fresh()->google_id)->toBe('google-bind-123');
});

test('authenticated user cannot bind google_id already used by someone else', function () {
    User::factory()->create([
        'username' => 'other_user',
        'google_id' => 'google-already-taken',
    ]);

    $user = User::factory()->create([
        'username' => 'user_attempt_bind',
        'google_id' => null,
    ]);

    mockSocialiteUser(id: 'google-already-taken', email: 'other@gmail.com');

    $response = $this->actingAs($user)->get(route('auth.google.callback'));

    $response->assertRedirect(route('account.index'));
    $response->assertSessionHas('error');
    expect($user->fresh()->google_id)->toBeNull();
});

test('authenticated user cannot unbind google if they do not have password', function () {
    $user = User::factory()->create([
        'username' => 'nopass_user',
        'google_id' => 'google-nopass-user',
        'password' => null,
    ]);

    $response = $this->actingAs($user)->post(route('account.google.unbind'));

    $response->assertRedirect(route('account.index'));
    $response->assertSessionHas('error');
    expect($user->fresh()->google_id)->toBe('google-nopass-user');
});

test('authenticated user can unbind google if they have password', function () {
    $user = User::factory()->create([
        'username' => 'haspass_user',
        'google_id' => 'google-haspass-user',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->actingAs($user)->post(route('account.google.unbind'));

    $response->assertRedirect(route('account.index'));
    $response->assertSessionHas('success');
    expect($user->fresh()->google_id)->toBeNull();
});

test('user without password can add password in account menu', function () {
    $user = User::factory()->create([
        'username' => 'nopass_creator',
        'google_id' => 'google-111',
        'password' => null,
    ]);

    $response = $this->actingAs($user)->put(route('account.updatePassword'), [
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $user->refresh();
    expect($user->hasPassword())->toBeTrue()
        ->and(Hash::check('newpassword123', $user->password))->toBeTrue();
});

test('user with password can change password in account menu', function () {
    $user = User::factory()->create([
        'username' => 'existingpass_user',
        'password' => Hash::make('oldpassword123'),
    ]);

    $response = $this->actingAs($user)->put(route('account.updatePassword'), [
        'current_password' => 'oldpassword123',
        'password' => 'newpassword456',
        'password_confirmation' => 'newpassword456',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $user->refresh();
    expect(Hash::check('newpassword456', $user->password))->toBeTrue();
});
