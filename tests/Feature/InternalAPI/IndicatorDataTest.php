<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Indicator;

class IndicatorDataTest extends TestCase
{
    protected array $expected_properties = [
        'indicator_id',
        'location_id', 
        'location',
        'location_type_id',
        'location_type',
        'timeframe',
        'breakdown',
        'format',
        'data',
    ];

    public function test_200_status(): void
    {   

        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $response = $this->get("api/app/indicators/$indicator->id/data");

        $response->assertStatus(200);
    }

    public function test_filter_invalidation(): void
    {   

        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $response = $this->get("api/app/indicators/$indicator->id/data?filter[timeframe][test]=2020");

        $response->assertStatus(400);
    }

    public function test_limit_max(): void
    {   

        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $response = $this->get("api/app/indicators/$indicator->id/data");

        $response->assertJsonCount(3000, 'data');
    }

    public function test_limit(): void
    {   

        $limit = 100;

        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $response = $this->get("api/app/indicators/$indicator->id/data?limit=$limit");

        $response->assertJsonCount($limit, 'data');
    }

    public function test_data_with_no_filters(){

        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $response = $this->get("api/app/indicators/$indicator->id/data");

        $response->assertJsonStructure([
            'error' => ['status', 'message'],
            'data' => ['*' => $this->expected_properties]
        ]);

    }

    public function test_data_as_geojson(){

        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $response = $this->get("api/app/indicators/$indicator->id/data?as=geojson");

        $response->assertJsonStructure([
            'error' => ['status', 'message'],
            'data' => [
                'type',
                'features' => [
                    '*' =>[
                        'type',
                        'geometry',
                        'properties' => $this->expected_properties 
                    ]
                    
                ]
            ]
        ]);

    }

    public function test_data_with_filter(){
        
        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $response = $this->get("api/app/indicators/$indicator->id/data?filter[timeframe][gte]=2022");

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'error' => ['status', 'message'],
            'data' => ['*' => $this->expected_properties]
            ]);

    }
}
