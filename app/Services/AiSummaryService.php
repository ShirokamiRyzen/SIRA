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
     * Hasilkan rangkuman atau respon berbasis AI menggunakan OpenAI API
     * dan simpan sebagai balasan otomatis dari @Sira.
     */
    public function generateAiResponse(Report $report, ReportComment $triggerComment): ?ReportComment
    {
        $apiUrl = config('services.openai.api_url', env('OPENAI_API', 'https://ai.rizuu.id/v1'));
        $apiKey = config('services.openai.api_key', env('OPENAI_KEY'));
        $model = config('services.openai.model') ?: env('OPENAI_MODEL', 'deepseek-v4-pro');

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

        try {
            $endpoint = rtrim($apiUrl, '/').'/chat/completions';
            $response = Http::timeout(35)
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
                $replyText = $data['choices'][0]['message']['content'] ?? null;

                if (! empty($replyText)) {
                    $botUser = $this->getOrCreateBotUser();

                    return ReportComment::create([
                        'report_id' => $report->id,
                        'user_id' => $botUser->id,
                        'parent_id' => $triggerComment->id,
                        'content' => trim($replyText),
                    ]);
                }
            } else {
                Log::error('OpenAI API Error: '.$response->body());
            }
        } catch (Exception $e) {
            Log::error('Gagal menghubungi OpenAI API: '.$e->getMessage());
        }

        // Fallback jika API sedang tidak dapat dijangkau
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
