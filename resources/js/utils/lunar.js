/**
 * Vietnamese Lunar Calendar Utilities (ESM)
 * Core: @nghiavuive/lunar_date_vi (Hồ Ngọc Đức algorithm, Vietnam UTC+7)
 */

import { SolarDate } from '@nghiavuive/lunar_date_vi';

// ─── Internal: Julian Day helpers (for lunarToSolar search) ──────────────────

const PI = Math.PI;

function INT(d) { return Math.floor(d); }

function jdFromDate(dd, mm, yy) {
    const a = INT((14 - mm) / 12);
    const y = yy + 4800 - a;
    const m = mm + 12 * a - 3;
    let jd = dd + INT((153 * m + 2) / 5) + 365 * y + INT(y / 4) - INT(y / 100) + INT(y / 400) - 32045;
    if (jd < 2299161) {
        jd = dd + INT((153 * m + 2) / 5) + 365 * y + INT(y / 4) - 32083;
    }
    return jd;
}

function jdToDate(jd) {
    let a, b, c;
    if (jd > 2299160) {
        a = jd + 32044;
        b = INT((4 * a + 3) / 146097);
        c = a - INT((b * 146097) / 4);
    } else {
        b = 0;
        c = jd + 32082;
    }
    const d = INT((4 * c + 3) / 1461);
    const e = c - INT((1461 * d) / 4);
    const m = INT((5 * e + 2) / 153);
    const day = e - INT((153 * m + 2) / 5) + 1;
    const month = m + 3 - 12 * INT(m / 10);
    const year = b * 100 + d - 4800 + INT(m / 10);
    return { day, month, year };
}

// ─── Public API ──────────────────────────────────────────────────────────────

/**
 * Convert a solar (Gregorian) date to Vietnamese lunar date.
 * @param {number} year
 * @param {number} month 1–12
 * @param {number} day
 * @returns {{ lunarDay: number, lunarMonth: number, lunarYear: number, isLeapMonth: boolean }}
 */
export function solarToLunar(year, month, day) {
    const solar = new SolarDate(new Date(year, month - 1, day));
    const lunar = solar.toLunarDate();
    return {
        lunarDay: lunar.day,
        lunarMonth: lunar.month,
        lunarYear: lunar.year,
        isLeapMonth: lunar.leap_month ?? false,
    };
}

/**
 * Convert Vietnamese lunar date to solar (Gregorian) date.
 * Scans a small window around the expected solar date — O(~45) iterations max.
 * @param {number} lunarDay
 * @param {number} lunarMonth 1–12
 * @param {number} lunarYear
 * @param {boolean} isLeapMonth
 * @returns {{ day: number, month: number, year: number }}
 */
export function lunarToSolar(lunarDay, lunarMonth, lunarYear, isLeapMonth) {
    // Approximate the solar year range to search
    // Lunar year starts ~Jan-Feb of the solar year
    const startSolarYear = lunarMonth >= 11 ? lunarYear : lunarYear - 1;

    // Search from Jan 1 of the approximate start year through Feb of year+1
    const startJD = jdFromDate(1, 1, startSolarYear);
    const endJD   = jdFromDate(28, 2, lunarYear + 1);

    for (let jd = startJD; jd <= endJD; jd++) {
        const { day, month, year } = jdToDate(jd);
        const solar = new SolarDate(new Date(year, month - 1, day));
        const lunar = solar.toLunarDate();
        if (
            lunar.day === lunarDay &&
            lunar.month === lunarMonth &&
            lunar.year === lunarYear &&
            (lunar.leap_month ?? false) === isLeapMonth
        ) {
            return { day, month, year };
        }
    }

    throw new Error(`Cannot convert lunar ${lunarDay}/${lunarMonth}/${lunarYear} isLeap=${isLeapMonth} to solar`);
}

/**
 * Return all lunar months in a given lunar year, in order.
 * In a leap year, the leap month appears twice consecutively:
 *   { month: 4, isLeap: false } then { month: 4, isLeap: true }
 *
 * @param {number} lunarYear
 * @returns {Array<{ month: number, isLeap: boolean, days: number }>}
 */
export function getLunarMonthsInYear(lunarYear) {
    const result = [];

    // Scan solar dates for the 12–13 month boundaries.
    // Month 11 of lunarYear starts sometime in Dec of (lunarYear-1) or Jan lunarYear.
    // Month 11 of the next lunar year ends in Jan/Feb (lunarYear+2).
    const scanStart = jdFromDate(1, 10, lunarYear - 1);
    const scanEnd   = jdFromDate(28, 2,  lunarYear + 2);

    let prevLunarMonth = null;
    let prevIsLeap = null;
    let monthStartJD = null;

    for (let jd = scanStart; jd <= scanEnd; jd++) {
        const { day, month, year } = jdToDate(jd);
        const solar = new SolarDate(new Date(year, month - 1, day));
        const lunar = solar.toLunarDate();

        if (lunar.year !== lunarYear) continue;

        const isLeap = lunar.leap_month ?? false;
        const key = `${lunar.month}-${isLeap}`;
        const prevKey = `${prevLunarMonth}-${prevIsLeap}`;

        if (key !== prevKey) {
            // Flush previous month
            if (prevLunarMonth !== null && monthStartJD !== null) {
                const days = jd - monthStartJD;
                result.push({ month: prevLunarMonth, isLeap: prevIsLeap, days });
            }
            prevLunarMonth = lunar.month;
            prevIsLeap = isLeap;
            monthStartJD = jd;
        }
    }

    // Flush last month
    if (prevLunarMonth !== null && monthStartJD !== null) {
        // Count remaining days up to the scan end
        let lastJD = scanEnd;
        for (let jd = monthStartJD + 1; jd <= scanEnd; jd++) {
            const { day, month, year } = jdToDate(jd);
            const solar = new SolarDate(new Date(year, month - 1, day));
            const lunar = solar.toLunarDate();
            if (lunar.year !== lunarYear) { lastJD = jd; break; }
        }
        const days = lastJD - monthStartJD;
        result.push({ month: prevLunarMonth, isLeap: prevIsLeap, days });
    }

    return result;
}
