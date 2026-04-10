<template>
    <AppLayout>
        <SeoHead
            title="Xem Tử Vi – Lá Số Tử Vi Theo Ngày Sinh"
            description="Xem lá số tử vi theo ngày sinh, giờ sinh và giới tính. Phân tích cung mệnh, cung tài lộc, hôn nhân và vận hạn theo tử vi Việt Nam."
            canonical="https://phongthuyviet.easycom.tech/tu-vi"
        />
        <section class="page-hero relative overflow-hidden">
            <div class="hero-noise absolute inset-0 pointer-events-none" aria-hidden="true"></div>
            <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-9 text-center">
                <p class="text-[#F3CF7A]/90 text-xs font-semibold uppercase tracking-[0.2em] mb-2">
                    Tích hợp engine tử vi
                </p>
                <h1 class="text-2xl sm:text-4xl font-bold text-white mb-2 page-display">
                    Lập lá số tử vi theo ngày sinh
                </h1>
                <p class="text-white/75 text-sm">
                    Chuẩn hóa dữ liệu qua Adapter và hiển thị kết quả trực quan theo cung - sao.
                </p>
            </div>
        </section>

        <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 -mt-4 sm:-mt-6 relative z-20 pb-10 space-y-4">
            <div class="rounded-3xl border border-[#C58E3B]/25 bg-white shadow-[0_18px_45px_rgba(42,17,0,0.08)] p-4 sm:p-6">
                <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-[220px_1fr_auto] gap-4 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Giới tính</label>
                        <select
                            v-model="form.gender"
                            class="w-full rounded-xl border border-[#C58E3B]/35 bg-[#FCF8F1] px-3 text-gray-800 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-[#8B0000]/30 focus:border-[#8B0000]"
                            style="min-height:44px;"
                        >
                            <option value="male">Nam</option>
                            <option value="female">Nữ</option>
                        </select>
                        <p v-if="form.errors.gender" class="text-xs text-red-500 mt-1">{{ form.errors.gender }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Ngày sinh (âm/dương)</label>
                        <LunarSolarDatePicker v-model="form.birth_date" dense />
                        <p v-if="form.errors.birth_date" class="text-xs text-red-500 mt-1">{{ form.errors.birth_date }}</p>
                    </div>

                    <button
                        type="submit"
                        class="rounded-xl px-3 py-2 min-h-[44px] text-sm font-semibold text-white
                               bg-gradient-to-r from-[#8B0000] via-[#A12500] to-[#C06A1D]
                               hover:brightness-105 active:scale-[0.99] transition-all"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Đang lập lá số...' : 'Lập Lá Số' }}
                    </button>
                </form>
            </div>

            <div v-if="result" class="space-y-4">
                <div class="rounded-3xl border border-[#C58E3B]/25 bg-white p-5 sm:p-6 shadow-sm">
                    <div class="grid grid-cols-1 lg:grid-cols-[1.1fr_0.9fr] gap-4">
                        <div>
                            <p class="text-xs uppercase tracking-[0.12em] text-[#8B0000]/55">Thông tin mệnh bàn</p>
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#651500] mt-1">{{ result.chart.profile.menh }}</h2>
                            <p class="text-sm text-[#6D584C] mt-1">
                                {{ result.chart.profile.gender }} · Sinh {{ result.chart.profile.birthDate }} · Âm lịch {{ result.chart.profile.lunarDateLabel }}
                            </p>
                            <p class="text-sm text-[#6D584C] mt-1">
                                Năm {{ result.chart.profile.canChiYear }} · Ngày {{ result.chart.profile.canChiDay }} · {{ result.chart.profile.cuc }}
                            </p>
                        </div>
                        <div class="rounded-2xl border border-[#E4CDA9] bg-[#FFF8EC] p-4">
                            <p class="text-xs uppercase tracking-[0.12em] text-[#8B0000]/55">Âm lịch quy đổi</p>
                            <p class="text-sm text-[#5D4A40] mt-1">
                                {{ result.lunarDate.lunar_day }}/{{ result.lunarDate.lunar_month }}/{{ result.lunarDate.lunar_year }}
                                <span v-if="result.lunarDate.is_leap_month">(Nhuận)</span>
                            </p>
                            <p class="text-xs text-[#7E675A] mt-2 leading-relaxed">{{ result.note }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-[#C58E3B]/25 bg-white p-4 sm:p-5 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="text-lg font-semibold text-[#651500]">Chế độ hiển thị lá số</h3>
                        <div class="inline-flex rounded-full border border-[#DAA520]/60 overflow-hidden text-sm font-medium">
                            <button
                                type="button"
                                class="px-4 py-2 min-h-[40px] transition-colors"
                                :class="viewMode === 'simple'
                                    ? 'bg-[#8B0000] text-white'
                                    : 'bg-white text-gray-600 hover:bg-[#DAA520]/10'"
                                @click="viewMode = 'simple'"
                            >
                                Đơn giản
                            </button>
                            <button
                                type="button"
                                class="px-4 py-2 min-h-[40px] transition-colors"
                                :class="viewMode === 'expert'
                                    ? 'bg-[#8B0000] text-white'
                                    : 'bg-white text-gray-600 hover:bg-[#DAA520]/10'"
                                @click="viewMode = 'expert'"
                            >
                                Chuyên gia
                            </button>
                        </div>
                    </div>
                </div>

                <TuViPalaceBoard
                    :palaces="result.chart.palaces"
                    :profile="result.chart.profile"
                    :mode="viewMode"
                />
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import SeoHead from '@/Components/SeoHead.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import LunarSolarDatePicker from '@/Components/LunarSolarDatePicker.vue';
import TuViPalaceBoard from '@/Components/TuViPalaceBoard.vue';

const props = defineProps({
    defaults: {
        type: Object,
        default: () => ({
            gender: 'male',
            birth_date: '1992-01-01',
        }),
    },
    result: {
        type: Object,
        default: null,
    },
});

const form = useForm({
    gender: props.defaults.gender || 'male',
    birth_date: props.defaults.birth_date || '1992-01-01',
});

const viewMode = ref('simple');

function submit() {
    form.post(route('tuvi.result'));
}
</script>

<style scoped>
.page-hero { /* → see app.css */ }

.hero-noise { /* → see app.css */ }

.page-display { /* → see app.css */ }
</style>
