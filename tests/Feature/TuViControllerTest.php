<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class TuViControllerTest extends TestCase
{
    /** @test */
    public function tu_vi_routes_are_available(): void
    {
        $this->assertTrue(Route::has('tuvi.index'));
        $this->assertTrue(Route::has('tuvi.result'));
    }

    /** @test */
    public function index_renders_tu_vi_page(): void
    {
        $response = $this->get('/tu-vi');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('TuVi/Index'));
    }

    /** @test */
    public function post_result_returns_chart_data(): void
    {
        $response = $this->post('/tu-vi/ket-qua', [
            'gender' => 'female',
            'birth_date' => '1996-06-12',
        ]);

        $response->assertInertia(fn ($page) => $page
            ->component('TuVi/Index')
            ->has('result.chart.profile')
            ->has('result.chart.palaces', 12)
        );
    }
}

