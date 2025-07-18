<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\LocationType;

class WellBeingRankingByLocationTest extends TestCase
{
    private function getLocationID():int {

        $rankable_location_type = LocationType::where('has_ranking', true)
            ->with('locations:id,location_type_id')
            ->first();
        
        return $rankable_location_type->locations->first()->id;

    }

    public function test_200_response(): void
    {   

        $location_id = $this->getLocationID();

        $response = $this->get("/api/app/locations/$location_id/well-being");

        $response->assertStatus(200);
    }

    public function test_response_data(){

        $location_id = $this->getLocationID();

        $response = $this->get("/api/app/locations/$location_id/well-being?filter[year][eq]=2020");

        $response->assertJsonStructure([
            'error', 
            'data' => ['*' => 
                [
                    'id', 'domain_id', 'year', 'score', 'location_id'
                ]
            ]
        ]);


    }

    public function test_returns_400_when_filter_malformed (){

        $location_id = $this->getLocationID();

        $response = $this->get("/api/app/locations/$location_id/well-being?filter[year][]=2020");

        $response->assertStatus(400);

    }


    public function test_returns_400_when_location_id_not_found (){

        $location_id = 9999999;

        $response = $this->get("/api/app/locations/$location_id/well-being?filter[year][]=2020");

        $response->assertStatus(400);

    }


}
