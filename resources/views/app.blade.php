<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Default SEO fallbacks (overridden per-page by SeoHead component) -->
    <title>Phong Thủy Việt – Tra Cứu Phong Thủy Chuẩn Việt</title>
    <meta name="description" content="Tra cứu phong thủy chuẩn Việt – Lịch âm dương, ngày tốt xấu, xem tuổi hợp, hướng nhà, tử vi, văn khấn và đặt tên cho con.">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Phong Thủy Việt">

    <!-- Default Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Phong Thủy Việt">
    <meta property="og:title" content="Phong Thủy Việt – Tra Cứu Phong Thủy Chuẩn Việt">
    <meta property="og:description" content="Tra cứu phong thủy chuẩn Việt – Lịch âm dương, ngày tốt xấu, xem tuổi hợp, hướng nhà, tử vi, văn khấn và đặt tên cho con.">
    <meta property="og:image" content="{{ config('app.url') }}/og-cover.jpg">
    <meta property="og:url" content="{{ config('app.url') }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">

    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="alternate icon" href="/favicon.ico">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#C9A84C">
    <meta name="msapplication-TileColor" content="#111110">
    @routes
    @vite(['resources/js/app.js'])
    @inertiaHead
</head>
<body class="bg-[#F2EDE4] font-sans">
    @inertia
</body>
</html>
