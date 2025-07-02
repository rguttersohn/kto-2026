<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Indicator;
use Inertia\Testing\AssertableInertia as Assert;

class IndicatorIndexPageTest extends TestCase
{
    public function test_returns_404_for_invalid_indicator_id(): void
    {
        $nonExistentId = 999999;

        $response = $this->get("/indicators/{$nonExistentId}");

        $response->assertStatus(404);
    }

    public function test_returns_200():void{

        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $response = $this->get("/indicators/$indicator->id");

        $response->assertStatus(200);

    }

    public function test_page_response_speed ():void{

        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $start = microtime(true);
        
        $this->get("/indicators/{$indicator->id}");

        $duration = microtime(true) - $start;

        dump("response time: $duration");

        $this->assertLessThan(0.50, $duration, "Response took too long: {$duration}s");

    }

    public function test_page_props_data ():void{

        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $response = $this->get("/indicators/{$indicator->id}");

        $response->assertInertia(function(Assert $page){
            $page->has('indicator', function(Assert $indicator){
                $indicator->hasAll(['id', 'name', 'definition', 'source', 'note']);
            });
        });

        
    }
}
