<template>
    <AppLayout>
        <section class="page-hero relative overflow-hidden">
            <div class="hero-noise absolute inset-0 pointer-events-none" aria-hidden="true"></div>
            <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-9 text-center">
                <p class="text-[#F3CF7A]/90 text-xs font-semibold uppercase tracking-[0.2em] mb-2">
                    Công cụ tra cứu phong thủy
                </p>
                <h1 class="text-2xl sm:text-4xl font-bold text-white mb-2 page-display">
                    Tra Cứu Ngày Tốt – Xấu
                </h1>
                <p class="text-white/75 text-sm">Xem ngày phù hợp theo lịch âm dương chuẩn Việt Nam</p>
            </div>
        </section>

        <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 -mt-4 sm:-mt-6 relative z-20">
            <div class="bg-white rounded-3xl shadow-[0_18px_45px_rgba(42,17,0,0.08)] p-5 sm:p-6 border border-[#C58E3B]/25">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 items-end">
                    <!-- Loại việc -->
                    <div class="lg:col-span-4">
                        <label class="block text-xs text-gray-500 mb-1 font-medium">Loại việc</label>
                        <select
                            v-model="form.loai"
                            class="w-full rounded-xl border border-[#C58E3B]/35 bg-[#FCF8F1] px-3 text-gray-800 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-[#8B0000]/30 focus:border-[#8B0000]"
                            style="min-height:44px;"
                        >
                            <option value="" disabled>Chọn loại việc...</option>
                            <option v-for="v in loaiViecs" :key="v.value" :value="v.value">
                                {{ v.label }}
                            </option>
                        </select>
                    </div>

                    <!-- Date picker -->
                    <div class="lg:col-span-6">
                        <label class="block text-xs text-gray-500 mb-1 font-medium">Ngày tra cứu</label>
                        <LunarSolarDatePicker v-model="form.ngay" dense />
                    </div>

                    <!-- Button -->
                    <div class="lg:col-span-2 flex items-end">
                        <button
                            type="button"
                            @click="traNgay"
                            class="w-full px-6 rounded-xl font-semibold text-white text-sm
                                   bg-gradient-to-r from-[#8B0000] via-[#A12500] to-[#C06A1D]
                                   hover:brightness-105 active:scale-[0.99] transition-all"
                            style="min-height:44px;"
                        >
                            Tra ngay
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- No selection state -->
            <div
                v-if="!form.loai || !form.ngay"
                class="text-center py-14 text-gray-400 rounded-3xl border border-[#E6D6C2] bg-white"
            >
                <div class="w-16 h-16 rounded-2xl bg-[#C9A84C]/10 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-[#C9A84C]/60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
                <p class="text-base">Vui lòng chọn loại việc và ngày để tra cứu.</p>
            </div>

            <!-- Result card -->
            <div v-else class="space-y-4">
                <!-- Date summary -->
                <div class="rounded-3xl border border-[#C58E3B]/25 bg-white p-5 sm:p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-[#C9A84C]/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#C9A84C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800 text-sm">{{ loaiLabel }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">
                                Dương lịch: <span class="text-[#8B0000] font-medium">{{ formatSolar(form.ngay) }}</span>
                                · Âm lịch: <span class="text-[#DAA520] font-medium">{{ lunarLabel }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Placeholder result -->
                    <div class="rounded-2xl bg-[#FDFBF7] border border-[#DAA520]/20 p-5 text-center">
                        <div class="w-12 h-12 rounded-xl bg-[#C9A84C]/10 flex items-center justify-center mx-auto mb-2"><svg class="w-6 h-6 text-[#C9A84C]/70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/></svg></div>
                        <p class="text-sm text-gray-500">
                            Tính năng tra cứu ngày tốt xấu đang được phát triển.
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            Sẽ hiển thị: Can Chi, Ngũ hành, Sao ngày, Hung/Cát cho {{ loaiLabel }}.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import LunarSolarDatePicker from '@/Components/LunarSolarDatePicker.vue';
import { solarToLunar } from '@/utils/lunar.js';

const props = defineProps({
    category_slug: { type: String, default: '' },
    date: { type: String, default: '' },
});

const form = ref({
    loai: props.category_slug,
    ngay: props.date || new Date().toISOString().split('T')[0],
});

const loaiViecs = [
    { value: 'cuoi-hoi',    label: 'Cưới hỏi' },
    { value: 'khoi-cong',   label: 'Khởi công xây dựng' },
    { value: 'nhap-trach',  label: '🏠 Nhập trạch – Dọn nhà' },
    { value: 'khai-truong', label: 'Khai trương kinh doanh' },
    { value: 'dong-tho',    label: '⛏️ Động thổ' },
    { value: 'xuat-hanh',   label: '🚗 Xuất hành' },
    { value: 'ma-chay',     label: '🕯️ Ma chay – Tang lễ' },
];

const loaiLabel = computed(() =>
    loaiViecs.find(v => v.value === form.value.loai)?.label ?? form.value.loai
);

const lunarLabel = computed(() => {
    if (!form.value.ngay) return '';
    try {
        const [y, m, d] = form.value.ngay.split('-').map(Number);
        const l = solarToLunar(y, m, d);
        const leap = l.isLeapMonth ? ' Nhuận' : '';
        return `${l.lunarDay} tháng ${l.lunarMonth}${leap} năm ${l.lunarYear}`;
    } catch {
        return '';
    }
});

function formatSolar(iso) {
    if (!iso) return '';
    const [y, m, d] = iso.split('-').map(Number);
    return `${d}/${m}/${y}`;
}

function traNgay() {
    if (!form.value.loai || !form.value.ngay) return;
    const monthYear = form.value.ngay.slice(0, 7);
    window.location.href = `/tra-cuu-ngay-tot?category_slug=${form.value.loai}&month_year=${monthYear}`;
}
</script>

<style scoped>
.page-hero { /* → see app.css */ }

.hero-noise { /* → see app.css */ }

.page-display { /* → see app.css */ }
</style>
