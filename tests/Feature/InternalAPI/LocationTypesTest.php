<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\LocationType;

class LocationTypesTest extends TestCase
{   

    private array $expected_properties = ['id', 'name', 'plural_name', 'scope','classification'];

    private array $indicator_expected_properties = ['name', 'definition', 'note', 'source'];


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

    public function test_location_type_indicators_index(){

        $location_type = LocationType::first();

        $start = microtime(true);
        
        $response = $this->get(route('api.app.location_types.indicators.index', [
            'location_type' => $location_type,
        ]));

        $duration = microtime(true) - $start;

        dump("response time: $duration");

        $response->assertStatus(200);

        $response->assertJsonStructure(['data' => 
            [
                ...$this->expected_properties, 
                'indicators' => [$this->indicator_expected_properties]
            ]

        ]);


    }


    public function test_location_type_indicator_search (){

        $location_type = LocationType::first();

        $start = microtime(true);
        
        $response = $this->get(route('api.app.location_types.indicators.search', [
            'location_type' => $location_type,
            'q' => 'how many children live in nyc'
        ]));

        $duration = microtime(true) - $start;

        dump("response time: $duration");

        $response->assertStatus(200);

        $response->assertJsonStructure(['data' => [$this->indicator_expected_properties]]);

    }

    public function test_location_type_indicator_search_returns_400_when_q_param_is_missing (){

        $location_type = LocationType::first();
        
        $response = $this->get(route('api.app.location_types.indicators.search', [
            'location_type' => $location_type,
        ]));

        $response->assertStatus(400);

    }

}
