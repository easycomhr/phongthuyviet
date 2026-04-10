<?php

declare(strict_types=1);

namespace App\Services\Wedding\Filters;

use App\Contracts\AstrologyEngineInterface;
use Carbon\Carbon;

class KimLauFilter implements WeddingFilterInterface
{
    private const REMAINDER_LABEL = [
        1 => 'Kim Lâu Thân',
        3 => 'Kim Lâu Thê',
        6 => 'Kim Lâu Tử',
        8 => 'Kim Lâu Súc',
    ];

    public function __construct(
        private readonly AstrologyEngineInterface $astrology,
    ) {}

    public function apply(array $state): array
    {
        $bride = $state['bride'];
        $target = $state['target'];
        $birthLunar = $this->astrology->getLunarDate(Carbon::create(
            $bride->birthDate->year,
            $bride->birthDate->month,
            $bride->birthDate->day,
            0,
            0,
            0,
            'Asia/Ho_Chi_Minh',
        ));
        $lunarAge = $target->year - (int) $birthLunar['lunar_year'] + 1;
        $remainder = $lunarAge % 9;

        $state['kimLau'] = [
            'lunarAge' => $lunarAge,
            'isViolated' => isset(self::REMAINDER_LABEL[$remainder]),
            'type' => self::REMAINDER_LABEL[$remainder] ?? null,
            'message' => isset(self::REMAINDER_LABEL[$remainder])
                ? sprintf(
                    'Năm %d nữ mạng %d tuổi mụ, phạm %s. Nếu vẫn tổ chức, nên chọn ngày rất cát và ưu tiên tháng Đại Lợi/Tiểu Lợi.',
                    $target->year,
                    $lunarAge,
                    self::REMAINDER_LABEL[$remainder],
                )
                : sprintf('Năm %d nữ mạng %d tuổi mụ, không phạm Kim Lâu.', $target->year, $lunarAge),
        ];

        return $state;
    }
}
