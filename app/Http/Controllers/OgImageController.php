<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Response;

class OgImageController extends Controller
{
    /**
     * Generate dynamic 1200x630 OpenGraph card for a specific report.
     */
    public function report(Report $report): Response
    {
        $report->loadMissing('user');

        $width = 1200;
        $height = 630;

        $img = imagecreatetruecolor($width, $height);
        imagesavealpha($img, true);

        // Color palette
        $bg = imagecolorallocate($img, 14, 14, 14); // #0E0E0E
        $cardBg = imagecolorallocate($img, 20, 20, 20); // #141414
        $borderColor = imagecolorallocate($img, 38, 38, 38); // #262626
        $innerBorder = imagecolorallocate($img, 48, 48, 48); // #303030
        $textWhite = imagecolorallocate($img, 237, 237, 236); // #EDEDEC
        $textMuted = imagecolorallocate($img, 150, 150, 148); // #969694
        $textDim = imagecolorallocate($img, 110, 110, 108); // #6E6E6C
        $emerald = imagecolorallocate($img, 16, 185, 129); // #10B981
        $rose = imagecolorallocate($img, 225, 29, 72); // #E11D48
        $amber = imagecolorallocate($img, 245, 158, 11); // #F59E0B
        $teal = imagecolorallocate($img, 13, 148, 136); // #0D9488
        $slate = imagecolorallocate($img, 71, 85, 105); // #475569

        // Fill background
        imagefill($img, 0, 0, $bg);

        // Subtle decorative grid lines
        $gridColor = imagecolorallocate($img, 22, 22, 22);
        for ($x = 40; $x < $width; $x += 60) {
            imageline($img, $x, 0, $x, $height, $gridColor);
        }
        for ($y = 40; $y < $height; $y += 60) {
            imageline($img, 0, $y, $width, $y, $gridColor);
        }

        // Outer card frame
        imagefilledrectangle($img, 40, 40, $width - 40, $height - 40, $cardBg);
        imagerectangle($img, 40, 40, $width - 40, $height - 40, $borderColor);

        // Accent top bar (depends on rank tier)
        $tierColor = match ($report->rank_tier) {
            'critical' => $rose,
            'urgent' => $amber,
            'trending' => $teal,
            default => $emerald,
        };
        imagefilledrectangle($img, 40, 40, $width - 40, 46, $tierColor);

        $fontPath = $this->getFontPath('bold');
        $regularFont = $this->getFontPath('regular') ?? $fontPath;

        // Brand header: SIRA // LAPORAN PUBLIK
        $brandText = 'SIRA // LAPORAN PUBLIK #'.$report->id;
        $this->drawText($img, 18, 70, 95, $brandText, $emerald, $fontPath);

        // Tier badge
        $tierLabel = strtoupper($report->rank_tier).' TIER';
        $badgeX = 70;
        $badgeY = 120;
        $badgeWidth = 140;
        $badgeHeight = 32;
        imagefilledrectangle($img, $badgeX, $badgeY, $badgeX + $badgeWidth, $badgeY + $badgeHeight, $tierColor);
        $this->drawText($img, 12, $badgeX + 16, $badgeY + 22, $tierLabel, imagecolorallocate($img, 255, 255, 255), $fontPath);

        // Status badge
        $statusLabel = 'STATUS: '.strtoupper(str_replace('_', ' ', $report->status));
        $statusX = $badgeX + $badgeWidth + 14;
        $statusWidth = 160;
        imagefilledrectangle($img, $statusX, $badgeY, $statusX + $statusWidth, $badgeY + $badgeHeight, imagecolorallocate($img, 28, 40, 30));
        imagerectangle($img, $statusX, $badgeY, $statusX + $statusWidth, $badgeY + $badgeHeight, $emerald);
        $this->drawText($img, 11, $statusX + 14, $badgeY + 21, $statusLabel, $emerald, $fontPath);

        // Report Title (wrapped, max 2 lines)
        $title = $report->title;
        $wrappedTitle = $this->wordWrapLines($title, 34, 2);
        $titleY = 205;
        foreach ($wrappedTitle as $line) {
            $this->drawText($img, 24, 70, $titleY, $line, $textWhite, $fontPath);
            $titleY += 42;
        }

        // Description preview (wrapped, max 2 lines)
        $desc = preg_replace('/\s+/', ' ', strip_tags($report->description));
        $wrappedDesc = $this->wordWrapLines($desc, 55, 2);
        $descY = $titleY + 10;
        foreach ($wrappedDesc as $dline) {
            $this->drawText($img, 14, 70, $descY, $dline, $textMuted, $regularFont);
            $descY += 26;
        }

        // Report Photo Preview on Right Side
        $photoX = 720;
        $photoY = 95;
        $photoW = 410;
        $photoH = 340;

        imagefilledrectangle($img, $photoX, $photoY, $photoX + $photoW, $photoY + $photoH, imagecolorallocate($img, 10, 10, 10));
        imagerectangle($img, $photoX, $photoY, $photoX + $photoW, $photoY + $photoH, $innerBorder);

        if ($report->image_base64 && str_contains($report->image_base64, 'base64,')) {
            $rawBase64 = explode('base64,', $report->image_base64)[1] ?? '';
            $photoData = base64_decode($rawBase64);
            if ($photoData) {
                $photoRes = @imagecreatefromstring($photoData);
                if ($photoRes) {
                    $origW = imagesx($photoRes);
                    $origH = imagesy($photoRes);

                    // Cover calculation
                    $scale = max($photoW / $origW, $photoH / $origH);
                    $cropW = (int) ($photoW / $scale);
                    $cropH = (int) ($photoH / $scale);
                    $cropX = (int) (($origW - $cropW) / 2);
                    $cropY = (int) (($origH - $cropH) / 2);

                    imagecopyresampled($img, $photoRes, $photoX, $photoY, $cropX, $cropY, $photoW, $photoH, $cropW, $cropH);
                    imagedestroy($photoRes);

                    // Inner border frame over image
                    imagerectangle($img, $photoX, $photoY, $photoX + $photoW, $photoY + $photoH, $innerBorder);
                }
            }
        } else {
            // Placeholder watermark
            $this->drawText($img, 16, $photoX + 110, $photoY + 175, 'FOTO BUKTI LAPORAN', $textDim, $fontPath);
        }

        // Bottom Info Bar inside Card
        $barY = 465;
        imageline($img, 70, $barY, $width - 70, $barY, $borderColor);

        // Location Info
        $location = ($report->district ? $report->district.', ' : '').($report->city ?? 'Indonesia');
        $this->drawText($img, 11, 70, $barY + 30, 'LOKASI KEJADIAN', $textDim, $fontPath);
        $this->drawText($img, 14, 70, $barY + 60, $this->truncate($location, 30), $textWhite, $fontPath);

        // Vote Score
        $this->drawText($img, 11, 380, $barY + 30, 'DUKUNGAN WARGA', $textDim, $fontPath);
        $this->drawText($img, 16, 380, $barY + 60, '+'.$report->vote_score.' Poin Vote', $emerald, $fontPath);

        // Pelapor Info
        $reporter = '@'.($report->user->username ?? 'anon');
        $this->drawText($img, 11, 620, $barY + 30, 'PELAPOR', $textDim, $fontPath);
        $this->drawText($img, 14, 620, $barY + 60, $reporter, $textWhite, $fontPath);

        // Tanggal
        $date = $report->created_at->format('d M Y, H:i');
        $this->drawText($img, 11, 850, $barY + 30, 'TANGGAL DIBUAT', $textDim, $fontPath);
        $this->drawText($img, 14, 850, $barY + 60, $date, $textMuted, $fontPath);

        // Watermark URL Footer
        $watermark = parse_url(config('app.url'), PHP_URL_HOST) . ' // Sistem Informasi & Laporan Real-Time Komunitas';
        $this->drawText($img, 11, 70, $height - 55, $watermark, $textDim, $regularFont);

        ob_start();
        imagepng($img, null, 7);
        $pngData = ob_get_clean();
        imagedestroy($img);

        return response($pngData, 200, [
            'Content-Type' => 'image/png',
            'Content-Length' => strlen($pngData),
            'Cache-Control' => 'public, max-age=86400, stale-while-revalidate=604800',
        ]);
    }

