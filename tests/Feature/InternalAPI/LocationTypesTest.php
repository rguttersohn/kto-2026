<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\LocationType;

class LocationTypesTest extends TestCase
{   

    private array $expected_properties = ['id', 'name', 'plural_name', 'scope','classification'];


    public function test_location_type_index(){

        $start = microtime(true);
        
        $response = $this->get(route('api.app.location_types.index'));

        $duration = microtime(true) - $start;

        dump("response time: $duration");

        $response->assertStatus(200);

        $response->assertJsonStructure(['data' => [$this->expected_properties]]);

    }

    public function test_location_type_show(){

        $location_type = LocationType::first();

        $start = microtime(true);
        
        $response = $this->get(route('api.app.location_types.show', [
            'location_type' => $location_type
        ]));

        $duration = microtime(true) - $start;

        dump("response time: $duration");

        $response->assertStatus(200);

        $response->assertJsonStructure(['data' => $this->expected_properties]);

    }

    public function test_location_type_index_returns_404_when_location_type_id_not_found(){

        $location_type = LocationType::first();

        $location_type->id = 9999;

        $response = $this->get(route('api.app.location_types.show', [
            'location_type' => $location_type
        ]));

        $response->assertStatus(404);

    }

    
}
