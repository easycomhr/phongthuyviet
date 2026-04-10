<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($staticRoutes as $route)
    <url>
        <loc>{{ $baseUrl }}{{ $route['url'] }}</loc>
        <lastmod>{{ $now }}</lastmod>
        <changefreq>{{ $route['changefreq'] }}</changefreq>
        <priority>{{ $route['priority'] }}</priority>
    </url>
@endforeach
@foreach ($prayers as $prayer)
    <url>
        <loc>{{ $baseUrl }}/van-khan/{{ $prayer->slug }}</loc>
        <lastmod>{{ $prayer->updated_at->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
@endforeach
</urlset>
