<?php

use Illuminate\Support\Facades\Http;

test('heatmap page renders location search and gps button', function () {
    $response = $this->get(route('heatmap.index'));

    $response->assertOk();
    $response->assertSee('locationSearchInput');
    $response->assertSee('btnMyLoc');
    $response->assertSee('searchResultsDropdown');
});

test('api geocode search returns empty array if query is less than 2 characters', function () {
    $response = $this->getJson(route('api.geocode.search', ['q' => 'a']));

    $response->assertOk();
    $response->assertExactJson([]);
});

test('api geocode search returns mapped results when location query is provided', function () {
    Http::fake([
        'https://nominatim.openstreetmap.org/search*' => Http::response([
            [
                'place_id' => 12345,
                'name' => 'Bandung',
                'display_name' => 'Kota Bandung, Jawa Barat, Indonesia',
                'lat' => '-6.9218',
                'lon' => '107.6070',
                'type' => 'city',
            ],
        ], 200),
    ]);

    $response = $this->getJson(route('api.geocode.search', ['q' => 'Bandung']));

    $response->assertOk();
    $response->assertJson([
        [
            'name' => 'Bandung',
            'display_name' => 'Kota Bandung, Jawa Barat, Indonesia',
            'lat' => -6.9218,
            'lng' => 107.6070,
            'type' => 'city',
        ],
    ]);
});
