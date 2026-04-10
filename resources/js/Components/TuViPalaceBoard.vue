<template>
    <div class="rounded-3xl border border-[#C58E3B]/25 bg-white p-4 sm:p-5 shadow-sm">
        <div class="flex items-center justify-between gap-3 mb-3">
            <h3 class="text-lg font-semibold text-[#651500]">Lá Số 12 Cung (Bàn Cờ)</h3>
            <span
                class="text-xs rounded-full px-2.5 py-1"
                :class="mode === 'expert'
                    ? 'bg-[#8B0000]/10 text-[#8B0000]'
                    : 'bg-[#DAA520]/18 text-[#7C5200]'"
            >
                {{ mode === 'expert' ? 'Chuyên gia' : 'Đơn giản' }}
            </span>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_300px] gap-4 items-start">
            <div class="board-shell hidden lg:block">
                <div class="board-grid">
                    <TransitionGroup name="card-flip">
                        <article
                            v-for="slot in slotAreas"
                            :key="`${slot}-${mode}`"
                            class="palace-card"
                            :style="{ gridArea: slot }"
                        >
                            <template v-if="palaceByArea[slot]">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-[12px] font-semibold text-[#6A1A00] uppercase tracking-[0.08em] leading-tight">
                                        {{ palaceByArea[slot].name }}
                                    </p>
                                    <span
                                        class="text-[11px] font-semibold rounded-full px-2 py-0.5 shrink-0"
                                        :class="scoreClass(palaceByArea[slot].score)"
                                    >
                                        {{ palaceByArea[slot].score }}
                                    </span>
                                </div>

                                <p class="text-[12px] text-[#7A6458] mt-1.5 leading-relaxed">
                                    {{ mode === 'expert' ? palaceByArea[slot].summary : shortSummary(palaceByArea[slot].summary) }}
                                </p>

                                <div class="mt-2.5 flex flex-wrap gap-1">
                                    <span
                                        v-for="star in starsFor(palaceByArea[slot].stars)"
                                        :key="palaceByArea[slot].index + '-' + star.name"
                                        class="inline-flex items-center rounded-full border px-1.5 py-0.5 text-[10px]"
                                        :class="starClass(star.quality)"
                                    >
                                        {{ star.name }}
                                        <template v-if="mode === 'expert'"> · {{ star.quality }}</template>
                                    </span>
                                </div>
                            </template>
                        </article>
                    </TransitionGroup>

                    <div class="board-center">
                        <p class="text-[11px] uppercase tracking-[0.16em] text-[#8B0000]/55">Tâm bàn</p>
                        <p class="text-xl font-semibold text-[#651500] mt-0.5">{{ profile.menh }}</p>
                        <p class="text-[13px] text-[#6D584C] mt-1">{{ profile.canChiYear }}</p>
                        <p class="text-[13px] text-[#6D584C]">{{ profile.cuc }}</p>
                    </div>
                </div>
            </div>

            <div class="lg:hidden space-y-3">
                <div class="rounded-2xl border border-[#C58E3B]/35 bg-gradient-to-b from-[#FFFDF8] to-[#FFF4E5] p-4 text-center">
                    <p class="text-[11px] uppercase tracking-[0.16em] text-[#8B0000]/55">Tâm bàn</p>
                    <p class="text-2xl font-semibold text-[#651500] mt-0.5">{{ profile.menh }}</p>
                    <p class="text-sm text-[#6D584C] mt-1">{{ profile.canChiYear }}</p>
                    <p class="text-sm text-[#6D584C]">{{ profile.cuc }}</p>
                </div>

                <TransitionGroup name="card-flip" tag="div" class="grid grid-cols-2 gap-2.5">
                    <article
                        v-for="palace in sortedPalaces"
                        :key="`${palace.index}-${mode}`"
                        class="palace-mobile-card"
                    >
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-[12px] font-semibold text-[#6A1A00] uppercase tracking-[0.06em] leading-tight">
                                {{ palace.name }}
                            </p>
                            <span class="text-[10px] font-semibold rounded-full px-1.5 py-0.5 shrink-0" :class="scoreClass(palace.score)">
                                {{ palace.score }}
                            </span>
                        </div>

                        <p class="text-[12px] text-[#7A6458] mt-1.5 leading-relaxed">
                            {{ mode === 'expert' ? palace.summary : shortSummary(palace.summary) }}
                        </p>

                        <div class="mt-2 flex flex-wrap gap-1">
                            <span
                                v-for="star in starsFor(palace.stars)"
                                :key="palace.index + '-m-' + star.name"
                                class="inline-flex items-center rounded-full border px-1.5 py-0.5 text-[10px]"
                                :class="starClass(star.quality)"
                            >
                                {{ star.name }}
                            </span>
                        </div>
                    </article>
                </TransitionGroup>
            </div>

            <aside class="rounded-2xl border border-[#E4CDA9] bg-[#FFF8EC] p-4 self-start hidden lg:block">
                <p class="text-xs uppercase tracking-[0.12em] text-[#8B0000]/55">Gợi ý đọc nhanh</p>
                <ul class="mt-2.5 space-y-2 text-[13px] text-[#6B5649] leading-relaxed">
                    <li>
                        Điểm từ 80+ là cung vượng, ưu tiên phát huy trong năm.
                    </li>
                    <li>
                        65-79 là cung ổn định, giữ nhịp kỷ luật để tăng cát khí.
                    </li>
                    <li>
                        Dưới 65 nên ưu tiên an toàn, phòng ngừa rủi ro.
                    </li>
                    <li v-if="mode === 'expert'">
                        Chế độ chuyên gia hiển thị đầy đủ tính chất sao để phân tích sâu.
                    </li>
                    <li v-else>
                        Chế độ đơn giản rút gọn luận giải để xem nhanh.
                    </li>
                </ul>
            </aside>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    palaces: {
        type: Array,
        default: () => [],
    },
    profile: {
        type: Object,
        default: () => ({}),
    },
    mode: {
        type: String,
        default: 'simple',
    },
});

