<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class WeddingDateControllerTest extends TestCase
{
    /** @test */
    public function wedding_routes_are_available(): void
    {
        $this->assertTrue(Route::has('wedding.index'));
        $this->assertTrue(Route::has('wedding.result'));
        $this->assertTrue(Route::has('wedding.pdf'));
    }

    /** @test */
    public function index_renders_wedding_page(): void
    {
        $response = $this->get('/ngay-cuoi');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Wedding/Index'));
    }

    /** @test */
    public function post_result_returns_dates_payload(): void
    {
        $response = $this->post('/ngay-cuoi/ket-qua', [
            'bride_name' => 'Lan',
            'bride_birth_date' => '1998-09-02',
            'groom_name' => 'Minh',
            'groom_birth_date' => '1996-07-12',
            'event_type' => 'wedding',
            'target_year' => 2026,
            'target_month' => 10,
        ]);

        $response->assertInertia(fn ($page) => $page
            ->component('Wedding/Index')
            ->has('result.kimLau')
            ->has('result.monthBenefit')
            ->has('result.dates')
        );
    }
}
