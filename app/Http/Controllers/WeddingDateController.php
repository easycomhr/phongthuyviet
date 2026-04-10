<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\Wedding\PartnerDTO;
use App\DTO\Wedding\TargetYearMonthDTO;
use App\Http\Requests\WeddingDateRequest;
use App\Services\Wedding\WeddingDatePipeline;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class WeddingDateController extends Controller
{
    public function __construct(
        private readonly WeddingDatePipeline $pipeline,
    ) {}

    public function index(): Response
    {
        $nextYear = CarbonImmutable::now('Asia/Ho_Chi_Minh')->addYear()->year;

        return Inertia::render('Wedding/Index', [
            'defaults' => [
                'bride_name' => 'Cô dâu',
                'bride_birth_date' => '1998-01-01',
                'groom_name' => 'Chú rể',
                'groom_birth_date' => '1996-01-01',
                'event_type' => 'wedding',
                'target_year' => $nextYear,
                'target_month' => 10,
            ],
            'result' => null,
        ]);
    }

    public function result(WeddingDateRequest $request): Response
    {
        $validated = $request->validated();
        $bride = PartnerDTO::fromArray([
            'name' => (string) $validated['bride_name'],
            'gender' => 'female',
            'birth_date' => (string) $validated['bride_birth_date'],
        ]);
        $groom = PartnerDTO::fromArray([
            'name' => (string) $validated['groom_name'],
            'gender' => 'male',
            'birth_date' => (string) $validated['groom_birth_date'],
        ]);
        $target = new TargetYearMonthDTO((int) $validated['target_year'], (int) $validated['target_month']);
        $result = $this->pipeline->run($bride, $groom, $target, (string) $validated['event_type']);

        return Inertia::render('Wedding/Index', [
            'defaults' => [
                'bride_name' => $validated['bride_name'],
                'bride_birth_date' => $validated['bride_birth_date'],
                'groom_name' => $validated['groom_name'],
                'groom_birth_date' => $validated['groom_birth_date'],
                'event_type' => $validated['event_type'],
                'target_year' => $validated['target_year'],
                'target_month' => $validated['target_month'],
            ],
            'result' => [
                ...$result,
                'targetLabel' => $target->label(),
                'eventType' => $validated['event_type'],
            ],
        ]);
    }

    public function exportPdf(Request $request): HttpResponse
    {
        $validated = $request->validate([
            'bride_name' => ['required', 'string', 'max:80'],
            'bride_birth_date' => ['required', 'date_format:Y-m-d'],
            'groom_name' => ['required', 'string', 'max:80'],
            'groom_birth_date' => ['required', 'date_format:Y-m-d'],
            'event_type' => ['required', 'in:engagement,wedding'],
            'target_year' => ['required', 'integer', 'between:1900,2100'],
            'target_month' => ['required', 'integer', 'between:1,12'],
            'date' => ['required', 'date_format:Y-m-d'],
        ]);

        $bride = PartnerDTO::fromArray([
            'name' => (string) $validated['bride_name'],
            'gender' => 'female',
            'birth_date' => (string) $validated['bride_birth_date'],
        ]);
        $groom = PartnerDTO::fromArray([
            'name' => (string) $validated['groom_name'],
            'gender' => 'male',
            'birth_date' => (string) $validated['groom_birth_date'],
        ]);
        $target = new TargetYearMonthDTO((int) $validated['target_year'], (int) $validated['target_month']);
        $result = $this->pipeline->run($bride, $groom, $target, (string) $validated['event_type']);
        $selected = collect($result['dates'])->firstWhere('date', $validated['date']);

        abort_if(!$selected, 404, 'Không tìm thấy ngày phù hợp để in.');

        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('Phong Thủy Việt');
        $pdf->SetAuthor('Phong Thủy Việt');
        $pdf->SetTitle('Giấy xem ngày cưới hỏi');
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->SetMargins(18, 20, 18);
        $pdf->SetAutoPageBreak(true, 18);
        $pdf->AddPage();

        $logoPath = public_path('favicon.svg');
        if (is_file($logoPath)) {
            $pdf->ImageSVG($logoPath, 18, 14, 7, 7, '', '', '', 0, false);
        }

        $pdf->SetFont('dejavusans', 'B', 12);
        $pdf->SetTextColor(108, 21, 0);
        $pdf->SetXY(27, 15);
        $pdf->Cell(0, 6, 'Phong Thủy Việt', 0, 1, 'L', false, '', 0, false, 'T', 'M');

        $pdf->Ln(7);
        $pdf->SetFont('dejavusans', 'B', 16);
        $pdf->SetTextColor(45, 18, 9);
        $pdf->MultiCell(0, 9, 'Phiếu Gợi Ý Ngày Cưới Hỏi', 0, 'L', false, 1);

        $pdf->SetFont('dejavusans', '', 11);
        $pdf->SetTextColor(30, 30, 30);
        $pdf->MultiCell(0, 7, sprintf('Nghi lễ: %s', $selected['eventLabel']), 0, 'L', false, 1);
        $pdf->MultiCell(0, 7, sprintf('Ngày dương: %s (%s)', $selected['solarLabel'], $selected['weekday']), 0, 'L', false, 1);
        $pdf->MultiCell(0, 7, sprintf('Ngày âm: %s | Can Chi: %s', $selected['lunarLabel'], $selected['canChi']), 0, 'L', false, 1);
        $pdf->MultiCell(0, 7, sprintf('Giờ tiến hành gợi ý: %s', implode(' · ', $selected['executionHours'])), 0, 'L', false, 1);
        $pdf->MultiCell(0, 7, sprintf('Điểm đánh giá: %d/100', $selected['score']), 0, 'L', false, 1);
        $pdf->Ln(2);
        $pdf->MultiCell(0, 7, sprintf('Nhà gái: %s (%s)', $validated['bride_name'], $validated['bride_birth_date']), 0, 'L', false, 1);
        $pdf->MultiCell(0, 7, sprintf('Nhà trai: %s (%s)', $validated['groom_name'], $validated['groom_birth_date']), 0, 'L', false, 1);
        $pdf->Ln(2);
        $pdf->MultiCell(0, 7.5, 'Luận giải: '.$selected['reason'], 0, 'L', false, 1);

        $fileName = sprintf('ngay-cuoi-%s.pdf', $selected['date']);

        return response($pdf->Output($fileName, 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }
}
