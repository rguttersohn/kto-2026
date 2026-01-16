<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Indicator;

class IndicatorTest extends TestCase
{   

    private array $expected_properties = ['id','name', 'definition', 'note', 'source', 'category', 'category_id', 'domain', 'domain_id'];
   
    public function test_indicator_index(): void
    {   
        $start = microtime(true);
        
        $response = $this->get(route('api.app.indicators.index'));

        $duration = microtime(true) - $start;

        dump("response time: $duration");

        $this->assertLessThan(0.5, $duration, "Respone too slow. Response time: $duration");

        $response->assertStatus(200);

        $response->assertJsonStructure(['data' => [$this->expected_properties]]);
    }

    public function test_indicator_show():void {

        $start = microtime(true);

        $indicator = Indicator::first();
        
        $response = $this->get(route('api.app.indicators.show', [
            'indicator' => $indicator
        ]));

        $duration = microtime(true) - $start;

        dump("response time: $duration");

        $this->assertLessThan(0.5, $duration, "Respone too slow. Response time: $duration");

        $response->assertStatus(200);

        $response->assertJsonStructure(['data' => $this->expected_properties]);
    }

    public function test_indicator_search():void {

        $start = microtime(true);
        
        $response = $this->get(route('api.app.indicators.index', [
            'q' => 'child population living in immigrant households'
        ]));

        $duration = microtime(true) - $start;

        dump("response time: $duration");

        $this->assertLessThan(3, $duration, "Respone too slow. Response time: $duration");

        $response->assertStatus(200);

        $response->assertJsonStructure(['data' => [$this->expected_properties]]);

    }


    public function test_indicator_search_works_with_filter_trait(){

        $start = microtime(true);
        
        $response = $this->get(route('api.app.indicators.index', [
            'filter[domain][eq]' => 1,
            'fitler[category[eq]' => 1
        ]));

        $duration = microtime(true) - $start;

        dump("response time: $duration");

        $this->assertLessThan(0.5, $duration, "Respone too slow. Response time: $duration");
        
        $response->assertOk();

        $data = $response->json('data');

        $domain_ids = array_map(fn($indicator)=>$indicator['domain_id'], $data);

        $category_ids = array_map(fn($indicator)=>$indicator['category_id'], $data);

        $this->assertEquals([1], array_unique($domain_ids), "Expected all domain_ids to be 1");

        $this->assertEquals([1], array_unique($category_ids), "Expected all categorys_ids to be 1");

        $response->assertJsonStructure(['data' => [$this->expected_properties]]);

    }

    public function test_indicator_search_returns_200_when_param_is_missing(){

        $response = $this->get(route('api.app.indicators.index'));

        $response->assertStatus(200);

        $response->assertJsonStructure(['data' => [$this->expected_properties]]);
        
    }

    public function test_indicator_search_works_with_both_filter_and_search_query(){

        $response = $this->get(route('api.app.indicators.index', [
            'filter[domain][eq]' => 1,
            'fitler[category[eq]' => 1,
            'q' => 'population'
        ]));

        $response->assertOk();

        $data = $response->json('data');

        $domain_ids = array_map(fn($indicator)=>$indicator['domain_id'], $data);

        $category_ids = array_map(fn($indicator)=>$indicator['category_id'], $data);

        $this->assertEquals([1], array_unique($domain_ids), "Expected all domain_ids to be 1");

        $this->assertEquals([1], array_unique($category_ids), "Expected all categorys_ids to be 1");

        $response->assertJsonStructure(['data' => [$this->expected_properties]]);

    }

}
