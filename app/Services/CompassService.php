<?php

declare(strict_types=1);

namespace App\Services;

class CompassService
{
    /**
     * @var array<string, array<string, string>>
     */
    private const CUNG_TO_DIRECTIONS = [
        'Khảm' => [
            'Đông Nam' => 'Sinh Khí',
            'Đông' => 'Thiên Y',
            'Nam' => 'Diên Niên',
            'Bắc' => 'Phục Vị',
            'Tây Nam' => 'Tuyệt Mệnh',
            'Đông Bắc' => 'Ngũ Quỷ',
            'Tây Bắc' => 'Lục Sát',
            'Tây' => 'Họa Hại',
        ],
        'Ly' => [
            'Đông' => 'Sinh Khí',
            'Đông Nam' => 'Thiên Y',
            'Bắc' => 'Diên Niên',
            'Nam' => 'Phục Vị',
            'Tây' => 'Tuyệt Mệnh',
            'Tây Bắc' => 'Ngũ Quỷ',
            'Đông Bắc' => 'Lục Sát',
            'Tây Nam' => 'Họa Hại',
        ],
        'Chấn' => [
            'Nam' => 'Sinh Khí',
            'Bắc' => 'Thiên Y',
            'Đông Nam' => 'Diên Niên',
            'Đông' => 'Phục Vị',
            'Tây Bắc' => 'Tuyệt Mệnh',
            'Tây' => 'Ngũ Quỷ',
            'Tây Nam' => 'Lục Sát',
            'Đông Bắc' => 'Họa Hại',
        ],
        'Tốn' => [
            'Bắc' => 'Sinh Khí',
            'Nam' => 'Thiên Y',
            'Đông' => 'Diên Niên',
            'Đông Nam' => 'Phục Vị',
            'Đông Bắc' => 'Tuyệt Mệnh',
            'Tây Nam' => 'Ngũ Quỷ',
            'Tây' => 'Lục Sát',
            'Tây Bắc' => 'Họa Hại',
        ],
        'Càn' => [
            'Tây' => 'Sinh Khí',
            'Đông Bắc' => 'Thiên Y',
            'Tây Nam' => 'Diên Niên',
            'Tây Bắc' => 'Phục Vị',
            'Đông' => 'Tuyệt Mệnh',
            'Nam' => 'Ngũ Quỷ',
            'Bắc' => 'Lục Sát',
            'Đông Nam' => 'Họa Hại',
        ],
        'Khôn' => [
            'Đông Bắc' => 'Sinh Khí',
            'Tây' => 'Thiên Y',
            'Tây Bắc' => 'Diên Niên',
            'Tây Nam' => 'Phục Vị',
            'Đông Nam' => 'Tuyệt Mệnh',
            'Bắc' => 'Ngũ Quỷ',
            'Đông' => 'Lục Sát',
            'Nam' => 'Họa Hại',
        ],
        'Cấn' => [
            'Tây Nam' => 'Sinh Khí',
            'Tây Bắc' => 'Thiên Y',
            'Tây' => 'Diên Niên',
            'Đông Bắc' => 'Phục Vị',
            'Bắc' => 'Tuyệt Mệnh',
            'Đông Nam' => 'Ngũ Quỷ',
            'Nam' => 'Lục Sát',
            'Đông' => 'Họa Hại',
        ],
        'Đoài' => [
            'Tây Bắc' => 'Sinh Khí',
            'Tây Nam' => 'Thiên Y',
            'Đông Bắc' => 'Diên Niên',
            'Tây' => 'Phục Vị',
            'Nam' => 'Tuyệt Mệnh',
            'Đông' => 'Ngũ Quỷ',
            'Đông Nam' => 'Lục Sát',
            'Bắc' => 'Họa Hại',
        ],
    ];

    /**
     * @var array<string, array{isGood: bool, score: int, description: string}>
     */
    private const STAR_META = [
        'Sinh Khí' => ['isGood' => true, 'score' => 95, 'description' => 'Vượng tài lộc, phù hợp đặt cửa chính và phòng làm việc.'],
        'Thiên Y' => ['isGood' => true, 'score' => 90, 'description' => 'Tốt cho sức khỏe, yên ổn gia đạo, hợp phòng ngủ.'],
        'Diên Niên' => ['isGood' => true, 'score' => 85, 'description' => 'Củng cố quan hệ gia đình, thuận hòa lâu dài.'],
        'Phục Vị' => ['isGood' => true, 'score' => 78, 'description' => 'Mang lại bình an, trấn tĩnh, hợp nơi thờ cúng.'],
        'Tuyệt Mệnh' => ['isGood' => false, 'score' => 18, 'description' => 'Hung khí mạnh, nên tránh đặt cửa chính hoặc phòng ngủ.'],
        'Ngũ Quỷ' => ['isGood' => false, 'score' => 28, 'description' => 'Dễ phát sinh tranh chấp, hao tổn, cần tránh hướng chính.'],
        'Lục Sát' => ['isGood' => false, 'score' => 35, 'description' => 'Bất ổn quan hệ và tâm lý, không nên dùng cho không gian chính.'],
        'Họa Hại' => ['isGood' => false, 'score' => 42, 'description' => 'Hay gặp trở ngại nhỏ, nên ưu tiên các hướng cát hơn.'],
    ];

    private const DIRECTION_ANGLE = [
        'Bắc' => 0,
        'Đông Bắc' => 45,
        'Đông' => 90,
        'Đông Nam' => 135,
        'Nam' => 180,
        'Tây Nam' => 225,
        'Tây' => 270,
        'Tây Bắc' => 315,
    ];

    /**
     * @return array{
     *   all: list<array{direction: string, duTinh: string, score: int, isGood: bool, description: string, angle: int}>,
     *   good: list<array{direction: string, duTinh: string, score: int, isGood: bool, description: string, angle: int}>,
     *   bad: list<array{direction: string, duTinh: string, score: int, isGood: bool, description: string, angle: int}>
     * }
     */
    public function directionsForCung(string $cungPhi): array
    {
        $map = self::CUNG_TO_DIRECTIONS[$cungPhi] ?? self::CUNG_TO_DIRECTIONS['Khảm'];
        $cards = [];

        foreach ($map as $direction => $duTinh) {
            $meta = self::STAR_META[$duTinh];
            $cards[] = [
                'direction' => $direction,
                'duTinh' => $duTinh,
                'score' => $meta['score'],
                'isGood' => $meta['isGood'],
                'description' => $meta['description'],
                'angle' => self::DIRECTION_ANGLE[$direction] ?? 0,
            ];
        }

        $good = array_values(array_filter($cards, fn (array $card): bool => $card['isGood']));
        $bad = array_values(array_filter($cards, fn (array $card): bool => !$card['isGood']));

        usort($good, fn (array $a, array $b): int => $b['score'] <=> $a['score']);
        usort($bad, fn (array $a, array $b): int => $a['score'] <=> $b['score']);

        return [
            'all' => $cards,
            'good' => $good,
            'bad' => $bad,
        ];
    }
}

