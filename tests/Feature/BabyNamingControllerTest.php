<?php

declare(strict_types=1);

namespace Tests\Feature;

use Database\Seeders\VietnameseNamesDictionarySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class BabyNamingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(VietnameseNamesDictionarySeeder::class);
    }

    /** @test */
    public function naming_routes_are_registered(): void
    {
        $this->assertTrue(Route::has('naming.index'));
        $this->assertTrue(Route::has('naming.result'));
    }

    /** @test */
    public function index_renders_naming_page(): void
    {
        $response = $this->get('/dat-ten-cho-con');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Naming/Index'));
    }

    /** @test */
    public function result_returns_analysis_and_suggestions(): void
    {
        $response = $this->post('/dat-ten-cho-con/ket-qua', [
            'father_name' => 'Nguyễn Văn Minh',
            'father_birth_year' => 1993,
            'mother_name' => 'Lê Thị Lan',
            'mother_birth_year' => 1995,
            'baby_gender' => 'male',
            'baby_birth_date' => '2026-10-20 09:30',
            'include_mother_surname' => true,
        ]);

        $response->assertInertia(fn ($page) => $page
            ->component('Naming/Index')
            ->has('result.analysis')
            ->has('result.suggestions')
        );
    }
}

