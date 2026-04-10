<template>
    <div class="lunar-datepicker select-none" :class="{ 'is-dense': dense }">

        <!-- ── Toggle Âm/Dương ── -->
        <div class="flex items-center justify-center mb-4 mode-wrap">
            <div class="inline-flex rounded-full border border-[#DAA520]/60 overflow-hidden text-sm font-medium">
                <button
                    type="button"
                    @click="setMode('solar')"
                    class="px-5 py-2 transition-colors min-h-[44px] mode-btn"
                    :class="currentMode === 'solar'
                        ? 'bg-[#8B0000] text-white'
                        : 'bg-white text-gray-600 hover:bg-[#DAA520]/10'"
                >
                    Dương lịch
                </button>
                <button
                    type="button"
                    @click="setMode('lunar')"
                    class="px-5 py-2 transition-colors min-h-[44px] mode-btn"
                    :class="currentMode === 'lunar'
                        ? 'bg-[#8B0000] text-white'
                        : 'bg-white text-gray-600 hover:bg-[#DAA520]/10'"
                >
                    Âm lịch
                </button>
            </div>
        </div>

        <!-- ── Dropdowns Năm / Tháng ── -->
        <div class="flex gap-2 mb-4 picker-selects">
            <select
                v-model.number="displayYear"
                @change="onYearChange"
                class="flex-1 rounded-lg border border-gray-200 px-3 text-gray-800 text-sm
                       focus:outline-none focus:ring-2 focus:ring-[#8B0000]/30 focus:border-[#8B0000]"
                style="min-height:44px;"
            >
                <option
                    v-for="y in yearRange"
                    :key="y"
                    :value="y"
                    :disabled="y < MIN_YEAR || y > MAX_YEAR"
                >
                    {{ y }}
                </option>
            </select>

            <select
                v-model="displayMonthKey"
                @change="onMonthChange"
                class="flex-1 rounded-lg border border-gray-200 px-3 text-gray-800 text-sm
                       focus:outline-none focus:ring-2 focus:ring-[#8B0000]/30 focus:border-[#8B0000]"
                style="min-height:44px;"
            >
                <option
                    v-for="m in availableMonths"
                    :key="m.key"
                    :value="m.key"
                >
                    {{ m.label }}
                </option>
            </select>
        </div>

        <!-- ── Calendar grid ── -->
        <div class="rounded-xl border border-[#DAA520]/30 overflow-hidden calendar-shell">
            <!-- Day headers -->
            <div class="grid grid-cols-7 bg-[#8B0000]/5">
                <div
                    v-for="h in ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7']"
                    :key="h"
                    class="text-center text-xs font-bold text-[#8B0000]/60 py-2.5 tracking-wide"
                >
                    {{ h }}
                </div>
            </div>

            <!-- Day cells -->
            <div class="grid grid-cols-7">
                <!-- Leading empty cells -->
                <div
                    v-for="_ in leadingBlanks"
                    :key="'b' + _"
                    class="h-14 calendar-blank"
                ></div>

                <!-- Day numbers -->
                <button
                    v-for="cell in dayCells"
                    :key="cell.day"
                    type="button"
                    @click="selectDay(cell.day)"
                    :disabled="cell.disabled"
                    class="h-14 flex flex-col items-center justify-center gap-0.5 calendar-day
                           transition-colors rounded-xl mx-0.5 my-0.5"
                    :class="[
                        cell.isSelected
                            ? 'bg-[#8B0000] text-white shadow-md shadow-[#8B0000]/30'
                            : cell.isToday
                                ? 'bg-[#DAA520]/20 text-[#8B0000] font-bold ring-1 ring-[#DAA520]'
                                : cell.disabled
                                    ? 'text-gray-300 cursor-not-allowed'
                                    : 'text-gray-700 hover:bg-[#8B0000]/10 hover:text-[#8B0000]'
                    ]"
                >
                    <span
                        class="font-semibold leading-none"
                        :class="currentMode === 'lunar' ? 'text-lg sm:text-[19px]' : 'text-base sm:text-[17px]'"
                    >
                        {{ cell.day }}
                    </span>
                    <span
                        v-if="currentMode === 'solar' && cell.lunarLabel"
                        class="text-[11px] leading-none"
                        :class="cell.isSelected ? 'opacity-80' : 'opacity-50'"
                    >
                        {{ cell.lunarLabel }}
                    </span>
                </button>
            </div>
        </div>

        <!-- ── Footer: selected date display ── -->
        <div class="mt-3 px-1 py-2.5 rounded-xl bg-[#8B0000]/5 text-center text-sm picker-footer">
            <span v-if="currentMode === 'solar'">
                Dương: <strong class="text-[#8B0000]">{{ formatSolar(selectedDate) }}</strong>
                <span class="mx-1.5 text-gray-300">·</span>
                Âm: <span class="text-[#DAA520] font-medium">{{ lunarLabel }}</span>
            </span>
            <span v-else>
                Âm: <strong class="text-[#8B0000] text-base">{{ lunarLabel }}</strong>
                <span class="mx-1.5 text-gray-300">·</span>
                Dương: <span class="text-[#DAA520] font-medium">{{ formatSolar(selectedDate) }}</span>
            </span>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { solarToLunar, lunarToSolar, getLunarMonthsInYear } from '@/utils/lunar.js';

// ────────────────────────────────────────────────────────────
// Props & Emits
// ────────────────────────────────────────────────────────────
const props = defineProps({
    modelValue: { type: String, default: '' }, // YYYY-MM-DD solar
    dense: { type: Boolean, default: false },
});
const emit = defineEmits(['update:modelValue']);

// ────────────────────────────────────────────────────────────
// Constants
// ────────────────────────────────────────────────────────────
const MIN_YEAR = 1900;
const MAX_YEAR = 2100;

// ────────────────────────────────────────────────────────────
// State
// ────────────────────────────────────────────────────────────
const today = new Date();
const todayISO = toISO(today.getFullYear(), today.getMonth() + 1, today.getDate());

function parseISO(iso) {
    const [y, m, d] = iso.split('-').map(Number);
    return { year: y, month: m, day: d };
}
function toISO(y, m, d) {
    return `${y}-${String(m).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
}

const initialISO = props.modelValue || todayISO;
const initSolar = parseISO(initialISO);
const initLunar = solarToLunar(initSolar.year, initSolar.month, initSolar.day);

const currentMode  = ref('solar');
const selectedDate = ref(initialISO); // always solar YYYY-MM-DD

// Display context (what the user is browsing, independent of selected)
const displayYear     = ref(initSolar.year);
const displayMonthKey = ref(monthKey(initSolar.month, false)); // solar: "M-false"

// Lunar-mode display context
const lunarDisplayYear     = ref(initLunar.lunarYear);
const lunarDisplayMonth    = ref(initLunar.lunarMonth);
const lunarDisplayIsLeap   = ref(initLunar.isLeapMonth);

// ────────────────────────────────────────────────────────────
// Helpers
// ────────────────────────────────────────────────────────────
function monthKey(month, isLeap) {
    return `${month}-${isLeap}`;
}
function parseMonthKey(key) {
    const [m, leap] = key.split('-');
    return { month: Number(m), isLeap: leap === 'true' };
}

// ────────────────────────────────────────────────────────────
// Year range (scrollable select)
// ────────────────────────────────────────────────────────────
const yearRange = computed(() => {
    const result = [];
    for (let y = MIN_YEAR; y <= MAX_YEAR; y++) result.push(y);
    return result;
});

// ────────────────────────────────────────────────────────────
// Available months (dynamic — includes leap months in lunar mode)
// ────────────────────────────────────────────────────────────
const availableMonths = computed(() => {
    if (currentMode.value === 'solar') {
        return Array.from({ length: 12 }, (_, i) => ({
            key: monthKey(i + 1, false),
            label: `Tháng ${i + 1}`,
        }));
    }
    // Lunar mode: use getLunarMonthsInYear for dynamic leap months
    const lunarMonths = getLunarMonthsInYear(lunarDisplayYear.value);
    return lunarMonths.map(m => ({
        key: monthKey(m.month, m.isLeap),
        label: m.isLeap ? `Tháng ${m.month} Nhuận` : `Tháng ${m.month}`,
        days: m.days,
    }));
});

// ────────────────────────────────────────────────────────────
// Days in current display month
// ────────────────────────────────────────────────────────────
const daysInMonth = computed(() => {
    if (currentMode.value === 'solar') {
        const { month } = parseMonthKey(displayMonthKey.value);
        return new Date(displayYear.value, month, 0).getDate();
    }
    // Lunar: find days from availableMonths
    const m = availableMonths.value.find(m => m.key === displayMonthKey.value);
    return m?.days ?? 30;
});

// ────────────────────────────────────────────────────────────
// First weekday of the display month (0=Sun)
// ────────────────────────────────────────────────────────────
const leadingBlanks = computed(() => {
    if (currentMode.value === 'solar') {
        const { month } = parseMonthKey(displayMonthKey.value);
        return new Date(displayYear.value, month - 1, 1).getDay();
    }
    // Lunar: find the solar date of day 1 of this lunar month
    try {
        const { month, isLeap } = parseMonthKey(displayMonthKey.value);
        const solar = lunarToSolar(1, month, lunarDisplayYear.value, isLeap);
        return new Date(solar.year, solar.month - 1, solar.day).getDay();
    } catch {
        return 0;
    }
});

// ────────────────────────────────────────────────────────────
// Day cells
// ────────────────────────────────────────────────────────────
const dayCells = computed(() => {
    const sel = parseISO(selectedDate.value);
    const cells = [];

    for (let d = 1; d <= daysInMonth.value; d++) {
        if (currentMode.value === 'solar') {
            const { month } = parseMonthKey(displayMonthKey.value);
            const iso = toISO(displayYear.value, month, d);
            const isToday = iso === todayISO;
            const isSelected = iso === selectedDate.value;
            // Show brief lunar label in solar mode
            let lunarLabel = '';
            try {
                const l = solarToLunar(displayYear.value, month, d);
                lunarLabel = l.lunarDay === 1
                    ? `${l.lunarMonth}/${l.lunarYear % 100}`
                    : String(l.lunarDay);
            } catch {}
            cells.push({ day: d, isToday, isSelected, lunarLabel, disabled: false });
        } else {
            // Lunar mode
            const { month, isLeap } = parseMonthKey(displayMonthKey.value);
            let isToday = false;
            let isSelected = false;
            try {
                const solar = lunarToSolar(d, month, lunarDisplayYear.value, isLeap);
                const iso = toISO(solar.year, solar.month, solar.day);
                isToday = iso === todayISO;
                isSelected = iso === selectedDate.value;
            } catch {}
            cells.push({ day: d, isToday, isSelected, lunarLabel: '', disabled: false });
        }
    }
    return cells;
});

// ────────────────────────────────────────────────────────────
// Lunar label for selected date
// ────────────────────────────────────────────────────────────
const lunarLabel = computed(() => {
    try {
        const { year, month, day } = parseISO(selectedDate.value);
        const l = solarToLunar(year, month, day);
        const leap = l.isLeapMonth ? ' Nhuận' : '';
        return `${l.lunarDay} tháng ${l.lunarMonth}${leap} năm ${l.lunarYear}`;
    } catch {
        return '';
    }
});

// ────────────────────────────────────────────────────────────
// Event handlers
// ────────────────────────────────────────────────────────────
function setMode(mode) {
    currentMode.value = mode;
    if (mode === 'lunar') {
        // Sync lunar display to current selectedDate
        const { year, month, day } = parseISO(selectedDate.value);
        const l = solarToLunar(year, month, day);
        lunarDisplayYear.value   = l.lunarYear;
        lunarDisplayMonth.value  = l.lunarMonth;
        lunarDisplayIsLeap.value = l.isLeapMonth;
        displayYear.value        = l.lunarYear;
        displayMonthKey.value    = monthKey(l.lunarMonth, l.isLeapMonth);
    } else {
        // Sync solar display to current selectedDate
        const { year, month } = parseISO(selectedDate.value);
        displayYear.value     = year;
        displayMonthKey.value = monthKey(month, false);
    }
}

function onYearChange() {
    if (currentMode.value === 'lunar') {
        lunarDisplayYear.value = displayYear.value;
        // Edge Case 2: after year change, the leap month set may differ.
        // Check if current monthKey still exists; if not, reset to month 1.
        const months = getLunarMonthsInYear(displayYear.value);
        const exists = months.find(m => m.key === displayMonthKey.value
            || monthKey(m.month, m.isLeap) === displayMonthKey.value);
        if (!exists) {
            displayMonthKey.value = monthKey(1, false);
            lunarDisplayMonth.value  = 1;
            lunarDisplayIsLeap.value = false;
        }
    }
}

function onMonthChange() {
    if (currentMode.value === 'lunar') {
        const { month, isLeap } = parseMonthKey(displayMonthKey.value);
        lunarDisplayMonth.value  = month;
        lunarDisplayIsLeap.value = isLeap;

        // Edge Case 1: clamp selectedDay if new month has fewer days
        const { year, month: sm, day } = parseISO(selectedDate.value);
        const l = solarToLunar(year, sm, day);
        const newMonthDays = availableMonths.value.find(
            m => m.key === displayMonthKey.value
        )?.days ?? 30;
        const clampedDay = Math.min(l.lunarDay, newMonthDays);
        if (clampedDay !== l.lunarDay) {
            try {
                const solar = lunarToSolar(clampedDay, month, lunarDisplayYear.value, isLeap);
                selectedDate.value = toISO(solar.year, solar.month, solar.day);
                emit('update:modelValue', selectedDate.value);
            } catch {}
        }
    }
}

function selectDay(day) {
    if (currentMode.value === 'solar') {
        const { month } = parseMonthKey(displayMonthKey.value);
        selectedDate.value = toISO(displayYear.value, month, day);
    } else {
        const { month, isLeap } = parseMonthKey(displayMonthKey.value);
        try {
            const solar = lunarToSolar(day, month, lunarDisplayYear.value, isLeap);
            selectedDate.value = toISO(solar.year, solar.month, solar.day);
        } catch { return; }
    }
    emit('update:modelValue', selectedDate.value);
}

// ────────────────────────────────────────────────────────────
// Helpers
// ────────────────────────────────────────────────────────────
function formatSolar(iso) {
    const { year, month, day } = parseISO(iso);
    return `${day}/${month}/${year}`;
}

// ────────────────────────────────────────────────────────────
// Watch: sync modelValue from outside
// ────────────────────────────────────────────────────────────
watch(() => props.modelValue, (val) => {
    if (val && val !== selectedDate.value) {
        selectedDate.value = val;
        setMode(currentMode.value); // re-sync display context
    }
});
</script>

<style scoped>
.lunar-datepicker.is-dense .mode-wrap {
    margin-bottom: 0.75rem;
}

.lunar-datepicker.is-dense .mode-btn {
    min-height: 38px;
    padding-top: 0.4rem;
    padding-bottom: 0.4rem;
}

.lunar-datepicker.is-dense .picker-selects {
    margin-bottom: 0.65rem;
}

.lunar-datepicker.is-dense .picker-selects select {
    min-height: 38px !important;
}

.lunar-datepicker.is-dense .calendar-shell .grid-cols-7 > div {
    padding-top: 0.45rem;
    padding-bottom: 0.45rem;
}

.lunar-datepicker.is-dense .calendar-blank,
.lunar-datepicker.is-dense .calendar-day {
    height: 2.85rem;
}

.lunar-datepicker.is-dense .calendar-day > span:first-child {
    font-size: 0.84rem;
}

.lunar-datepicker.is-dense .calendar-day > span:last-child {
    font-size: 0.56rem;
}

.lunar-datepicker.is-dense .picker-footer {
    margin-top: 0.5rem;
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
    font-size: 0.79rem;
}

@media (max-width: 639px) {
    .lunar-datepicker.is-dense .calendar-blank,
    .lunar-datepicker.is-dense .calendar-day {
        height: 3rem;
    }
}
</style>
