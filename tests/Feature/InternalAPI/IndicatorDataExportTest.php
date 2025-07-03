<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Indicator;

class IndicatorDataExportTest extends TestCase
{
    

    public function test_200_response(){

        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $response = $this->get("api/app/indicators/$indicator->id/data/export");

        $response->assertStatus(200);

    }

    public function test_400_response(){

        $indicator_id = 99999;

        $response = $this->get("api/app/indicators/$indicator_id/data/export");

        $response->assertStatus(404);

    }

    public function test_returns_data_as_csv (){

        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $response = $this->get("api/app/indicators/$indicator->id/data/export?as=csv");

        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $this->assertStringContainsString(
            'attachment; filename=',
            $response->headers->get('Content-Disposition')
        );
    }


    public function test_returns_data_as_geojson(){

        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $response = $this->get("api/app/indicators/$indicator->id/data/export?as=geojson");

        $response->assertHeader('Content-Type', 'application/geo+json');

        $this->assertStringContainsString(
            'attachment; filename=',
            $response->headers->get('Content-Disposition')
        );
    }

    public function test_returns_data_as_json(){

        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $response = $this->get("api/app/indicators/$indicator->id/data/export");

        $response->assertHeader('Content-Type', 'application/json');

        $this->assertStringContainsString(
            'attachment; filename=',
            $response->headers->get('Content-Disposition')
        );
    }


}
