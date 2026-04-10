<?php

declare(strict_types=1);

namespace App\Services;

use Carbon\CarbonImmutable;

/**
 * Analyze relationship compatibility for 2 people using 3 core factors:
 * 1) Mệnh (Ngũ hành)
 * 2) Thiên Can - Địa Chi
 * 3) Cung Phi Bát Trạch
 */
class CompatibilityService
{
    private const ELEMENT_ORDER = ['Mộc', 'Hỏa', 'Thổ', 'Kim', 'Thủy'];

    private const STEM_ELEMENT = [
        'Giáp' => 'Mộc', 'Ất' => 'Mộc',
        'Bính' => 'Hỏa', 'Đinh' => 'Hỏa',
        'Mậu' => 'Thổ', 'Kỷ' => 'Thổ',
        'Canh' => 'Kim', 'Tân' => 'Kim',
        'Nhâm' => 'Thủy', 'Quý' => 'Thủy',
    ];

    private const LUC_HOP_CHI = [
        'Tý-Sửu', 'Dần-Hợi', 'Mão-Tuất', 'Thìn-Dậu', 'Tỵ-Thân', 'Ngọ-Mùi',
    ];

    private const LUC_HAI_CHI = [
        'Tý-Mùi', 'Sửu-Ngọ', 'Dần-Tỵ', 'Mão-Thìn', 'Thân-Hợi', 'Dậu-Tuất',
    ];

    private const TU_HANH_XUNG = [
        ['Tý', 'Ngọ', 'Mão', 'Dậu'],
        ['Dần', 'Thân', 'Tỵ', 'Hợi'],
        ['Thìn', 'Tuất', 'Sửu', 'Mùi'],
    ];

    private const TAM_HOP = [
        ['Thân', 'Tý', 'Thìn'],
        ['Dần', 'Ngọ', 'Tuất'],
        ['Hợi', 'Mão', 'Mùi'],
        ['Tỵ', 'Dậu', 'Sửu'],
    ];

    private const THIEN_CAN_HOP = [
        'Giáp-Kỷ', 'Ất-Canh', 'Bính-Tân', 'Đinh-Nhâm', 'Mậu-Quý',
    ];

    private const KUA_TO_CUNG = [
        1 => 'Khảm',
        2 => 'Khôn',
        3 => 'Chấn',
        4 => 'Tốn',
        6 => 'Càn',
        7 => 'Đoài',
        8 => 'Cấn',
        9 => 'Ly',
    ];

    private const BAT_TRACH_SCORE = [
        'Sinh Khí' => 3.5,
        'Thiên Y' => 3.2,
        'Diên Niên' => 3.0,
        'Phục Vị' => 2.6,
        'Họa Hại' => 1.6,
        'Lục Sát' => 1.1,
        'Ngũ Quỷ' => 0.8,
        'Tuyệt Mệnh' => 0.2,
    ];

    private const BAT_TRACH_RELATIONS = [
        'Khảm-Khảm' => 'Phục Vị',
        'Khảm-Ly' => 'Diên Niên',
        'Khảm-Chấn' => 'Thiên Y',
        'Khảm-Tốn' => 'Sinh Khí',
        'Khảm-Càn' => 'Lục Sát',
        'Khảm-Khôn' => 'Tuyệt Mệnh',
        'Khảm-Cấn' => 'Ngũ Quỷ',
        'Khảm-Đoài' => 'Họa Hại',

        'Ly-Ly' => 'Phục Vị',
        'Ly-Chấn' => 'Sinh Khí',
        'Ly-Tốn' => 'Thiên Y',
        'Ly-Càn' => 'Ngũ Quỷ',
        'Ly-Khôn' => 'Lục Sát',
        'Ly-Cấn' => 'Họa Hại',
        'Ly-Đoài' => 'Tuyệt Mệnh',

        'Chấn-Chấn' => 'Phục Vị',
        'Chấn-Tốn' => 'Diên Niên',
        'Chấn-Càn' => 'Tuyệt Mệnh',
        'Chấn-Khôn' => 'Họa Hại',
        'Chấn-Cấn' => 'Lục Sát',
        'Chấn-Đoài' => 'Ngũ Quỷ',

        'Tốn-Tốn' => 'Phục Vị',
        'Tốn-Càn' => 'Họa Hại',
        'Tốn-Khôn' => 'Ngũ Quỷ',
        'Tốn-Cấn' => 'Tuyệt Mệnh',
        'Tốn-Đoài' => 'Lục Sát',

        'Càn-Càn' => 'Phục Vị',
        'Càn-Khôn' => 'Diên Niên',
        'Càn-Cấn' => 'Thiên Y',
        'Càn-Đoài' => 'Sinh Khí',

        'Khôn-Khôn' => 'Phục Vị',
        'Khôn-Cấn' => 'Sinh Khí',
        'Khôn-Đoài' => 'Thiên Y',

        'Cấn-Cấn' => 'Phục Vị',
        'Cấn-Đoài' => 'Diên Niên',

        'Đoài-Đoài' => 'Phục Vị',
    ];

