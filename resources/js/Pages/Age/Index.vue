<template>
    <AppLayout>
        <SeoHead
            title="Xem Tuổi Hợp – Tính Tuổi Vợ Chồng Theo Phong Thủy"
            description="Xem tuổi hợp theo phong thủy Việt Nam. Tính cung mệnh, quái số, xem vợ chồng hợp hay không, xem tuổi xây nhà và kinh doanh."
            canonical="https://phongthuyviet.easycom.tech/xem-tuoi"
        />
        <section class="page-hero relative overflow-hidden">
            <div class="hero-noise absolute inset-0 pointer-events-none" aria-hidden="true"></div>
            <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-9 text-center">
                <p class="text-[#F3CF7A]/90 text-xs font-semibold uppercase tracking-[0.2em] mb-2">
                    Xem tuổi vợ chồng / đối tác
                </p>
                <h1 class="text-2xl sm:text-4xl font-bold text-white mb-2 page-display">
                    Luận giải hòa hợp theo Bát Tự - Ngũ Hành - Cung Phi
                </h1>
                <p class="text-white/75 text-sm">
                    Nhập đầy đủ ngày sinh để tra cứu mức độ hợp khắc và gợi ý cân bằng.
                </p>
            </div>
        </section>

        <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 -mt-4 sm:-mt-6 relative z-20 pb-10">
            <div class="rounded-3xl border border-[#C58E3B]/25 bg-white shadow-[0_18px_45px_rgba(42,17,0,0.08)] p-4 sm:p-6">
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="rounded-2xl border border-[#E2C69A]/45 bg-[#FFFCF6] p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h2 class="text-base font-semibold text-[#601100]">Người A</h2>
                                <span class="text-xs rounded-full bg-[#8B0000]/10 px-2.5 py-1 text-[#8B0000]">Hồ sơ 1</span>
                            </div>

                            <label class="block text-xs font-medium text-gray-600 mb-1">Giới tính</label>
                            <select
                                v-model="form.person_a_gender"
                                class="w-full rounded-xl border border-[#C58E3B]/35 bg-white px-3 text-gray-800 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-[#8B0000]/30 focus:border-[#8B0000]"
                                style="min-height:44px;"
                            >
                                <option value="male">Nam</option>
                                <option value="female">Nữ</option>
                            </select>
                            <p v-if="form.errors.person_a_gender" class="text-xs text-red-500 mt-1">
                                {{ form.errors.person_a_gender }}
                            </p>

                            <div class="mt-3">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Ngày sinh dương lịch</label>
                                <LunarSolarDatePicker v-model="form.person_a_birth_date" dense />
                                <p v-if="form.errors.person_a_birth_date" class="text-xs text-red-500 mt-1">
                                    {{ form.errors.person_a_birth_date }}
                                </p>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-[#E2C69A]/45 bg-[#FFFCF6] p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h2 class="text-base font-semibold text-[#601100]">Người B</h2>
                                <span class="text-xs rounded-full bg-[#8B0000]/10 px-2.5 py-1 text-[#8B0000]">Hồ sơ 2</span>
                            </div>

                            <label class="block text-xs font-medium text-gray-600 mb-1">Giới tính</label>
                            <select
                                v-model="form.person_b_gender"
                                class="w-full rounded-xl border border-[#C58E3B]/35 bg-white px-3 text-gray-800 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-[#8B0000]/30 focus:border-[#8B0000]"
                                style="min-height:44px;"
                            >
                                <option value="male">Nam</option>
                                <option value="female">Nữ</option>
                            </select>
                            <p v-if="form.errors.person_b_gender" class="text-xs text-red-500 mt-1">
                                {{ form.errors.person_b_gender }}
                            </p>

                            <div class="mt-3">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Ngày sinh dương lịch</label>
                                <LunarSolarDatePicker v-model="form.person_b_birth_date" dense />
                                <p v-if="form.errors.person_b_birth_date" class="text-xs text-red-500 mt-1">
                                    {{ form.errors.person_b_birth_date }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-[#E2C69A]/35 bg-[#FFF8EC] p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <p class="text-sm text-[#6B5447]">
                            Kết quả trả về thang điểm 10 kèm luận giải chi tiết theo từng yếu tố.
                        </p>
                        <button
                            type="submit"
                            class="w-full sm:w-auto min-w-[220px] rounded-xl px-3 py-2 min-h-[44px] text-sm font-semibold text-white
                                   bg-gradient-to-r from-[#8B0000] via-[#A12500] to-[#C06A1D] hover:brightness-105
                                   active:scale-[0.99] transition-all shadow-lg shadow-[#8B0000]/15"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'Đang phân tích...' : 'Xem Kết Quả Hòa Hợp' }}
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import LunarSolarDatePicker from '@/Components/LunarSolarDatePicker.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    defaults: {
        type: Object,
        default: () => ({
            person_a_gender: 'male',
            person_b_gender: 'female',
        }),
    },
});

const form = useForm({
    person_a_gender: props.defaults.person_a_gender || 'male',
    person_a_birth_date: '1996-01-01',
    person_b_gender: props.defaults.person_b_gender || 'female',
    person_b_birth_date: '1998-01-01',
});

function submit() {
    form.post(route('age.result'));
}
</script>

<style scoped>
.page-hero { /* → see app.css */ }

.hero-noise { /* → see app.css */ }

.page-display { /* → see app.css */ }
</style>
