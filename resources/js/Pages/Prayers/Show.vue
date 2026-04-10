<template>
    <AppLayout>
        <SeoHead
            :title="prayer.title"
            :description="`Đọc và in văn khấn ${prayer.title} chuẩn theo phong tục Việt Nam. Văn khấn đầy đủ, dễ đọc, có thể lưu PDF.`"
            :canonical="`https://phongthuyviet.easycom.tech/van-khan/${prayer.slug}`"
            :schema="prayerSchema"
        />
        <section class="page-hero relative overflow-hidden no-print">
            <div class="hero-noise absolute inset-0 pointer-events-none" aria-hidden="true"></div>
            <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-9 text-center">
                <p class="text-[#F3CF7A]/90 text-xs font-semibold uppercase tracking-[0.2em] mb-2">
                    {{ prayer.prayer_category?.name || 'Văn khấn' }}
                </p>
                <h1 class="text-2xl sm:text-4xl font-bold text-white mb-2 page-display">
                    {{ prayer.title }}
                </h1>
            </div>
        </section>

        <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 -mt-4 sm:-mt-6 relative z-20 pb-20">
            <article
                class="rounded-3xl border border-[#C58E3B]/25 shadow-[0_18px_45px_rgba(42,17,0,0.08)] p-5 sm:p-7 transition-colors"
                :class="articleThemeClass"
            >
                <div class="print-brand">
                    <img :src="'/favicon.svg'" alt="Phong Thủy Việt" class="print-brand-logo">
                    <p class="print-brand-name">Phong Thủy Việt</p>
                </div>

                <header class="mb-5 border-b border-[#DCC7A3]/45 pb-4">
                    <p class="text-xs text-[#7F6A5D]">
                        Bài đọc tối ưu di động, hỗ trợ lưu cỡ chữ và nền đọc.
                    </p>
                </header>

                <div
                    class="prayer-content text-[15px] sm:text-base whitespace-pre-line"
                    :style="{ fontSize: `${fontSize}px`, lineHeight: 1.72 }"
                >
                    {{ prayer.content }}
                </div>

                <div class="mt-5 rounded-2xl border border-[#E6D0AF] bg-[#FFF8EC] p-3.5 text-sm text-[#5D4A40]">
                    <p class="font-semibold text-[#6A1A00]">Lưu ý nghi lễ</p>
                    <p class="mt-1 leading-relaxed">
                        Với phần trong ngoặc vuông (ví dụ `[Họ tên]`, `[Địa chỉ]`, `[Ngày tháng năm]`), cần thay đúng thông tin thực tế
                        trước khi đọc. Nên đọc chậm, rõ ràng và giữ sự trang nghiêm theo phong tục gia đình.
                    </p>
                </div>
            </article>

            <aside v-if="relatedPrayers.length" class="mt-4 rounded-3xl border border-[#C58E3B]/25 bg-white p-4 sm:p-5 no-print">
                <h2 class="text-base font-semibold text-[#5D1300] mb-2">Bài cùng danh mục</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <a
                        v-for="item in relatedPrayers"
                        :key="item.id"
                        :href="route('prayers.show', item.slug)"
                        class="rounded-xl border border-[#E8D4B6] bg-[#FFFCF6] px-3.5 py-2.5 text-sm text-[#5D4A40]
                               hover:border-[#B35A16]/55 hover:text-[#7A1700] transition-colors"
                    >
                        {{ item.title }}
                    </a>
                </div>
            </aside>
        </section>

        <div class="fixed bottom-4 left-1/2 -translate-x-1/2 z-40 no-print">
            <div class="toolbar-shell rounded-2xl border border-[#C58E3B]/45 bg-white/95 backdrop-blur-md shadow-lg px-2 py-2 flex items-center gap-2">
                <button
                    type="button"
                    class="toolbar-btn"
                    @click="decreaseFont"
                >
                    A-
                </button>
                <button
                    type="button"
                    class="toolbar-btn"
                    @click="increaseFont"
                >
                    A+
                </button>
                <button
                    type="button"
                    class="toolbar-btn min-w-[96px]"
                    @click="cycleTheme"
                >
                    Nền: {{ themeLabel }}
                </button>
                <button
                    type="button"
                    class="toolbar-btn min-w-[90px]"
                    @click="exportPdf"
                >
                    Xuất PDF
                </button>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { computed, onMounted, ref, watch } from 'vue';

const STORAGE_KEY_FONT_SIZE = 'prayer_reader_font_size';
const STORAGE_KEY_THEME = 'prayer_reader_theme';

const THEMES = ['light', 'sepia', 'dark'];
const FONT_MIN = 14;
const FONT_MAX = 26;

const props = defineProps({
    prayer: {
        type: Object,
        required: true,
    },
    relatedPrayers: {
        type: Array,
        default: () => [],
    },
});

const fontSize = ref(17);
const theme = ref('light');

const articleThemeClass = computed(() => {
    if (theme.value === 'sepia') return 'theme-sepia';
    if (theme.value === 'dark') return 'theme-dark';
    return 'theme-light';
});

const prayerSchema = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'Article',
    headline: props.prayer.title,
    description: `Văn khấn ${props.prayer.title} chuẩn theo phong tục Việt Nam.`,
    url: `https://phongthuyviet.easycom.tech/van-khan/${props.prayer.slug}`,
    inLanguage: 'vi',
    publisher: {
        '@type': 'Organization',
        name: 'Phong Thủy Việt',
        url: 'https://phongthuyviet.easycom.tech',
    },
}));

