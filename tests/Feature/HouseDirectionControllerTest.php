<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class HouseDirectionControllerTest extends TestCase
{
    /** @test */
    public function index_route_returns_200(): void
    {
        $response = $this->get('/huong-nha');
        $response->assertStatus(200);
    }

    /** @test */
    public function route_names_are_registered(): void
    {
        $this->assertTrue(Route::has('direction.index'));
        $this->assertTrue(Route::has('direction.result'));
    }

    /** @test */
    public function index_renders_inertia_direction_page(): void
    {
        $response = $this->get('/huong-nha');
        $response->assertInertia(fn ($page) => $page->component('Direction/Index'));
    }

    /** @test */
    public function post_result_returns_profile_and_directions(): void
    {
        $response = $this->post('/huong-nha/ket-qua', [
            'gender' => 'male',
            'birth_date' => '1993-02-04',
        ]);

        $response->assertInertia(fn ($page) => $page
            ->component('Direction/Index')
            ->has('result.profile')
            ->has('result.directions.good')
            ->has('result.directions.bad')
        );
    }
}

