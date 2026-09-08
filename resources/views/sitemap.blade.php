{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Halaman Beranda -->
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    <!-- Halaman Arsip Laporan Publik -->
    <url>
        <loc>{{ route('reports.index') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>hourly</changefreq>
        <priority>0.9</priority>
    </url>

    <!-- Halaman Peta Sebaran / Heatmap GIS -->
    <url>
        <loc>{{ route('heatmap.index') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>

    <!-- Detail Laporan Warga -->
    @foreach ($reports as $report)
        <url>
            <loc>{{ route('reports.show', $report->id) }}</loc>
            <lastmod>{{ $report->updated_at?->toAtomString() ?? now()->toAtomString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.7</priority>
        </url>
    @endforeach
</urlset>
