<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Indicator;

class IndicatorDataCountTest extends TestCase
{
    
    public function test_200_response(){

        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $response = $this->get(route('api.app.indicators.data.count', $indicator));

        $response->assertStatus(200);

    }

    public function test_400_response(){

        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $indicator->id = 99999;

        $response = $this->get(route('api.app.indicators.data.count', $indicator));

        $response->assertStatus(404);

    }

    public function test_data(){
       
        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $response = $this->get(route('api.app.indicators.data.count', $indicator));

        $response->assertJsonStructure([
            'data'
        ]);
    }
}
