<template>
    <AppLayout>
        <SeoHead
            title="Lịch Hôm Nay – Lịch Âm Dương"
            description="Xem lịch hôm nay theo âm dương lịch Việt Nam. Giờ hoàng đạo, ngày tốt xấu, tiết khí và các thông tin phong thủy hữu ích."
            canonical="https://phongthuyviet.vn/lich-hom-nay"
        />
        <section class="page-hero relative overflow-hidden">
            <div class="hero-noise absolute inset-0 pointer-events-none" aria-hidden="true"></div>
            <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-9">
                <div class="max-w-3xl mx-auto text-center">
                    <p class="text-[#F3CF7A]/90 text-xs font-semibold uppercase tracking-[0.2em] mb-2">
                        Lịch Hôm Nay
                    </p>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white page-display">
                        {{ daily.solar.weekday }}, {{ daily.solar.day }}/{{ daily.solar.month }}/{{ daily.solar.year }}
                    </h1>
                </div>
            </div>
        </section>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-4">
            <div class="max-w-3xl mx-auto space-y-4">

                <div class="bg-white rounded-3xl border border-[#C58E3B]/25 shadow-[0_18px_45px_rgba(42,17,0,0.08)] p-4">
                    <div class="flex justify-between items-center">
                        <button
                            type="button"
                            @click="navigate(prevDate)"
                            class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-medium
                                   border border-[#DAA520]/40 text-[#8B0000] hover:bg-[#DAA520]/10 transition-colors"
                        >
                            ← Hôm qua
                        </button>
                        <button
                            type="button"
                            @click="goToday()"
                            class="px-3 py-1.5 rounded-full text-xs font-medium bg-[#8B0000]/10 text-[#8B0000] hover:bg-[#8B0000]/20 transition-colors"
                        >
                            Hôm nay
                        </button>
                        <button
                            type="button"
                            @click="navigate(nextDate)"
                            class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-medium
                                   border border-[#DAA520]/40 text-[#8B0000] hover:bg-[#DAA520]/10 transition-colors"
                        >
                            Ngày mai →
                        </button>
                    </div>
                </div>

                <div class="rounded-3xl border border-[#C58E3B]/25 bg-white shadow-sm overflow-hidden">

                <!-- Top strip (red, torn-paper style) -->
                <div
                    class="px-5 py-3 text-center text-white text-xs font-semibold uppercase tracking-widest"
                    style="background:#8B0000;"
                >
                    {{ daily.solar.weekday }}
                    <span v-if="daily.solarTerm" class="ml-2 text-[#DAA520]">· {{ daily.solarTerm }}</span>
                </div>

                <!-- Day number -->
                <div class="py-6 text-center border-b border-[#DAA520]/20"
                     style="background: repeating-linear-gradient(0deg, #FFF9F0 0px, #FFF9F0 1px, transparent 1px, transparent 24px);"
                >
                    <div
                        class="text-8xl font-bold leading-none"
                        style="font-family:'Playfair Display',serif; color:#8B0000;"
                    >
                        {{ daily.solar.day }}
                    </div>
                    <div class="text-gray-500 text-sm mt-2">
                        Tháng {{ daily.solar.month }} · {{ daily.solar.year }}
                    </div>
                </div>

                <!-- Lunar + Can Chi -->
                <div class="px-5 py-4 text-center bg-[#FDFBF7]">
                    <div class="text-[#8B0000] font-semibold text-sm">
                        Âm lịch: {{ daily.lunar.day }}/{{ daily.lunar.month }}
                        <span v-if="daily.lunar.isLeapMonth">(Nhuận)</span>
                        /{{ daily.lunar.year }}
                    </div>
                    <div class="text-gray-500 text-xs mt-1">
                        Ngày {{ daily.canChi.dayCan }} {{ daily.canChi.dayChi }}
                        · Tháng {{ daily.canChi.monthCan }} {{ daily.canChi.monthChi }}
                        · Năm {{ daily.canChi.yearCan }} {{ daily.canChi.yearChi }}
                    </div>
                </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <!-- Giờ Hoàng Đạo -->
                <div class="rounded-2xl border border-[#DAA520]/30 bg-white p-4">
                    <h2 class="text-xs font-semibold text-[#8B0000]/60 uppercase tracking-widest mb-3">
                        ⏰ Giờ Hoàng / Hắc Đạo
                    </h2>
                    <div class="grid grid-cols-3 gap-1.5">
                        <div
                            v-for="hour in daily.luckyHours"
                            :key="hour.name"
                            class="text-center rounded-lg py-1.5 text-xs font-medium"
                            :class="hour.isLucky
                                ? 'bg-[#DAA520]/15 text-[#8B6914]'
                                : 'bg-gray-100 text-gray-400'"
                        >
                            {{ hour.name }}
                            <span class="block text-[10px] font-normal opacity-70">
                                {{ hour.isLucky ? 'HD' : 'ĐD' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Hướng + Tuổi xung -->
                <div class="space-y-3">
                    <!-- Directions -->
                    <div class="rounded-2xl border border-[#DAA520]/30 bg-white p-4">
                        <h2 class="text-xs font-semibold text-[#8B0000]/60 uppercase tracking-widest mb-3">
                            🧭 Hướng Thần
                        </h2>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Hỷ Thần</span>
                                <span class="font-medium text-[#8B0000]">{{ daily.directions.happiness }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Tài Thần</span>
                                <span class="font-medium text-[#8B0000]">{{ daily.directions.wealth }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Conflict zodiacs -->
                    <div class="rounded-2xl border border-red-200 bg-red-50 p-4">
                        <h2 class="text-xs font-semibold text-red-500/70 uppercase tracking-widest mb-2">
                            ⚠️ Tuổi xung
                        </h2>
                        <div class="flex flex-wrap gap-2">
                            <span
                                v-for="zodiac in daily.conflictZodiacs"
                                :key="zodiac"
                                class="px-2.5 py-1 rounded-full bg-red-100 text-red-600 text-xs font-medium"
                            >
                                {{ zodiac }}
                            </span>
                        </div>
                        <p class="text-xs text-red-400 mt-2">
                            Tuổi này nên tránh việc lớn hôm nay
                        </p>
                    </div>
                </div>

                </div>
            </div>

        </div>

    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    daily: {
        type: Object,
        required: true,
    },
    prevDate: {
        type: String,
        required: true,
    },
    nextDate: {
        type: String,
        required: true,
    },
});

function navigate(date) {
    router.get(route('calendar.today'), { date }, { preserveScroll: true });
}

function goToday() {
    router.get(route('calendar.today'), {}, { preserveScroll: true });
}
</script>

<style scoped>
.page-hero { /* → see app.css */ }

.hero-noise { /* → see app.css */ }

.page-display { /* → see app.css */ }
</style>
