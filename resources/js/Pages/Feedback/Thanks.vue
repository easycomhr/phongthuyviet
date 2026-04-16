<template>
    <AppLayout>
        <section class="bg-[#0A0908] min-h-[calc(100vh-160px)] text-[#E8DFC8]">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
                <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full border border-[#C9A84C]/60 bg-[#C9A84C]/10">
                    <svg class="h-8 w-8 text-[#C9A84C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M20 6 9 17l-5-5"/>
                    </svg>
                </div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-[#F2EDE4]">Cảm ơn bạn đã góp ý!</h1>
                <p class="mt-3 text-sm sm:text-base text-[#E8DFC8]/80">
                    Chúng tôi đã nhận được phản hồi của bạn và sẽ xem xét trong thời gian sớm nhất.
                </p>
                <Link
                    href="/"
                    class="mt-8 inline-flex items-center justify-center rounded-lg bg-[#C9A84C] px-5 py-2.5 text-sm font-semibold text-[#0A0908] transition hover:bg-[#d8b85f]"
                >
                    Quay về trang chủ
                </Link>
            </div>
        </section>
    </AppLayout>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
</script>
