<template>
    <AppLayout>
        <SeoHead
            title="Đặt Tên Cho Con Theo Phong Thủy – Tên Đẹp Hợp Mệnh"
            description="Gợi ý tên đặt cho con theo ngũ hành và phong thủy Việt Nam. Nhập ngày sinh bé để nhận danh sách tên đẹp, hợp mệnh và ý nghĩa."
            canonical="https://phongthuyviet.easycom.tech/dat-ten-cho-con"
        />
        <section class="page-hero relative overflow-hidden">
            <div class="hero-noise absolute inset-0 pointer-events-none" aria-hidden="true"></div>
            <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-9 text-center">
                <p class="text-[#F3CF7A]/90 text-xs font-semibold uppercase tracking-[0.2em] mb-2">
                    Đặt tên cho con theo phong thủy
                </p>
                <h1 class="text-2xl sm:text-4xl font-bold text-white mb-2 page-display">
                    Gợi ý tên hợp Bát Tự và hòa hợp gia đạo
                </h1>
                <p class="text-white/75 text-sm">
                    Phân tích hành khuyết theo giờ sinh của bé, kết hợp ngũ hành bố mẹ để tạo danh sách tên phù hợp.
                </p>
            </div>
        </section>

        <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 -mt-4 sm:-mt-6 relative z-20 pb-10 space-y-4">
            <div class="rounded-3xl border border-[#C58E3B]/25 bg-white shadow-[0_18px_45px_rgba(42,17,0,0.08)] p-4 sm:p-6">
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <div class="rounded-2xl border border-[#E2C69A]/45 bg-[#FFFCF6] p-4">
                            <h2 class="text-base font-semibold text-[#601100] mb-3">Thông tin Cha</h2>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Họ tên cha</label>
                            <input v-model="form.father_name" type="text" class="field-input">
                            <p v-if="form.errors.father_name" class="error-text">{{ form.errors.father_name }}</p>
                            <label class="block text-xs font-medium text-gray-600 mt-3 mb-1">Năm sinh cha</label>
                            <input v-model.number="form.father_birth_year" type="number" min="1900" max="2100" class="field-input">
                            <p v-if="form.errors.father_birth_year" class="error-text">{{ form.errors.father_birth_year }}</p>
                        </div>

                        <div class="rounded-2xl border border-[#E2C69A]/45 bg-[#FFFCF6] p-4">
                            <h2 class="text-base font-semibold text-[#601100] mb-3">Thông tin Mẹ</h2>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Họ tên mẹ</label>
                            <input v-model="form.mother_name" type="text" class="field-input">
                            <p v-if="form.errors.mother_name" class="error-text">{{ form.errors.mother_name }}</p>
                            <label class="block text-xs font-medium text-gray-600 mt-3 mb-1">Năm sinh mẹ</label>
                            <input v-model.number="form.mother_birth_year" type="number" min="1900" max="2100" class="field-input">
                            <p v-if="form.errors.mother_birth_year" class="error-text">{{ form.errors.mother_birth_year }}</p>
                        </div>

                        <div class="rounded-2xl border border-[#E2C69A]/45 bg-[#FFFCF6] p-4">
                            <h2 class="text-base font-semibold text-[#601100] mb-3">Thông tin Bé</h2>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Giới tính bé</label>
                            <select v-model="form.baby_gender" class="field-input">
                                <option value="male">Bé trai</option>
                                <option value="female">Bé gái</option>
                            </select>
                            <p v-if="form.errors.baby_gender" class="error-text">{{ form.errors.baby_gender }}</p>
                            <label class="block text-xs font-medium text-gray-600 mt-3 mb-1">Ngày giờ sinh bé</label>
                            <input v-model="babyBirthDateLocal" type="datetime-local" class="field-input">
                            <p v-if="form.errors.baby_birth_date" class="error-text">{{ form.errors.baby_birth_date }}</p>

                            <label class="mt-3 flex items-start gap-2 text-sm text-[#6B5649]">
                                <input v-model="form.include_mother_surname" type="checkbox" class="mt-1 rounded border-[#C58E3B]/55 text-[#8B0000] focus:ring-[#8B0000]/35">
                                Muốn ghép họ mẹ vào tên con (ví dụ: Nguyễn Lê ...)
                            </label>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-[#E2C69A]/35 bg-[#FFF8EC] p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <p class="text-sm text-[#6B5447]">Hệ thống sẽ ưu tiên hành bổ khuyết cho bé và tránh hành khắc với bố mẹ.</p>
                        <button type="submit" class="w-full sm:w-auto min-w-[220px] rounded-xl px-3 py-2 min-h-[44px] text-sm font-semibold text-white bg-gradient-to-r from-[#8B0000] via-[#A12500] to-[#C06A1D] hover:brightness-105 active:scale-[0.99] transition-all shadow-lg shadow-[#8B0000]/15" :disabled="form.processing">
                            {{ form.processing ? 'Đang phân tích...' : 'Gợi Ý Tên Cho Bé' }}
                        </button>
                    </div>
                </form>
            </div>

            <div v-if="result" class="space-y-4">
                <div class="rounded-3xl border border-[#C58E3B]/25 bg-white p-4 sm:p-5 shadow-sm">
                    <h3 class="text-lg font-semibold text-[#651500] mb-3">Phân tích Bát Tự & Ngũ Hành</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-[1.1fr_0.9fr] gap-4">
                        <div class="space-y-2">
                            <p class="text-sm text-[#5F4B3F] leading-relaxed">{{ result.analysis.message }}</p>
                            <p class="text-xs text-[#7A665B]">
                                Hành bố: <strong>{{ elementLabel(result.analysis.parent.father_element) }}</strong> ·
                                Hành mẹ: <strong>{{ elementLabel(result.analysis.parent.mother_element) }}</strong>
                            </p>
                            <p class="text-xs text-[#7A665B]">
                                Hành nên ưu tiên đặt tên:
                                <strong>{{ result.analysis.targetElements.map(elementLabel).join(', ') }}</strong>
                            </p>
                            <p v-if="result.tabooNames.length" class="text-xs text-[#7A665B]">
                                Loại trừ phạm húy: <strong>{{ result.tabooNames.join(', ') }}</strong>
                            </p>
                        </div>
                        <div class="rounded-2xl border border-[#E4CDA9] bg-[#FFF8EC] p-3.5">
                            <p class="text-xs uppercase tracking-[0.12em] text-[#8B0000]/55 mb-2">Cân bằng ngũ hành của bé</p>
                            <div class="space-y-2">
                                <div v-for="entry in elementBars" :key="entry.key">
                                    <div class="flex justify-between text-[12px] text-[#6C5649]">
                                        <span>{{ entry.label }}</span>
                                        <strong>{{ entry.value }}</strong>
                                    </div>
                                    <div class="h-2 rounded-full bg-[#F2E4CF] overflow-hidden">
                                        <div class="h-2 rounded-full bg-gradient-to-r from-[#8B0000] to-[#D89C3A]" :style="{ width: `${entry.percent}%` }"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-[#C58E3B]/25 bg-white p-4 sm:p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-semibold text-[#651500]">Gợi ý tên phù hợp</h3>
                        <span class="text-xs text-[#7A665B]">{{ result.suggestions.length }} tên</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                        <article v-for="item in result.suggestions" :key="item.full_name" class="rounded-2xl border border-[#E6D0AF] bg-[#FFFCF7] p-3.5 shadow-sm">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <p class="text-sm font-semibold text-[#651500]">{{ item.full_name }}</p>
                                    <p class="text-xs text-[#7A6458] mt-0.5">Hành tên: {{ elementLabel(item.element) }} · {{ item.score }}/100</p>
                                </div>
                                <button type="button" class="text-lg leading-none hover:scale-105 transition-transform" @click="toggleFavorite(item.full_name)">
                                    {{ isFavorite(item.full_name) ? '❤️' : '🤍' }}
                                </button>
                            </div>
                            <p class="mt-2 text-xs text-[#6B5649] leading-relaxed">{{ item.meaning }}</p>
                        </article>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';

const props = defineProps({
    defaults: { type: Object, default: () => ({}) },
    result: { type: Object, default: null },
});

const form = useForm({
    father_name: props.defaults.father_name || '',
    father_birth_year: Number(props.defaults.father_birth_year || 1990),
    mother_name: props.defaults.mother_name || '',
    mother_birth_year: Number(props.defaults.mother_birth_year || 1992),
    baby_gender: props.defaults.baby_gender || 'male',
    baby_birth_date: props.defaults.baby_birth_date || '2026-10-20 09:30',
    include_mother_surname: Boolean(props.defaults.include_mother_surname),
});

const babyBirthDateLocal = ref((form.baby_birth_date || '').replace(' ', 'T'));
watch(babyBirthDateLocal, (val) => {
    form.baby_birth_date = (val || '').replace('T', ' ');
});

const favorites = ref(JSON.parse(localStorage.getItem('baby_name_favorites') || '[]'));

const elementBars = computed(() => {
    const counts = props.result?.analysis?.bazi?.element_count || {};
    const max = Math.max(1, ...Object.values(counts));
    const order = ['kim', 'moc', 'thuy', 'hoa', 'tho'];

    return order.map((key) => ({
        key,
        label: elementLabel(key),
        value: counts[key] ?? 0,
        percent: Math.round(((counts[key] ?? 0) / max) * 100),
    }));
});

function submit() {
    form.post(route('naming.result'), { preserveScroll: true });
}

function elementLabel(key) {
    const map = { kim: 'Kim', moc: 'Mộc', thuy: 'Thủy', hoa: 'Hỏa', tho: 'Thổ' };
    return map[key] || key;
}

function toggleFavorite(name) {
    if (favorites.value.includes(name)) {
        favorites.value = favorites.value.filter((x) => x !== name);
    } else {
        favorites.value = [name, ...favorites.value].slice(0, 50);
    }
    localStorage.setItem('baby_name_favorites', JSON.stringify(favorites.value));
}

function isFavorite(name) {
    return favorites.value.includes(name);
}
</script>

<style scoped>
.page-hero { /* → see app.css */ }

.hero-noise { /* → see app.css */ }

.page-display { /* → see app.css */ }

.field-input {
    width: 100%;
    border-radius: 0.75rem;
    border: 1px solid rgba(197, 142, 59, 0.35);
    background: #fff;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    color: #1f2937;
    min-height: 44px;
}

.field-input:focus {
    outline: none;
    border-color: #8b0000;
    box-shadow: 0 0 0 2px rgba(139, 0, 0, 0.3);
}

.error-text {
    color: #ef4444;
    font-size: 0.75rem;
    margin-top: 0.25rem;
}
</style>
