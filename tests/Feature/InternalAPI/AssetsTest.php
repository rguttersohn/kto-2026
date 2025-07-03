<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AssetsTest extends TestCase
{
    
    public function test_200_status(): void
    {
        $response = $this->get('api/app/assets?filter[category][in][]=1');

        $response->assertStatus(200);
    }

    public function test_missing_atleast_one_filter_returns_404(): void
    {
        $response = $this->get('api/app/assets');

        $response->assertStatus(404);
    }

    public function test_response_data(){


        $response = $this->get('api/app/assets?filter[category][in][]=1');

        $response->assertJsonStructure([
            'error' => ['status', 'message'],
            'data' => ['*' => 
                    [
                        'description',
                        'id'
                    ]
                ]
            ]);

    }

    public function test_geojson_response_data(){

        $response = $this->get('api/app/assets?filter[category][in][]=1&as=geojson');

        $response->assertJsonStructure([
            'error' => ['status', 'message'],
            'data' => [
                'type',
                'features' => [
                    '*' => [
                        'type',
                        'geometry',
                        'properties' => ['id','description']
                    ]
                ]
            ]
        ]);

    }
}
