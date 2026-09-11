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

test('buildReportContext, area context, discussion context, and civic guidance provide dynamic multi-source data', function () {
    $reporter = User::factory()->create(['username' => 'warga_pelapor', 'name' => 'Budi Santoso', 'is_verified' => true]);
    $commenter = User::factory()->create(['username' => 'warga_komentar', 'name' => 'Siti Rahma']);
    $replier = User::factory()->create(['username' => 'warga_penanya', 'name' => 'Ahmad Dani']);

    $report = Report::create([
        'user_id' => $reporter->id,
        'title' => 'Jembatan Penyeberangan Rusak Parah',
        'category' => 'jalan_jembatan',
        'description' => 'Bantalan kayu jembatan patah dan sangat berisiko bagi warga melintas.',
        'image_base64' => 'data:image/jpeg;base64,samplephoto',
        'latitude' => -6.914744,
        'longitude' => 107.609810,
        'province' => 'Jawa Barat',
        'city' => 'Kota Bandung',
        'district' => 'Coblong',
        'subdistrict' => 'Dago',
        'formatted_address' => 'Jl. Ir. H. Juanda No. 123, Dago, Coblong',
        'status' => 'active',
        'rank_tier' => 'critical',
    ]);

    // Laporan lain di koordinat yang sama persis (multi-masalah)
    Report::create([
        'user_id' => $reporter->id,
        'title' => 'Lampu PJU di dekat jembatan mati',
        'category' => 'lampu_pju',
        'description' => 'PJU padam total sehingga jembatan gelap gulita.',
        'image_base64' => 'data:image/jpeg;base64,dummy',
        'latitude' => -6.914744,
        'longitude' => 107.609810,
        'province' => 'Jawa Barat',
        'city' => 'Kota Bandung',
        'district' => 'Coblong',
        'status' => 'active',
        'rank_tier' => 'urgent',
    ]);

    // Komentar pertama dari warga lain
    $firstComment = ReportComment::create([
        'report_id' => $report->id,
        'user_id' => $commenter->id,
        'content' => 'Kemarin malam hampir ada anak sekolah yang terperosok di sini!',
    ]);

    // Komentar kedua yang membalas komentar pertama dan men-tag @Sira
    $replyComment = ReportComment::create([
        'report_id' => $report->id,
        'user_id' => $replier->id,
        'parent_id' => $firstComment->id,
        'content' => 'Bahaya sekali ini. Halo @sira instansi mana yang berwenang menangani ini dan apa langkah daruratnya?',
    ]);

    $service = app(AiSummaryService::class);

    // 1. Uji buildReportContext
    $reportContext = $service->buildReportContext($report);
    expect($reportContext)->toContain('Jembatan Penyeberangan Rusak Parah')
        ->toContain('Jalan & Jembatan')
        ->toContain('@warga_pelapor')
        ->toContain('Coblong')
        ->toContain('Kota Bandung');

    // 2. Uji buildAreaAndRelatedReportsContext (Deteksi Titik Multi-Masalah)
    $areaContext = $service->buildAreaAndRelatedReportsContext($report);
    expect($areaContext)->toContain('DETEKSI TITIK MULTI-MASALAH')
        ->toContain('Lampu PJU di dekat jembatan mati')
        ->toContain('Lampu Jalan & PJU');

    // 3. Uji buildDiscussionContext (Konteks Balasan Langsung & Percakapan Sebelumnya)
    $discussionContext = $service->buildDiscussionContext($report, $replyComment);
    expect($discussionContext)->toContain('KONTEKS BALASAN LANGSUNG')
        ->toContain('@warga_komentar')
        ->toContain('Kemarin malam hampir ada anak sekolah yang terperosok');

    // 4. Uji getCivicGuidance
    $guidance = $service->getCivicGuidance($report);
    expect($guidance)->toContain('Dinas Pekerjaan Umum dan Penataan Ruang (PUPR)')
        ->toContain('SP4N-LAPOR');

    // 5. Uji buildPrompts integrasi
    $prompts = $service->buildPrompts($report, $replyComment);
    expect($prompts['systemPrompt'])->toContain('SIRA AI')
        ->toContain('PRINSIP RESPON DINAMIS & FLEKSIBEL');
    expect($prompts['userPrompt'])->toContain('SUMBER 1: DATA LENGKAP LAPORAN PUBLIK')
        ->toContain('SUMBER 2: KONTEKS MULTI-MASALAH & RIWAYAT WILAYAH')
        ->toContain('SUMBER 3: REFERENSI KEWENANGAN DINAS & PROSEDUR PUBLIK')
        ->toContain('SUMBER 4: STRUKTUR DISKUSI & RIWAYAT KOMENTAR WARGA')
        ->toContain('PESAN DARI PENGGUNA @warga_penanya');
});
