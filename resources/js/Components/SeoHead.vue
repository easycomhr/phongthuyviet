<script setup>
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    title: { type: String, required: true },
    description: { type: String, default: 'Tra cứu phong thủy chuẩn Việt – Lịch âm dương, ngày tốt xấu, xem tuổi, hướng nhà, tử vi, văn khấn và đặt tên cho con.' },
    canonical: { type: String, default: null },
    ogImage: { type: String, default: '/og-cover.jpg' },
    schema: { type: Object, default: null },
});

const siteName = 'Phong Thủy Việt';
const fullTitle = `${props.title} – ${siteName}`;
</script>

<template>
    <Head>
        <title>{{ fullTitle }}</title>
        <meta name="description" :content="description" />

        <!-- Open Graph -->
        <meta property="og:type" content="website" />
        <meta property="og:site_name" :content="siteName" />
        <meta property="og:title" :content="fullTitle" />
        <meta property="og:description" :content="description" />
        <meta property="og:image" :content="ogImage" />

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="fullTitle" />
        <meta name="twitter:description" :content="description" />
        <meta name="twitter:image" :content="ogImage" />

        <!-- Canonical -->
        <link v-if="canonical" rel="canonical" :href="canonical" />

        <!-- Hreflang -->
        <link rel="alternate" hreflang="vi" :href="canonical || '/'" />

        <!-- JSON-LD Structured Data -->
        <component
            v-if="schema"
            :is="'script'"
            type="application/ld+json"
            v-html="JSON.stringify(schema)"
        />
    </Head>
</template>
