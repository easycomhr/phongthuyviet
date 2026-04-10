<template>
    <AppLayout>
        <SeoHead
            title="Chọn Ngày Cưới Đẹp Theo Phong Thủy 2025"
            description="Tìm ngày cưới đẹp, hợp tuổi đôi uyên ương theo phong thủy Việt Nam. Tra ngày tốt để tổ chức hôn lễ, tránh ngày xấu và giờ hung."
            canonical="https://phongthuyviet.easycom.tech/ngay-cuoi"
        />
        <section class="page-hero relative overflow-hidden">
            <div class="hero-noise absolute inset-0 pointer-events-none" aria-hidden="true"></div>
            <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-9 text-center">
                <p class="text-[#F3CF7A]/90 text-xs font-semibold uppercase tracking-[0.2em] mb-2">
                    Tra cứu ngày cưới hỏi
                </p>
                <h1 class="text-2xl sm:text-4xl font-bold text-white mb-2 page-display">
                    Chọn ngày đại cát cho ăn hỏi - rước dâu
                </h1>
                <p class="text-white/75 text-sm">
                    Lọc theo Kim Lâu, tháng Đại/Tiểu Lợi, ngày Bất Tương - Hoàng Đạo và xung khắc can chi.
                </p>
            </div>
        </section>

        <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 -mt-4 sm:-mt-6 relative z-20 pb-10 space-y-4">
            <div class="rounded-3xl border border-[#C58E3B]/25 bg-white shadow-[0_18px_45px_rgba(42,17,0,0.08)] p-4 sm:p-6">
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="rounded-2xl border border-[#E2C69A]/45 bg-[#FFFCF6] p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h2 class="text-base font-semibold text-[#601100]">Nhà gái (Cô dâu)</h2>
                                <span class="text-xs rounded-full bg-[#8B0000]/10 px-2.5 py-1 text-[#8B0000]">Nữ mạng làm gốc</span>
                            </div>

                            <label class="block text-xs font-medium text-gray-600 mb-1">Tên cô dâu</label>
                            <input
                                v-model="form.bride_name"
                                type="text"
                                class="w-full rounded-xl border border-[#C58E3B]/35 bg-white px-3 py-2 text-sm text-gray-800
                                       focus:outline-none focus:ring-2 focus:ring-[#8B0000]/30 focus:border-[#8B0000]"
                            >
                            <p v-if="form.errors.bride_name" class="text-xs text-red-500 mt-1">{{ form.errors.bride_name }}</p>

                            <div class="mt-3">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Ngày sinh dương lịch</label>
                                <LunarSolarDatePicker v-model="form.bride_birth_date" dense />
                                <p v-if="form.errors.bride_birth_date" class="text-xs text-red-500 mt-1">{{ form.errors.bride_birth_date }}</p>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-[#E2C69A]/45 bg-[#FFFCF6] p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h2 class="text-base font-semibold text-[#601100]">Nhà trai (Chú rể)</h2>
                                <span class="text-xs rounded-full bg-[#8B0000]/10 px-2.5 py-1 text-[#8B0000]">Đối chiếu xung khắc</span>
                            </div>

                            <label class="block text-xs font-medium text-gray-600 mb-1">Tên chú rể</label>
                            <input
                                v-model="form.groom_name"
                                type="text"
                                class="w-full rounded-xl border border-[#C58E3B]/35 bg-white px-3 py-2 text-sm text-gray-800
                                       focus:outline-none focus:ring-2 focus:ring-[#8B0000]/30 focus:border-[#8B0000]"
                            >
                            <p v-if="form.errors.groom_name" class="text-xs text-red-500 mt-1">{{ form.errors.groom_name }}</p>

                            <div class="mt-3">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Ngày sinh dương lịch</label>
                                <LunarSolarDatePicker v-model="form.groom_birth_date" dense />
                                <p v-if="form.errors.groom_birth_date" class="text-xs text-red-500 mt-1">{{ form.errors.groom_birth_date }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-[#E2C69A]/40 bg-[#FFF8EC] p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#8B0000]/55 mb-2">Thời gian dự kiến tổ chức</p>
                        <div class="grid grid-cols-2 sm:grid-cols-[minmax(260px,1.35fr)_120px_110px_120px] gap-2.5 items-end">
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Loại nghi lễ</label>
                                <select
                                    v-model="form.event_type"
                                    class="w-full rounded-xl border border-[#C58E3B]/35 bg-white px-3 py-2 text-sm text-gray-800
                                           focus:outline-none focus:ring-2 focus:ring-[#8B0000]/30 focus:border-[#8B0000]"
                                    style="min-height:44px;"
                                >
                                    <option value="engagement">Ngày hỏi vợ · Ăn hỏi</option>
                                    <option value="wedding">Ngày cưới · Rước dâu</option>
                                </select>
                                <p v-if="form.errors.event_type" class="text-xs text-red-500 mt-1">{{ form.errors.event_type }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Tháng</label>
                                <select
                                    v-model.number="form.target_month"
                                    class="w-full rounded-xl border border-[#C58E3B]/35 bg-white px-3 py-2 text-sm text-gray-800
                                           focus:outline-none focus:ring-2 focus:ring-[#8B0000]/30 focus:border-[#8B0000]"
                                    style="min-height:44px;"
                                >
                                    <option v-for="m in 12" :key="m" :value="m">Tháng {{ m }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Năm</label>
                                <select
                                    v-model.number="form.target_year"
                                    class="w-full rounded-xl border border-[#C58E3B]/35 bg-white px-3 py-2 text-sm text-gray-800
                                           focus:outline-none focus:ring-2 focus:ring-[#8B0000]/30 focus:border-[#8B0000]"
                                    style="min-height:44px;"
                                >
                                    <option v-for="y in yearRange" :key="y" :value="y">{{ y }}</option>
                                </select>
                            </div>
                            <button
                                type="submit"
                                class="col-span-2 sm:col-span-1 w-full rounded-xl px-3 py-2 min-h-[44px] text-sm font-semibold text-white
                                       bg-gradient-to-r from-[#8B0000] via-[#A12500] to-[#C06A1D]
                                       hover:brightness-105 active:scale-[0.99] transition-all"
                                :disabled="form.processing"
                            >
                                {{ form.processing ? 'Đang lọc ngày...' : 'Tra Ngày' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div v-if="result" class="space-y-4">
                <div
                    v-if="result.kimLau?.isViolated"
                    class="rounded-2xl border border-amber-300 bg-amber-50 p-4 text-amber-900"
                >
                    <p class="text-xs uppercase tracking-[0.12em] font-semibold">Cảnh báo Kim Lâu</p>
                    <p class="text-sm mt-1">{{ result.kimLau.message }}</p>
                </div>

                <div class="rounded-3xl border border-[#C58E3B]/25 bg-white p-4 sm:p-5 shadow-sm">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <p class="text-xs uppercase tracking-[0.12em] text-[#8B0000]/55">Kết quả lọc ngày</p>
                            <h3 class="text-lg font-semibold text-[#651500]">
                                {{ result.targetLabel }} · {{ result.dates.length }} ngày phù hợp
                            </h3>
                        </div>
                        <p class="text-xs text-[#7A665B]">
                            Tuổi xung cần tránh:
                            {{ result.zodiac.brideClashDayChi }} (nhà gái) · {{ result.zodiac.groomClashDayChi }} (nhà trai)
                        </p>
                    </div>
                </div>

                <div v-if="result.dates.length" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
                    <article
                        v-for="item in result.dates"
                        :key="item.date"
                        class="rounded-2xl border border-[#E6D0AF] bg-[#FFFCF7] p-3.5 shadow-sm"
                    >
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="text-sm font-semibold text-[#651500]">{{ item.solarLabel }}</p>
                                <p class="text-xs text-[#7A6458] mt-0.5">{{ item.weekday }} · Âm {{ item.lunarLabel }}</p>
                                <p class="text-xs text-[#7A6458]">Can chi ngày: {{ item.canChi }}</p>
                                <p class="text-xs text-[#7A6458]">Nghi lễ: {{ item.eventLabel }}</p>
                            </div>
                            <span class="rounded-full bg-[#8B0000]/10 text-[#8B0000] text-xs font-semibold px-2 py-0.5">
                                {{ item.score }}/100
                            </span>
                        </div>

                        <div class="mt-2 flex flex-wrap gap-1.5">
                            <span
                                v-for="badge in item.badges"
                                :key="item.date + badge"
                                class="inline-flex items-center rounded-full border px-2 py-0.5 text-[11px] font-semibold"
                                :class="badge === 'Bất Tương'
                                    ? 'border-red-300 bg-red-50 text-red-700'
                                    : 'border-emerald-300 bg-emerald-50 text-emerald-700'"
                            >
                                {{ badge }}
                            </span>
                        </div>

                        <p class="mt-2 text-xs text-[#6B5649] leading-relaxed">{{ item.reason }}</p>
                        <p class="mt-1.5 text-xs text-[#7A4A12] leading-relaxed">
                            Giờ tiến hành: {{ item.executionHours.length ? item.executionHours.join(' · ') : 'Chưa có dữ liệu' }}
                        </p>

                        <div class="mt-3">
                            <button
                                type="button"
                                class="w-full rounded-lg border border-[#C58E3B]/45 bg-white px-2 py-2 text-xs font-medium text-[#8B0000] hover:bg-[#FFF7EA]"
                                @click="printPdf(item)"
                            >
                                In PDF
                            </button>
                        </div>
                    </article>
                </div>

                <div v-else class="rounded-2xl border border-[#E4CDA9] bg-[#FFF8EC] p-4 text-sm text-[#6F5A4D]">
                    Chưa tìm được ngày phù hợp trong tháng này theo bộ lọc hiện tại. Bạn có thể đổi sang tháng khác hoặc nới điều kiện.
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import LunarSolarDatePicker from '@/Components/LunarSolarDatePicker.vue';
import SeoHead from '@/Components/SeoHead.vue';

const props = defineProps({
    defaults: {
        type: Object,
        default: () => ({
            bride_name: 'Cô dâu',
            bride_birth_date: '1998-01-01',
            groom_name: 'Chú rể',
            groom_birth_date: '1996-01-01',
            event_type: 'wedding',
            target_year: 2026,
            target_month: 10,
        }),
    },
    result: {
        type: Object,
        default: null,
    },
});

const form = useForm({
    bride_name: props.defaults.bride_name,
    bride_birth_date: props.defaults.bride_birth_date,
    groom_name: props.defaults.groom_name,
    groom_birth_date: props.defaults.groom_birth_date,
    event_type: props.defaults.event_type || 'wedding',
    target_year: Number(props.defaults.target_year),
    target_month: Number(props.defaults.target_month),
});

const currentYear = new Date().getFullYear();
const yearRange = computed(() => Array.from({ length: 18 }, (_, i) => currentYear - 1 + i));

function submit() {
    form.post(route('wedding.result'), { preserveScroll: true });
}

function printPdf(item) {
    const params = {
        bride_name: form.bride_name,
        bride_birth_date: form.bride_birth_date,
        groom_name: form.groom_name,
        groom_birth_date: form.groom_birth_date,
        event_type: form.event_type,
        target_year: form.target_year,
        target_month: form.target_month,
        date: item.date,
    };

    window.open(route('wedding.pdf', params), '_blank', 'noopener');
}
</script>

<style scoped>
.page-hero { /* → see app.css */ }

.hero-noise { /* → see app.css */ }

.page-display { /* → see app.css */ }
</style>
