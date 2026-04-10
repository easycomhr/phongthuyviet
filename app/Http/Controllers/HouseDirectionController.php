<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\HouseDirectionRequest;
use App\Services\CompassService;
use App\Services\KuaNumberService;
use Carbon\CarbonImmutable;
use Inertia\Inertia;
use Inertia\Response;

class HouseDirectionController extends Controller
{
    public function __construct(
        private readonly KuaNumberService $kua,
        private readonly CompassService $compass,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Direction/Index', [
            'defaults' => [
                'gender' => 'male',
                'birth_date' => '1990-01-01',
            ],
            'result' => null,
        ]);
    }

    public function result(HouseDirectionRequest $request): Response
    {
        $validated = $request->validated();
        $birthDate = CarbonImmutable::createFromFormat('Y-m-d', (string) $validated['birth_date'], 'Asia/Ho_Chi_Minh');
        $kua = $this->kua->analyze((string) $validated['gender'], $birthDate);
        $directions = $this->compass->directionsForCung($kua['cungPhi']);

        return Inertia::render('Direction/Index', [
            'defaults' => [
                'gender' => $validated['gender'],
                'birth_date' => $validated['birth_date'],
            ],
            'result' => [
                'profile' => [
                    ...$kua,
                    'genderLabel' => $validated['gender'] === 'male' ? 'Nam' : 'Nữ',
                ],
                'directions' => $directions,
                'note' => 'Kết quả mang tính tham khảo phong thủy Bát Trạch truyền thống, nên kết hợp điều kiện thực tế khi thiết kế nhà.',
            ],
        ]);
    }
}

