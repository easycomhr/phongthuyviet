<template>
    <!-- Dark Yin (Âm) header — contrasts with Yang (Dương) content below -->
    <header class="sticky top-0 z-50 bg-[#111110] border-b border-[#C9A84C]/20 shadow-[0_2px_20px_rgba(0,0,0,0.4)]">
        <!-- Top decorative gold line -->
        <div class="h-px bg-gradient-to-r from-transparent via-[#C9A84C]/70 to-transparent"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">

            <!-- Logo -->
            <a href="/" class="flex items-center gap-2.5 min-h-[44px] cursor-pointer group">
                <!-- Lotus flame SVG icon -->
                <svg class="w-9 h-9 flex-shrink-0 transition-transform duration-200 group-hover:scale-110"
                     viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <circle cx="18" cy="18" r="16.5" stroke="#C9A84C" stroke-width="1"/>
                    <!-- Center petal -->
                    <path d="M18 6 C13.5 11 10.5 15 10.5 20 C10.5 24.5 13.8 27.5 18 27.5 C22.2 27.5 25.5 24.5 25.5 20 C25.5 15 22.5 11 18 6Z"
                          fill="#C9A84C" opacity="0.9"/>
                    <!-- Left petal -->
                    <path d="M10 11 C7 14 6.5 19 9 22 C11 24.5 15 26 18 25 C15 22 13 18 10 11Z"
                          fill="#C9A84C" opacity="0.45"/>
                    <!-- Right petal -->
                    <path d="M26 11 C29 14 29.5 19 27 22 C25 24.5 21 26 18 25 C21 22 23 18 26 11Z"
                          fill="#C9A84C" opacity="0.45"/>
                    <!-- Yin center dot — dark inside gold -->
                    <circle cx="18" cy="21" r="3.5" fill="#111110"/>
                    <circle cx="18" cy="21" r="1.5" fill="#C9A84C" opacity="0.5"/>
                </svg>

                <div class="leading-tight">
                    <div class="font-bold text-lg text-[#F2EDE4] tracking-tight"
                         style="font-family: 'Playfair Display', serif;">
                        Phong Thủy Việt
                    </div>
                    <div class="text-[10px] text-[#C9A84C]/80 tracking-widest uppercase hidden sm:block">
                        Lịch Vạn Niên &amp; Văn Khấn
                    </div>
                </div>
            </a>

            <!-- Desktop nav -->
            <nav class="hidden lg:flex items-center gap-0.5" aria-label="Navigation chính">
                <!-- v-html is safe: icon strings are trusted static constants, never user input -->
                <a
                    v-for="link in navLinks"
                    :key="link.href"
                    :href="link.href"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-[#C5B89A]
                           hover:text-[#C9A84C] hover:bg-[#C9A84C]/10 transition-colors duration-200
                           min-h-[44px] cursor-pointer group"
                >
                    <span class="w-4 h-4 text-[#C9A84C]/70 group-hover:text-[#C9A84C] transition-colors duration-200 flex-shrink-0"
                          v-html="link.icon" aria-hidden="true"></span>
                    {{ link.label }}
                </a>
            </nav>

            <!-- Mobile hamburger -->
            <Disclosure v-slot="{ open }" as="div" class="lg:hidden">
                <DisclosureButton
                    class="flex items-center justify-center w-11 h-11 rounded-lg text-[#C9A84C]
                           hover:bg-[#C9A84C]/15 transition-colors duration-200 cursor-pointer"
                    aria-label="Mở menu điều hướng"
                >
                    <svg v-if="!open" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </DisclosureButton>

                <!-- Mobile dropdown panel — Yang (light) against the yin header -->
                <DisclosurePanel
                    class="absolute top-[65px] left-0 right-0 bg-[#1A1916] border-b border-[#C9A84C]/20
                           shadow-[0_8px_30px_rgba(0,0,0,0.5)] px-4 py-3 flex flex-col gap-1"
                >
                    <a
                        v-for="link in navLinks"
                        :key="link.href"
                        :href="link.href"
                        class="flex items-center gap-3 px-3 py-3 rounded-lg text-base font-medium text-[#C5B89A]
                               hover:text-[#C9A84C] hover:bg-[#C9A84C]/10 transition-colors duration-200
                               min-h-[44px] cursor-pointer group"
                    >
                        <span class="w-5 h-5 text-[#C9A84C]/70 group-hover:text-[#C9A84C] transition-colors flex-shrink-0"
                              v-html="link.icon" aria-hidden="true"></span>
                        {{ link.label }}
                    </a>

                    <div class="my-2 h-px bg-gradient-to-r from-transparent via-[#C9A84C]/30 to-transparent"></div>

                    <p class="text-xs text-center text-[#C9A84C]/60 py-1" style="font-family: 'Playfair Display', serif;">
                        An lành – Tài lộc – Bình an
                    </p>
                </DisclosurePanel>
            </Disclosure>

        </div>

        <!-- Bottom decorative gold line -->
        <div class="h-px bg-gradient-to-r from-transparent via-[#C9A84C]/40 to-transparent"></div>
    </header>
</template>

<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue';

// SVG icon strings — static trusted content, safe for v-html
const calendarIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><rect x="8" y="14" width="3" height="3" rx="0.5" fill="currentColor" stroke="none"/></svg>`;
const heartIcon     = `<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>`;
const userPlusIcon  = `<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>`;
const compassIcon   = `<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76" fill="currentColor" stroke="none"/></svg>`;
const starIcon      = `<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>`;
const galaxyIcon    = `<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/></svg>`;
const bookIcon      = `<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>`;
const moonIcon      = `<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>`;

const navLinks = [
    { href: '/tra-ngay-tot',    icon: calendarIcon, label: 'Tra Ngày Tốt' },
    { href: '/ngay-cuoi',       icon: heartIcon,    label: 'Ngày Cưới' },
    { href: '/dat-ten-cho-con', icon: userPlusIcon, label: 'Đặt Tên Con' },
    { href: '/huong-nha',       icon: compassIcon,  label: 'Hướng Nhà' },
    { href: '/xem-tuoi',        icon: starIcon,     label: 'Xem Tuổi' },
    { href: '/tu-vi',           icon: galaxyIcon,   label: 'Tử Vi' },
    { href: '/van-khan',        icon: bookIcon,     label: 'Văn Khấn' },
    { href: '/lich-hom-nay',    icon: moonIcon,     label: 'Lịch Hôm Nay' },
];
</script>
