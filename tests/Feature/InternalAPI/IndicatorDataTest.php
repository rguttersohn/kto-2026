<?php

namespace Tests\Feature;

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

        $response = $this->get(route('api.app.indicators.data', $indicator));

        $response->assertStatus(200);
    }

    public function test_filter_invalidation(): void
    {   

        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $response = $this->get(route('api.app.indicators.data', [
            'indicator' => $indicator,
            'filter[timeframe][test]' => 2020,
        ]));

        $response->assertStatus(400);

        $response->assertJsonStructure([
            'message'
        ]);
    }

    public function test_limit_max(): void
    {   

        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $response = $this->get(route('api.app.indicators.data', [
            'indicator' => $indicator
        ]));

        $this->assertTrue(count($response->json('data')) <= 3000);
    }

    public function test_limit(): void
    {   

        $limit = 100;

        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $response = $this->get(route('api.app.indicators.data', [
            'indicator' => $indicator,
            'limit' => $limit
        ]));

        $response->assertJsonCount($limit, 'data');
    }

    public function test_data_with_no_filters(){

        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $response = $this->get(route('api.app.indicators.data', [
            'indicator' => $indicator
        ]));

        $response->assertJsonStructure(['data']);

    }

    public function test_data_as_geojson(){

        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $response = $this->get(route('api.app.indicators.data', [
            'indicator' => $indicator,
            'as' => 'geojson'
        ]));

        $response->assertJsonStructure([
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

        $response = $this->get(route('api.app.indicators.data', [
            'indicator' => $indicator,
            'filter[timeframe][gte]' => 2022
        ]));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => ['*' => $this->expected_properties]
            ]);

    }
}
