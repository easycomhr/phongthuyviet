import { describe, it, expect } from 'vitest';
import { solarToLunar, lunarToSolar, getLunarMonthsInYear } from '../lunar.js';

// ============================================================
// solarToLunar
// ============================================================
describe('solarToLunar', () => {
    it('converts Mid-Autumn 2024 correctly (2024-09-17 = 15/8/2024)', () => {
        const result = solarToLunar(2024, 9, 17);
        expect(result.lunarDay).toBe(15);
        expect(result.lunarMonth).toBe(8);
        expect(result.lunarYear).toBe(2024);
        expect(result.isLeapMonth).toBe(false);
    });

    it('converts Tết 2024 correctly (2024-02-10 = 1/1/2024)', () => {
        const result = solarToLunar(2024, 2, 10);
        expect(result.lunarDay).toBe(1);
        expect(result.lunarMonth).toBe(1);
        expect(result.lunarYear).toBe(2024);
    });

    it('detects leap month 4 in 2020 (2020-05-23 = 1/leap4/2020)', () => {
        const result = solarToLunar(2020, 5, 23);
        expect(result.lunarMonth).toBe(4);
        expect(result.isLeapMonth).toBe(true);
        expect(result.lunarDay).toBe(1);
    });

    it('returns isLeapMonth=false for the regular month 4 of 2020', () => {
        const result = solarToLunar(2020, 4, 24); // day 2 of regular month 4
        expect(result.lunarMonth).toBe(4);
        expect(result.isLeapMonth).toBe(false);
    });

    it('handles min boundary year 1900', () => {
        const result = solarToLunar(1900, 1, 31);
        expect(result).toBeDefined();
        expect(result.lunarYear).toBeGreaterThanOrEqual(1899);
    });

    it('handles max boundary year 2100', () => {
        const result = solarToLunar(2100, 6, 1);
        expect(result).toBeDefined();
        expect(result.lunarYear).toBeGreaterThanOrEqual(2099);
    });
});

// ============================================================
// lunarToSolar
// ============================================================
describe('lunarToSolar', () => {
    it('converts 15/8/2024 back to solar 2024-09-17', () => {
        const result = lunarToSolar(15, 8, 2024, false);
        expect(result.year).toBe(2024);
        expect(result.month).toBe(9);
        expect(result.day).toBe(17);
    });

    it('round-trips: solar -> lunar -> solar', () => {
        const solar = { year: 2025, month: 5, day: 20 };
        const lunar = solarToLunar(solar.year, solar.month, solar.day);
        const back = lunarToSolar(lunar.lunarDay, lunar.lunarMonth, lunar.lunarYear, lunar.isLeapMonth);
        expect(back.year).toBe(solar.year);
        expect(back.month).toBe(solar.month);
        expect(back.day).toBe(solar.day);
    });

    it('correctly converts first day of leap month 4 in 2020 to 2020-05-23', () => {
        const result = lunarToSolar(1, 4, 2020, true);
        expect(result.year).toBe(2020);
        expect(result.month).toBe(5);
        expect(result.day).toBe(23);
    });

    it('distinguishes regular month 4 vs leap month 4 in 2020', () => {
        const regular = lunarToSolar(1, 4, 2020, false); // 2020-04-23
        const leap    = lunarToSolar(1, 4, 2020, true);  // 2020-05-23
        expect(regular.month).not.toBe(leap.month); // April vs May
        expect(regular.month).toBe(4);
        expect(leap.month).toBe(5);
    });
});

// ============================================================
// getLunarMonthsInYear — Edge Case 2: tháng nhuận
// ============================================================
describe('getLunarMonthsInYear', () => {
    it('returns 12 months for a non-leap lunar year (2024)', () => {
        const months = getLunarMonthsInYear(2024);
        expect(months).toHaveLength(12);
        expect(months.filter(m => m.isLeap)).toHaveLength(0);
    });

    it('returns 13 entries for leap lunar year 2020 (leap month 4)', () => {
        const months = getLunarMonthsInYear(2020);
        expect(months).toHaveLength(13);
        const leapEntries = months.filter(m => m.isLeap);
        expect(leapEntries).toHaveLength(1);
        expect(leapEntries[0].month).toBe(4);
    });

    it('each month has 29 or 30 days (Edge Case 1: tháng thiếu/đủ)', () => {
        const months = getLunarMonthsInYear(2024);
        months.forEach(m => {
            expect(m).toHaveProperty('month');
            expect(m).toHaveProperty('isLeap');
            expect(m).toHaveProperty('days');
            expect([29, 30]).toContain(m.days);
        });
    });

    it('leap month entry appears after its regular counterpart', () => {
        const months = getLunarMonthsInYear(2020);
        const idx4 = months.findIndex(m => m.month === 4 && !m.isLeap);
        const idxLeap4 = months.findIndex(m => m.month === 4 && m.isLeap);
        expect(idx4).toBeGreaterThanOrEqual(0);
        expect(idxLeap4).toBe(idx4 + 1);
    });
});
