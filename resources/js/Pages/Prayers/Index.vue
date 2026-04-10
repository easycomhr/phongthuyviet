<template>
    <AppLayout>
        <SeoHead
            title="Văn Khấn Chuẩn – Văn Khấn Theo Phong Tục Việt"
            description="Tổng hợp văn khấn chuẩn theo phong tục Việt Nam. Văn khấn thần tài, văn khấn gia tiên, văn khấn ngày rằm mùng một và các dịp lễ tết."
            canonical="https://phongthuyviet.easycom.tech/van-khan"
        />
        <section class="page-hero relative overflow-hidden no-print">
            <div class="hero-noise absolute inset-0 pointer-events-none" aria-hidden="true"></div>
            <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-9 text-center">
                <p class="text-[#F3CF7A]/90 text-xs font-semibold uppercase tracking-[0.2em] mb-2">
                    Văn khấn theo truyền thống Việt
                </p>
                <h1 class="text-2xl sm:text-4xl font-bold text-white mb-2 page-display">
                    Chọn bài văn khấn và in để cúng lễ
                </h1>
                <p class="text-white/75 text-sm">
                    Chọn đúng nghi lễ, đọc nội dung đầy đủ và in ra để cúng vái đúng chuẩn nghi thức.
                </p>
            </div>
        </section>

        <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 -mt-4 sm:-mt-6 relative z-20 pb-12">
            <div class="rounded-3xl border border-[#C58E3B]/25 bg-white shadow-[0_18px_45px_rgba(42,17,0,0.08)] p-4 sm:p-5 no-print">
                <div class="grid grid-cols-1 md:grid-cols-[220px_1fr_auto] gap-3 items-end">
                    <div>
                        <label class="block text-xs font-medium text-[#7B6559] mb-1">Loại văn khấn</label>
                        <select
                            v-model="selectedCategorySlug"
                            class="w-full rounded-xl border border-[#C58E3B]/35 bg-[#FCF8F1] px-3 text-gray-800 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-[#8B0000]/25 focus:border-[#8B0000]"
                            style="min-height:44px;"
                        >
                            <option value="">Tất cả danh mục</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.slug">
                                {{ cat.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#7B6559] mb-1">Bài văn khấn</label>
                        <select
                            v-model="selectedPrayerSlug"
                            class="w-full rounded-xl border border-[#C58E3B]/35 bg-[#FCF8F1] px-3 text-gray-800 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-[#8B0000]/25 focus:border-[#8B0000]"
                            style="min-height:44px;"
                        >
                            <option
                                v-for="item in filteredPrayers"
                                :key="item.slug"
                                :value="item.slug"
                            >
                                {{ item.title }}
                            </option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 md:flex gap-2">
                        <button
                            type="button"
                            @click="loadPrayer"
                            class="rounded-xl px-4 py-2.5 text-sm font-semibold text-white
                                   bg-gradient-to-r from-[#8B0000] via-[#A12500] to-[#C06A1D] hover:brightness-105 transition-all"
                        >
                            Xem bài
                        </button>
                        <button
                            type="button"
                            @click="printPrayer"
                            class="rounded-xl px-4 py-2.5 text-sm font-semibold text-[#7A1700]
                                   border border-[#C58E3B]/45 bg-[#FFF5E7] hover:bg-[#FFE8C6] transition-colors"
                            :disabled="!selectedPrayer"
                        >
                            In bài
                        </button>
                    </div>
                </div>
            </div>

            <article
                v-if="selectedPrayer"
                class="mt-4 rounded-3xl border border-[#C58E3B]/25 bg-white p-5 sm:p-7 print-article"
            >
                <div class="print-brand">
                    <img :src="'/favicon.svg'" alt="Phong Thủy Việt" class="print-brand-logo">
                    <p class="print-brand-name">Phong Thủy Việt</p>
                </div>

                <header class="mb-5 border-b border-[#DCC7A3]/45 pb-4">
                    <p class="text-xs uppercase tracking-[0.12em] text-[#8B0000]/60">
                        {{ selectedPrayer.prayer_category?.name || categoryNameFromList(selectedPrayer.category_id) }}
                    </p>
                    <h2 class="mt-1 text-2xl sm:text-3xl font-bold text-[#6C1500] page-display">
                        {{ selectedPrayer.title }}
                    </h2>
                    <p class="text-xs text-[#7F6A5D] mt-2">
                        Bài văn khấn tham khảo theo truyền thống cúng lễ của người Việt.
                    </p>
                </header>

                <div class="prayer-content text-[15px] sm:text-base leading-[1.74] whitespace-pre-line text-[#2F231C]">
                    {{ selectedPrayer.content }}
                </div>

                <div class="mt-5 rounded-2xl border border-[#E6D0AF] bg-[#FFF8EC] p-3.5 text-sm text-[#5D4A40] no-print">
                    <p class="font-semibold text-[#6A1A00]">Lưu ý khi sử dụng văn khấn</p>
                    <p class="mt-1 leading-relaxed">
                        Điền đầy đủ các trường trong ngoặc vuông như `[Họ tên]`, `[Địa chỉ]`, `[Ngày tháng năm]`,
                        đọc rõ ràng, thành tâm và giữ không khí trang nghiêm theo đúng nghi lễ gia đình.
                    </p>
                </div>
            </article>

            <div
                v-else
                class="mt-4 rounded-3xl border border-[#E3CBAB] bg-[#FFFCF6] p-5 text-sm text-[#6F5A4D]"
            >
                Chưa có dữ liệu bài văn khấn để hiển thị.
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { computed, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
    selectedPrayer: {
        type: Object,
        default: null,
    },
});

const selectedPrayerSlug = ref(props.selectedPrayer?.slug || '');
const selectedCategorySlug = ref(
    props.selectedPrayer?.prayer_category?.slug
    || findCategorySlugById(props.selectedPrayer?.category_id, props.categories)
    || ''
);

const allPrayers = computed(() => props.categories.flatMap((cat) =>
    (cat.prayers || []).map((prayer) => ({
        ...prayer,
        categorySlug: cat.slug,
    }))
));

const filteredPrayers = computed(() => {
    if (!selectedCategorySlug.value) {
        return allPrayers.value;
    }
    return allPrayers.value.filter((item) => item.categorySlug === selectedCategorySlug.value);
});

watch(filteredPrayers, (list) => {
    if (!list.length) {
        selectedPrayerSlug.value = '';
        return;
    }

    if (!list.find((item) => item.slug === selectedPrayerSlug.value)) {
        selectedPrayerSlug.value = list[0].slug;
    }
}, { immediate: true });

function loadPrayer() {
    if (!selectedPrayerSlug.value) return;
    router.get(route('prayers.index'), {
        prayer_slug: selectedPrayerSlug.value,
    }, {
        preserveScroll: true,
    });
}

function printPrayer() {
    if (!selectedPrayerSlug.value) return;
    window.open(route('prayers.pdf', selectedPrayerSlug.value), '_blank', 'noopener');
}

function findCategorySlugById(categoryId, categories) {
    if (!categoryId) return '';
    return categories.find((cat) => cat.id === categoryId)?.slug || '';
}

function categoryNameFromList(categoryId) {
    return props.categories.find((cat) => cat.id === categoryId)?.name || 'Văn khấn';
}
</script>

<style scoped>
.page-hero { /* → see app.css */ }

.hero-noise { /* → see app.css */ }

.page-display { /* → see app.css */ }

.prayer-content {
    font-family: "Nunito Sans", "Inter", system-ui, sans-serif;
}

@media print {
    .no-print,
    header.sticky,
    footer {
        display: none !important;
    }

    .print-article {
        position: relative !important;
        isolation: isolate !important;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        box-shadow: none !important;
        background: #fff !important;
    }

    .prayer-content {
        position: relative !important;
        z-index: 1 !important;
        color: #000 !important;
        font-size: 13pt !important;
        line-height: 1.7 !important;
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

    .print-article > header,
    .print-article > .no-print {
        position: relative;
        z-index: 1;
    }
}

@media screen {
    .print-brand {
        display: none;
    }
}
</style>
