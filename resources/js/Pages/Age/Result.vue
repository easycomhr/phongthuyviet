<template>
    <AppLayout>
        <section class="page-hero relative overflow-hidden">
            <div class="hero-noise absolute inset-0 pointer-events-none" aria-hidden="true"></div>
            <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-9 text-center">
                <p class="text-[#F3CF7A]/90 text-xs font-semibold uppercase tracking-[0.2em] mb-2">
                    Kết quả xem tuổi
                </p>
                <h1 class="text-2xl sm:text-4xl font-bold text-white mb-2 page-display">
                    Mức độ hòa hợp: {{ result.summary.rating }}
                </h1>
                <p class="text-white/75 text-sm">
                    Đánh giá theo Mệnh, Thiên Can - Địa Chi và Cung Phi Bát Trạch.
                </p>
            </div>
        </section>

        <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 -mt-4 sm:-mt-6 relative z-20 pb-10 space-y-4">
            <div class="rounded-3xl border border-[#C58E3B]/25 bg-white shadow-[0_18px_45px_rgba(42,17,0,0.08)] p-5 sm:p-6">
                <div class="grid grid-cols-1 lg:grid-cols-[260px_1fr] gap-6 items-center">
                    <div class="flex items-center justify-center">
                        <div
                            class="w-48 h-48 rounded-full flex items-center justify-center ring-4 ring-white shadow-lg"
                            :style="{
                                background: `conic-gradient(#8B0000 ${scorePercent}%, #F3E8D4 ${scorePercent}% 100%)`,
                            }"
                        >
                            <div class="w-36 h-36 rounded-full bg-white flex flex-col items-center justify-center text-center">
                                <p class="text-[11px] uppercase tracking-[0.12em] text-[#8B0000]/60">Tổng điểm</p>
                                <p class="text-4xl font-bold text-[#7A1700]">{{ result.summary.totalScore }}</p>
                                <p class="text-xs text-gray-500">/ {{ result.summary.maxScore }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-[#6A5346]">{{ result.summary.shortAdvice }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ result.note }}</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div
                                v-for="(person, idx) in result.people"
                                :key="idx"
                                class="rounded-2xl border border-[#E2C69A]/40 bg-[#FFFCF6] p-4"
                            >
                                <p class="text-xs uppercase tracking-[0.12em] text-[#8B0000]/55">
                                    {{ idx === 0 ? 'Người A' : 'Người B' }}
                                </p>
                                <p class="text-sm font-semibold text-[#5D1400] mt-1">
                                    {{ person.genderLabel }} - {{ person.birthDate }}
                                </p>
                                <p class="text-xs text-[#6F5A4D] mt-2">
                                    Năm âm: {{ person.lunarYear }} ({{ person.yearCanChiLabel }})
                                </p>
                                <p class="text-xs text-[#6F5A4D]">
                                    Mệnh: {{ person.menh }} · Cung phi: {{ person.cungPhi }}
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-2">
                            <a
                                href="/xem-tuoi"
                                class="inline-flex items-center justify-center rounded-xl border border-[#C58E3B]/45 px-4 py-2.5 text-sm font-medium text-[#7A1700] hover:bg-[#FFF5E7] transition-colors"
                            >
                                Nhập lại dữ liệu
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-[#C58E3B]/25 bg-white p-4 sm:p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-[#631300] mb-3">Luận giải chi tiết</h2>
                <div class="space-y-2.5">
                    <div
                        v-for="factor in factorList"
                        :key="factor.key"
                        class="rounded-2xl border border-[#E6D0AF] overflow-hidden"
                    >
                        <button
                            type="button"
                            class="w-full px-4 py-3 text-left bg-[#FFF8EC] hover:bg-[#FFF3DF] transition-colors"
                            @click="toggle(factor.key)"
                        >
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-sm text-[#5C1300]">{{ factor.title }}</p>
                                    <p class="text-xs text-[#7A6357] mt-0.5">{{ factor.result }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-[#8B0000]">{{ factor.score }} / {{ factor.maxScore }}</p>
                                    <p class="text-[11px] text-gray-500">{{ expandedKey === factor.key ? 'Thu gọn' : 'Xem chi tiết' }}</p>
                                </div>
                            </div>
                        </button>

                        <div v-if="expandedKey === factor.key" class="px-4 py-3 bg-white border-t border-[#E6D0AF]">
                            <p class="text-sm text-[#5D4A40] leading-relaxed">{{ factor.detail }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed, ref } from 'vue';

const props = defineProps({
    input: {
        type: Object,
        required: true,
    },
    result: {
        type: Object,
        required: true,
    },
});

const expandedKey = ref('menh');

const scorePercent = computed(() => {
    const score = Number(props.result?.summary?.totalScore || 0);
    return Math.max(0, Math.min(100, (score / 10) * 100));
});

const factorList = computed(() => ([
    { key: 'menh', ...props.result.factors.menh },
    { key: 'canChi', ...props.result.factors.canChi },
    { key: 'cungPhi', ...props.result.factors.cungPhi },
]));

function toggle(key) {
    expandedKey.value = expandedKey.value === key ? '' : key;
}
</script>

<style scoped>
.page-hero { /* → see app.css */ }

.hero-noise { /* → see app.css */ }

.page-display { /* → see app.css */ }
</style>
