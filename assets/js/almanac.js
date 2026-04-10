/**
 * almanac.js - Auspicious/Inauspicious Day Logic
 * Tra cứu ngày tốt xấu theo Lịch Vạn Niên
 */

// 12 Trực
const TRUC = ['Kiến', 'Trừ', 'Mãn', 'Bình', 'Định', 'Chấp', 'Phá', 'Nguy', 'Thành', 'Thu', 'Khai', 'Bế'];

// Trực index: tính theo (chiNgày + chiTháng) % 12
// Bảng trực tốt / xấu
const TRUC_GOOD = [2, 4, 8, 10]; // Mãn(2), Định(4), Thành(8), Khai(10)
const TRUC_BAD  = [6, 7, 11];    // Phá(6), Nguy(7), Bế(11)

// Ngày Tam Nương (xấu cho hầu hết việc)
const TAM_NUONG = [3, 7, 13, 18, 22, 27];

// Ngày Nguyệt Kị (xấu)
const NGUYET_KI = [5, 14, 23];

// Ngày Dương Công (13 ngày hung trong năm - theo lịch âm)
// tháng: ngày
const DUONG_CONG = {
  1: [13], 2: [11], 3: [9], 4: [7], 5: [5], 6: [3],
  7: [1, 29], 8: [27], 9: [25], 10: [23], 11: [21], 12: [19]
};

// Giờ Hoàng Đạo theo Chi của ngày
// Chi ngày -> [giờ Hoàng Đạo]
// Giờ: Tý(23-1), Sửu(1-3), Dần(3-5), Mão(5-7), Thìn(7-9), Tỵ(9-11),
//      Ngọ(11-13), Mùi(13-15), Thân(15-17), Dậu(17-19), Tuất(19-21), Hợi(21-23)
const GIO_HOANG_DAO = {
  0:  ['Tý', 'Sửu', 'Mão', 'Ngọ', 'Thân', 'Dậu'],   // Tý
  1:  ['Dần', 'Mão', 'Tỵ', 'Thân', 'Tuất', 'Hợi'],   // Sửu
  2:  ['Tý', 'Sửu', 'Thìn', 'Tỵ', 'Mùi', 'Tuất'],    // Dần
  3:  ['Dần', 'Thìn', 'Ngọ', 'Mùi', 'Dậu', 'Hợi'],   // Mão
  4:  ['Tý', 'Mão', 'Thìn', 'Ngọ', 'Dậu', 'Tuất'],   // Thìn
  5:  ['Sửu', 'Dần', 'Tỵ', 'Mùi', 'Thân', 'Hợi'],    // Tỵ
  6:  ['Tý', 'Sửu', 'Mão', 'Ngọ', 'Thân', 'Dậu'],    // Ngọ
  7:  ['Dần', 'Mão', 'Tỵ', 'Thân', 'Tuất', 'Hợi'],   // Mùi
  8:  ['Tý', 'Sửu', 'Thìn', 'Tỵ', 'Mùi', 'Tuất'],    // Thân
  9:  ['Dần', 'Thìn', 'Ngọ', 'Mùi', 'Dậu', 'Hợi'],   // Dậu
  10: ['Tý', 'Mão', 'Thìn', 'Ngọ', 'Dậu', 'Tuất'],   // Tuất
  11: ['Sửu', 'Dần', 'Tỵ', 'Mùi', 'Thân', 'Hợi'],    // Hợi
};

// Chi giờ -> thời gian
const GIO_CHI_TIME = {
  'Tý':  '23:00 – 01:00',
  'Sửu': '01:00 – 03:00',
  'Dần': '03:00 – 05:00',
  'Mão': '05:00 – 07:00',
  'Thìn':'07:00 – 09:00',
  'Tỵ':  '09:00 – 11:00',
  'Ngọ': '11:00 – 13:00',
  'Mùi': '13:00 – 15:00',
  'Thân':'15:00 – 17:00',
  'Dậu': '17:00 – 19:00',
  'Tuất':'19:00 – 21:00',
  'Hợi': '21:00 – 23:00',
};

