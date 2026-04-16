<template>
    <AppLayout>
        <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
            <div class="mb-6">
                <h1 class="text-2xl sm:text-3xl font-semibold text-[#1A1714]">Danh sách Góp Ý</h1>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-[#D8CCBC] bg-white">
                <table class="w-full min-w-[900px] text-sm">
                    <thead>
                        <tr class="border-b border-[#E9DFD3] text-left text-[#7C6A58] uppercase tracking-[0.08em] text-xs">
                            <th class="px-4 py-3">STT</th>
                            <th class="px-4 py-3">Họ tên</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Loại</th>
                            <th class="px-4 py-3">Nội dung</th>
                            <th class="px-4 py-3">Thời gian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in feedbacks.data" :key="item.id" class="border-b border-[#F1EBE3] align-top">
                            <td class="px-4 py-3 text-[#2A2018]">
                                {{ (feedbacks.current_page - 1) * feedbacks.per_page + index + 1 }}
                            </td>
                            <td class="px-4 py-3 text-[#2A2018]">{{ item.name }}</td>
                            <td class="px-4 py-3 text-[#4A3D31]">{{ item.email || '-' }}</td>
                            <td class="px-4 py-3">
                                <span :class="badgeClass(item.type)" class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold">
                                    {{ typeLabel(item.type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-[#4A3D31]" :title="item.content">{{ truncateContent(item.content) }}</td>
                            <td class="px-4 py-3 text-[#4A3D31]">{{ formatDate(item.created_at) }}</td>
                        </tr>
                        <tr v-if="feedbacks.data.length === 0">
                            <td class="px-4 py-6 text-[#7C6A58]" colspan="6">Chưa có góp ý nào.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="feedbacks.links.length > 3" class="mt-5 flex flex-wrap gap-2">
                <Link
                    v-for="(link, index) in feedbacks.links"
                    :key="`${index}-${link.label}`"
                    :href="link.url || '#'"
                    class="rounded-lg border px-3 py-1.5 text-sm transition"
                    :class="linkClass(link)"
                    :preserve-scroll="true"
                    :aria-disabled="link.url === null"
                    v-html="link.label"
                />
            </div>
        </section>
    </AppLayout>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

interface FeedbackItem {
    id: number
    name: string
    email: string | null
    type: 'bug' | 'feature' | 'compliment' | 'other'
    content: string
    created_at: string
}

interface PaginationLink {
    url: string | null
    label: string
    active: boolean
}

interface FeedbackPagination {
    data: FeedbackItem[]
    current_page: number
    last_page: number
    per_page: number
    links: PaginationLink[]
}

interface Props {
    feedbacks: FeedbackPagination
}

const props = defineProps<Props>();

function truncateContent(content: string): string {
    if (content.length <= 80) {
        return content;
    }

    return `${content.slice(0, 80)}...`;
}

function typeLabel(type: FeedbackItem['type']): string {
    const map: Record<FeedbackItem['type'], string> = {
        bug: 'Báo lỗi',
        feature: 'Đề xuất tính năng',
        compliment: 'Khen ngợi',
        other: 'Khác',
    };

    return map[type] ?? 'Khác';
}

function badgeClass(type: FeedbackItem['type']): string {
    const map: Record<FeedbackItem['type'], string> = {
        bug: 'bg-red-100 text-red-700',
        feature: 'bg-blue-100 text-blue-700',
        compliment: 'bg-emerald-100 text-emerald-700',
        other: 'bg-gray-100 text-gray-700',
    };

    return map[type] ?? map.other;
}

function formatDate(value: string): string {
    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return new Intl.DateTimeFormat('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date);
}

function linkClass(link: PaginationLink): string {
    if (link.active) {
        return 'border-[#8B5E34] bg-[#8B5E34] text-white';
    }

    if (link.url === null) {
        return 'cursor-not-allowed border-[#E2D8CA] text-[#B6A697]';
    }

    return 'border-[#D8CCBC] text-[#2A2018] hover:bg-[#F6F1EA]';
}

const feedbacks = props.feedbacks;
</script>
