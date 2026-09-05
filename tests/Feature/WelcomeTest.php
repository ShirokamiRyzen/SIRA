<?php

use Livewire\Volt\Volt;

test('welcome page renders successfully with livewire and volt telemetry', function () {
    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee('Utilitarian simplicity for modern web applications.');
    $response->assertSee('Volt Active');
    $response->assertSee('theme-toggle');
    $response->assertSee('localStorage.getItem(\'theme\')', false);
});

test('stack status volt component functions reactively', function () {
    Volt::test('stack-status')
        ->assertSee('01. Framework')
        ->assertSee('Livewire v4 Ready')
        ->call('selectTab', 'livewire')
        ->assertSee('Livewire Volt v1.x');
});