    private const NAP_AM_ELEMENT = [
        'Giáp Tý' => 'Kim', 'Ất Sửu' => 'Kim', 'Bính Dần' => 'Hỏa', 'Đinh Mão' => 'Hỏa',
        'Mậu Thìn' => 'Mộc', 'Kỷ Tỵ' => 'Mộc', 'Canh Ngọ' => 'Thổ', 'Tân Mùi' => 'Thổ',
        'Nhâm Thân' => 'Kim', 'Quý Dậu' => 'Kim', 'Giáp Tuất' => 'Hỏa', 'Ất Hợi' => 'Hỏa',
        'Bính Tý' => 'Thủy', 'Đinh Sửu' => 'Thủy', 'Mậu Dần' => 'Thổ', 'Kỷ Mão' => 'Thổ',
        'Canh Thìn' => 'Kim', 'Tân Tỵ' => 'Kim', 'Nhâm Ngọ' => 'Mộc', 'Quý Mùi' => 'Mộc',
        'Giáp Thân' => 'Thủy', 'Ất Dậu' => 'Thủy', 'Bính Tuất' => 'Thổ', 'Đinh Hợi' => 'Thổ',
        'Mậu Tý' => 'Hỏa', 'Kỷ Sửu' => 'Hỏa', 'Canh Dần' => 'Mộc', 'Tân Mão' => 'Mộc',
        'Nhâm Thìn' => 'Thủy', 'Quý Tỵ' => 'Thủy', 'Giáp Ngọ' => 'Kim', 'Ất Mùi' => 'Kim',
        'Bính Thân' => 'Hỏa', 'Đinh Dậu' => 'Hỏa', 'Mậu Tuất' => 'Mộc', 'Kỷ Hợi' => 'Mộc',
        'Canh Tý' => 'Thổ', 'Tân Sửu' => 'Thổ', 'Nhâm Dần' => 'Kim', 'Quý Mão' => 'Kim',
        'Giáp Thìn' => 'Hỏa', 'Ất Tỵ' => 'Hỏa', 'Bính Ngọ' => 'Thủy', 'Đinh Mùi' => 'Thủy',
        'Mậu Thân' => 'Thổ', 'Kỷ Dậu' => 'Thổ', 'Canh Tuất' => 'Kim', 'Tân Hợi' => 'Kim',
        'Nhâm Tý' => 'Mộc', 'Quý Sửu' => 'Mộc', 'Giáp Dần' => 'Thủy', 'Ất Mão' => 'Thủy',
        'Bính Thìn' => 'Thổ', 'Đinh Tỵ' => 'Thổ', 'Mậu Ngọ' => 'Hỏa', 'Kỷ Mùi' => 'Hỏa',
        'Canh Thân' => 'Mộc', 'Tân Dậu' => 'Mộc', 'Nhâm Tuất' => 'Thủy', 'Quý Hợi' => 'Thủy',
    ];

