<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Indicator;

class IndicatorFiltersTest extends TestCase
{
    
    public function test_indicator_filters(){

        $indicator = Indicator::first();

        $response = $this->get(route('api.app.indicators.filters',[
            'indicator' => $indicator
        ]));

        $response->assertStatus(200);

    }

    public function test_indicator_filters_status_is_404_when_indicator_id_not_found(){

        $indicator = Indicator::first();

        $indicator->id = 9999;

        $response = $this->get(route('api.app.indicators.filters',[
            'indicator' => $indicator
        ]));

        $response->assertStatus(404);

    }
    
}
