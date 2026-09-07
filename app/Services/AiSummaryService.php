<?php

namespace App\Services;

use App\Models\Report;
use App\Models\ReportComment;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiSummaryService
{
    /**
     * Cek apakah isi komentar memanggil bot AI @Sira.
     */
    public function isAiMentioned(string $content): bool
    {
        return (bool) preg_match('/@sira\b/i', $content);
    }

    /**
     * Ambil atau buat akun bot sistem @Sira.
     */
    public function getOrCreateBotUser(): User
    {
        return User::firstOrCreate(
            ['username' => 'Sira'],
            [
                'name' => 'SIRA AI Assistant',
                'email' => 'ai@sira.local',
                'password' => Hash::make('sira-ai-bot-secure-'.config('app.key')),
            ]
        );
    }

    /**
     * Dapatkan daftar model AI yang terkonfigurasi secara berurutan untuk auto fallback.
     *
     * @return array<int, string>
     */
    public function getConfiguredModels(): array
    {
        $raw = config('services.openai.models')
            ?? config('services.openai.model')
            ?? env('OPENAI_MODEL', 'deepseek-v4-pro');

        return $this->parseModelList($raw);
    }

    /**
     * Parsing string atau array konfigurasi model menjadi daftar model yang bersih.
     *
     * @return array<int, string>
     */
    public function parseModelList(mixed $raw): array
    {
        if (is_array($raw)) {
            $models = $raw;
        } elseif (is_string($raw)) {
            $trimmed = trim($raw);
            if (str_starts_with($trimmed, '[') && str_ends_with($trimmed, ']')) {
                $trimmed = substr($trimmed, 1, -1);
            }
            $models = explode(',', $trimmed);
        } else {
            $models = [];
        }

        $cleaned = [];
        foreach ($models as $item) {
            if (! is_string($item)) {
                continue;
            }
            $name = trim($item, " \t\n\r\0\x0B'\"");
            if ($name !== '') {
                $cleaned[] = $name;
            }
        }

        return ! empty($cleaned) ? array_values(array_unique($cleaned)) : ['deepseek-v4-pro'];
    }

    /**
     * Cek apakah teks respon merupakan pesan overload atau error dari upstream AI.
     */
    public function isOverloadOrErrorResponse(string $text): bool
    {
        if (strlen($text) > 250) {
            return false;
        }

        $lower = strtolower($text);

        return str_contains($lower, 'system overload')
            || str_contains($lower, 'system overloaded')
            || str_contains($lower, 'server overloaded')
            || str_contains($lower, 'currently overloaded')
            || str_contains($lower, 'rate limit')
            || str_contains($lower, 'too many requests')
            || str_contains($lower, 'capacity')
            || str_starts_with($lower, 'error:');
    }

    /**
     * Hasilkan rangkuman atau respon berbasis AI menggunakan OpenAI API
     * dengan auto-fallback jika model utama sedang mengalami overload/error,
     * dan simpan sebagai balasan otomatis dari @Sira.
     */
    public function generateAiResponse(Report $report, ReportComment $triggerComment): ?ReportComment
    {
        $apiUrl = config('services.openai.api_url', env('OPENAI_API', 'https://ai.rizuu.id/v1'));
        $apiKey = config('services.openai.api_key', env('OPENAI_KEY'));
        $models = $this->getConfiguredModels();
        $timeout = (int) config('services.openai.timeout', 25);

        if (empty($apiKey) || empty($apiUrl)) {
            Log::warning('AI Summary: OPENAI_API atau OPENAI_KEY belum dikonfigurasi.');

            return null;
        }

        // Ambil riwayat diskusi warga terdahulu untuk konteks AI
        $previousComments = ReportComment::where('report_id', $report->id)
            ->where('id', '!=', $triggerComment->id)
            ->with('user')
            ->latest()
            ->take(10)
            ->get()
            ->reverse()
            ->map(fn (ReportComment $c) => "- @{$c->user?->username}: {$c->content}")
            ->implode("\n");

        $systemPrompt = <<<'PROMPT'
Kamu adalah SIRA AI, asisten virtual resmi untuk platform SIRA (Sistem Informasi Ruang Aman).
Tugasmu adalah menganalisis laporan fasilitas/masalah publik dan diskusi warga, serta memberikan respon cerdas, ringkasan, atau saran konstruktif.

Panduan:
1. Jawab selalu dalam Bahasa Indonesia yang santun, objektif, solutif, dan ringkas.
2. Jika pengguna meminta ringkasan/summary, berikan poin-poin inti:
   - Masalah Utama
   - Sentimen & Poin Diskusi Warga
   - Rekomendasi Tindak Lanjut Pemda/Warga
3. Gunakan formatting Markdown yang rapi (bold, bullet points).
4. Jangan bertele-tele. Langsung pada substansi jawaban.
PROMPT;

        $userPrompt = <<<PROMPT
[DATA LAPORAN]
Judul: {$report->title}
Deskripsi: {$report->description}
Lokasi: {$report->formatted_address} ({$report->district}, {$report->city}, {$report->province})
Status: {$report->status}
Skor Dukungan Warga: {$report->vote_score} poin (Upvotes: {$report->upvotes_count}, Downvotes: {$report->downvotes_count})
Kategori Tier: {$report->rank_tier}

[DISKUSI WARGA SEBELUMNYA]
{$previousComments}

[PESAN PENGGUNA @{$triggerComment->user?->username}]
{$triggerComment->content}

Tolong berikan respon atau ringkasan sesuai pesan pengguna di atas!
PROMPT;

        $endpoint = rtrim($apiUrl, '/').'/chat/completions';
        $replyText = null;

        foreach ($models as $model) {
            try {
                $response = Http::timeout($timeout)
                    ->withToken($apiKey)
                    ->post($endpoint, [
                        'model' => $model,
                        'messages' => [
                            ['role' => 'system', 'content' => $systemPrompt],
                            ['role' => 'user', 'content' => $userPrompt],
                        ],
                        'temperature' => 0.7,
                        'max_tokens' => 900,
                    ]);

                if ($response->successful()) {
                    $data = $response->json();

                    if (! empty($data['error'])) {
                        $errMsg = is_array($data['error']) ? ($data['error']['message'] ?? json_encode($data['error'])) : (string) $data['error'];
                        Log::warning("AI Summary: Model [{$model}] mengembalikan error: {$errMsg}. Mencoba model berikutnya...");

                        continue;
                    }

                    $candidate = $data['choices'][0]['message']['content'] ?? null;

                    if (! empty($candidate)) {
                        $candidate = trim($candidate);

                        if ($this->isOverloadOrErrorResponse($candidate)) {
                            Log::warning("AI Summary: Model [{$model}] mengembalikan indikasi overload: {$candidate}. Mencoba model berikutnya...");

                            continue;
                        }

                        $replyText = $candidate;
                        Log::info("AI Summary: Berhasil mendapatkan respon menggunakan model [{$model}].");

                        break;
                    }

                    Log::warning("AI Summary: Model [{$model}] mengembalikan konten kosong. Mencoba model berikutnya...");
                } else {
                    Log::warning("AI Summary: Model [{$model}] gagal dengan HTTP {$response->status()}: ".substr($response->body(), 0, 200).'. Mencoba model berikutnya...');
                }
            } catch (Exception $e) {
                Log::warning("AI Summary: Gagal menghubungi model [{$model}]: {$e->getMessage()}. Mencoba model berikutnya...");
            }
        }

        if (! empty($replyText)) {
            try {
                $botUser = $this->getOrCreateBotUser();

                return ReportComment::create([
                    'report_id' => $report->id,
                    'user_id' => $botUser->id,
                    'parent_id' => $triggerComment->id,
                    'content' => $replyText,
                ]);
            } catch (Exception $e) {
                Log::error('Gagal menyimpan komentar balasan AI: '.$e->getMessage());

                return null;
            }
        }

        Log::error('AI Summary: Semua model AI yang dikonfigurasi gagal merespon. Menjalankan fallback pesan standar.');

        // Fallback jika seluruh model AI tidak dapat dijangkau
        try {
            $botUser = $this->getOrCreateBotUser();

            return ReportComment::create([
                'report_id' => $report->id,
                'user_id' => $botUser->id,
                'parent_id' => $triggerComment->id,
                'content' => "Halo @{$triggerComment->user?->username}, terima kasih telah menandai saya. Saat ini layanan AI sedang mengalami beban tinggi atau kendala koneksi ke server. Laporan ini tetap tercatat dengan skor {$report->vote_score} poin.",
            ]);
        } catch (Exception $e) {
            Log::error('Gagal membuat fallback comment @Sira: '.$e->getMessage());

            return null;
        }
    }
}
