<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AssetsByCustomLocationTest extends TestCase
{
    public function test_geometry_missing_returns_400(){

        $response = $this->postJson('/api/app/assets/aggregate-custom-location?filter[category][in][]=3');

        $response->assertStatus(400);

    }

    public function test_missing_filter_returns_400(){

        $response = $this->postJson('/api/app/assets/aggregate-custom-location',[
            'geometry' => [
                'type' => 'Polygon',
                'coordinates' => [
                    [
                        [-74.01, 40.675],
                        [-73.95, 40.675],
                        [-73.95, 40.72],
                        [-74.01, 40.72],
                        [-74.01, 40.675]
                    ]
                ]
        ]
    ]);

        $response->assertStatus(400);
        
    }

    public function test_200_status_code(): void
    {
        $response = $this->postJson('/api/app/assets/aggregate-custom-location?filter[category][in][]=3', [
                'geometry' => [
                    'type' => 'Polygon',
                    'coordinates' => [
                        [
                            [-74.01, 40.675],
                            [-73.95, 40.675],
                            [-73.95, 40.72],
                            [-74.01, 40.72],
                            [-74.01, 40.675]
                        ]
                    ]
            ]
        ]);

        $response->assertStatus(200);
    }

    public function test_response_data(){

            $response = $this->postJson('/api/app/assets/aggregate-custom-location?filter[category][in][]=3', [
                'geometry' => [
                    'type' => 'Polygon',
                    'coordinates' => [
                        [
                            [-74.01, 40.675],
                            [-73.95, 40.675],
                            [-73.95, 40.72],
                            [-74.01, 40.72],
                            [-74.01, 40.675]
                        ]
                    ]
                ]
            ]);

            $response->assertJsonStructure([
                'error',
                'data' => [
                    'geometry',
                    'assets' => [
                        'total',
                        'counts' => [
                            '*' => [
                                'name',
                                'count'
                            ]
                        ]
                    ]
                ]
            ]);
        
    }
}
