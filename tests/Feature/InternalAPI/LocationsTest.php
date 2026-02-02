<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Location;
use App\Models\LocationType;

class LocationsTest extends TestCase
{

    
    private array $expected_properties = ['id', 'name', 'fips', 'district_id'];

    private array $location_type_expected_properties = ['id', 'name', 'plural_name', 'scope','classification'];

    private array $indicator_expected_properties = ['name', 'definition', 'note', 'source'];

    
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

        $location = Location::first();

        $response = $this->get(route('api.app.location_types.locations.index',[
            'location_type' => $location,
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

        $location = Location::first();

        $location->id = 99999;

        $response = $this->get(route('api.app.location_types.locations.index',[
            'location_type' => $location,
            'as' => 'geojson'
        ]));

        $response->assertStatus(404);

    }


    public function test_location_indicator_filter_returns_proper_structure (){

        $location = Location::first();

        $start = microtime(true);
        
        $response = $this->get(route('api.app.location.indicators.index', [
            'location' => $location,
            'filter[category][eq]' => 1,
            'filter[domain][eq]' => 1
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

        $data = $response->json('data');

        $domain_ids = array_map(fn($indicator)=>$indicator['domain_id'], $data['indicators']);

        $category_ids = array_map(fn($indicator)=>$indicator['category_id'], $data['indicators']);

        $this->assertEquals([1], array_unique($domain_ids), "Expected all domain_ids to be 1");

        $this->assertEquals([1], array_unique($category_ids), "Expected all categorys_ids to be 1");

    }


    public function test_location_type_indicator_filter_with_search_returns_proper_json_structure (){

        $location = Location::first();

        $start = microtime(true);
        
        $response = $this->get(route('api.app.location.indicators.index', [
            'location' => $location,
            'filter[category][eq]' => 1,
            'filter[domain][eq]' => 1,
            'q' => 'population'
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

        $data = $response->json('data');

        $domain_ids = array_map(fn($indicator)=>$indicator['domain_id'], $data['indicators']);

        $category_ids = array_map(fn($indicator)=>$indicator['category_id'], $data['indicators']);


        $this->assertEquals([1], array_unique($domain_ids), "Expected all domain_ids to be 1");

        $this->assertEquals([1], array_unique($category_ids), "Expected all categorys_ids to be 1");

    }

    public function test_location_type_indicators_index(){

        $location = Location::first();

        $start = microtime(true);
        
        $response = $this->get(route('api.app.location.indicators.index', [
            'location' => $location,
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


    public function test_location_indicator_search (){

        $location = Location::first();

        $start = microtime(true);
        
        $response = $this->get(route('api.app.location.indicators.index', [
            'location' => $location,
            'q' => 'how many children live sin nyc'
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

}