// Trực tốt theo loại sự kiện
const EVENT_TRUC = {
  'cuoi-hoi':    { good: [2, 4, 8, 10], bad: [6, 7, 11] },
  'khoi-cong':   { good: [2, 4, 8, 10], bad: [6, 7, 11] },
  'ma-chay':     { good: [1, 5, 9, 11], bad: [2, 4, 8]  },
  'khai-truong': { good: [2, 4, 8, 10], bad: [6, 7, 11] },
  'nhap-trach':  { good: [4, 8, 10],    bad: [6, 7]     },
  'xuat-hanh':   { good: [0, 4, 8, 10], bad: [6, 7, 11] },
  'ky-ket':      { good: [4, 8, 10],    bad: [6, 7, 11] },
};

// Tháng không tốt cho cưới hỏi (tháng ngâu, tháng cô hồn)
const BAD_MONTHS_CUOI = [7]; // tháng 7 âm lịch

// Chi tháng theo lunar month
const CHI_MONTH = ['Dần', 'Mão', 'Thìn', 'Tỵ', 'Ngọ', 'Mùi', 'Thân', 'Dậu', 'Tuất', 'Hợi', 'Tý', 'Sửu'];
const CHI = ['Tý', 'Sửu', 'Dần', 'Mão', 'Thìn', 'Tỵ', 'Ngọ', 'Mùi', 'Thân', 'Dậu', 'Tuất', 'Hợi'];

/**
 * Tính trực của ngày
 * @param {number} dayChiIndex - chi index của ngày (0-11)
 * @param {number} lunarMonth - tháng âm lịch (1-12)
 */
function getTruc(dayChiIndex, lunarMonth) {
  const monthChiIndex = (lunarMonth + 1) % 12; // Tháng 1 âm = Dần (2), nhưng offset
  // Công thức: trực = (chiNgày - chiThángĐầuTháng + baseOffset) % 12
  // Tháng 1 bắt đầu từ trực Kiến khi ngày có chi Dần
  // base: tháng Dần (1) -> Kiến bắt đầu từ ngày Dần
  const monthBase = (lunarMonth + 1) % 12; // chi index của ngày đầu tiên có trực Kiến
  const trucIdx = ((dayChiIndex - monthBase) % 12 + 12) % 12;
  return { index: trucIdx, name: TRUC[trucIdx] };
}

/**
 * Lấy giờ Hoàng Đạo trong ngày
 */
function getGioHoangDao(dayChiIndex) {
  const hours = GIO_HOANG_DAO[dayChiIndex] || [];
  return hours.map(chi => ({ chi, time: GIO_CHI_TIME[chi] }));
}

/**
 * Đánh giá chất lượng ngày
 * @returns { quality: 'good'|'neutral'|'bad', reasons: [], score: number }
 */