const themeLabel = computed(() => {
    if (theme.value === 'sepia') return 'Giấy úa';
    if (theme.value === 'dark') return 'Đen';
    return 'Trắng';
});

function clampFont(next) {
    return Math.max(FONT_MIN, Math.min(FONT_MAX, next));
}

function decreaseFont() {
    fontSize.value = clampFont(fontSize.value - 1);
}

function increaseFont() {
    fontSize.value = clampFont(fontSize.value + 1);
}

function cycleTheme() {
    const i = THEMES.indexOf(theme.value);
    theme.value = THEMES[(i + 1) % THEMES.length];
}

function exportPdf() {
    window.open(route('prayers.pdf', props.prayer.slug), '_blank', 'noopener');
}

onMounted(() => {
    try {
        const savedFont = Number(localStorage.getItem(STORAGE_KEY_FONT_SIZE));
        if (!Number.isNaN(savedFont)) {
            fontSize.value = clampFont(savedFont);
        }
    } catch {}

    try {
        const savedTheme = localStorage.getItem(STORAGE_KEY_THEME);
        if (savedTheme && THEMES.includes(savedTheme)) {
            theme.value = savedTheme;
        }
    } catch {}
});

watch(fontSize, (val) => {
    try {
        localStorage.setItem(STORAGE_KEY_FONT_SIZE, String(val));
    } catch {}
});

watch(theme, (val) => {
    try {
        localStorage.setItem(STORAGE_KEY_THEME, val);
    } catch {}
});
</script>

<style scoped>
.page-hero { /* → see app.css */ }

.hero-noise { /* → see app.css */ }

.page-display { /* → see app.css */ }

.prayer-content {
    font-family: "Nunito Sans", "Inter", system-ui, sans-serif;
}

.theme-light {
    background: #ffffff;
    color: #2d1b13;
}

.theme-sepia {
    background: #f6ecd9;
    color: #45362e;
}

.theme-dark {
    background: #171513;
    color: #ece6da;
    border-color: rgba(218, 165, 32, 0.3);
}

.theme-dark .prayer-content,
.theme-dark header p {
    color: #ece6da;
}

.toolbar-btn {
    min-height: 40px;
    border-radius: 0.8rem;
    border: 1px solid rgba(138, 79, 23, 0.28);
    background: #fff7ea;
    color: #7a1700;
    font-size: 0.85rem;
    font-weight: 700;
    padding: 0.45rem 0.75rem;
    transition: background-color 0.15s ease;
}

.toolbar-btn:hover {
    background: #ffe8c5;
}

@media (max-width: 640px) {
    .toolbar-shell {
        width: calc(100vw - 1.5rem);
        justify-content: center;
    }
}

@media print {
    .no-print,
    header.sticky,
    footer {
        display: none !important;
    }

    .max-w-4xl {
        max-width: 100% !important;
    }

    article {
        position: relative !important;
        isolation: isolate !important;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        box-shadow: none !important;
        background: #fff !important;
        color: #000 !important;
    }

    .prayer-content {
        position: relative !important;
        z-index: 1 !important;
        color: #000 !important;
        font-size: 13pt !important;
        line-height: 1.7 !important;
    }

    article > header,
    article > div {
        position: relative;
        z-index: 1;
    }

    .print-brand {
        display: flex !important;
        align-items: center;
        gap: 8px;
        margin-bottom: 16px;
    }

    .print-brand-logo {
        width: 22px;
        height: 22px;
    }

    .print-brand-name {
        font-size: 12pt;
        font-weight: 700;
        color: #6c1500;
        margin: 0;
        font-family: "Playfair Display", serif;
    }
}

@media screen {
    .print-brand {
        display: none;
    }
}
</style>
