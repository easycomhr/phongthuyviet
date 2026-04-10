<template>
    <AppLayout>
        <SeoHead
            title="Tra Cứu Ngày Tốt Xấu 2025"
            description="Tra cứu ngày tốt xấu theo phong thủy Việt Nam. Chọn loại việc, nhập ngày âm dương để xem giờ hoàng đạo, ngày đẹp phù hợp."
            canonical="https://phongthuyviet.easycom.tech/tra-cuu-ngay-tot"
        />
        <section class="page-hero relative overflow-hidden">
            <div class="hero-noise absolute inset-0 pointer-events-none" aria-hidden="true"></div>
            <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-9 text-center">
                <p class="text-[#F3CF7A]/90 text-xs font-semibold uppercase tracking-[0.2em] mb-2">
                    Tra cứu ngày tốt theo việc
                </p>
                <h1 class="text-2xl sm:text-3xl font-bold text-white page-display">
                    {{ category.name }} — {{ monthLabel }}
                </h1>
                <p class="text-white/75 text-sm mt-2">
                    Chọn ngày cụ thể để xem kết luận nên làm hay nên tránh, kèm giải thích chi tiết.
                </p>
            </div>
        </section>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 -mt-4 sm:-mt-6 relative z-20">
            <div class="bg-white rounded-3xl border border-[#C58E3B]/25 shadow-[0_18px_45px_rgba(42,17,0,0.08)] p-4 sm:p-5">
                <div class="grid grid-cols-1 md:grid-cols-[1fr_1fr_auto] gap-3 items-end">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1 font-medium">Loại việc</label>
                        <select
                            v-model="form.categorySlug"
                            class="w-full rounded-xl border border-[#C58E3B]/35 bg-[#FCF8F1] px-3 py-2 text-sm text-gray-800
                                   focus:outline-none focus:ring-2 focus:ring-[#8B0000]/30 focus:border-[#8B0000]"
                            style="min-height:44px;"
                        >
                            <option v-for="cat in categories" :key="cat.slug" :value="cat.slug">
                                {{ cat.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs text-gray-500 mb-1 font-medium">Ngày cần tra</label>
                        <input
                            v-model="form.date"
                            type="date"
                            class="w-full rounded-xl border border-[#C58E3B]/35 bg-[#FCF8F1] px-3 py-2 text-sm text-gray-800
                                   focus:outline-none focus:ring-2 focus:ring-[#8B0000]/30 focus:border-[#8B0000]"
                            style="min-height:44px;"
                        >
                    </div>

                    <button
                        type="button"
                        @click="search()"
                        class="px-3 py-2 min-h-[44px] rounded-xl bg-gradient-to-r from-[#8B0000] via-[#A12500] to-[#C06A1D]
                               text-white text-sm font-semibold hover:brightness-105 transition-colors shadow-sm"
                    >
                        Tra cứu ngày
                    </button>
                </div>

                <div class="flex items-center justify-between mt-4">
                    <button
                        type="button"
                        @click="navigateMonth(-1)"
                        class="px-3 py-2 rounded-xl border border-[#DAA520]/40 text-[#8B0000] text-sm
                               hover:bg-[#DAA520]/10 transition-colors"
                    >
                        ← Tháng trước
                    </button>
                    <span class="text-sm font-medium text-[#612000]">{{ monthLabel }}</span>
                    <button
                        type="button"
                        @click="navigateMonth(1)"
                        class="px-3 py-2 rounded-xl border border-[#DAA520]/40 text-[#8B0000] text-sm
                               hover:bg-[#DAA520]/10 transition-colors"
                    >
                        Tháng sau →
                    </button>
                </div>
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-8 grid grid-cols-1 lg:grid-cols-[1.15fr_0.85fr] gap-4">
            <div class="rounded-3xl border border-[#C58E3B]/25 bg-white p-4 sm:p-5">
                <div class="grid grid-cols-7 mb-1">
                    <div
                        v-for="wd in weekdays"
                        :key="wd"
                        class="text-center text-xs font-semibold py-2"
                        :class="wd === 'CN' ? 'text-[#8B0000]' : 'text-gray-500'"
                    >
                        {{ wd }}
                    </div>
                </div>

                <div class="grid grid-cols-7 gap-1">
                    <div
                        v-for="n in firstDayOffset"
                        :key="'empty-' + n"
                        class="aspect-square"
                    />

                    <button
                        v-for="day in days"
                        :key="day.date"
                        type="button"
                        @click="selectDay(day)"
                        class="aspect-square rounded-xl flex flex-col items-center justify-center p-1 transition-all text-center"
                        :class="dayCellClass(day)"
                    >
                        <span class="text-base font-bold leading-none">{{ day.solar.day }}</span>
                        <span class="text-[10px] leading-none mt-0.5 opacity-70">{{ day.lunar.day }}/{{ day.lunar.month }}</span>
                        <span v-if="day.isTamNuong || day.isNguyetKy" class="text-[9px] text-red-500">⚠</span>
                        <span v-else-if="day.isGood" class="text-[10px] leading-none">⭐</span>
                    </button>
                </div>
            </div>

            <div class="rounded-3xl border border-[#C58E3B]/25 bg-white p-4 sm:p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-[#8B0000]/60 mb-2">Kết luận ngày đã chọn</p>
                <h2 class="text-lg font-semibold text-[#5D1300]">
                    {{ selectedDateLabel }}
                </h2>

                <div class="mt-3 rounded-2xl border p-3"
                    :class="selectedVerdictClass">
                    <p class="text-sm font-semibold">{{ selectedVerdictTitle }}</p>
                    <p class="text-xs mt-1">{{ selectedVerdictSummary }}</p>
                </div>

                <div class="mt-3 text-sm text-[#5D4A40] space-y-2" v-if="selectedDayState">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Điểm tổng</span>
                        <strong class="text-[#8B0000]">{{ selectedDayState.score }}/100</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Trực</span>
                        <strong>{{ selectedDayState.truc }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tú sao</span>
                        <strong>{{ selectedDayState.xiuSao.name }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Can chi ngày</span>
                        <strong>{{ selectedDayState.canChi.dayCan }} {{ selectedDayState.canChi.dayChi }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tuổi xung</span>
                        <strong>{{ conflictLabel }}</strong>
                    </div>
                </div>

                <div class="mt-4 rounded-2xl bg-[#FFF8EC] border border-[#E6D0AF] p-3">
                    <p class="text-xs uppercase tracking-[0.12em] text-[#8B0000]/60 mb-1">Giải thích</p>
                    <ul class="text-sm text-[#5D4A40] leading-relaxed list-disc ml-5 space-y-1">
                        <li v-for="line in explanationLines" :key="line">{{ line }}</li>
                    </ul>
                </div>

                <div v-if="selectedDayState?.isGood" class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-3">
                    <p class="text-xs uppercase tracking-[0.12em] text-emerald-700 mb-1">Giờ tốt nên thực hiện</p>
                    <p class="text-sm text-emerald-800 leading-relaxed">
                        {{ luckyHourRanges.length ? luckyHourRanges.join(' · ') : 'Không có dữ liệu giờ tốt.' }}
                    </p>
                </div>

                <div v-if="selectedDayState?.isGood" class="mt-3 rounded-2xl border border-sky-200 bg-sky-50 p-3">
                    <p class="text-xs uppercase tracking-[0.12em] text-sky-700 mb-1">Tuổi hợp đứng ra chủ sự</p>
                    <p class="text-sm text-sky-900 leading-relaxed">
                        {{ compatibleAges.length ? compatibleAges.join(', ') : 'Nên chọn người không phạm tuổi xung trong ngày.' }}
                    </p>
                </div>

                <div class="mt-3 rounded-2xl border border-[#E6D0AF] bg-[#FFFDF8] p-3">
                    <p class="text-xs uppercase tracking-[0.12em] text-[#8B0000]/60 mb-1">Gợi ý nghi thức & văn khấn</p>
                    <p v-if="prayerGuide" class="text-sm font-semibold text-[#6A1A00] leading-relaxed">
                        {{ prayerGuide.title }}
                    </p>
                    <p class="text-sm text-[#5D4A40] leading-relaxed mt-1">
                        {{ ritualGuideText }}
                    </p>
                    <blockquote class="mt-2 border-l-2 border-[#C58E3B] pl-3 text-sm text-[#5D4A40] italic leading-relaxed">
                        {{ prayerGuideExcerpt }}
                    </blockquote>
                    <a
                        v-if="prayerGuide"
                        :href="route('prayers.index', { prayer_slug: prayerGuide.slug })"
                        class="inline-flex mt-2 text-xs font-medium text-[#8B0000] hover:underline"
                    >
                        Xem toàn văn khấn chuẩn →
                    </a>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    days:         { type: Array, required: true },
    category:     { type: Object, required: true },
    monthYear:    { type: String, required: true },
    categories:   { type: Array, required: true },
    selectedDate: { type: String, default: '' },
    selectedDay:  { type: Object, default: null },
    prayerGuide:  { type: Object, default: null },
});

const weekdays = ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'];
const HOUR_RANGES = {
    'Tý': '23:00-01:00',
    'Sửu': '01:00-03:00',
    'Dần': '03:00-05:00',
    'Mão': '05:00-07:00',
    'Thìn': '07:00-09:00',
    'Tỵ': '09:00-11:00',
    'Ngọ': '11:00-13:00',
    'Mùi': '13:00-15:00',
    'Thân': '15:00-17:00',
    'Dậu': '17:00-19:00',
    'Tuất': '19:00-21:00',
    'Hợi': '21:00-23:00',
};

const LUC_HOP = {
    'Tý': 'Sửu',
    'Sửu': 'Tý',
    'Dần': 'Hợi',
    'Hợi': 'Dần',
    'Mão': 'Tuất',
    'Tuất': 'Mão',
    'Thìn': 'Dậu',
    'Dậu': 'Thìn',
    'Tỵ': 'Thân',
    'Thân': 'Tỵ',
    'Ngọ': 'Mùi',
    'Mùi': 'Ngọ',
};

const TAM_HOP_GROUPS = [
    ['Thân', 'Tý', 'Thìn'],
    ['Dần', 'Ngọ', 'Tuất'],
    ['Hợi', 'Mão', 'Mùi'],
    ['Tỵ', 'Dậu', 'Sửu'],
];

const form = ref({
    categorySlug: props.category.slug,
    monthYear: props.monthYear,
    date: props.selectedDate || `${props.monthYear}-01`,
});

const prayerGuide = computed(() => props.prayerGuide);

const selectedDayState = ref(
    props.selectedDay
    || props.days.find((d) => d.date === form.value.date)
    || props.days[0]
    || null
);

if (selectedDayState.value && !form.value.date) {
    form.value.date = selectedDayState.value.date;
}

const monthLabel = computed(() => {
    const [y, m] = form.value.monthYear.split('-').map(Number);
    return `Tháng ${m}/${y}`;
});

const firstDayOffset = computed(() => {
    if (!props.days.length) return 0;
    const d = new Date(props.days[0].date + 'T00:00:00');
    return d.getDay() === 0 ? 6 : d.getDay() - 1;
});

const selectedDateLabel = computed(() => {
    if (!selectedDayState.value) return 'Chưa có dữ liệu ngày';
    const s = selectedDayState.value.solar;
    return `${s.weekday}, ${s.day}/${s.month}/${s.year}`;
});

const selectedVerdictTitle = computed(() => {
    if (!selectedDayState.value) return 'Chưa thể kết luận';
    if (selectedDayState.value.isTamNuong) return `Không nên ${props.category.name.toLowerCase()}`;
    if (selectedDayState.value.isNguyetKy) return `Cần cân nhắc khi ${props.category.name.toLowerCase()}`;
    if (selectedDayState.value.isGood) return `Nên ${props.category.name.toLowerCase()}`;
    return `Mức trung bình cho ${props.category.name.toLowerCase()}`;
});

const selectedVerdictSummary = computed(() => {
    if (!selectedDayState.value) return 'Không có dữ liệu ngày.';
    if (selectedDayState.value.isTamNuong) return 'Ngày Tam Nương, nên tránh việc lớn để giảm rủi ro.';
    if (selectedDayState.value.isNguyetKy) return 'Ngày Nguyệt Kỵ, ưu tiên việc nhỏ và tránh quyết định quan trọng.';
    if (selectedDayState.value.isGood) return 'Điểm phong thủy tích cực, phù hợp triển khai công việc đã chọn.';
    return 'Không xấu mạnh nhưng chưa phải ngày đẹp, nên chọn ngày điểm cao hơn.';
});

const selectedVerdictClass = computed(() => {
    if (!selectedDayState.value) return 'border-gray-200 bg-gray-50 text-gray-600';
    if (selectedDayState.value.isTamNuong) return 'border-red-300 bg-red-50 text-red-700';
    if (selectedDayState.value.isNguyetKy) return 'border-amber-300 bg-amber-50 text-amber-700';
    if (selectedDayState.value.isGood) return 'border-emerald-300 bg-emerald-50 text-emerald-700';
    return 'border-gray-300 bg-gray-50 text-gray-700';
});

const conflictLabel = computed(() => {
    if (!selectedDayState.value?.conflictZodiacs?.length) return 'Không rõ';
    return selectedDayState.value.conflictZodiacs.join(', ');
});

const luckyHourRanges = computed(() => {
    if (!selectedDayState.value?.luckyHours?.length) return [];

    return selectedDayState.value.luckyHours
        .filter((h) => h.isLucky)
        .map((h) => `${h.name} (${HOUR_RANGES[h.name] || '--:--'})`);
});

const compatibleAges = computed(() => {
    const dayChi = selectedDayState.value?.canChi?.dayChi;
    if (!dayChi) return [];

    const group = TAM_HOP_GROUPS.find((g) => g.includes(dayChi)) || [];
    const lucHop = LUC_HOP[dayChi];

    const set = new Set();
    group.forEach((chi) => {
        if (chi !== dayChi) set.add(`Tuổi ${chi}`);
    });
    if (lucHop && lucHop !== dayChi) {
        set.add(`Tuổi ${lucHop}`);
    }

    return Array.from(set);
});

const ritualGuideText = computed(() => {
    if (props.prayerGuide?.category_name) {
        return `Nên thực hiện đúng nghi thức của "${props.prayerGuide.category_name}": sắm lễ vừa đủ, thắp hương kính cáo, đọc văn khấn rõ ràng rồi mới tiến hành việc chính.`;
    }
    return 'Nên làm lễ gọn: thắp hương, trình việc, xin phép thần linh/gia tiên rồi mới triển khai công việc chính.';
});

const prayerGuideExcerpt = computed(() => {
    if (!props.prayerGuide?.content) {
        return 'Con kính lạy chư vị Tôn thần và gia tiên, hôm nay chọn ngày lành để tiến hành công việc, cúi xin chứng giám và phù hộ mọi sự hanh thông.';
    }
    return props.prayerGuide.content.split('\n').slice(0, 4).join(' ');
});

const explanationLines = computed(() => {
    if (!selectedDayState.value) return ['Không có dữ liệu để luận giải.'];
    const day = selectedDayState.value;
    const lines = [];

    if (day.isTamNuong) {
        lines.push('Ngày này phạm Tam Nương, theo truyền thống nên tránh khởi sự việc lớn.');
    } else if (day.isNguyetKy) {
        lines.push('Ngày này thuộc Nguyệt Kỵ, nên ưu tiên việc ổn định thay vì mạo hiểm.');
    } else {
        lines.push(`Ngày có điểm ${day.score}/100 dựa trên Trực, Tú sao và các yếu tố kiêng kỵ.`);
    }

    lines.push(`Trực ${day.truc} và Tú sao ${day.xiuSao.name}${day.xiuSao.isLucky ? ' đang ở trạng thái thuận' : ' chưa thuận mạnh'}.`);
    lines.push(`Can chi ngày ${day.canChi.dayCan} ${day.canChi.dayChi}; tuổi xung cần lưu ý: ${conflictLabel.value}.`);

    if (day.isGood) {
        lines.push(`Kết luận: phù hợp để ${props.category.name.toLowerCase()}, vẫn nên chọn giờ hoàng đạo trước khi tiến hành.`);
    } else {
        lines.push(`Kết luận: chưa tối ưu để ${props.category.name.toLowerCase()}, nên tham khảo ngày khác trong tháng.`);
    }

    return lines;
});

function dayCellClass(day) {
    const isSelected = selectedDayState.value?.date === day.date;

    if (isSelected) {
        return 'bg-[#8B0000] border border-[#8B0000] text-white shadow-md';
    }
    if (day.isTamNuong || day.isNguyetKy) {
        return 'bg-red-50 border border-red-200 text-red-500 hover:border-red-300';
    }
    if (day.isGood) {
        return 'bg-emerald-50 border border-emerald-200 text-emerald-700 hover:bg-emerald-100';
    }
    return 'bg-gray-50 border border-gray-200 text-gray-500 hover:bg-gray-100';
}

function selectDay(day) {
    selectedDayState.value = day;
    form.value.date = day.date;
}

function navigateMonth(delta) {
    const [y, m] = form.value.monthYear.split('-').map(Number);
    const d = new Date(y, m - 1 + delta, 1);
    form.value.monthYear = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
    form.value.date = `${form.value.monthYear}-01`;
    search();
}

function search() {
    const monthYear = form.value.date?.slice(0, 7) || form.value.monthYear;

    router.get(
        route('gooddays.index'),
        {
            category_slug: form.value.categorySlug,
            month_year: monthYear,
            date: form.value.date,
        },
        { preserveScroll: true },
    );
}
</script>

<style scoped>
.page-hero { /* → see app.css */ }

.hero-noise { /* → see app.css */ }

.page-display { /* → see app.css */ }
</style>
