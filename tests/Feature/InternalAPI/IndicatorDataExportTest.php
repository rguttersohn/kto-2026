<?php

namespace Tests\Feature;


use Tests\TestCase;
use App\Models\Indicator;

class IndicatorDataExportTest extends TestCase
{
    

    public function test_200_response(){

        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $response = $this->get(route('api.app.indicators.data.export', $indicator ));

        $response->assertStatus(200);

    }

    public function test_400_response(){

        $indicator = Indicator::first();
        
        $indicator->id = 99999;

        $response = $this->get(route('api.app.indicators.data.export', $indicator));

        $response->assertStatus(404);

    }

    public function test_400_response_if_query_param_is_invalidated(){

        $indicator = Indicator::first();

        $response = $this->get(route('api.app.indicators.data.export',[
            'indicator' => $indicator,
            'as' => 'excel'
        ]));

        $response->assertStatus(400);

        $response->assertJsonStructure(['message']);

    }

    public function test_returns_data_as_csv (){

        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $response = $this->get(route('api.app.indicators.data.export', [
            'indicator' => $indicator,
            'as' => 'csv'
        ]));

        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $this->assertStringContainsString(
            'attachment; filename=',
            $response->headers->get('Content-Disposition')
        );
    }


    public function test_returns_data_as_geojson(){

        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $response = $this->get(route('api.app.indicators.data.export', [
            'indicator' => $indicator,
            'as' => 'geojson'
        ]));

        $response->assertHeader('Content-Type', 'application/geo+json');

        $this->assertStringContainsString(
            'attachment; filename=',
            $response->headers->get('Content-Disposition')
        );
    }

    public function test_returns_data_as_json(){

        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $response = $this->get(route('api.app.indicators.data.export', [
            'indicator' => $indicator,
            'as' => 'json'
        ]));

        $response->assertHeader('Content-Type', 'application/json');

        $this->assertStringContainsString(
            'attachment; filename=',
            $response->headers->get('Content-Disposition')
        );
    }


}
