<template>
    <!-- Deep Yin (Âm) footer — deepest darkness balancing the yang content above -->
    <footer class="bg-[#0A0908] text-[#E8DFC8]">
        <!-- Gold divider wave -->
        <div class="flex items-center gap-3 px-6 pt-6 opacity-50">
            <div class="flex-1 h-px bg-gradient-to-r from-transparent to-[#C9A84C]"></div>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 16" class="w-12 h-4 fill-[#C9A84C]" aria-hidden="true">
                <path d="M24 0 C18 5 8 7 0 5 C8 10 18 12 24 16 C30 12 40 10 48 5 C40 7 30 5 24 0Z"/>
            </svg>
            <div class="flex-1 h-px bg-gradient-to-l from-transparent to-[#C9A84C]"></div>
        </div>

        <div class="max-w-6xl mx-auto px-4 py-8 grid grid-cols-1 sm:grid-cols-3 gap-8">

            <!-- Col 1: Giới thiệu -->
            <div>
                <div class="flex items-center gap-2.5 mb-3">
                    <svg class="w-7 h-7 flex-shrink-0" viewBox="0 0 36 36" fill="none" aria-hidden="true">
                        <circle cx="18" cy="18" r="16.5" stroke="#C9A84C" stroke-width="1"/>
                        <path d="M18 6 C13.5 11 10.5 15 10.5 20 C10.5 24.5 13.8 27.5 18 27.5 C22.2 27.5 25.5 24.5 25.5 20 C25.5 15 22.5 11 18 6Z" fill="#C9A84C" opacity="0.9"/>
                        <path d="M10 11 C7 14 6.5 19 9 22 C11 24.5 15 26 18 25 C15 22 13 18 10 11Z" fill="#C9A84C" opacity="0.45"/>
                        <path d="M26 11 C29 14 29.5 19 27 22 C25 24.5 21 26 18 25 C21 22 23 18 26 11Z" fill="#C9A84C" opacity="0.45"/>
                        <circle cx="18" cy="21" r="3.5" fill="#0A0908"/>
                        <circle cx="18" cy="21" r="1.5" fill="#C9A84C" opacity="0.5"/>
                    </svg>
                    <span class="font-bold text-lg text-[#F2EDE4]" style="font-family:'Playfair Display',serif;">
                        Phong Thủy Việt
                    </span>
                </div>
                <p class="text-sm text-[#E8DFC8]/60 leading-relaxed">
                    Tra cứu ngày tốt, Lịch vạn niên, Văn khấn chuẩn truyền thống Việt Nam.
                    Giúp mọi việc hanh thông, gia đình bình an.
                </p>
                <p class="mt-3 text-xs text-[#C9A84C]/70 italic" style="font-family:'Playfair Display',serif;">
                    An lành – Tài lộc – Bình an
                </p>
            </div>

            <!-- Col 2: Liên kết nhanh -->
            <div>
                <h3 class="font-semibold text-[#C9A84C] mb-3 text-sm uppercase tracking-wider">Liên kết nhanh</h3>
                <ul class="space-y-2">
                    <li v-for="link in quickLinks" :key="link.href">
                        <a :href="link.href"
                           class="text-sm text-[#E8DFC8]/60 hover:text-[#C9A84C] transition-colors duration-200
                                  flex items-center gap-2 cursor-pointer group">
                            <span class="w-3.5 h-3.5 text-[#C9A84C]/50 group-hover:text-[#C9A84C] transition-colors flex-shrink-0"
                                  v-html="link.icon" aria-hidden="true"></span>
                            {{ link.label }}
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Col 3: Kết nối -->
            <div>
                <h3 class="font-semibold text-[#C9A84C] mb-3 text-sm uppercase tracking-wider">Kết nối</h3>
                <ul class="space-y-2 mb-4">
                    <li class="text-sm text-[#E8DFC8]/60 flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#C9A84C]/60 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
                        </svg>
                        <span>lienhe@phongthuyviet.easycomhr.tech</span>
                    </li>
                    <li class="text-sm text-[#E8DFC8]/60 flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#C9A84C]/60 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                        </svg>
                        <span>Việt Nam</span>
                    </li>
                </ul>
                <div class="flex gap-2">
                    <a v-for="social in socialLinks" :key="social.label"
                       :href="social.href" :aria-label="social.label"
                       class="w-9 h-9 rounded-full border border-[#C9A84C]/30 flex items-center justify-center
                              text-[#C9A84C]/60 hover:text-[#C9A84C] hover:border-[#C9A84C]/70
                              hover:bg-[#C9A84C]/10 transition-all duration-200 cursor-pointer">
                        <span class="w-4 h-4" v-html="social.icon" aria-hidden="true"></span>
                    </a>
                </div>
            </div>
        </div>

        <div class="border-t border-[#C9A84C]/10 py-4 text-center text-xs text-[#E8DFC8]/30">
            © 2026 Phong Thủy Việt. Bảo lưu mọi quyền.
        </div>
    </footer>
</template>

<script setup>
const calendarIcon = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>`;
const starIcon     = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>`;
const bookIcon     = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>`;
const moonIcon     = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>`;
const compassIcon  = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76" fill="currentColor" stroke="none"/></svg>`;

const fbIcon  = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>`;
const ytIcon  = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.97C18.88 4 12 4 12 4s-6.88 0-8.59.45a2.78 2.78 0 0 0-1.95 1.97A29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58A2.78 2.78 0 0 0 3.41 19.6C5.12 20 12 20 12 20s6.88 0 8.59-.45a2.78 2.78 0 0 0 1.95-1.95A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" fill="currentColor" stroke="none"/></svg>`;
const msgIcon = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>`;

const quickLinks = [
    { href: '/tra-ngay-tot', icon: calendarIcon, label: 'Tra Ngày Tốt' },
    { href: '/xem-tuoi',     icon: starIcon,     label: 'Xem Tuổi Vợ Chồng' },
    { href: '/van-khan',     icon: bookIcon,     label: 'Văn Khấn' },
    { href: '/lich-hom-nay', icon: moonIcon,     label: 'Lịch Hôm Nay' },
    { href: '/huong-nha',    icon: compassIcon,  label: 'Hướng Nhà Tốt' },
];

const socialLinks = [
    { href: '#', icon: fbIcon,  label: 'Facebook' },
    { href: '#', icon: ytIcon,  label: 'YouTube' },
    { href: '#', icon: msgIcon, label: 'Zalo' },
];
</script>
