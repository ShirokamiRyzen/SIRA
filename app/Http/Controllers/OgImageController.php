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
        $panelBg = imagecolorallocate($img, 24, 24, 24); // #181818
        $textWhite = imagecolorallocate($img, 237, 237, 236); // #EDEDEC
        $textMuted = imagecolorallocate($img, 150, 150, 148); // #969694
        $textDim = imagecolorallocate($img, 110, 110, 108); // #6E6E6C
        $emerald = imagecolorallocate($img, 16, 185, 129); // #10B981
        $rose = imagecolorallocate($img, 225, 29, 72); // #E11D48
        $amber = imagecolorallocate($img, 245, 158, 11); // #F59E0B
        $teal = imagecolorallocate($img, 13, 148, 136); // #0D9488
        $slate = imagecolorallocate($img, 100, 116, 139); // #64748B
        $sky = imagecolorallocate($img, 56, 189, 248); // #38BDF8

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

        // Status & Tier mappings
        $statusLabel = strtoupper($report->status_label);
        $statusColor = match ($report->status) {
            'resolved' => $emerald,
            'in_progress' => $amber,
            'archived' => $slate,
            default => $sky,
        };

        $statusBg = match ($report->status) {
            'resolved' => imagecolorallocate($img, 18, 38, 26),
            'in_progress' => imagecolorallocate($img, 45, 30, 10),
            'archived' => imagecolorallocate($img, 30, 36, 44),
            default => imagecolorallocate($img, 14, 32, 50),
        };

        $tierLabel = strtoupper($report->tier_label);
        $tierColor = match ($report->rank_tier) {
            'critical' => $rose,
            'urgent' => $amber,
            'trending' => $teal,
            default => $emerald,
        };

        // Accent top bar (color of status or urgency)
        imagefilledrectangle($img, 40, 40, $width - 40, 46, $statusColor);

        $fontPath = $this->getFontPath('bold');
        $regularFont = $this->getFontPath('regular') ?? $fontPath;

        // Brand header: SIRA // LAPORAN PUBLIK
        $brandText = 'SIRA // LAPORAN PUBLIK #'.$report->id;
        $this->drawText($img, 16, 70, 92, $brandText, $emerald, $fontPath);

        // Dynamically sized badges
        $badgeY = 114;
        $currentBadgeX = 70;

        // 1. Status badge
        $w1 = $this->drawBadge($img, $currentBadgeX, $badgeY, $statusLabel, $statusColor, $statusBg, $statusColor, $fontPath);
        $currentBadgeX += $w1 + 10;

        // 2. Category badge
        $catLabel = strtoupper($report->category_label);
        $w2 = $this->drawBadge($img, $currentBadgeX, $badgeY, $catLabel, $textWhite, imagecolorallocate($img, 28, 28, 28), $innerBorder, $fontPath);
        $currentBadgeX += $w2 + 10;

        // 3. Urgency / Tier badge
        $this->drawBadge($img, $currentBadgeX, $badgeY, $tierLabel, $tierColor, imagecolorallocate($img, 26, 26, 26), $tierColor, $fontPath);

        // Report Title (wrapped, max 2 lines)
        $title = $report->title;
        $wrappedTitle = $this->wordWrapLines($title, 36, 2);
        $titleY = 190;
        foreach ($wrappedTitle as $line) {
            $this->drawText($img, 22, 70, $titleY, $line, $textWhite, $fontPath);
            $titleY += 38;
        }

        // Post Content / Description (wrapped, up to 4 lines)
        $desc = preg_replace('/\s+/', ' ', strip_tags($report->description));
        $wrappedDesc = $this->wordWrapLines($desc, 54, 4);
        $descY = $titleY + 10;
        foreach ($wrappedDesc as $dline) {
            $this->drawText($img, 13, 70, $descY, $dline, $textMuted, $regularFont);
            $descY += 24;
        }

        // Right-Side Visual: Real Photo Preview OR Rich GIS Radar / Data Card
        $photoX = 705;
        $photoY = 88;
        $photoW = 425;
        $photoH = 355;

        $hasRasterImage = false;
        if ($report->image_base64 && str_contains($report->image_base64, 'base64,')) {
            $rawBase64 = explode('base64,', $report->image_base64)[1] ?? '';
            $photoData = base64_decode($rawBase64);
            if ($photoData) {
                $photoRes = @imagecreatefromstring($photoData);
                if ($photoRes) {
                    $hasRasterImage = true;
                    $origW = imagesx($photoRes);
                    $origH = imagesy($photoRes);

                    $scale = max($photoW / $origW, $photoH / $origH);
                    $cropW = (int) ($photoW / $scale);
                    $cropH = (int) ($photoH / $scale);
                    $cropX = (int) (($origW - $cropW) / 2);
                    $cropY = (int) (($origH - $cropH) / 2);

                    imagecopyresampled($img, $photoRes, $photoX, $photoY, $cropX, $cropY, $photoW, $photoH, $cropW, $cropH);
                    imagedestroy($photoRes);

                    // Overlay bottom pill on image
                    imagefilledrectangle($img, $photoX, $photoY + $photoH - 45, $photoX + $photoW, $photoY + $photoH, imagecolorallocatealpha($img, 10, 10, 10, 30));
                    $this->drawText($img, 11, $photoX + 16, $photoY + $photoH - 18, 'FOTO BUKTI LAPORAN // '.$report->category_label, $textWhite, $fontPath);

                    // Inner border over photo
                    imagerectangle($img, $photoX, $photoY, $photoX + $photoW, $photoY + $photoH, $innerBorder);
                }
            }
        }

        if (! $hasRasterImage) {
            // Elegant Editorial GIS Data & Status Panel
            imagefilledrectangle($img, $photoX, $photoY, $photoX + $photoW, $photoY + $photoH, $panelBg);
            imagerectangle($img, $photoX, $photoY, $photoX + $photoW, $photoY + $photoH, $innerBorder);

            // Panel Header
            $this->drawText($img, 12, $photoX + 22, $photoY + 36, 'DATA & VERIFIKASI SISTEM', $emerald, $fontPath);
            imageline($img, $photoX + 22, $photoY + 50, $photoX + $photoW - 22, $photoY + 50, $borderColor);

            // Status Row in Panel
            $this->drawText($img, 11, $photoX + 22, $photoY + 80, 'STATUS PENANGANAN', $textDim, $fontPath);
            $this->drawText($img, 15, $photoX + 22, $photoY + 106, $report->status_label, $statusColor, $fontPath);

            // Coordinates & Area
            $this->drawText($img, 11, $photoX + 22, $photoY + 140, 'WILAYAH & KOORDINAT GIS', $textDim, $fontPath);
            $this->drawText($img, 13, $photoX + 22, $photoY + 164, $this->truncate($report->location_short, 34), $textWhite, $fontPath);
            $coordText = 'Lat: '.number_format($report->latitude ?? 0, 4).' | Lng: '.number_format($report->longitude ?? 0, 4);
            $this->drawText($img, 11, $photoX + 22, $photoY + 188, $coordText, $textMuted, $regularFont);

            // Support & Engagement
            $this->drawText($img, 11, $photoX + 22, $photoY + 222, 'DUKUNGAN & TANGGAPAN WARGA', $textDim, $fontPath);
            $supportText = '+'.$report->vote_score.' Suara Warga  •  '.$report->comments_count.' Tanggapan Diskusi';
            $this->drawText($img, 13, $photoX + 22, $photoY + 246, $supportText, $emerald, $fontPath);

            // Urgency & Priority Score
            $this->drawText($img, 11, $photoX + 22, $photoY + 280, 'PRIORITAS ALGORITMA WILSON', $textDim, $fontPath);
            $this->drawText($img, 13, $photoX + 22, $photoY + 304, $report->tier_label.' (ID #'.$report->id.')', $tierColor, $fontPath);

            // Panel Footer Tag
            $this->drawText($img, 10, $photoX + 22, $photoY + 338, 'OPENMAP GIS RADAR // TERVERIFIKASI SISTEM', $textDim, $regularFont);
        }

        // Bottom Info Bar inside Card (4 spacious columns to prevent any overlap)
        $barY = 465;
        imageline($img, 70, $barY, $width - 70, $barY, $borderColor);

        // 1. Lokasi Kejadian
        $this->drawText($img, 11, 70, $barY + 28, 'LOKASI KEJADIAN', $textDim, $fontPath);
        $this->drawText($img, 13, 70, $barY + 54, $this->truncate($report->location_short, 26), $textWhite, $fontPath);

        // 2. Dukungan & Respon
        $this->drawText($img, 11, 350, $barY + 28, 'DUKUNGAN & RESPON', $textDim, $fontPath);
        $this->drawText($img, 13, 350, $barY + 54, '+'.$report->vote_score.' Suara • '.$report->comments_count.' Diskusi', $emerald, $fontPath);

        // 3. Pelapor
        $reporter = '@'.($report->user->username ?? 'anon');
        $this->drawText($img, 11, 620, $barY + 28, 'PELAPOR', $textDim, $fontPath);
        $this->drawText($img, 13, 620, $barY + 54, $this->truncate($reporter, 24), $textWhite, $fontPath);

        // 4. Tanggal Dibuat
        $dateText = $report->created_at->format('d M Y, H:i');
        $this->drawText($img, 11, 880, $barY + 28, 'TANGGAL DIBUAT', $textDim, $fontPath);
        $this->drawText($img, 13, 880, $barY + 54, $dateText, $textMuted, $fontPath);

        // Watermark URL Footer
        $watermark = parse_url(config('app.url'), PHP_URL_HOST).' // Sistem Informasi Ruang Aman — Partisipasi Warga Untuk Perubahan Nyata';
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
        $this->drawText($img, 16, 80, 120, 'SIRA // SISTEM INFORMASI RUANG AMAN', $emerald, $fontPath);
        $this->drawText($img, 32, 80, 200, 'Kawal Masalah Kota Anda Secara Real-Time', $textWhite, $fontPath);

        $tagline1 = 'Platform pengaduan publik berbasis GIS OpenMap dengan algoritma prioritas Wilson Score.';
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
            ['label' => 'PETA GIS', 'val' => 'OpenMap'],
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

        $watermark = parse_url(config('app.url'), PHP_URL_HOST).' // Partisipasi Warga Untuk Perubahan Nyata';
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
     * Locate TrueType font file on host system or local resources.
     */
    protected function getFontPath(string $type = 'bold'): ?string
    {
        $bundledFonts = $type === 'bold' ? [
            resource_path('fonts/PlusJakartaSans-Bold.ttf'),
            resource_path('fonts/PlusJakartaSans.ttf'),
            resource_path('fonts/Roboto.ttf'),
            resource_path('fonts/Inter-Bold.ttf'),
        ] : [
            resource_path('fonts/PlusJakartaSans-Regular.ttf'),
            resource_path('fonts/PlusJakartaSans.ttf'),
            resource_path('fonts/Roboto.ttf'),
            resource_path('fonts/Inter-Regular.ttf'),
        ];

        // 1. Check local project fonts first (always within open_basedir)
        foreach ($bundledFonts as $path) {
            if (@is_file($path) && @is_readable($path)) {
                return $path;
            }
        }

        // 2. Only check system fonts if open_basedir is not active to prevent ErrorException
        $openBaseDir = ini_get('open_basedir');
        if (empty($openBaseDir)) {
            $systemCandidates = $type === 'bold' ? [
                'C:/Windows/Fonts/arialbd.ttf',
                'C:/Windows/Fonts/segoeuib.ttf',
                'C:/Windows/Fonts/arial.ttf',
                '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
                '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
                '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
                '/usr/share/fonts/truetype/freefont/FreeSansBold.ttf',
            ] : [
                'C:/Windows/Fonts/arial.ttf',
                'C:/Windows/Fonts/segoeui.ttf',
                '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
                '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf',
                '/usr/share/fonts/truetype/freefont/FreeSans.ttf',
            ];

            foreach ($systemCandidates as $path) {
                if (@file_exists($path)) {
                    return $path;
                }
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

    /**
     * Draw dynamically sized rounded/bordered badge.
     */
    protected function drawBadge($img, int $x, int $y, string $text, int $textColor, int $bgColor, int $borderColor, ?string $fontPath): int
    {
        $fontSize = 10;
        $paddingX = 12;
        $height = 28;

        if ($fontPath && function_exists('imagettfbbox')) {
            $box = imagettfbbox($fontSize, 0, $fontPath, $text);
            $textWidth = abs($box[4] - $box[0]);
        } else {
            $textWidth = strlen($text) * 7;
        }

        $badgeWidth = $textWidth + ($paddingX * 2);

        imagefilledrectangle($img, $x, $y, $x + $badgeWidth, $y + $height, $bgColor);
        imagerectangle($img, $x, $y, $x + $badgeWidth, $y + $height, $borderColor);

        $this->drawText($img, $fontSize, $x + $paddingX, $y + 19, $text, $textColor, $fontPath);

        return $badgeWidth;
    }
}