function getDayQuality(lunarDay, lunarMonth, lunarYear, eventType, dayCanChi) {
  let score = 0;
  const reasons = { good: [], bad: [] };
  const dayChiIndex = dayCanChi ? dayCanChi.chiIndex : 0;

  // 1. Ngày Tam Nương
  if (TAM_NUONG.includes(lunarDay)) {
    score -= 3;
    reasons.bad.push('Ngày Tam Nương (đại hung)');
  }

  // 2. Ngày Nguyệt Kị
  if (NGUYET_KI.includes(lunarDay)) {
    score -= 2;
    reasons.bad.push('Ngày Nguyệt Kị (xấu)');
  }

  // 3. Ngày Dương Công
  const duongCongDays = DUONG_CONG[lunarMonth] || [];
  if (duongCongDays.includes(lunarDay)) {
    score -= 3;
    reasons.bad.push('Ngày Dương Công (hung nhật)');
  }

  // 4. Trực
  const truc = getTruc(dayChiIndex, lunarMonth);
  const evTruc = EVENT_TRUC[eventType] || EVENT_TRUC['khoi-cong'];
  if (evTruc.good.includes(truc.index)) {
    score += 2;
    reasons.good.push(`Trực ${truc.name} (tốt cho ${getEventName(eventType)})`);
  } else if (evTruc.bad.includes(truc.index)) {
    score -= 2;
    reasons.bad.push(`Trực ${truc.name} (không tốt cho ${getEventName(eventType)})`);
  } else {
    reasons.good.push(`Trực ${truc.name}`);
  }

  // 5. Ngày rằm và mồng một
  if (lunarDay === 15) {
    score += 1;
    reasons.good.push('Ngày Rằm (trăng tròn, cát lợi)');
  }
  if (lunarDay === 1) {
    score += 1;
    reasons.good.push('Ngày Mồng Một (đầu tháng, khởi đầu mới)');
  }

  // 6. Quy tắc riêng theo loại sự kiện
  if (eventType === 'cuoi-hoi' && BAD_MONTHS_CUOI.includes(lunarMonth)) {
    score -= 3;
    reasons.bad.push('Tháng 7 âm lịch (tháng ngâu, không nên cưới hỏi)');
  }

  // 7. Ngày đẹp: mùng 2, 6, 10, 12, 16, 20, 26, 30 (theo quan niệm dân gian)
  const nice_days = [2, 6, 10, 12, 16, 20, 26, 30];
  if (nice_days.includes(lunarDay) && score >= 0) {
    score += 1;
    reasons.good.push('Ngày chẵn đẹp theo lịch dân gian');
  }

  let quality;
  if (score >= 2) quality = 'good';
  else if (score <= -2) quality = 'bad';
  else quality = 'neutral';

  // Nếu có ngày Tam Nương hoặc Dương Công -> luôn xấu
  if (reasons.bad.some(r => r.includes('Tam Nương') || r.includes('Dương Công'))) {
    quality = 'bad';
  }

  return { quality, reasons, score, truc };
}

function getEventName(eventType) {
  const map = {
    'cuoi-hoi':    'Cưới hỏi',
    'khoi-cong':   'Khởi công / Xây nhà',
    'ma-chay':     'Ma chay / Tang lễ',
    'khai-truong': 'Khai trương',
    'nhap-trach':  'Nhập trạch',
    'xuat-hanh':   'Xuất hành',
    'ky-ket':      'Ký kết hợp đồng',
  };
  return map[eventType] || eventType;
}

/**
 * Lấy thông tin đầy đủ cho một ngày
 */
function getDayInfo(solarYear, solarMonth, solarDay, eventType) {
  const lunar = solarToLunar(solarYear, solarMonth, solarDay);
  const canChi = getCanChi(solarYear, solarMonth, solarDay);
  const dayChiIndex = canChi.day.chiIndex;
  const quality = getDayQuality(
    lunar.lunarDay, lunar.lunarMonth, lunar.lunarYear,
    eventType || 'khoi-cong',
    canChi.day
  );
  const gioHoangDao = getGioHoangDao(dayChiIndex);

  return {
    solar: { year: solarYear, month: solarMonth, day: solarDay },
    lunar,
    canChi,
    truc: quality.truc,
    quality: quality.quality,
    reasons: quality.reasons,
    score: quality.score,
    gioHoangDao,
  };
}

/**
 * Lấy tất cả ngày trong tháng với chất lượng
 */
function getMonthCalendar(solarYear, solarMonth, eventType) {
  const daysInMonth = getDaysInMonth(solarYear, solarMonth);
  const result = [];
  for (let d = 1; d <= daysInMonth; d++) {
    const info = getDayInfo(solarYear, solarMonth, d, eventType);
    result.push(info);
  }
  return result;
}
