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

        $response = $this->get("api/app/indicators/$indicator->id/data/count");

        $response->assertStatus(200);

    }

    public function test_400_response(){

        $indicator_id = 99999;

        $response = $this->get("api/app/indicators/$indicator_id/data/count");

        $response->assertStatus(400);

    }

    public function test_data(){
       
        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $response = $this->get("api/app/indicators/$indicator->id/data/count");

        $response->assertJsonStructure([
            'error' => ['status', 'message'],
            'data' => ['count']
        ]);
    }
}
