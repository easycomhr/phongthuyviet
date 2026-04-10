<template>
    <AppLayout>
        <SeoHead
            title="Tra Cứu Phong Thủy Chuẩn Việt"
            description="Tra cứu phong thủy chuẩn Việt – Lịch âm dương, ngày tốt xấu, xem tuổi hợp, hướng nhà, tử vi, văn khấn và đặt tên cho con."
            canonical="https://phongthuyviet.easycom.tech"
            :schema="websiteSchema"
        />
        <section class="home-hero relative overflow-hidden">
            <div class="hero-noise absolute inset-0 pointer-events-none" aria-hidden="true"></div>
            <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
                <!-- Hero badge — SVG lotus replacing emoji -->
                <div class="inline-flex items-center gap-2 rounded-full border border-[#C9A84C]/30 bg-[#C9A84C]/10 px-4 py-1.5 text-xs font-medium text-[#C9A84C] backdrop-blur-sm">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 36 36" fill="none" aria-hidden="true">
                        <path d="M18 3 C13 9 9 14 9 19 C9 24.5 13 28 18 28 C23 28 27 24.5 27 19 C27 14 23 9 18 3Z" fill="currentColor" opacity="0.9"/>
                        <path d="M8 10 C5 14 5 19 8 22 C10 25 14 27 18 26 C14 23 12 18 8 10Z" fill="currentColor" opacity="0.45"/>
                        <path d="M28 10 C31 14 31 19 28 22 C26 25 22 27 18 26 C22 23 24 18 28 10Z" fill="currentColor" opacity="0.45"/>
                    </svg>
                    Trang web tra cứu phong thủy Việt
                </div>
                <h1 class="mt-4 text-3xl sm:text-4xl lg:text-5xl font-bold text-white leading-tight home-display">
                    Tra cứu phong thủy chuẩn Việt,
                    <span class="text-[#C9A84C]">mang đến tài lộc, may mắn, an lành</span>
                </h1>
                <p class="mt-3 text-sm sm:text-base text-[#E8DFC8]/75 max-w-3xl">
                    Chọn loại việc, chọn ngày âm dương và nhận gợi ý tốt xấu để an tâm triển khai việc lớn.
                </p>
            </div>
        </section>

        <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 -mt-4 sm:-mt-6 relative z-20">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-5">
                <div class="lg:col-span-7 rounded-3xl border border-[#C9A84C]/20 bg-white shadow-[0_18px_45px_rgba(10,9,8,0.10)] p-5 sm:p-6">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#8B0000]/50">Thiết lập tra cứu</p>
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#1A1714] mt-1 home-heading">Tối ưu chọn nhanh</h2>
                        </div>
                        <span class="hidden sm:inline-flex items-center rounded-full bg-[#C9A84C]/10 px-3 py-1 text-xs text-[#8A6820]">
                            Gợi ý theo nhu cầu
                        </span>
                    </div>

                    <div class="mt-5">
                        <label for="viec-type" class="block text-sm text-[#4A3C30] font-medium mb-2">
                            Loại việc cần tra
                        </label>
                        <select
                            id="viec-type"
                            v-model="form.category_slug"
                            class="w-full rounded-2xl border border-[#C9A84C]/30 bg-[#F8F3EC] px-4 text-[#1A1714] text-sm font-medium
                                   focus:outline-none focus:ring-2 focus:ring-[#C9A84C]/30 focus:border-[#C9A84C]/60 transition-colors min-h-[50px]"
                            :class="{ 'border-red-400': form.errors.category_slug }"
                        >
                            <option value="" disabled>Chọn loại việc...</option>
                            <option v-for="cat in categories" :key="cat.slug" :value="cat.slug">
                                {{ cat.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.category_slug" class="text-red-500 text-xs mt-1.5">
                            {{ form.errors.category_slug }}
                        </p>
                    </div>

                    <div class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <button
                            v-for="quick in quickPicks"
                            :key="quick.value"
                            type="button"
                            @click="form.category_slug = quick.value"
                            class="rounded-xl border px-3 py-2.5 text-sm font-medium text-left transition-all min-h-[46px] cursor-pointer"
                            :class="form.category_slug === quick.value
                                ? 'bg-[#111110] text-[#C9A84C] border-[#111110] shadow-md'
                                : 'border-[#C9A84C]/30 text-[#4A3C30] hover:border-[#C9A84C]/60 hover:bg-[#C9A84C]/5'"
                        >
                            {{ quick.label }}
                        </button>
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-3">
                        <div class="rounded-2xl border border-[#C9A84C]/20 bg-[#F8F3EC] p-3">
                            <p class="text-[11px] uppercase tracking-[0.12em] text-[#8A6820]/70">Tốc độ tra cứu</p>
                            <p class="text-sm font-semibold text-[#1A1714] mt-1">1 màn hình, 1 thao tác</p>
                        </div>
                        <div class="rounded-2xl border border-[#C9A84C]/20 bg-[#F8F3EC] p-3">
                            <p class="text-[11px] uppercase tracking-[0.12em] text-[#8A6820]/70">Lịch hỗ trợ</p>
                            <p class="text-sm font-semibold text-[#1A1714] mt-1">Âm lịch + Dương lịch</p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5 rounded-3xl border border-[#C9A84C]/20 bg-white shadow-[0_18px_45px_rgba(10,9,8,0.10)] p-4 sm:p-5">
                    <p class="text-xs font-semibold text-[#8A6820] uppercase tracking-[0.16em] mb-3 text-center">
                        Lịch tra cứu
                    </p>
                    <LunarSolarDatePicker v-model="form.date" dense />
                    <p v-if="form.errors.date" class="text-red-500 text-xs mt-2 text-center">
                        {{ form.errors.date }}
                    </p>

                    <button
                        type="button"
                        @click="traNgay"
                        class="mt-3 w-full rounded-xl px-3 py-2 min-h-[44px] font-semibold text-sm
                               text-white bg-gradient-to-r from-[#8B0000] via-[#A12500] to-[#C06A1D]
                               hover:brightness-105 active:scale-[0.99] transition-all cursor-pointer"
                    >
                        Tra Ngày Tốt
                    </button>
                </div>
            </div>
        </section>

        <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-12">
            <div class="flex items-end justify-between gap-4 mb-4">
                <div>
                    <h2 class="text-2xl font-semibold text-[#1A1714] home-heading">Tiện ích nhanh</h2>
                    <p class="text-sm text-[#6B5C50] mt-1">Công cụ phong thủy truyền thống — tra cứu nhanh, kết quả chuẩn</p>
                </div>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
                <!-- v-html safe: feature icons are static trusted SVG strings -->
                <a
                    v-for="feature in features"
                    :key="feature.title"
                    :href="feature.href"
                    class="group rounded-2xl border border-[#C9A84C]/20 bg-white px-4 py-4 cursor-pointer
                           hover:border-[#C9A84C]/50 hover:shadow-[0_10px_30px_rgba(201,168,76,0.12)] transition-all duration-200"
                >
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-[#111110]/5
                                    group-hover:bg-[#111110] transition-colors duration-200 flex-shrink-0">
                            <span class="w-5 h-5 text-[#C9A84C] group-hover:text-[#C9A84C] transition-colors"
                                  v-html="feature.icon" aria-hidden="true"></span>
                        </div>
                        <div>
                            <p class="font-semibold text-sm text-[#1A1714] group-hover:text-[#8B0000] transition-colors">
                                {{ feature.title }}
                            </p>
                            <p class="text-xs text-[#7B6B5D] mt-0.5 leading-snug">{{ feature.desc }}</p>
                        </div>
                    </div>
                </a>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import LunarSolarDatePicker from '@/Components/LunarSolarDatePicker.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { useForm } from '@inertiajs/vue3';

const websiteSchema = {
    '@context': 'https://schema.org',
    '@type': 'WebSite',
    name: 'Phong Thủy Việt',
    url: 'https://phongthuyviet.easycom.tech',
    description: 'Tra cứu phong thủy chuẩn Việt – Lịch âm dương, ngày tốt xấu, xem tuổi hợp, hướng nhà, tử vi, văn khấn.',
    inLanguage: 'vi',
    potentialAction: {
        '@type': 'SearchAction',
        target: {
            '@type': 'EntryPoint',
            urlTemplate: 'https://phongthuyviet.easycom.tech/tra-cuu-ngay-tot?date={search_term_string}',
        },
        'query-input': 'required name=search_term_string',
    },
};

const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    category_slug: '',
    date: new Date().toISOString().split('T')[0],
});

const quickPicks = [
    { value: 'cuoi-hoi',    label: 'Cưới hỏi' },
    { value: 'khoi-cong',   label: 'Khởi công' },
    { value: 'khai-truong', label: 'Khai trương' },
];

// Feature icons — static trusted SVG strings, safe for v-html
const calendarIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>`;
const heartIcon    = `<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>`;
const userStarIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>`;
const starIcon     = `<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>`;
const bookIcon     = `<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>`;
const compassIcon  = `<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76" fill="currentColor" stroke="none"/></svg>`;
const galaxyIcon   = `<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/></svg>`;

const features = [
    { href: '/tra-ngay-tot',    icon: calendarIcon, title: 'Ngày Tốt – Xấu',    desc: 'Chọn ngày lành theo lịch âm' },
    { href: '/ngay-cuoi',       icon: heartIcon,    title: 'Ngày Cưới Hỏi',     desc: 'Lọc ngày đại cát cho hôn lễ' },
    { href: '/dat-ten-cho-con', icon: userStarIcon, title: 'Đặt Tên Cho Con',   desc: 'Gợi ý tên hợp mệnh cho bé' },
    { href: '/xem-tuoi',        icon: starIcon,     title: 'Xem Tuổi Vợ Chồng', desc: 'Có hợp tuổi kết hôn không?' },
    { href: '/van-khan',        icon: bookIcon,     title: 'Văn Khấn',           desc: 'Hơn 50 bài khấn truyền thống' },
    { href: '/huong-nha',       icon: compassIcon,  title: 'Hướng Nhà Tốt',     desc: 'Theo tuổi và cung mệnh' },
    { href: '/tu-vi',           icon: galaxyIcon,   title: 'Tử Vi – Bát Tự',    desc: 'Lá số tử vi theo năm sinh' },
];

function traNgay() {
    form.get(route('search.index'));
}
</script>

<style scoped>
/* home-hero, hero-noise, home-display, home-heading — defined globally in app.css */
</style>
