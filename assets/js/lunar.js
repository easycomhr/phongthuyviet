/**
 * lunar.js - Vietnamese Lunar Calendar Conversion
 * Chuyển đổi Dương lịch sang Âm lịch Việt Nam
 */

const CAN = ['Giáp', 'Ất', 'Bính', 'Đinh', 'Mậu', 'Kỷ', 'Canh', 'Tân', 'Nhâm', 'Quý'];
const CHI = ['Tý', 'Sửu', 'Dần', 'Mão', 'Thìn', 'Tỵ', 'Ngọ', 'Mùi', 'Thân', 'Dậu', 'Tuất', 'Hợi'];
const ZODIAC = ['Chuột', 'Trâu', 'Hổ', 'Mèo', 'Rồng', 'Rắn', 'Ngựa', 'Dê', 'Khỉ', 'Gà', 'Chó', 'Lợn'];
const ZODIAC_EN = ['Tý', 'Sửu', 'Dần', 'Mão', 'Thìn', 'Tỵ', 'Ngọ', 'Mùi', 'Thân', 'Dậu', 'Tuất', 'Hợi'];

const PI = Math.PI;

function INT(d) { return Math.floor(d); }

function jdFromDate(dd, mm, yy) {
  let a = INT((14 - mm) / 12);
  let y = yy + 4800 - a;
  let m = mm + 12 * a - 3;
  let jd = dd + INT((153 * m + 2) / 5) + 365 * y + INT(y / 4) - INT(y / 100) + INT(y / 400) - 32045;
  if (jd < 2299161) {
    jd = dd + INT((153 * m + 2) / 5) + 365 * y + INT(y / 4) - 32083;
  }
  return jd;
}

function jdToDate(jd) {
  let a, b, c, d, e, m;
  if (jd > 2299160) {
    a = jd + 32044;
    b = INT((4 * a + 3) / 146097);
    c = a - INT((b * 146097) / 4);
  } else {
    b = 0;
    c = jd + 32082;
  }
  d = INT((4 * c + 3) / 1461);
  e = c - INT((1461 * d) / 4);
  m = INT((5 * e + 2) / 153);
  let day = e - INT((153 * m + 2) / 5) + 1;
  let month = m + 3 - 12 * INT(m / 10);
  let year = b * 100 + d - 4800 + INT(m / 10);
  return [day, month, year];
}

function sunLongitude(jdn) {
  let T = (jdn - 2451545.0) / 36525;
  let T2 = T * T;
  let dr = PI / 180;
  let M = 357.52911 + 35999.05029 * T - 0.0001537 * T2;
  let L0 = 280.46646 + 36000.76983 * T + 0.0003032 * T2;
  let DL = (1.914602 - 0.004817 * T - 0.000014 * T2) * Math.sin(dr * M);
  DL += (0.019993 - 0.000101 * T) * Math.sin(dr * 2 * M) + 0.000289 * Math.sin(dr * 3 * M);
  let L = L0 + DL;
  let anomaly = 125.04 - 1934.136 * T;
  L = L - 0.00569 - 0.00478 * Math.sin(dr * anomaly);
  L = L * dr;
  L = L - PI * 2 * INT(L / (PI * 2));
  return L;
}

function newMoon(k) {
  let T = k / 1236.85;
  let T2 = T * T;
  let T3 = T2 * T;
  let dr = PI / 180;
  let Jd1 = 2415020.75933 + 29.53058868 * k + 0.0001178 * T2 - 0.000000155 * T3;
  Jd1 += 0.00033 * Math.sin((166.56 + 132.87 * T - 0.009173 * T2) * dr);
  let M = 359.2242 + 29.10535608 * k - 0.0000333 * T2 - 0.00000347 * T3;
  let Mpr = 306.0253 + 385.81691806 * k + 0.0107306 * T2 + 0.00001236 * T3;
  let F = 21.2964 + 390.67050646 * k - 0.0016528 * T2 - 0.00000239 * T3;
  let C1 = (0.1734 - 0.000393 * T) * Math.sin(M * dr) + 0.0021 * Math.sin(2 * dr * M);
  C1 -= 0.4068 * Math.sin(Mpr * dr) + 0.0161 * Math.sin(dr * 2 * Mpr);
  C1 -= 0.0004 * Math.sin(dr * 3 * Mpr);
  C1 += 0.0104 * Math.sin(dr * 2 * F) - 0.0051 * Math.sin(dr * (M + Mpr));
  C1 -= 0.0074 * Math.sin(dr * (M - Mpr)) + 0.0004 * Math.sin(dr * (2 * F + M));
  C1 -= 0.0004 * Math.sin(dr * (2 * F - M)) - 0.0006 * Math.sin(dr * (2 * F + Mpr));
  C1 += 0.0010 * Math.sin(dr * (2 * F - Mpr)) + 0.0005 * Math.sin(dr * (2 * Mpr + M));
  let deltat;
  if (T < -11) {
    deltat = 0.001 + 0.000839 * T + 0.0002261 * T2 - 0.00000845 * T3 - 0.000000081 * T * T3;
  } else {
    deltat = -0.000278 + 0.000265 * T + 0.000262 * T2;
  }
  return Jd1 + C1 - deltat;
}

