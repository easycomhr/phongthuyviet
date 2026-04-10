/**
 * app.js - Main Application Controller
 * Phong Thủy Việt - Website Tra Ngày Tốt & Văn Khấn
 */

// =============================================
// STATE
// =============================================
let currentSection = 'home';
let currentEventType = 'khoi-cong';
let currentCalendarYear = new Date().getFullYear();
let currentCalendarMonth = new Date().getMonth() + 1;
let selectedDayInfo = null;
let currentPrayerId = null;

// =============================================
// NAVIGATION
// =============================================
function showSection(id) {
  document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
  const sec = document.getElementById(id);
  if (sec) sec.classList.add('active');
  const link = document.querySelector(`[data-section="${id}"]`);
  if (link) link.classList.add('active');
  currentSection = id;
  // Close mobile menu
  document.getElementById('nav-menu').classList.remove('open');
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

// =============================================
// TODAY'S ALMANAC (Lịch hôm nay)
// =============================================
function renderTodayAlmanac() {
  const today = new Date();
  const y = today.getFullYear();
  const m = today.getMonth() + 1;
  const d = today.getDate();

  const lunar = solarToLunar(y, m, d);
  const canChi = getCanChi(y, m, d);
  const zodiac = getZodiacByLunarYear(lunar.lunarYear);
  const dayInfo = getDayInfo(y, m, d, 'khoi-cong');
  const gioHD = getGioHoangDao(canChi.day.chiIndex);

  const weekdays = ['Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy'];
  const months_vi = ['tháng 1', 'tháng 2', 'tháng 3', 'tháng 4', 'tháng 5', 'tháng 6',
    'tháng 7', 'tháng 8', 'tháng 9', 'tháng 10', 'tháng 11', 'tháng 12'];

  const qualityMap = { good: { text: 'Ngày Tốt', cls: 'quality-good' }, neutral: { text: 'Bình thường', cls: 'quality-neutral' }, bad: { text: 'Ngày Xấu', cls: 'quality-bad' } };
  const q = qualityMap[dayInfo.quality];

  const leapStr = lunar.isLeapMonth ? ' (nhuận)' : '';

  // Update hero date
  const heroEl = document.getElementById('hero-date');
  if (heroEl) {
    heroEl.innerHTML = `
      <div class="hero-solar">${weekdays[today.getDay()]}, ${d} ${months_vi[m-1]} ${y}</div>
      <div class="hero-lunar">🌙 Âm lịch: ${lunar.lunarDay} tháng ${lunar.lunarMonth}${leapStr} năm ${canChi.year.can} ${canChi.year.chi} (${zodiac.name} – ${zodiac.chi})</div>
      <div class="hero-cachi">Ngày ${canChi.day.can} ${canChi.day.chi} &nbsp;|&nbsp; Tháng ${canChi.month.can} ${canChi.month.chi} &nbsp;|&nbsp; Năm ${canChi.year.can} ${canChi.year.chi}</div>
      <div class="hero-truc">Trực: <strong>${dayInfo.truc.name}</strong> &nbsp;|&nbsp; <span class="${q.cls}">${q.text}</span></div>
    `;
  }

  // Update today card
  const todayCard = document.getElementById('today-detail');
  if (todayCard) {
    const gioList = gioHD.map(g => `<span class="gio-item">${g.chi}<small>${g.time}</small></span>`).join('');
    const goodReasons = dayInfo.reasons.good.map(r => `<li>✅ ${r}</li>`).join('');
    const badReasons = dayInfo.reasons.bad.map(r => `<li>⚠️ ${r}</li>`).join('');

    todayCard.innerHTML = `
      <div class="today-grid">
        <div class="today-col">
          <h4>📅 Thông tin ngày</h4>
          <div class="info-row"><span>Dương lịch</span><strong>${d}/${m}/${y}</strong></div>
          <div class="info-row"><span>Âm lịch</span><strong>${lunar.lunarDay}/${lunar.lunarMonth}${leapStr}/${lunar.lunarYear}</strong></div>
          <div class="info-row"><span>Can Chi ngày</span><strong>${canChi.day.can} ${canChi.day.chi}</strong></div>
          <div class="info-row"><span>Can Chi tháng</span><strong>${canChi.month.can} ${canChi.month.chi}</strong></div>
          <div class="info-row"><span>Can Chi năm</span><strong>${canChi.year.can} ${canChi.year.chi}</strong></div>
          <div class="info-row"><span>Trực</span><strong>${dayInfo.truc.name}</strong></div>
          <div class="info-row"><span>Tuổi năm</span><strong>${zodiac.name} (${zodiac.chi})</strong></div>
        </div>
        <div class="today-col">
          <h4>⭐ Giờ Hoàng Đạo</h4>
          <div class="gio-list">${gioList}</div>
          <h4 style="margin-top:16px">📊 Nhận xét ngày</h4>
          <ul class="reason-list">${goodReasons}${badReasons}</ul>
        </div>
      </div>
    `;
  }
}

// =============================================
// CALENDAR (Tra ngày tốt)
// =============================================
function renderCalendar() {
  const y = currentCalendarYear;
  const m = currentCalendarMonth;

  // Update month/year display
  const months_vi = ['', 'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
    'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'];
  const calTitle = document.getElementById('cal-title');
  if (calTitle) calTitle.textContent = `${months_vi[m]} năm ${y}`;

  const calBody = document.getElementById('cal-body');
  if (!calBody) return;

  const calData = getMonthCalendar(y, m, currentEventType);
  const firstDay = getFirstDayOfMonth(y, m); // 0=Sun, 1=Mon...6=Sat
  // Calendar shows Sunday first (CN T2 T3 T4 T5 T6 T7)
  const offset = firstDay;

  let html = '';
  // Empty cells
  for (let i = 0; i < offset; i++) html += '<div class="cal-cell empty"></div>';

  const today = new Date();
  calData.forEach(info => {
    const d = info.solar.day;
    const lunarStr = `${info.lunar.lunarDay}/${info.lunar.lunarMonth}${info.lunar.isLeapMonth ? 'n' : ''}`;
    const isToday = (d === today.getDate() && m === today.getMonth() + 1 && y === today.getFullYear());
    const qClass = `q-${info.quality}`;
    html += `
      <div class="cal-cell ${qClass}${isToday ? ' today' : ''}" onclick="showDayDetail(${d})">
        <div class="cal-solar">${d}</div>
        <div class="cal-lunar">${lunarStr}</div>
        <div class="cal-dot"></div>
      </div>`;
  });

  calBody.innerHTML = html;
  hideDayDetail();
}

function showDayDetail(day) {
  const y = currentCalendarYear;
  const m = currentCalendarMonth;
  const info = getDayInfo(y, m, day, currentEventType);
  selectedDayInfo = info;

  const panel = document.getElementById('day-detail-panel');
  if (!panel) return;

  const gioList = info.gioHoangDao.map(g =>
    `<div class="gio-badge">${g.chi} <span>${g.time}</span></div>`
  ).join('');

  const goodReasons = info.reasons.good.map(r => `<li>✅ ${r}</li>`).join('');
  const badReasons = info.reasons.bad.map(r => `<li>⚠️ ${r}</li>`).join('');
  const leapStr = info.lunar.isLeapMonth ? ' (nhuận)' : '';

  const qualityLabel = { good: '✅ Ngày Tốt', neutral: '🟡 Bình thường', bad: '❌ Ngày Xấu' };
  const qLabel = qualityLabel[info.quality];

  panel.innerHTML = `
    <div class="detail-header">
      <div>
        <h3>Ngày ${day}/${m}/${y}</h3>
        <p>Âm lịch: ${info.lunar.lunarDay} tháng ${info.lunar.lunarMonth}${leapStr}</p>
      </div>
      <div class="detail-quality q-${info.quality}">${qLabel}</div>
    </div>
    <div class="detail-body">
      <div class="detail-col">
        <div class="info-row"><span>Can Chi ngày</span><strong>${info.canChi.day.can} ${info.canChi.day.chi}</strong></div>
        <div class="info-row"><span>Can Chi tháng</span><strong>${info.canChi.month.can} ${info.canChi.month.chi}</strong></div>
        <div class="info-row"><span>Can Chi năm</span><strong>${info.canChi.year.can} ${info.canChi.year.chi}</strong></div>
        <div class="info-row"><span>Trực</span><strong>${info.truc.name}</strong></div>
      </div>
      <div class="detail-col">
        <p class="detail-label">⭐ Giờ Hoàng Đạo</p>
        <div class="gio-badges">${gioList}</div>
      </div>
    </div>
    <div class="detail-reasons">
      <p class="detail-label">📊 Phân tích</p>
      <ul class="reason-list">${goodReasons}${badReasons}</ul>
    </div>
  `;
  panel.classList.add('visible');

  // Highlight selected cell
  document.querySelectorAll('.cal-cell').forEach(c => c.classList.remove('selected'));
  const cells = document.querySelectorAll('.cal-cell:not(.empty)');
  if (cells[day - 1]) cells[day - 1].classList.add('selected');
}

function hideDayDetail() {
  const panel = document.getElementById('day-detail-panel');
  if (panel) { panel.innerHTML = ''; panel.classList.remove('visible'); }
}

function prevMonth() {
  currentCalendarMonth--;
  if (currentCalendarMonth < 1) { currentCalendarMonth = 12; currentCalendarYear--; }
  renderCalendar();
}

function nextMonth() {
  currentCalendarMonth++;
  if (currentCalendarMonth > 12) { currentCalendarMonth = 1; currentCalendarYear++; }
  renderCalendar();
}

function onEventTypeChange(val) {
  currentEventType = val;
  renderCalendar();
}

// =============================================
// PRAYER TEXTS (Văn khấn)
// =============================================
function renderPrayerList() {
  const prayers = getAllPrayers();
  const list = document.getElementById('prayer-list');
  if (!list) return;

  list.innerHTML = prayers.map(p => `
    <div class="prayer-card" onclick="showPrayer('${p.id}')">
      <div class="prayer-icon">${p.icon}</div>
      <div class="prayer-info">
        <h3>${p.title}</h3>
        <p>${p.subtitle}</p>
      </div>
      <div class="prayer-arrow">›</div>
    </div>
  `).join('');
}

function showPrayer(id) {
  const prayer = getPrayerById(id);
  if (!prayer) return;
  currentPrayerId = id;

  document.getElementById('prayer-list-view').classList.add('hidden');
  document.getElementById('prayer-detail-view').classList.remove('hidden');

  const detail = document.getElementById('prayer-detail');
  detail.innerHTML = `
    <div class="prayer-detail-header">
      <button class="btn-back" onclick="closePrayer()">‹ Quay lại</button>
      <span class="prayer-cat-badge">${prayer.icon} ${prayer.category}</span>
    </div>
    <h2 class="prayer-title">${prayer.title}</h2>
    <p class="prayer-subtitle">${prayer.subtitle}</p>

    <div class="instructions-box">
      <h4>📋 Hướng dẫn chuẩn bị</h4>
      <p>${prayer.instructions}</p>
    </div>

    <div class="prayer-text-box">
      <div class="prayer-text-header">
        <h4>📜 Văn Khấn</h4>
        <button class="btn-copy" onclick="copyPrayer()">📋 Sao chép</button>
      </div>
      <pre class="prayer-text" id="prayer-text-content">${escapeHtml(prayer.text)}</pre>
    </div>

    <div class="prayer-note">
      <strong>Lưu ý:</strong> Điền đầy đủ thông tin vào các chỗ có dấu "..........." trước khi đọc.
      Đọc với lòng thành kính, giọng rõ ràng, trang nghiêm.
    </div>
  `;
}

function closePrayer() {
  document.getElementById('prayer-list-view').classList.remove('hidden');
  document.getElementById('prayer-detail-view').classList.add('hidden');
  currentPrayerId = null;
}

function copyPrayer() {
  const textEl = document.getElementById('prayer-text-content');
  if (!textEl) return;
  const text = textEl.textContent;
  navigator.clipboard.writeText(text).then(() => {
    const btn = document.querySelector('.btn-copy');
    if (btn) { btn.textContent = '✅ Đã sao chép!'; setTimeout(() => { btn.textContent = '📋 Sao chép'; }, 2000); }
  }).catch(() => {
    // Fallback for older browsers
    const ta = document.createElement('textarea');
    ta.value = text;
    document.body.appendChild(ta);
    ta.select();
    document.execCommand('copy');
    document.body.removeChild(ta);
    const btn = document.querySelector('.btn-copy');
    if (btn) { btn.textContent = '✅ Đã sao chép!'; setTimeout(() => { btn.textContent = '📋 Sao chép'; }, 2000); }
  });
}

function escapeHtml(str) {
  return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

// =============================================
// MOBILE NAV
// =============================================
function toggleMobileMenu() {
  document.getElementById('nav-menu').classList.toggle('open');
}

// =============================================
// QUICK LOOKUP (from home section)
// =============================================
function quickLookup(eventType) {
  currentEventType = eventType;
  currentCalendarYear = new Date().getFullYear();
  currentCalendarMonth = new Date().getMonth() + 1;
  document.getElementById('event-select').value = eventType;
  renderCalendar();
  showSection('tra-ngay');
}

// =============================================
// INIT
// =============================================
document.addEventListener('DOMContentLoaded', () => {
  // Set default month/year selectors
  const monthSel = document.getElementById('cal-month');
  const yearSel = document.getElementById('cal-year');
  if (monthSel) monthSel.value = currentCalendarMonth;
  if (yearSel) {
    // Populate years
    const curYear = new Date().getFullYear();
    for (let yr = curYear - 2; yr <= curYear + 5; yr++) {
      const opt = document.createElement('option');
      opt.value = yr;
      opt.textContent = `Năm ${yr}`;
      if (yr === curYear) opt.selected = true;
      yearSel.appendChild(opt);
    }
  }

  // Render all sections
  renderTodayAlmanac();
  renderCalendar();
  renderPrayerList();

  // Event listeners
  document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const sec = link.dataset.section;
      if (sec) showSection(sec);
    });
  });

  const eventSel = document.getElementById('event-select');
  if (eventSel) eventSel.addEventListener('change', e => onEventTypeChange(e.target.value));

  if (monthSel) monthSel.addEventListener('change', e => {
    currentCalendarMonth = parseInt(e.target.value);
    renderCalendar();
  });
  if (yearSel) yearSel.addEventListener('change', e => {
    currentCalendarYear = parseInt(e.target.value);
    renderCalendar();
  });

  // Render today detail for dedicated section too
  const fullEl = document.getElementById('today-detail-full');
  if (fullEl) {
    const src = document.getElementById('today-detail');
    if (src) fullEl.innerHTML = src.innerHTML;
  }

  // Show home section by default
  showSection('home');
});
