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
        
        $response = $this->get(route('api.app.location_types.indicators.index', [
            'location_type' => $location_type,
            'q' => 'how many children live in nyc'
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


    public function test_location_type_indicator_filter_returns_proper_structure (){

        $location_type = LocationType::first();

        $start = microtime(true);
        
        $response = $this->get(route('api.app.location_types.indicators.index', [
            'location_type' => $location_type,
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

        $location_type = LocationType::first();

        $start = microtime(true);
        
        $response = $this->get(route('api.app.location_types.indicators.index', [
            'location_type' => $location_type,
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

}