function sunLongitudeAtNewMoon(jdn) {
  return sunLongitude(jdn);
}

function getLunarMonth11(yy, timeZone) {
  let off = jdFromDate(31, 12, yy) - 2415021.076998695;
  let k = INT(off / 29.530588853);
  let nm = newMoon(k);
  let sunLong = INT(sunLongitude(nm + 0.5 + timeZone / 24) / PI * 6);
  if (sunLong >= 9) k--;
  nm = newMoon(k);
  sunLong = INT(sunLongitude(nm + 0.5 + timeZone / 24) / PI * 6);
  if (sunLong >= 9) k--;
  return k;
}

function isLeapYear(k) {
  return newMoon(k + 14) - newMoon(k) > 365.5;
}

function convertSolar2Lunar(dd, mm, yy, timeZone) {
  let dayNumber = jdFromDate(dd, mm, yy);
  let k = INT((dayNumber - 2415021.076998695) / 29.530588853);
  let monthStart = newMoon(k + 1);
  if (INT(monthStart) > dayNumber) monthStart = newMoon(k);
  let a11 = getLunarMonth11(yy, timeZone);
  let b11 = a11;
  let lunarYear;
  if (a11 >= k - 9) {
    lunarYear = yy;
    a11 = getLunarMonth11(yy - 1, timeZone);
  } else {
    lunarYear = yy + 1;
    b11 = getLunarMonth11(yy + 1, timeZone);
  }
  let lunarDay = dayNumber - INT(monthStart) + 1;
  let diff = INT(monthStart) - INT(newMoon(a11));
  let isLeapMonth = false;
  let lunarMonth = INT(diff / 29) + 11;
  if (b11 - a11 > 365) {
    let leapMonthDiff = INT((monthStart - newMoon(a11)) / 29.530588853);
    let leapMonth = leapMonthDiff - 11;
    if (leapMonth < 0) leapMonth += 12;
    if (isLeapYear(a11) && leapMonthDiff >= leapMonth) {
      if (leapMonthDiff == leapMonth + 1) isLeapMonth = true;
      if (leapMonthDiff > leapMonth) lunarMonth--;
    }
  }
  if (lunarMonth > 12) lunarMonth -= 12;
  if (lunarMonth >= 11 && diff < 4) lunarYear--;
  return [lunarDay, lunarMonth, lunarYear, isLeapMonth];
}

/**
 * Main export: solarToLunar
 * @param {number} year
 * @param {number} month (1-12)
 * @param {number} day
 * @returns {{ lunarDay, lunarMonth, lunarYear, isLeapMonth }}
 */
function solarToLunar(year, month, day) {
  const timeZone = 7; // Vietnam UTC+7
  const [lunarDay, lunarMonth, lunarYear, isLeapMonth] = convertSolar2Lunar(day, month, year, timeZone);
  return { lunarDay, lunarMonth, lunarYear, isLeapMonth };
}

/**
 * Get Can Chi for a solar date
 */
function getCanChiDay(year, month, day) {
  const jd = jdFromDate(day, month, year);
  const canIndex = (jd + 9) % 10;
  const chiIndex = (jd + 1) % 12;
  return { can: CAN[canIndex], chi: CHI[chiIndex], canIndex, chiIndex };
}

function getCanChiMonth(lunarMonth, lunarYear) {
  const canIndex = (lunarYear * 12 + lunarMonth + 3) % 10;
  const chiIndex = (lunarMonth + 1) % 12;
  return { can: CAN[canIndex], chi: CHI[chiIndex] };
}

function getCanChiYear(year) {
  const canIndex = (year + 6) % 10;
  const chiIndex = (year + 8) % 12;
  return { can: CAN[canIndex], chi: CHI[chiIndex], canIndex, chiIndex };
}

function getCanChi(year, month, day) {
  const lunar = solarToLunar(year, month, day);
  const dayCC = getCanChiDay(year, month, day);
  const monthCC = getCanChiMonth(lunar.lunarMonth, lunar.lunarYear);
  const yearCC = getCanChiYear(lunar.lunarYear);
  return {
    day: dayCC,
    month: monthCC,
    year: yearCC,
    dayStr: `${dayCC.can} ${dayCC.chi}`,
    monthStr: `${monthCC.can} ${monthCC.chi}`,
    yearStr: `${yearCC.can} ${yearCC.chi}`
  };
}

function getZodiac(year) {
  const idx = (year + 8) % 12;
  return { name: ZODIAC[idx], chi: CHI[idx] };
}

function getZodiacByLunarYear(lunarYear) {
  return getZodiac(lunarYear);
}

// Format lunar date string
function formatLunarDate(lunarDay, lunarMonth, lunarYear, isLeapMonth) {
  const leap = isLeapMonth ? ' (nhuận)' : '';
  return `${lunarDay} tháng ${lunarMonth}${leap} năm ${lunarYear}`;
}

// Get days in a solar month
function getDaysInMonth(year, month) {
  return new Date(year, month, 0).getDate();
}

// Get first day of month (0=Sun, 1=Mon...)
function getFirstDayOfMonth(year, month) {
  return new Date(year, month - 1, 1).getDay();
}
