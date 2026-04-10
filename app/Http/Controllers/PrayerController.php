<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Prayer;
use App\Models\PrayerCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class PrayerController extends Controller
{
    public function index(Request $request): Response
    {
        $categories = PrayerCategory::query()
            ->with([
                'prayers:id,category_id,title,slug',
            ])
            ->withCount('prayers')
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $selectedPrayer = Prayer::query()
            ->with(['prayerCategory:id,name,slug'])
            ->when(
                $request->filled('prayer_slug'),
                fn ($q) => $q->where('slug', (string) $request->query('prayer_slug')),
            )
            ->orderBy('title')
            ->first(['id', 'category_id', 'title', 'slug', 'content', 'created_at']);

        if (!$selectedPrayer) {
            $selectedPrayer = Prayer::query()
                ->with(['prayerCategory:id,name,slug'])
                ->orderBy('title')
                ->first(['id', 'category_id', 'title', 'slug', 'content', 'created_at']);
        }

        return Inertia::render('Prayers/Index', [
            'categories' => $categories,
            'selectedPrayer' => $selectedPrayer,
        ]);
    }

    public function show(string $slug): Response
    {
        $prayer = Prayer::query()
            ->with(['prayerCategory:id,name,slug'])
            ->where('slug', $slug)
            ->firstOrFail(['id', 'category_id', 'title', 'slug', 'content', 'created_at']);

        $relatedPrayers = Prayer::query()
            ->where('category_id', $prayer->category_id)
            ->where('id', '!=', $prayer->id)
            ->latest('id')
            ->limit(6)
            ->get(['id', 'title', 'slug']);

        return Inertia::render('Prayers/Show', [
            'prayer' => $prayer,
            'relatedPrayers' => $relatedPrayers,
        ]);
    }

    public function exportPdf(string $slug): HttpResponse
    {
        $prayer = Prayer::query()
            ->with(['prayerCategory:id,name,slug'])
            ->where('slug', $slug)
            ->firstOrFail(['id', 'category_id', 'title', 'slug', 'content', 'created_at']);

        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('Phong Thủy Việt');
        $pdf->SetAuthor('Phong Thủy Việt');
        $pdf->SetTitle($prayer->title);
        $pdf->SetSubject('Văn khấn');
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

        $pdf->Ln(6);
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->SetTextColor(95, 95, 95);
        $pdf->Cell(0, 6, (string) ($prayer->prayerCategory->name ?? 'Văn khấn'), 0, 1, 'L');

        $pdf->SetFont('dejavusans', 'B', 16);
        $pdf->SetTextColor(40, 18, 9);
        $pdf->MultiCell(0, 10, $prayer->title, 0, 'L', false, 1);

        $pdf->Ln(2);
        $pdf->SetFont('dejavusans', '', 12);
        $pdf->SetTextColor(20, 20, 20);
        $pdf->MultiCell(0, 7.8, $prayer->content, 0, 'L', false, 1);

        $fileName = $prayer->slug.'.pdf';

        return response($pdf->Output($fileName, 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }
}
