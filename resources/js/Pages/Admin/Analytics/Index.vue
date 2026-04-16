<template>
    <AppLayout>
        <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
            <div class="flex items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-semibold text-[#1A1714]">Website Analytics</h1>
                    <p class="text-sm text-[#6B5C50] mt-1">Theo dõi lượt truy cập và trang được xem nhiều nhất.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <article class="rounded-2xl border border-[#D8CCBC] bg-white p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-[#7C6A58]">Today Views</p>
                    <p class="mt-2 text-2xl font-semibold text-[#1A1714]">{{ formatNumber(summary.today.views) }}</p>
                </article>
                <article class="rounded-2xl border border-[#D8CCBC] bg-white p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-[#7C6A58]">Today Unique Visitors</p>
                    <p class="mt-2 text-2xl font-semibold text-[#1A1714]">{{ formatNumber(summary.today.visitors) }}</p>
                </article>
                <article class="rounded-2xl border border-[#D8CCBC] bg-white p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-[#7C6A58]">7-Day Views</p>
                    <p class="mt-2 text-2xl font-semibold text-[#1A1714]">{{ formatNumber(summary.week.views) }}</p>
                </article>
                <article class="rounded-2xl border border-[#D8CCBC] bg-white p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-[#7C6A58]">30-Day Views</p>
                    <p class="mt-2 text-2xl font-semibold text-[#1A1714]">{{ formatNumber(summary.month.views) }}</p>
                </article>
            </div>

            <div class="rounded-2xl border border-[#D8CCBC] bg-white p-4 sm:p-5 mb-6">
                <div class="flex items-center justify-between gap-4 mb-4">
                    <h2 class="text-lg font-semibold text-[#1A1714]">7-Day Views Trend</h2>
                    <span class="text-xs text-[#7C6A58]">Total: {{ formatNumber(summary.week.views) }}</span>
                </div>

                <svg viewBox="0 0 760 240" class="w-full h-56">
                    <g>
                        <line
                            v-for="lineY in [30, 80, 130, 180]"
                            :key="lineY"
                            x1="40"
                            :y1="lineY"
                            x2="740"
                            :y2="lineY"
                            stroke="#EFE8DE"
                            stroke-width="1"
                        />
                    </g>
                    <polyline
                        :points="chartPoints"
                        fill="none"
                        stroke="#8B5E34"
                        stroke-width="3"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                    <g v-for="(point, index) in parsedPoints" :key="index">
                        <circle :cx="point.x" :cy="point.y" r="4" fill="#8B5E34" />
                        <text :x="point.x" y="215" text-anchor="middle" class="text-[10px] fill-[#7C6A58]">
                            {{ shortDate(dailyStats[index]?.date ?? '') }}
                        </text>
                    </g>
                </svg>
            </div>

            <div class="rounded-2xl border border-[#D8CCBC] bg-white p-4 sm:p-5 overflow-x-auto">
                <div class="flex items-center justify-between gap-4 mb-3">
                    <h2 class="text-lg font-semibold text-[#1A1714]">Top 10 Pages (30 ngày)</h2>
                    <span class="text-xs text-[#7C6A58]">All-time Views: {{ formatNumber(summary.total) }}</span>
                </div>

                <table class="w-full min-w-[520px] text-sm">
                    <thead>
                        <tr class="border-b border-[#E9DFD3] text-left text-[#7C6A58] uppercase tracking-[0.08em] text-xs">
                            <th class="py-2 pr-4">URL</th>
                            <th class="py-2 text-right">Views</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="page in topPages"
                            :key="`${page.url}-${page.views}`"
                            class="border-b border-[#F1EBE3]"
                        >
                            <td class="py-2.5 pr-4 text-[#2A2018]">{{ page.url }}</td>
                            <td class="py-2.5 text-right font-medium text-[#2A2018]">{{ formatNumber(page.views) }}</td>
                        </tr>
                        <tr v-if="topPages.length === 0">
                            <td class="py-4 text-[#7C6A58]" colspan="2">Chưa có dữ liệu.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </AppLayout>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

interface Props {
    summary: {
        today: { views: number; visitors: number }
        week: { views: number; visitors: number }
        month: { views: number; visitors: number }
        total: number
    }
    dailyStats: Array<{ date: string; views: number; visitors: number }>
    topPages: Array<{ url: string; views: number }>
}

const props = defineProps<Props>();

const maxViews = computed<number>(() => {
    if (props.dailyStats.length === 0) {
        return 1;
    }

    return Math.max(...props.dailyStats.map((item) => item.views), 1);
});

const parsedPoints = computed<Array<{ x: number; y: number }>>(() => {
    if (props.dailyStats.length === 0) {
        return [];
    }

    const startX = 60;
    const endX = 720;
    const topY = 30;
    const bottomY = 180;
    const stepX = props.dailyStats.length === 1 ? 0 : (endX - startX) / (props.dailyStats.length - 1);

    return props.dailyStats.map((item, index) => {
        const ratio = item.views / maxViews.value;

        return {
            x: startX + stepX * index,
            y: bottomY - ratio * (bottomY - topY),
        };
    });
});

const chartPoints = computed<string>(() => parsedPoints.value.map((point) => `${point.x},${point.y}`).join(' '));

function formatNumber(value: number): string {
    return new Intl.NumberFormat('vi-VN').format(value);
}

function shortDate(date: string): string {
    if (!date) {
        return '';
    }

    const [year, month, day] = date.split('-');

    return `${day}/${month}/${year.slice(2)}`;
}

const dailyStats = props.dailyStats;
const topPages = props.topPages;
const summary = props.summary;
</script>
