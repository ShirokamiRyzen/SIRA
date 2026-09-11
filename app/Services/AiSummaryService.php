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
     * Susun konteks komprehensif data laporan publik.
     */
    public function buildReportContext(Report $report): string
    {
        $report->loadMissing(['user:id,name,username,is_admin,is_verified']);

        $reporterName = $report->user ? "@{$report->user->username} ({$report->user->name})" : 'Warga Anonim';
        $reporterStatus = $report->user?->isAdmin() ? 'Administrator Sistem' : ($report->user?->isVerified() ? 'Warga/Lembaga Terverifikasi' : 'Warga Terdaftar');
        $hasImage = ! empty($report->image_base64) ? 'Tersedia bukti foto dokumentasi visual di lapangan' : 'Tidak ada foto dokumentasi';

        return <<<TEXT
- ID Laporan: #{$report->id}
- Judul Masalah: {$report->title}
- Kategori: {$report->category_label} ({$report->category_symbol})
- Deskripsi Lengkap: {$report->description}
- Status Saat Ini: {$report->status_label} (Internal: {$report->status})
- Tingkat Urgensi / Prioritas: {$report->tier_label} ({$report->rank_tier})
- Pelapor: {$reporterName} [Status Akun: {$reporterStatus}]
- Waktu Pelaporan: {$report->created_at?->translatedFormat('d F Y H:i')} (Waktu tunggu penanganan: {$report->pending_duration})
- Lokasi Kejadian: {$report->formatted_address}
- Wilayah Administratif: Kecamatan {$report->district}, Kota/Kabupaten {$report->city}, Provinsi {$report->province}
- Titik Koordinat GPS: Lat {$report->latitude}, Long {$report->longitude}
- Bukti Dokumentasi: {$hasImage}
- Respon Komunitas: Skor Net +{$report->vote_score} (Dukungan Upvote: {$report->upvotes_count}, Downvote: {$report->downvotes_count}, Total Komentar: {$report->comments_count})
TEXT;
    }

    /**
     * Susun konteks titik multi-masalah (co-located) dan laporan terkait di wilayah sekitar.
     */
    public function buildAreaAndRelatedReportsContext(Report $report): string
    {
        // 1. Cek laporan lain di titik koordinat yang sama persis (Co-Located / Multi-Masalah)
        $coLocatedReports = Report::where('latitude', $report->latitude)
            ->where('longitude', $report->longitude)
            ->where('id', '!=', $report->id)
            ->with(['user:id,username'])
            ->take(5)
            ->get();

        $lines = [];

        if ($coLocatedReports->isNotEmpty()) {
            $totalIssues = $coLocatedReports->count() + 1;
            $lines[] = "DETEKSI TITIK MULTI-MASALAH: Titik koordinat ini teridentifikasi memiliki {$totalIssues} masalah berbeda yang terjadi di lokasi yang sama:";
            foreach ($coLocatedReports as $other) {
                $lines[] = "  * Laporan #{$other->id}: \"{$other->title}\" [Kategori: {$other->category_label} | Status: {$other->status_label} | Prioritas: {$other->tier_label} | Skor: +{$other->vote_score} oleh @{$other->user?->username}]";
            }
        } else {
            $lines[] = '- Titik Lokasi: Laporan tunggal pada titik koordinat ini (tidak terdeteksi masalah bertumpuk).';
        }

        // 2. Statistik masalah sejenis di kecamatan & kota
        $districtReportsCount = Report::where('district', $report->district)
            ->where('id', '!=', $report->id)
            ->count();

        $citySameCategoryCount = Report::where('city', $report->city)
            ->where('category', $report->category)
            ->where('id', '!=', $report->id)
            ->count();

        $lines[] = "- Riwayat Wilayah Terkait: Terdapat {$districtReportsCount} laporan fasilitas publik lain yang tercatat di Kecamatan {$report->district}, serta {$citySameCategoryCount} laporan kategori {$report->category_label} lainnya di wilayah {$report->city}.";

        return implode("\n", $lines);
    }

    /**
     * Susun riwayat diskusi, hierarki thread balasan, dan komentar induk.
     */
    public function buildDiscussionContext(Report $report, ReportComment $triggerComment): string
    {
        $triggerComment->loadMissing(['parent.user:id,name,username,is_admin,is_verified', 'user:id,name,username,is_admin,is_verified']);

        $lines = [];

        // Jika user sedang membalas komentar spesifik
        if ($triggerComment->parent) {
            $parentAuthor = "@{$triggerComment->parent->user?->username}";
            $parentTime = $triggerComment->parent->created_at?->diffForHumans() ?? 'sebelumnya';
            $lines[] = 'KONTEKS BALASAN LANGSUNG:';
            $lines[] = "Pengguna @{$triggerComment->user?->username} sedang membalas komentar dari {$parentAuthor} ({$parentTime}):";
            $lines[] = ">>> \"{$triggerComment->parent->content}\"";
            $lines[] = '';
        }

        // Ambil riwayat diskusi warga sebelumnya
        $previousComments = ReportComment::where('report_id', $report->id)
            ->where('id', '!=', $triggerComment->id)
            ->with(['user:id,name,username,is_admin,is_verified', 'parent.user:id,name,username'])
            ->oldest()
            ->take(15)
            ->get();

        if ($previousComments->isEmpty()) {
            $lines[] = 'Belum ada komentar atau diskusi warga lain sebelum pesan ini.';
        } else {
            $lines[] = "Riwayat Percakapan Warga Sebelumnya ({$previousComments->count()} komentar):";
            foreach ($previousComments as $c) {
                $author = "@{$c->user?->username}".($c->user?->isAdmin() ? ' [ADMIN]' : ($c->user?->isVerified() ? ' [TERVERIFIKASI]' : ''));
                $replyInfo = $c->parent ? " (membalas @{$c->parent->user?->username})" : '';
                $lines[] = "- {$author}{$replyInfo}: \"{$c->content}\"";
            }
        }

        return implode("\n", $lines);
    }

    /**
     * Dapatkan panduan kewenangan instansi pemerintahan dan prosedur penanganan publik sesuai kategori laporan.
     */
    public function getCivicGuidance(Report $report): string
    {
        $cat = $report->category ?: 'jalan_jembatan';

        return match ($cat) {
            'lampu_pju', 'kelistrikan', 'penerangan' => <<<GUIDE
- Instansi Penanggung Jawab: Dinas Perhubungan (Dishub) Bidang Penerangan Jalan Umum (PJU) Kota/Kabupaten {$report->city}, atau PT PLN (Persero) jika menyangkut korsleting gardu/kabel distribusi.
- Jalur Pengaduan Resmi: Unit Layanan Cepat Dishub setempat, Call Center 112, dan portal SP4N-LAPOR!.
- Aspek Keselamatan & Mitigasi: Area gelap rawan tindak kejahatan dan kecelakaan lalu lintas di malam hari. Warga disarankan meningkatkan kewaspadaan atau swadaya penerangan darurat sementara.
GUIDE,
            'sampah_kebersihan', 'lingkungan', 'kebersihan' => <<<GUIDE
- Instansi Penanggung Jawab: Dinas Lingkungan Hidup dan Kebersihan (DLHK) Kota/Kabupaten {$report->city}, UPTD Pengelolaan Sampah wilayah {$report->district}, serta pihak Kelurahan/Kecamatan setempat.
- Jalur Pengaduan Resmi: Call center DLHK, aplikasi pengaduan warga Pemda, dan SP4N-LAPOR!.
- Aspek Keselamatan & Mitigasi: Penumpukan sampah berisiko menimbulkan bau menyengat, sumber penyakit, serta penyumbatan saluran saat hujan. Warga dihimbau tidak menambah timbunan sampah liar.
GUIDE,
            'drainase_saluran', 'bencana_alam', 'drainase' => <<<GUIDE
- Instansi Penanggung Jawab: Dinas Sumber Daya Air (SDA) / Dinas Bina Marga & Penataan Ruang Bidang Drainase Kota/Kabupaten {$report->city}, serta BPBD setempat.
- Jalur Pengaduan Resmi: Layanan Cepat Tanggap SDA/PUPR, Call Center 112, dan portal SP4N-LAPOR!.
- Aspek Keselamatan & Mitigasi: Saluran mampet berisiko meluap ke badan jalan dan pemukiman warga saat hujan lebat. Bersihkan sumbatan sampah padat jika memungkinkan secara swadaya.
GUIDE,
            'rambu_lalulintas', 'lalulintas' => <<<GUIDE
- Instansi Penanggung Jawab: Dinas Perhubungan (Dishub) Bidang Lalu Lintas Kota/Kabupaten {$report->city} dan Satlantas Polres setempat.
- Jalur Pengaduan Resmi: Layanan aduan Dishub, Call Center Polri 110, dan SP4N-LAPOR!.
- Aspek Keselamatan & Mitigasi: Lampu merah atau rambu rusak memicu kemacetan parah dan rawan kecelakaan tabrakan. Pengendara dihimbau saling mengalah dan menurunkan kecepatan.
GUIDE,
            'trotoar_pedestrian', 'trotoar' => <<<GUIDE
- Instansi Penanggung Jawab: Dinas Pekerjaan Umum dan Penataan Ruang (PUPR) / Bina Marga Kota/Kabupaten {$report->city}.
- Jalur Pengaduan Resmi: Aplikasi pengaduan Pemda setempat, Layanan Cepat Tanggap PUPR, dan SP4N-LAPOR!.
- Aspek Keselamatan & Mitigasi: Trotoar dan fasilitas pemandu difabel yang rusak sangat berisiko mencelakai pejalan kaki, lansia, dan penyandang disabilitas.
GUIDE,
            'taman_fasum', 'fasilitas_umum', 'fasilitas' => <<<GUIDE
- Instansi Penanggung Jawab: Dinas Perumahan dan Kawasan Permukiman (Disperkim) / DPKP Kota/Kabupaten {$report->city} atau instansi pengelola taman dan fasilitas publik.
- Jalur Pengaduan Resmi: Layanan aduan publik Pemda setempat dan SP4N-LAPOR!.
- Aspek Keselamatan & Mitigasi: Pasang tanda peringatan atau barikade sementara pada bagian fasilitas yang rusak agar tidak mencelakai warga sekitar.
GUIDE,
            'ketertiban_umum', 'ketertiban' => <<<GUIDE
- Instansi Penanggung Jawab: Satuan Polisi Pamong Praja (Satpol PP) Kota/Kabupaten {$report->city} dan Dinas Perhubungan (untuk penertiban parkir liar).
- Jalur Pengaduan Resmi: Hotline Satpol PP Kota/Kabupaten, Call Center 112, dan SP4N-LAPOR!.
- Aspek Keselamatan & Mitigasi: Okupasi ruang publik atau parkir liar mempersempit jalan dan hak pejalan kaki, laporkan dengan santun tanpa aksi main hakim sendiri.
GUIDE,
            default => <<<GUIDE
- Instansi Penanggung Jawab: Dinas Pekerjaan Umum dan Penataan Ruang (PUPR) / Dinas Bina Marga Kota/Kabupaten {$report->city}, atau Dinas Bina Marga Provinsi {$report->province} jika merupakan Jalan Provinsi/Nasional.
- Jalur Pengaduan Resmi: Aplikasi pengaduan resmi Pemda (seperti Sapawarga di Jabar), Layanan Cepat Tanggap PUPR, dan SP4N-LAPOR!.
- Aspek Keselamatan & Mitigasi: Kerusakan jalan atau jembatan sangat membahayakan pengendara, terutama roda dua di malam hari atau kondisi hujan. Diperlukan rambu penanda darurat di lokasi.
GUIDE,
        };
    }

    /**
     * Bangun System Prompt dan User Prompt secara dinamis dari berbagai sumber data.
     *
     * @return array{systemPrompt: string, userPrompt: string}
     */
    public function buildPrompts(Report $report, ReportComment $triggerComment): array
    {
        $reportContext = $this->buildReportContext($report);
        $areaContext = $this->buildAreaAndRelatedReportsContext($report);
        $discussionContext = $this->buildDiscussionContext($report, $triggerComment);
        $civicGuidance = $this->getCivicGuidance($report);
        $triggerAuthor = $triggerComment->user ? "@{$triggerComment->user->username}" : 'Pengguna';

        $systemPrompt = <<<'PROMPT'
Kamu adalah SIRA AI, asisten intelijen dan advokasi ruang aman resmi dari platform SIRA (Sistem Informasi Ruang Aman).
Tugas utamamu adalah mendampingi warga, komunitas, dan aparatur daerah dalam menganalisis masalah fasilitas publik, berdiskusi secara konstruktif, serta memberikan wawasan solutif.

PRINSIP RESPON DINAMIS & FLEKSIBEL (TIDAK KAKU):
1. BACA & CERMATI PESAN PENGGUNA: Pahami apa kebutuhan spesifik pengguna saat ini:
   - Jika pengguna MENGAJUKAN PERTANYAAN (misal: "siapa yang harus dihubungi?", "kenapa belum selesai?", "apakah berbahaya?", "bagaimana aturannya?"): Jawab langsung inti pertanyaannya dengan lugas dan informatif, dukung dengan data laporan serta panduan instansi yang relevan.
   - Jika pengguna MEMINTA RINGKASAN / SUMMARY: Berikan sintesis cerdas (inti masalah, peta sentimen warga dari diskusi, dan langkah prioritas).
   - Jika pengguna MENANYAKAN LAPORAN SEKITAR / MULTI-MASALAH: Gunakan informasi titik multi-masalah & data riwayat wilayah untuk menjelaskan kondisi sekitar secara komprehensif.
   - Jika pengguna MEMINTA SOLUSI / JALUR LAPOR RESMI: Jelaskan instansi penanggung jawab, kanal pengaduan resmi (seperti SP4N-LAPOR, call center 112, dll), serta tips mitigasi darurat bagi warga.
   - Jika pengguna MEMBALAS KOMENTAR WARGA LAIN: Pahami konteks komentar induk yang sedang dibahas dan berikan tanggapan yang menyambung secara relevan.
   - Jika pengguna MENYAPA, MENGAPRESIASI, ATAU BERKOMENTAR SINGKAT: Tanggapi secara hangat, komunikatif, dan apresiatif.
2. JANGAN MEMAKSAKAN SATU FORMAT / TEMPLATE KAKU:
   - Hindari selalu mengulang-ulang sub-judul kaku yang sama jika tidak diminta.
   - Sesuaikan gaya dan panjang jawaban secara proporsional dengan pesan pengguna.
3. TATA BAHASA & GAYA KOMUNIKASI:
   - Gunakan Bahasa Indonesia yang santun, objektif, empatik, solutif, dan profesional.
   - Sapa pengguna secara personal (@username).
   - Gunakan Markdown yang rapi (bold, bullet points, kutipan jika merujuk komentar) agar nyaman dibaca.
   - Tetap berpijak pada data fakta; jangan mengklaim masalah sudah selesai jika status laporan masih aktif/menunggu respon.
PROMPT;

        $userPrompt = <<<PROMPT
=== SUMBER 1: DATA LENGKAP LAPORAN PUBLIK ===
{$reportContext}

=== SUMBER 2: KONTEKS MULTI-MASALAH & RIWAYAT WILAYAH ===
{$areaContext}

=== SUMBER 3: REFERENSI KEWENANGAN DINAS & PROSEDUR PUBLIK ===
{$civicGuidance}

=== SUMBER 4: STRUKTUR DISKUSI & RIWAYAT KOMENTAR WARGA ===
{$discussionContext}

=== PESAN DARI PENGGUNA {$triggerAuthor} ===
"{$triggerComment->content}"

Instruksi: Berikan respon yang cerdas, kontekstual, dan dinamis untuk pesan {$triggerAuthor} di atas, dengan memanfaatkan berbagai sumber informasi yang telah disediakan secara relevan!
PROMPT;

        return [
            'systemPrompt' => $systemPrompt,
            'userPrompt' => $userPrompt,
        ];
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

        ['systemPrompt' => $systemPrompt, 'userPrompt' => $userPrompt] = $this->buildPrompts($report, $triggerComment);

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