const slotAreas = ['s0', 's1', 's2', 's3', 's4', 's5', 's6', 's7', 's8', 's9', 's10', 's11'];

const sortedPalaces = computed(() => [...props.palaces].sort((a, b) => a.index - b.index));

const palaceByArea = computed(() => {
    const map = {};
    slotAreas.forEach((area, i) => {
        map[area] = sortedPalaces.value[i] || null;
    });
    return map;
});

function scoreClass(score) {
    if (score >= 80) return 'bg-emerald-100 text-emerald-700';
    if (score >= 65) return 'bg-amber-100 text-amber-700';
    return 'bg-rose-100 text-rose-700';
}

function shortSummary(summary) {
    if (!summary) return '';
    const cut = 72;
    return summary.length > cut ? `${summary.slice(0, cut).trim()}...` : summary;
}

function starsFor(stars) {
    if (props.mode === 'expert') return stars || [];
    return (stars || []).slice(0, 2);
}

function starClass(quality) {
    if (String(quality).includes('Hung')) return 'border-rose-300 bg-rose-50 text-rose-700';
    if (String(quality).includes('Cát')) return 'border-emerald-300 bg-emerald-50 text-emerald-700';
    return 'border-[#D9C2A0] bg-[#FFF4E1] text-[#805A2A]';
}
</script>

<style scoped>
.board-shell {
    border: 1px solid rgba(197, 142, 59, 0.32);
    border-radius: 1rem;
    background:
        radial-gradient(circle at 18% 14%, rgba(139, 0, 0, 0.08), transparent 33%),
        linear-gradient(165deg, #fffefb 0%, #fff6e7 100%);
    padding: 0.75rem;
}

.board-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.58rem;
    grid-template-areas:
        "s0 s1 s2 s3"
        "s11 c c s4"
        "s10 c c s5"
        "s9 s8 s7 s6";
}

.palace-card {
    min-height: 156px;
    border-radius: 0.95rem;
    border: 1px solid rgba(230, 208, 175, 0.95);
    background: linear-gradient(180deg, rgba(255, 253, 248, 0.98), rgba(255, 249, 239, 0.98));
    padding: 0.66rem;
    box-shadow: 0 2px 6px rgba(52, 24, 0, 0.04);
}

.board-center {
    grid-area: c;
    border-radius: 1rem;
    border: 1px solid rgba(197, 142, 59, 0.52);
    background: radial-gradient(circle at 20% 14%, rgba(139, 0, 0, 0.08), #fff);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    text-align: center;
    padding: 0.95rem 0.75rem;
}

.card-flip-enter-active,
.card-flip-leave-active {
    transition: transform 0.35s ease, opacity 0.35s ease, filter 0.35s ease;
    backface-visibility: hidden;
    transform-style: preserve-3d;
}

.card-flip-enter-from {
    opacity: 0;
    filter: blur(1px);
    transform: perspective(900px) rotateY(-14deg) scale(0.98);
}

.card-flip-leave-to {
    opacity: 0;
    filter: blur(1px);
    transform: perspective(900px) rotateY(14deg) scale(0.98);
}

.palace-mobile-card {
    min-height: 162px;
    border-radius: 0.9rem;
    border: 1px solid rgba(230, 208, 175, 0.95);
    background: linear-gradient(180deg, rgba(255, 253, 248, 0.98), rgba(255, 249, 239, 0.98));
    padding: 0.55rem;
    box-shadow: 0 2px 6px rgba(52, 24, 0, 0.04);
}

@media (max-width: 640px) {
    .board-grid {
        gap: 0.45rem;
    }

    .palace-mobile-card {
        min-height: 150px;
    }
}
</style>
