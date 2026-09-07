<?php

use App\Models\Report;
use App\Models\ReportComment;
use App\Models\User;
use App\Services\AiSummaryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

test('parseModelList correctly parses various formats of OPENAI_MODEL', function () {
    $service = app(AiSummaryService::class);

    // Format single quote dalam kurung siku (seperti di .env)
    expect($service->parseModelList("['kimi-k2.7-code', 'glm-5.2', 'deepseek-v4-mod', 'kimi-k3', 'auto']"))
        ->toBe(['kimi-k2.7-code', 'glm-5.2', 'deepseek-v4-mod', 'kimi-k3', 'auto']);

    // Format json array string
    expect($service->parseModelList('["kimi-k2.7-code", "glm-5.2"]'))
        ->toBe(['kimi-k2.7-code', 'glm-5.2']);

    // Format comma-separated
    expect($service->parseModelList('kimi-k2.7-code, glm-5.2, auto'))
        ->toBe(['kimi-k2.7-code', 'glm-5.2', 'auto']);

    // Format single string
    expect($service->parseModelList('kimi-k2.7-code'))
        ->toBe(['kimi-k2.7-code']);

    // Format native array
    expect($service->parseModelList(['kimi-k2.7-code', 'glm-5.2']))
        ->toBe(['kimi-k2.7-code', 'glm-5.2']);

    // Format null atau kosong fallback ke default
    expect($service->parseModelList(''))
        ->toBe(['deepseek-v4-pro']);
});

test('isOverloadOrErrorResponse identifies overload messages', function () {
    $service = app(AiSummaryService::class);

    expect($service->isOverloadOrErrorResponse('System overload. Please try again later.'))->toBeTrue();
    expect($service->isOverloadOrErrorResponse('The server is currently overloaded'))->toBeTrue();
    expect($service->isOverloadOrErrorResponse('rate limit exceeded for this model'))->toBeTrue();
    expect($service->isOverloadOrErrorResponse('error: upstream timeout'))->toBeTrue();

    expect($service->isOverloadOrErrorResponse('Halo @andi! Ini adalah rangkuman resmi mengenai penumpukan sampah di lokasi tersebut.'))->toBeFalse();
});

test('generateAiResponse automatically falls back to the next model when first model fails or is overloaded', function () {
    Config::set('services.openai.api_key', 'test-key');
    Config::set('services.openai.api_url', 'https://api.test/v1');
    Config::set('services.openai.models', ['model-1-overloaded', 'model-2-working']);

    $user = User::factory()->create(['username' => 'warga_budi']);
    $report = Report::create([
        'user_id' => $user->id,
        'title' => 'Jalan Rusak Berlubang',
        'description' => 'Jalan berlubang cukup dalam dan membahayakan pengendara motor.',
        'image_base64' => 'data:image/jpeg;base64,dummy',
        'latitude' => -6.914744,
        'longitude' => 107.609810,
        'status' => 'active',
        'rank_tier' => 'normal',
    ]);

    $comment = ReportComment::create([
        'report_id' => $report->id,
        'user_id' => $user->id,
        'content' => 'Mohon bantuan ringkasan @sira',
    ]);

    $attemptedModels = [];

    Http::fake([
        'https://api.test/v1/chat/completions' => function (Request $request) use (&$attemptedModels) {
            $data = $request->data();
            $model = $data['model'] ?? '';
            $attemptedModels[] = $model;

            if ($model === 'model-1-overloaded') {
                return Http::response([
                    'error' => [
                        'message' => 'System overload: model is currently unavailable',
                        'type' => 'server_error',
                    ],
                ], 503);
            }

            if ($model === 'model-2-working') {
                return Http::response([
                    'choices' => [
                        [
                            'message' => [
                                'role' => 'assistant',
                                'content' => 'Tentu @warga_budi, berikut adalah rangkuman dari laporan Jalan Rusak Berlubang...',
                            ],
                        ],
                    ],
                ], 200);
            }

            return Http::response([], 400);
        },
    ]);

    $service = app(AiSummaryService::class);
    $aiComment = $service->generateAiResponse($report, $comment);

    expect($aiComment)->not->toBeNull();
    expect($aiComment->content)->toContain('Tentu @warga_budi, berikut adalah rangkuman');
    expect($attemptedModels)->toBe(['model-1-overloaded', 'model-2-working']);
});

test('generateAiResponse uses standard fallback comment if all configured models fail', function () {
    Config::set('services.openai.api_key', 'test-key');
    Config::set('services.openai.api_url', 'https://api.test/v1');
    Config::set('services.openai.models', ['model-a', 'model-b']);

    $user = User::factory()->create(['username' => 'warga_siti']);
    $report = Report::create([
        'user_id' => $user->id,
        'title' => 'Lampu PJU Mati',
        'description' => 'Lampu penerangan jalan umum mati total di jalan utama.',
        'image_base64' => 'data:image/jpeg;base64,dummy',
        'latitude' => -6.914744,
        'longitude' => 107.609810,
        'status' => 'active',
        'rank_tier' => 'normal',
    ]);

    $comment = ReportComment::create([
        'report_id' => $report->id,
        'user_id' => $user->id,
        'content' => 'Coba cek @sira',
    ]);

    Http::fake([
        'https://api.test/v1/chat/completions' => Http::response([
            'error' => ['message' => 'System overload'],
        ], 503),
    ]);

    $service = app(AiSummaryService::class);
    $aiComment = $service->generateAiResponse($report, $comment);

    expect($aiComment)->not->toBeNull();
    expect($aiComment->content)->toContain('Saat ini layanan AI sedang mengalami beban tinggi');
});

test('generateAiResponse falls back when model returns 200 with overload text message', function () {
    Config::set('services.openai.api_key', 'test-key');
    Config::set('services.openai.api_url', 'https://api.test/v1');
    Config::set('services.openai.models', ['model-busy', 'model-ready']);

    $user = User::factory()->create(['username' => 'warga_ani']);
    $report = Report::create([
        'user_id' => $user->id,
        'title' => 'Banjir Luapan Saluran',
        'description' => 'Saluran air tersumbat dan meluap ke jalan raya.',
        'image_base64' => 'data:image/jpeg;base64,dummy',
        'latitude' => -6.914744,
        'longitude' => 107.609810,
        'status' => 'active',
        'rank_tier' => 'normal',
    ]);

    $comment = ReportComment::create([
        'report_id' => $report->id,
        'user_id' => $user->id,
        'content' => 'Halo @sira tolong ringkas',
    ]);

    Http::fake([
        'https://api.test/v1/chat/completions' => function (Request $request) {
            $data = $request->data();
            $model = $data['model'] ?? '';

            if ($model === 'model-busy') {
                return Http::response([
                    'choices' => [
                        [
                            'message' => [
                                'role' => 'assistant',
                                'content' => 'System overload: please try again later.',
                            ],
                        ],
                    ],
                ], 200);
            }

            if ($model === 'model-ready') {
                return Http::response([
                    'choices' => [
                        [
                            'message' => [
                                'role' => 'assistant',
                                'content' => 'Rangkuman banjir luapan saluran: Masalah utama adalah drainase tersumbat.',
                            ],
                        ],
                    ],
                ], 200);
            }

            return Http::response([], 400);
        },
    ]);

    $service = app(AiSummaryService::class);
    $aiComment = $service->generateAiResponse($report, $comment);

    expect($aiComment)->not->toBeNull();
    expect($aiComment->content)->toContain('Rangkuman banjir luapan saluran');
});
