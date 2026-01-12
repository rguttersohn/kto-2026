<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Indicator;

class IndicatorTest extends TestCase
{   

    private array $expected_properties = ['name', 'definition', 'note', 'source'];
   
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
        
        $response = $this->get(route('api.app.indicators.search', [
            'q' => 'child population living in immigrant households'
        ]));

        $duration = microtime(true) - $start;

        dump("response time: $duration");

        $this->assertLessThan(3, $duration, "Respone too slow. Response time: $duration");

        $response->assertStatus(200);

        $response->assertJsonStructure(['data' => [$this->expected_properties]]);

    }

    public function test_indicator_search_returns_400_when_param_is_missing(){

        $response = $this->get(route('api.app.indicators.search'));

        $response->assertStatus(400);
        
    }


}
