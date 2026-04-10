<template>
    <AppLayout>
        <SeoHead
            title="Xem Hướng Nhà Hợp Tuổi Theo Phong Thủy"
            description="Tính hướng nhà hợp tuổi theo phong thủy Bát Trạch. Nhập năm sinh và giới tính để xem hướng tốt, hướng xấu cần tránh."
            canonical="https://phongthuyviet.easycom.tech/huong-nha"
        />
        <section class="page-hero relative overflow-hidden">
            <div class="hero-noise absolute inset-0 pointer-events-none" aria-hidden="true"></div>
            <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-9 text-center">
                <p class="text-[#F3CF7A]/90 text-xs font-semibold uppercase tracking-[0.2em] mb-2">
                    Tra cứu hướng nhà Bát Trạch
                </p>
                <h1 class="text-2xl sm:text-4xl font-bold text-white mb-2 page-display">
                    Xác định hướng tốt xấu theo cung phi gia chủ
                </h1>
                <p class="text-white/75 text-sm">
                    Nhập giới tính và ngày sinh để luận hướng Sinh Khí, Thiên Y, Diên Niên, Phục Vị cùng các hướng cần tránh.
                </p>
            </div>
        </section>

        <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 -mt-4 sm:-mt-6 relative z-20 pb-10 space-y-4">
            <div class="rounded-3xl border border-[#C58E3B]/25 bg-white shadow-[0_18px_45px_rgba(42,17,0,0.08)] p-4 sm:p-6">
                <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-[220px_1fr_auto] gap-4 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Giới tính gia chủ</label>
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
                        <label class="block text-xs font-medium text-gray-600 mb-1">Ngày sinh (hỗ trợ âm/dương)</label>
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
                        {{ form.processing ? 'Đang luận giải...' : 'Tra Hướng Nhà' }}
                    </button>
                </form>
            </div>

            <div v-if="result" class="grid grid-cols-1 lg:grid-cols-[1fr_1fr] gap-4">
                <div class="rounded-3xl border border-[#C58E3B]/25 bg-white p-5 sm:p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs uppercase tracking-[0.14em] text-[#8B0000]/55">Hồ sơ gia chủ</p>
                            <h2 class="text-xl font-semibold text-[#641500] mt-1">
                                {{ result.profile.cungPhi }} · {{ result.profile.group }}
                            </h2>
                            <p class="text-sm text-[#6F5A4D] mt-1">
                                {{ result.profile.genderLabel }} · Năm sinh dương {{ result.profile.solarYear }} · Năm tính phong thủy {{ result.profile.lunarYearForFengShui }}
                            </p>
                            <p class="text-xs text-[#8B7569] mt-1">
                                Quái số: {{ result.profile.kuaNumber }} · Mốc Lập Xuân tham chiếu: {{ result.profile.lapXuanDate }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 rounded-2xl border border-[#E7D2B3] bg-[#FFF8EC] p-4">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-sm font-semibold text-[#6A1A00]">La bàn Bát Trạch</p>
                            <label class="text-xs text-[#7B6559] flex items-center gap-2">
                                Xoay:
                                <input v-model.number="rotation" type="range" min="-45" max="45" step="5" class="w-24">
                            </label>
                        </div>

                        <div class="compass-wrap">
                            <svg viewBox="0 0 320 320" class="w-full max-w-[360px] mx-auto">
                                <defs>
                                    <radialGradient id="compassBg" cx="50%" cy="50%" r="50%">
                                        <stop offset="0%" stop-color="#fff9ee" />
                                        <stop offset="100%" stop-color="#ffe7bf" />
                                    </radialGradient>
                                </defs>
                                <g :transform="`rotate(${rotation} 160 160)`">
                                    <circle cx="160" cy="160" r="138" fill="url(#compassBg)" stroke="#C58E3B" stroke-width="2" />
                                    <circle cx="160" cy="160" r="114" fill="none" stroke="#C58E3B" stroke-dasharray="4 5" />
                                    <line
                                        v-for="line in spokes"
                                        :key="line"
                                        x1="160"
                                        y1="26"
                                        x2="160"
                                        y2="294"
                                        stroke="#D8B885"
                                        stroke-width="1"
                                        :transform="`rotate(${line} 160 160)`"
                                    />

                                    <g
                                        v-for="item in result.directions.all"
                                        :key="item.direction"
                                        :transform="labelTransform(item.angle)"
                                    >
                                        <circle r="20" :fill="item.isGood ? '#8B0000' : '#4A4A4A'" opacity="0.92" />
                                        <text
                                            x="0"
                                            y="-2"
                                            fill="#fff"
                                            font-size="9"
                                            text-anchor="middle"
                                            font-family="Nunito Sans, sans-serif"
                                            font-weight="700"
                                        >
                                            {{ shortDirection(item.direction) }}
                                        </text>
                                        <text
                                            x="0"
                                            y="10"
                                            fill="#fff"
                                            font-size="7"
                                            text-anchor="middle"
                                            font-family="Nunito Sans, sans-serif"
                                        >
                                            {{ item.duTinh }}
                                        </text>
                                    </g>
                                </g>
                                <circle cx="160" cy="160" r="44" fill="#fff" stroke="#8B0000" stroke-width="2" />
                                <text x="160" y="153" text-anchor="middle" fill="#7A1700" font-size="11" font-weight="700">CUNG PHI</text>
                                <text x="160" y="173" text-anchor="middle" fill="#8B0000" font-size="22" font-weight="700">{{ result.profile.cungPhi }}</text>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="rounded-3xl border border-[#C58E3B]/25 bg-white p-4 sm:p-5 shadow-sm">
                        <h3 class="text-base font-semibold text-[#6C1500] mb-3">Hướng tốt nên ưu tiên</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <div
                                v-for="item in result.directions.good"
                                :key="'good-' + item.direction"
                                class="rounded-2xl border border-[#D8B885]/45 bg-[#FFF8EC] p-3"
                            >
                                <p class="text-xs uppercase tracking-[0.12em] text-[#8B0000]/55">{{ item.direction }}</p>
                                <p class="text-sm font-semibold text-[#7A1700] mt-1">{{ item.duTinh }} · {{ item.score }}/100</p>
                                <p class="text-xs text-[#6A5548] mt-1 leading-relaxed">{{ item.description }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-[#2E2E2E]/20 bg-white p-4 sm:p-5 shadow-sm">
                        <h3 class="text-base font-semibold text-[#2E2E2E] mb-3">Hướng xấu nên tránh</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <div
                                v-for="item in result.directions.bad"
                                :key="'bad-' + item.direction"
                                class="rounded-2xl border border-[#303030]/22 bg-[#F9F9F9] p-3"
                            >
                                <p class="text-xs uppercase tracking-[0.12em] text-[#3C3C3C]/80">{{ item.direction }}</p>
                                <p class="text-sm font-semibold text-[#2F2F2F] mt-1">{{ item.duTinh }} · {{ item.score }}/100</p>
                                <p class="text-xs text-[#555] mt-1 leading-relaxed">{{ item.description }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-[#E6D0AF] bg-[#FFFDF8] p-3 text-xs text-[#735D50] leading-relaxed">
                        {{ result.note }}
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import LunarSolarDatePicker from '@/Components/LunarSolarDatePicker.vue';
import SeoHead from '@/Components/SeoHead.vue';

const props = defineProps({
    defaults: {
        type: Object,
        default: () => ({
            gender: 'male',
            birth_date: '1990-01-01',
        }),
    },
    result: {
        type: Object,
        default: null,
    },
});

const form = useForm({
    gender: props.defaults.gender || 'male',
    birth_date: props.defaults.birth_date || '1990-01-01',
});

const rotation = ref(0);
const spokes = [0, 45, 90, 135];

function submit() {
    form.post(route('direction.result'));
}

function shortDirection(direction) {
    const map = {
        'Bắc': 'B',
        'Đông Bắc': 'ĐB',
        'Đông': 'Đ',
        'Đông Nam': 'ĐN',
        'Nam': 'N',
        'Tây Nam': 'TN',
        'Tây': 'T',
        'Tây Bắc': 'TB',
    };
    return map[direction] || direction;
}

function labelTransform(angle) {
    const radius = 114;
    const rad = ((angle - 90) * Math.PI) / 180;
    const x = 160 + radius * Math.cos(rad);
    const y = 160 + radius * Math.sin(rad);
    return `translate(${x} ${y})`;
}
</script>

<style scoped>
.page-hero { /* → see app.css */ }

.hero-noise { /* → see app.css */ }

.page-display { /* → see app.css */ }

.compass-wrap {
    background:
        radial-gradient(circle at 20% 20%, rgba(139, 0, 0, 0.08), transparent 42%),
        linear-gradient(145deg, #fffefc 0%, #fff3dc 100%);
    border: 1px solid rgba(197, 142, 59, 0.3);
    border-radius: 1.2rem;
    padding: 0.75rem;
}
</style>
