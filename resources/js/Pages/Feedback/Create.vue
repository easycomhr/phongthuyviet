<template>
    <AppLayout>
        <section class="bg-[#0A0908] min-h-[calc(100vh-160px)] text-[#E8DFC8]">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
                <div class="mb-8 flex items-center gap-3">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-[#C9A84C]/60 bg-[#C9A84C]/10">
                        <svg class="h-5 w-5 text-[#C9A84C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-semibold text-[#F2EDE4]">Góp Ý &amp; Phản Hồi</h1>
                </div>

                <form class="space-y-5 rounded-2xl border border-[#C9A84C]/30 bg-[#15120F] p-5 sm:p-7" @submit.prevent="submit">
                    <div>
                        <label for="name" class="mb-1.5 block text-sm text-[#E8DFC8]">Họ tên</label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="w-full rounded-lg border border-[#6E5A2B] bg-[#0A0908] px-3 py-2.5 text-[#F2EDE4] placeholder:text-[#B4A98A]/60 focus:border-[#C9A84C] focus:outline-none"
                            required
                        />
                        <p v-if="form.errors.name" class="mt-1 text-xs text-red-300">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label for="email" class="mb-1.5 block text-sm text-[#E8DFC8]">Email</label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="w-full rounded-lg border border-[#6E5A2B] bg-[#0A0908] px-3 py-2.5 text-[#F2EDE4] placeholder:text-[#B4A98A]/60 focus:border-[#C9A84C] focus:outline-none"
                            placeholder="Không bắt buộc"
                        />
                        <p v-if="form.errors.email" class="mt-1 text-xs text-red-300">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label for="type" class="mb-1.5 block text-sm text-[#E8DFC8]">Loại góp ý</label>
                        <select
                            id="type"
                            v-model="form.type"
                            class="w-full rounded-lg border border-[#6E5A2B] bg-[#0A0908] px-3 py-2.5 text-[#F2EDE4] focus:border-[#C9A84C] focus:outline-none"
                        >
                            <option v-for="(label, key) in types" :key="key" :value="key">{{ label }}</option>
                        </select>
                        <p v-if="form.errors.type" class="mt-1 text-xs text-red-300">{{ form.errors.type }}</p>
                    </div>

                    <div>
                        <label for="content" class="mb-1.5 block text-sm text-[#E8DFC8]">Nội dung</label>
                        <textarea
                            id="content"
                            v-model="form.content"
                            rows="5"
                            minlength="10"
                            class="w-full rounded-lg border border-[#6E5A2B] bg-[#0A0908] px-3 py-2.5 text-[#F2EDE4] placeholder:text-[#B4A98A]/60 focus:border-[#C9A84C] focus:outline-none"
                        />
                        <p v-if="form.errors.content" class="mt-1 text-xs text-red-300">{{ form.errors.content }}</p>
                    </div>

                    <input
                        v-model="form.website"
                        type="text"
                        name="website"
                        class="hidden"
                        tabindex="-1"
                        autocomplete="off"
                    />

                    <p v-if="form.errors.general" class="text-xs text-red-300">{{ form.errors.general }}</p>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-[#C9A84C] px-5 py-2.5 text-sm font-semibold text-[#0A0908] transition hover:bg-[#d8b85f] disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Đang gửi...' : 'Gửi Góp Ý' }}
                    </button>
                </form>
            </div>
        </section>
    </AppLayout>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

interface Props {
    types: Record<string, string>
}

defineProps<Props>();

const form = useForm({
    name: '',
    email: '',
    type: 'other',
    content: '',
    website: '',
});

function submit(): void {
    form.post(route('feedback.store'));
}
</script>