    public function __construct(
        private readonly LunarCalendarService $lunar,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function analyze(
        string $personAGender,
        string $personABirthDate,
        string $personBGender,
        string $personBBirthDate,
    ): array {
        $personA = $this->buildPerson($personAGender, $personABirthDate);
        $personB = $this->buildPerson($personBGender, $personBBirthDate);

        $menh = $this->scoreMenh($personA, $personB);
        $canChi = $this->scoreCanChi($personA, $personB);
        $cungPhi = $this->scoreCungPhi($personA, $personB);

        $total = round(min(10, $menh['score'] + $canChi['score'] + $cungPhi['score']), 1);

        return [
            'summary' => [
                'totalScore' => $total,
                'maxScore' => 10,
                'rating' => $this->ratingLabel($total),
                'shortAdvice' => $this->shortAdvice($total),
            ],
            'people' => [$personA, $personB],
            'factors' => [
                'menh' => $menh,
                'canChi' => $canChi,
                'cungPhi' => $cungPhi,
            ],
            'note' => 'Kết quả mang tính tham khảo phong thủy truyền thống, không thay thế quyết định cá nhân hoặc chuyên gia tư vấn.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildPerson(string $gender, string $birthDate): array
    {
        $date = CarbonImmutable::createFromFormat('Y-m-d', $birthDate, 'Asia/Ho_Chi_Minh');
        $canChi = $this->lunar->getCanChi($date);
        [$lunarDay, $lunarMonth, $lunarYear] = $this->lunar->solarToLunar($date);

        $yearPair = $canChi['yearCan'].' '.$canChi['yearChi'];
        $menh = self::NAP_AM_ELEMENT[$yearPair] ?? self::STEM_ELEMENT[$canChi['yearCan']];
        $cung = $this->cungPhi($gender, $lunarYear);

        return [
            'gender' => $gender,
            'genderLabel' => $gender === 'male' ? 'Nam' : 'Nữ',
            'birthDate' => $birthDate,
            'solarYear' => $date->year,
            'lunarYear' => $lunarYear,
            'lunarDateLabel' => sprintf('%d/%d/%d', $lunarDay, $lunarMonth, $lunarYear),
            'yearCan' => $canChi['yearCan'],
            'yearChi' => $canChi['yearChi'],
            'yearCanChiLabel' => $yearPair,
            'menh' => $menh,
            'cungPhi' => $cung,
        ];
    }

    /**
     * @param array<string, mixed> $a
     * @param array<string, mixed> $b
     * @return array<string, mixed>
     */
    private function scoreMenh(array $a, array $b): array
    {
        $relation = $this->elementRelation((string) $a['menh'], (string) $b['menh']);

        return [
            'title' => 'Mệnh Ngũ Hành',
            'score' => $relation['score'],
            'maxScore' => 3.5,
            'result' => $relation['result'],
            'detail' => sprintf(
                'Người A thuộc mệnh %s, người B thuộc mệnh %s. %s',
                $a['menh'],
                $b['menh'],
                $relation['detail'],
            ),
        ];
    }

    /**
     * @param array<string, mixed> $a
     * @param array<string, mixed> $b
     * @return array<string, mixed>
     */
    private function scoreCanChi(array $a, array $b): array
    {
        $chiA = (string) $a['yearChi'];
        $chiB = (string) $b['yearChi'];
        $canA = (string) $a['yearCan'];
        $canB = (string) $b['yearCan'];

        $score = 1.2;
        $chiDetail = 'Địa Chi ở mức bình hòa.';

        if ($this->isInGroup($chiA, $chiB, self::TAM_HOP)) {
            $score += 1.2;
            $chiDetail = 'Địa Chi thuộc nhóm Tam Hợp, dễ đồng hành lâu dài.';
        } elseif ($this->isPair($chiA, $chiB, self::LUC_HOP_CHI)) {
            $score += 0.9;
            $chiDetail = 'Địa Chi thuộc cặp Lục Hợp, có lực hỗ trợ tự nhiên.';
        } elseif ($this->isInGroup($chiA, $chiB, self::TU_HANH_XUNG)) {
            $score -= 0.9;
            $chiDetail = 'Địa Chi rơi vào nhóm Tứ Hành Xung, cần nhiều nhường nhịn để hòa hợp.';
        } elseif ($this->isPair($chiA, $chiB, self::LUC_HAI_CHI)) {
            $score -= 0.6;
            $chiDetail = 'Địa Chi thuộc cặp Lục Hại, dễ phát sinh hiểu lầm trong phối hợp.';
        } elseif ($chiA === $chiB) {
            $score += 0.4;
            $chiDetail = 'Địa Chi trùng nhau, ổn định nhưng có thể thiếu bổ trợ lẫn nhau.';
        } else {
            $score += 0.2;
        }

        $canDetail = 'Thiên Can ở mức trung tính.';
        if ($this->isPair($canA, $canB, self::THIEN_CAN_HOP)) {
            $score += 0.8;
            $canDetail = 'Thiên Can tương hợp, thuận lợi khi cùng lập kế hoạch dài hạn.';
        } else {
            $canRelation = $this->elementRelation(
                self::STEM_ELEMENT[$canA] ?? 'Thổ',
                self::STEM_ELEMENT[$canB] ?? 'Thổ',
            );

            if ($canRelation['result'] === 'Tương sinh') {
                $score += 0.4;
                $canDetail = 'Thiên Can xét theo ngũ hành thiên can có tính tương sinh.';
            } elseif ($canRelation['result'] === 'Tương khắc') {
                $score -= 0.4;
                $canDetail = 'Thiên Can xét theo ngũ hành thiên can có xu hướng tương khắc.';
            } else {
                $score += 0.1;
            }
        }

        $score = round(max(0, min(3.0, $score)), 1);

        return [
            'title' => 'Thiên Can - Địa Chi',
            'score' => $score,
            'maxScore' => 3.0,
            'result' => $score >= 2.2 ? 'Thuận' : ($score >= 1.3 ? 'Trung bình' : 'Cần lưu ý'),
            'detail' => sprintf(
                'Năm sinh người A: %s, người B: %s. %s %s',
                $a['yearCanChiLabel'],
                $b['yearCanChiLabel'],
                $chiDetail,
                $canDetail,
            ),
        ];
    }

    /**
     * @param array<string, mixed> $a
     * @param array<string, mixed> $b
     * @return array<string, mixed>
     */
    private function scoreCungPhi(array $a, array $b): array
    {
        $cungA = (string) $a['cungPhi'];
        $cungB = (string) $b['cungPhi'];
        $relation = $this->batTrachType($cungA, $cungB);

        $score = self::BAT_TRACH_SCORE[$relation] ?? 1.6;

        return [
            'title' => 'Cung Phi Bát Trạch',
            'score' => $score,
            'maxScore' => 3.5,
            'result' => $relation,
            'detail' => sprintf(
                'Cung phi người A là %s, người B là %s. Hai cung tạo thế %s theo Bát Trạch.',
                $cungA,
                $cungB,
                $relation,
            ),
        ];
    }

    private function elementRelation(string $a, string $b): array
    {
        if ($a === $b) {
            return [
                'score' => 3.5,
                'result' => 'Bình hòa',
                'detail' => 'Hai mệnh đồng hành, dễ đồng điệu về nhịp sống và cách ra quyết định.',
            ];
        }

        $aToB = $this->isGenerating($a, $b);
        $bToA = $this->isGenerating($b, $a);
        $aKhacB = $this->isControlling($a, $b);
        $bKhacA = $this->isControlling($b, $a);

        if ($aToB || $bToA) {
            return [
                'score' => 3.0,
                'result' => 'Tương sinh',
                'detail' => $aToB
                    ? sprintf('Mệnh %s của người A tương sinh cho mệnh %s của người B.', $a, $b)
                    : sprintf('Mệnh %s của người B tương sinh cho mệnh %s của người A.', $b, $a),
            ];
        }

        if ($aKhacB || $bKhacA) {
            return [
                'score' => 1.2,
                'result' => 'Tương khắc',
                'detail' => $aKhacB
                    ? sprintf('Mệnh %s của người A khắc mệnh %s của người B, cần tăng đối thoại và tôn trọng khác biệt.', $a, $b)
                    : sprintf('Mệnh %s của người B khắc mệnh %s của người A, cần thống nhất nguyên tắc khi làm việc chung.', $b, $a),
            ];
        }

        return [
            'score' => 2.0,
            'result' => 'Trung tính',
            'detail' => 'Hai mệnh không sinh khắc trực diện, mức hòa hợp phụ thuộc nhiều vào cách ứng xử thực tế.',
        ];
    }

    private function isGenerating(string $from, string $to): bool
    {
        $i = array_search($from, self::ELEMENT_ORDER, true);
        $j = array_search($to, self::ELEMENT_ORDER, true);

        if ($i === false || $j === false) {
            return false;
        }

        return (($i + 1) % count(self::ELEMENT_ORDER)) === $j;
    }

    private function isControlling(string $from, string $to): bool
    {
        $control = [
            'Mộc' => 'Thổ',
            'Thổ' => 'Thủy',
            'Thủy' => 'Hỏa',
            'Hỏa' => 'Kim',
            'Kim' => 'Mộc',
        ];

        return ($control[$from] ?? '') === $to;
    }

    /**
     * @param list<array<int, string>> $groups
     */
    private function isInGroup(string $a, string $b, array $groups): bool
    {
        foreach ($groups as $group) {
            if (in_array($a, $group, true) && in_array($b, $group, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param list<string> $pairs
     */
    private function isPair(string $a, string $b, array $pairs): bool
    {
        $key = $this->pairKey($a, $b);

        foreach ($pairs as $pair) {
            [$x, $y] = explode('-', $pair);
            if ($this->pairKey($x, $y) === $key) {
                return true;
            }
        }

        return false;
    }

    private function pairKey(string $a, string $b): string
    {
        return strcmp($a, $b) <= 0 ? "{$a}-{$b}" : "{$b}-{$a}";
    }

    private function batTrachType(string $cungA, string $cungB): string
    {
        $key = $this->pairKey($cungA, $cungB);

        return self::BAT_TRACH_RELATIONS[$key] ?? 'Họa Hại';
    }

    private function cungPhi(string $gender, int $lunarYear): string
    {
        $digitSum = $this->reduceToDigit($lunarYear);

        if ($lunarYear >= 2000) {
            $kua = $gender === 'male' ? (9 - $digitSum) : ($digitSum + 6);
        } else {
            $kua = $gender === 'male' ? (10 - $digitSum) : ($digitSum + 5);
        }

        while ($kua > 9) {
            $kua -= 9;
        }

        if ($kua <= 0) {
            $kua += 9;
        }

        if ($kua === 5) {
            $kua = $gender === 'male' ? 2 : 8;
        }

        return self::KUA_TO_CUNG[$kua] ?? 'Khảm';
    }

    private function reduceToDigit(int $year): int
    {
        $sum = array_sum(array_map('intval', str_split((string) $year)));

        while ($sum > 9) {
            $sum = array_sum(array_map('intval', str_split((string) $sum)));
        }

        return $sum;
    }

    private function ratingLabel(float $score): string
    {
        if ($score >= 8.5) {
            return 'Rất hợp';
        }

        if ($score >= 7.0) {
            return 'Hợp';
        }

        if ($score >= 5.5) {
            return 'Trung bình';
        }

        return 'Cần cân nhắc';
    }

    private function shortAdvice(float $score): string
    {
        if ($score >= 8.5) {
            return 'Nền tảng hòa hợp tốt, phù hợp để đồng hành trong hôn nhân hoặc hợp tác dài hạn.';
        }

        if ($score >= 7.0) {
            return 'Mức độ hòa hợp khá tích cực, nên thống nhất mục tiêu và nguyên tắc tài chính ngay từ đầu.';
        }

        if ($score >= 5.5) {
            return 'Có điểm thuận và điểm nghịch, cần tăng giao tiếp và phân vai rõ ràng để giảm xung đột.';
        }

        return 'Nên cân nhắc kỹ, đặc biệt ở cách xử lý mâu thuẫn và định hướng dài hạn trước quyết định lớn.';
    }
}