    /**
     * Generate dynamic 1200x630 OpenGraph card for homepage & default platform pages.
     */
    public function default(): Response
    {
        $width = 1200;
        $height = 630;

        $img = imagecreatetruecolor($width, $height);
        imagesavealpha($img, true);

        $bg = imagecolorallocate($img, 14, 14, 14);
        $cardBg = imagecolorallocate($img, 20, 20, 20);
        $borderColor = imagecolorallocate($img, 38, 38, 38);
        $textWhite = imagecolorallocate($img, 237, 237, 236);
        $textMuted = imagecolorallocate($img, 150, 150, 148);
        $textDim = imagecolorallocate($img, 110, 110, 108);
        $emerald = imagecolorallocate($img, 16, 185, 129);

        imagefill($img, 0, 0, $bg);

        // Decorative grid
        $gridColor = imagecolorallocate($img, 22, 22, 22);
        for ($x = 40; $x < $width; $x += 60) {
            imageline($img, $x, 0, $x, $height, $gridColor);
        }
        for ($y = 40; $y < $height; $y += 60) {
            imageline($img, 0, $y, $width, $y, $gridColor);
        }

        imagefilledrectangle($img, 40, 40, $width - 40, $height - 40, $cardBg);
        imagerectangle($img, 40, 40, $width - 40, $height - 40, $borderColor);
        imagefilledrectangle($img, 40, 40, $width - 40, 46, $emerald);

        $fontPath = $this->getFontPath('bold');
        $regularFont = $this->getFontPath('regular') ?? $fontPath;

        // Platform Brand
        $this->drawText($img, 16, 80, 120, 'SIRA // SISTEM INFORMASI & LAPORAN KOMUNITAS', $emerald, $fontPath);
        $this->drawText($img, 32, 80, 200, 'Kawal Masalah Kota Anda Secara Real-Time', $textWhite, $fontPath);

        $tagline1 = 'Platform pengaduan publik berbasis GIS OpenFreeMap dengan algoritma prioritas Wilson Score.';
        $tagline2 = 'Transparan, akuntabel, dan mengutamakan masalah paling krusial di lingkungan Anda.';
        $this->drawText($img, 16, 80, 260, $tagline1, $textMuted, $regularFont);
        $this->drawText($img, 16, 80, 295, $tagline2, $textMuted, $regularFont);

        // Stats blocks
        $totalReports = Report::count();
        $criticalReports = Report::where('rank_tier', 'critical')->count();
        $resolvedReports = Report::where('status', 'resolved')->count();

        $stats = [
            ['label' => 'TOTAL LAPORAN', 'val' => (string) $totalReports],
            ['label' => 'CRITICAL TIER', 'val' => (string) $criticalReports],
            ['label' => 'TERSELESAIKAN', 'val' => (string) $resolvedReports],
            ['label' => 'PETA GIS', 'val' => 'OpenFreeMap'],
        ];

        $statX = 80;
        $statY = 390;
        foreach ($stats as $st) {
            imagefilledrectangle($img, $statX, $statY, $statX + 220, $statY + 110, imagecolorallocate($img, 26, 26, 26));
            imagerectangle($img, $statX, $statY, $statX + 220, $statY + 110, $borderColor);

            $this->drawText($img, 11, $statX + 20, $statY + 38, $st['label'], $textDim, $fontPath);
            $this->drawText($img, 24, $statX + 20, $statY + 84, $st['val'], $emerald, $fontPath);
            $statX += 250;
        }

        $watermark = parse_url(config('app.url'), PHP_URL_HOST) . ' // Partisipasi Warga Untuk Perubahan Nyata';
        $this->drawText($img, 12, 80, $height - 60, $watermark, $textDim, $regularFont);

        ob_start();
        imagepng($img, null, 7);
        $pngData = ob_get_clean();
        imagedestroy($img);

        return response($pngData, 200, [
            'Content-Type' => 'image/png',
            'Content-Length' => strlen($pngData),
            'Cache-Control' => 'public, max-age=86400, stale-while-revalidate=604800',
        ]);
    }

