<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\LocationType;

class LocationsTest extends TestCase
{

    
    private array $expected_properties = ['id', 'name', 'fips', 'district_id'];

    private array $location_type_expected_properties = ['id', 'name', 'plural_name', 'scope','classification'];

    
    public function test_location_type_locations_index(){
        
        $start = microtime(true);

        $location_type = LocationType::first();

        $response = $this->get(route('api.app.location_types.locations.index',[
            'location_type' => $location_type
        ]));

        $duration = microtime(true) - $start;

        dump("response time: $duration");

        $response->assertStatus(200);

        $response->assertJsonStructure(['data' => [
                ...$this->location_type_expected_properties,
                'locations' => [$this->expected_properties]
            ]
        ]);    

    }

    public function test_locations_returned_as_geojson_when_wants_geojson_is_true(){

        $start = microtime(true);

        $location_type = LocationType::first();

        $response = $this->get(route('api.app.location_types.locations.index',[
            'location_type' => $location_type,
            'as' => 'geojson'
        ]));

        $duration = microtime(true) - $start;

        dump("response time: $duration");

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                ... $this->location_type_expected_properties,
                'locations' => [
                    'type',
                    'features' => [
                        '*' =>[
                            'type',
                            'geometry',
                            'properties' => $this->expected_properties 
                        ]
                        
                    ]
                ]
            ]
        ]);

    }

    public function test_location_type_locations_index_returns_404_when_location_type_id_not_found(){

        $location_type = LocationType::first();

        $location_type->id = 99999;

        $response = $this->get(route('api.app.location_types.locations.index',[
            'location_type' => $location_type,
            'as' => 'geojson'
        ]));

        $response->assertStatus(404);

    }

}
