<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\LocationType;

class AssetsByLocationTest extends TestCase
{

    private array $expected_properties = [
       'location_name',
        'location_id',
        'count'
    ];
    
    public function test_missing_by_param_returns_400(): void
    {
        $response = $this->get('api/app/assets/aggregate?location_type=2');

        $response->assertStatus(400);

        $response->assertJson([
            'error' => ['status' => true]
        ]);
    }

    public function test_invalid_by_param_returns_400(): void {

        $response = $this->get('api/app/assets/aggregate?by=test');

        $response->assertStatus(400);

        $response->assertJson([
            'error' => ['status' => true]
        ]);

    }

    public function test_returns_400_when_by_is_location_type_but_missing_location_type(){

        $response = $this->get('api/app/assets/aggregate?by=location_type&custom_location=test');

        $response->assertStatus(400);

        $response->assertJson([
            'error' => ['status' => true]
        ]);

    }

    public function test_invalid_location_type_param_returns_400(): void
    {
        $response = $this->get('api/app/assets/aggregate?by=location_type&location_type=test');

        $response->assertStatus(400);

        $response->assertJson([
            'error' => ['status' => true]
        ]);
    }

    public function test_missing_filter_param_returns_400(): void
    {

        $location_type = LocationType::inRandomOrder()->firstOrFail();

        $response = $this->get("api/app/assets/aggregate?by=location_type&location_type=$location_type->id");

        $response->assertStatus(400);

        $response->assertJson([
            'error' => ['status' => true]
        ]);
    }

    public function test_status_200(): void
    {

        $location_type = LocationType::inRandomOrder()->firstOrFail();

        $response = $this->get("api/app/assets/aggregate?by=location_type&location_type=$location_type->id&filter[category][in][]=1");

        $response->assertStatus(200);
    
    }

    public function test_response_data(): void{

        $location_type = LocationType::inRandomOrder()->firstOrFail();

        $response = $this->get("api/app/assets/aggregate?by=location_type&location_type=$location_type->id&filter[category][in][]=1");

        $response->assertJsonStructure([
            'error', 
            'data' => ['*' => $this->expected_properties]
        ]);
    }


    public function test_response_as_geojson_data (){

        $location_type = LocationType::inRandomOrder()->firstOrFail();

        $response = $this->get("api/app/assets/aggregate?by=location_type&location_type=$location_type->id&filter[category][in][]=1&as=geojson");

        $response->assertJsonStructure([
            'error',
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


}