    /**
     * Draw text using TrueType if available, or fall back to imagestring.
     */
    protected function drawText($img, int $size, int $x, int $y, string $text, int $color, ?string $fontPath): void
    {
        if ($fontPath && function_exists('imagettftext')) {
            imagettftext($img, $size, 0, $x, $y, $color, $fontPath, $text);
        } else {
            $gdFont = $size > 18 ? 5 : ($size > 13 ? 4 : 2);
            $adjustedY = max(10, $y - (int) ($size * 1.1));
            imagestring($img, $gdFont, $x, $adjustedY, $text, $color);
        }
    }

    /**
     * Locate TrueType font file on host system.
     */
    protected function getFontPath(string $type = 'bold'): ?string
    {
        $boldCandidates = [
            resource_path('fonts/PlusJakartaSans-Bold.ttf'),
            resource_path('fonts/Inter-Bold.ttf'),
            'C:/Windows/Fonts/arialbd.ttf',
            'C:/Windows/Fonts/segoeuib.ttf',
            'C:/Windows/Fonts/arial.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf',
            '/usr/share/fonts/truetype/freefont/FreeSansBold.ttf',
        ];

        $regularCandidates = [
            resource_path('fonts/PlusJakartaSans-Regular.ttf'),
            resource_path('fonts/Inter-Regular.ttf'),
            'C:/Windows/Fonts/arial.ttf',
            'C:/Windows/Fonts/segoeui.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf',
            '/usr/share/fonts/truetype/freefont/FreeSans.ttf',
        ];

        $list = $type === 'bold' ? $boldCandidates : $regularCandidates;

        foreach ($list as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        // Secondary fallback
        foreach (array_merge($boldCandidates, $regularCandidates) as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Wrap lines and truncate to max lines.
     */
    protected function wordWrapLines(string $text, int $maxCharsPerLine, int $maxLines): array
    {
        $wrapped = wordwrap($text, $maxCharsPerLine, "\n", true);
        $lines = explode("\n", $wrapped);

        if (count($lines) > $maxLines) {
            $lines = array_slice($lines, 0, $maxLines);
            $lines[$maxLines - 1] = rtrim($lines[$maxLines - 1], '.').'...';
        }

        return $lines;
    }

    /**
     * Truncate string with ellipsis.
     */
    protected function truncate(string $text, int $limit): string
    {
        return mb_strlen($text) > $limit ? mb_substr($text, 0, $limit).'...' : $text;
    }
}
