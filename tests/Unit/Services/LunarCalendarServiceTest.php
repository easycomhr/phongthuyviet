<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\LunarCalendarService;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class LunarCalendarServiceTest extends TestCase
{
    private LunarCalendarService $service;

    protected function setUp(): void
    {
        $this->service = new LunarCalendarService();
    }

    // ── solarToLunar ──────────────────────────────────────────────────────────

    /** @test */
    public function it_converts_tet_2025_to_lunar_new_year(): void
    {
        // January 29, 2025 = Mùng 1 Tháng 1 năm Ất Tỵ
        $date   = CarbonImmutable::create(2025, 1, 29, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $result = $this->service->solarToLunar($date);

        $this->assertSame(1,     $result[0], 'Lunar day');
        $this->assertSame(1,     $result[1], 'Lunar month');
        $this->assertSame(2025,  $result[2], 'Lunar year');
        $this->assertFalse($result[3], 'Not a leap month');
    }

    /** @test */
    public function it_converts_tet_2024_to_lunar_new_year(): void
    {
        // February 10, 2024 = Mùng 1 Tháng 1 năm Giáp Thìn
        $date   = CarbonImmutable::create(2024, 2, 10, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $result = $this->service->solarToLunar($date);

        $this->assertSame(1,    $result[0], 'Lunar day');
        $this->assertSame(1,    $result[1], 'Lunar month');
        $this->assertSame(2024, $result[2], 'Lunar year');
        $this->assertFalse($result[3], 'Not a leap month');
    }

    /** @test */
    public function it_converts_mid_autumn_2025_correctly(): void
    {
        // October 6, 2025 = Rằm tháng 8 (15/8 âm) năm Ất Tỵ
        $date   = CarbonImmutable::create(2025, 10, 6, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $result = $this->service->solarToLunar($date);

        $this->assertSame(15,   $result[0], 'Lunar day = 15');
        $this->assertSame(8,    $result[1], 'Lunar month = 8');
        $this->assertSame(2025, $result[2], 'Lunar year = 2025');
    }

    /** @test */
    public function it_handles_lunar_new_year_eve_2025(): void
    {
        // January 28, 2025 = 29/12 Âm (Giao Thừa eve)
        $date   = CarbonImmutable::create(2025, 1, 28, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $result = $this->service->solarToLunar($date);

        $this->assertSame(12, $result[1], 'Still month 12 of prior lunar year');
        $this->assertSame(2024, $result[2], 'Still lunar year 2024');
    }

    // ── getCanChi ─────────────────────────────────────────────────────────────

    /** @test */
    public function it_returns_year_at_ty_for_2025(): void
    {
        $date   = CarbonImmutable::create(2025, 6, 15, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $result = $this->service->getCanChi($date);

        $this->assertSame('Ất',  $result['yearCan']);
        $this->assertSame('Tỵ',  $result['yearChi']);
    }

    /** @test */
    public function it_returns_giap_thin_for_year_2024(): void
    {
        $date   = CarbonImmutable::create(2024, 6, 15, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $result = $this->service->getCanChi($date);

        $this->assertSame('Giáp', $result['yearCan']);
        $this->assertSame('Thìn', $result['yearChi']);
    }

    /** @test */
    public function it_returns_all_required_keys(): void
    {
        $date   = CarbonImmutable::create(2025, 1, 29, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $result = $this->service->getCanChi($date);

        foreach (['dayCan', 'dayChi', 'monthCan', 'monthChi', 'yearCan', 'yearChi'] as $key) {
            $this->assertArrayHasKey($key, $result);
            $this->assertNotEmpty($result[$key]);
        }
    }

    /** @test */
    public function day_can_chi_uses_valid_values(): void
    {
        $can = ['Giáp', 'Ất', 'Bính', 'Đinh', 'Mậu', 'Kỷ', 'Canh', 'Tân', 'Nhâm', 'Quý'];
        $chi = ['Tý', 'Sửu', 'Dần', 'Mão', 'Thìn', 'Tỵ', 'Ngọ', 'Mùi', 'Thân', 'Dậu', 'Tuất', 'Hợi'];

        $date   = CarbonImmutable::create(2025, 1, 29, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $result = $this->service->getCanChi($date);

        $this->assertContains($result['dayCan'],   $can);
        $this->assertContains($result['dayChi'],   $chi);
        $this->assertContains($result['monthCan'], $can);
        $this->assertContains($result['monthChi'], $chi);
    }

    /** @test */
    public function consecutive_days_have_consecutive_day_can_chi(): void
    {
        $can = ['Giáp', 'Ất', 'Bính', 'Đinh', 'Mậu', 'Kỷ', 'Canh', 'Tân', 'Nhâm', 'Quý'];
        $chi = ['Tý', 'Sửu', 'Dần', 'Mão', 'Thìn', 'Tỵ', 'Ngọ', 'Mùi', 'Thân', 'Dậu', 'Tuất', 'Hợi'];

        $day1 = CarbonImmutable::create(2025, 3, 1, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $day2 = $day1->addDay();

        $r1 = $this->service->getCanChi($day1);
        $r2 = $this->service->getCanChi($day2);

        $canIdx1 = array_search($r1['dayCan'], $can, true);
        $canIdx2 = array_search($r2['dayCan'], $can, true);
        $chiIdx1 = array_search($r1['dayChi'], $chi, true);
        $chiIdx2 = array_search($r2['dayChi'], $chi, true);

        $this->assertSame(($canIdx1 + 1) % 10, $canIdx2, 'Can advances by 1 each day');
        $this->assertSame(($chiIdx1 + 1) % 12, $chiIdx2, 'Chi advances by 1 each day');
    }

    // ── getSolarTerm ──────────────────────────────────────────────────────────

    /** @test */
    public function it_detects_lap_xuan_2025(): void
    {
        // Lập Xuân 2025: sun crosses 315° at 21:10 VN on Feb 3.
        // Algorithm measures at midnight, so the term is first visible on Feb 4.
        $date = CarbonImmutable::create(2025, 2, 4, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $term = $this->service->getSolarTerm($date);

        $this->assertSame('Lập Xuân', $term);
    }

    /** @test */
    public function it_returns_null_for_non_solar_term_day(): void
    {
        // Jan 29 is not a solar term boundary
        $date = CarbonImmutable::create(2025, 1, 29, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $term = $this->service->getSolarTerm($date);

        $this->assertNull($term);
    }

    /** @test */
    public function it_detects_dong_chi_2025(): void
    {
        // Đông Chí 2025 = December 22, 2025 (270°)
        $date = CarbonImmutable::create(2025, 12, 22, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $term = $this->service->getSolarTerm($date);

        $this->assertSame('Đông Chí', $term);
    }

    // ── getTruc ───────────────────────────────────────────────────────────────

    /** @test */
    public function it_returns_truc_thanh_for_tet_2025(): void
    {
        // Jan 29, 2025: JD=2460705, dayChiIdx=(2460705+1)%12=10(Tuất)
        // lunarMonth=1, monthChiIdx=(1+1)%12=2(Dần)
        // trucIdx = (10-2+12)%12 = 8 = Thành
        $date = CarbonImmutable::create(2025, 1, 29, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $this->assertSame('Thành', $this->service->getTruc($date));
    }

    /** @test */
    public function consecutive_days_advance_truc_by_one(): void
    {
        $truc = ['Kiến', 'Trừ', 'Mãn', 'Bình', 'Định', 'Chấp', 'Phá', 'Nguy', 'Thành', 'Thu', 'Khai', 'Bế'];
        $day1 = CarbonImmutable::create(2025, 1, 29, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $day2 = $day1->addDay();

        $t1   = $this->service->getTruc($day1);
        $t2   = $this->service->getTruc($day2);
        $idx1 = array_search($t1, $truc, true);
        $idx2 = array_search($t2, $truc, true);

        $this->assertSame(($idx1 + 1) % 12, $idx2, 'Trực phải tiến thêm 1 mỗi ngày');
    }

    /** @test */
    public function it_returns_valid_truc_name(): void
    {
        $valid = ['Kiến', 'Trừ', 'Mãn', 'Bình', 'Định', 'Chấp', 'Phá', 'Nguy', 'Thành', 'Thu', 'Khai', 'Bế'];
        $date  = CarbonImmutable::create(2025, 3, 15, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $this->assertContains($this->service->getTruc($date), $valid);
    }

    // ── isTamNuong ────────────────────────────────────────────────────────────

    /** @test */
    public function it_returns_false_for_non_tam_nuong_day(): void
    {
        // Jan 29, 2025 = Mùng 1 → NOT Tam Nương
        $date = CarbonImmutable::create(2025, 1, 29, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $this->assertFalse($this->service->isTamNuong($date));
    }

    /** @test */
    public function it_detects_all_six_tam_nuong_days(): void
    {
        // Tam Nương = âm ngày 3, 7, 13, 18, 22, 26
        // Base: Jan 29, 2025 = Mùng 1; add (n-1) days to reach lunar day n
        $base = CarbonImmutable::create(2025, 1, 29, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        foreach ([3, 7, 13, 18, 22, 26] as $lunarDay) {
            $date = $base->addDays($lunarDay - 1);
            $this->assertTrue($this->service->isTamNuong($date), "Âm ngày $lunarDay phải là Tam Nương");
        }
    }

    // ── isNguyetKy ────────────────────────────────────────────────────────────

    /** @test */
    public function it_returns_false_for_non_nguyet_ky_day(): void
    {
        // Jan 29, 2025 = Mùng 1 → NOT Nguyệt Kỵ
        $date = CarbonImmutable::create(2025, 1, 29, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $this->assertFalse($this->service->isNguyetKy($date));
    }

    /** @test */
    public function it_detects_all_three_nguyet_ky_days(): void
    {
        // Nguyệt Kỵ = âm ngày 5, 14, 23
        $base = CarbonImmutable::create(2025, 1, 29, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        foreach ([5, 14, 23] as $lunarDay) {
            $date = $base->addDays($lunarDay - 1);
            $this->assertTrue($this->service->isNguyetKy($date), "Âm ngày $lunarDay phải là Nguyệt Kỵ");
        }
    }

    // ── getXiuSao ─────────────────────────────────────────────────────────────

    /** @test */
    public function it_returns_valid_xiu_sao_structure(): void
    {
        $validTu = [
            'Giốc', 'Cang', 'Đê',   'Phòng', 'Tâm',  'Vĩ',    'Cơ',
            'Đẩu',  'Ngưu', 'Nữ',   'Hư',    'Nguy',  'Thất',  'Bích',
            'Khuê', 'Lâu',  'Vị',   'Mão',   'Tất',   'Chủy',  'Sâm',
            'Tỉnh', 'Quỷ',  'Liễu', 'Tinh',  'Trương','Dực',   'Chẩn',
        ];
        $date   = CarbonImmutable::create(2025, 1, 29, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $result = $this->service->getXiuSao($date);

        $this->assertArrayHasKey('name',    $result);
        $this->assertArrayHasKey('isLucky', $result);
        $this->assertContains($result['name'], $validTu);
        $this->assertIsBool($result['isLucky']);
    }

    /** @test */
    public function consecutive_days_advance_xiu_sao(): void
    {
        $tuNames = [
            'Giốc', 'Cang', 'Đê',   'Phòng', 'Tâm',  'Vĩ',    'Cơ',
            'Đẩu',  'Ngưu', 'Nữ',   'Hư',    'Nguy',  'Thất',  'Bích',
            'Khuê', 'Lâu',  'Vị',   'Mão',   'Tất',   'Chủy',  'Sâm',
            'Tỉnh', 'Quỷ',  'Liễu', 'Tinh',  'Trương','Dực',   'Chẩn',
        ];
        $day1 = CarbonImmutable::create(2025, 1, 29, 0, 0, 0, 'Asia/Ho_Chi_Minh');
        $day2 = $day1->addDay();
        $r1   = $this->service->getXiuSao($day1);
        $r2   = $this->service->getXiuSao($day2);
        $idx1 = array_search($r1['name'], $tuNames, true);
        $idx2 = array_search($r2['name'], $tuNames, true);

        $this->assertSame(($idx1 + 1) % 28, $idx2, 'Tú Sao phải tiến thêm 1 mỗi ngày');
    }

    // ── jdFromDate / dateFromJd (round-trip) ─────────────────────────────────

    /** @test */
    public function jd_round_trip_is_consistent(): void
    {
        $cases = [
            [2025, 1, 29],
            [2025, 2,  3],
            [2024, 2, 10],
            [2025, 12, 31],
        ];

        foreach ($cases as [$y, $m, $d]) {
            $jd            = $this->service->jdFromDate($d, $m, $y);
            [$rd, $rm, $ry] = $this->service->dateFromJd($jd);
            $this->assertSame($d, $rd, "Day round-trip for $y-$m-$d");
            $this->assertSame($m, $rm, "Month round-trip for $y-$m-$d");
            $this->assertSame($y, $ry, "Year round-trip for $y-$m-$d");
        }
    }
}
