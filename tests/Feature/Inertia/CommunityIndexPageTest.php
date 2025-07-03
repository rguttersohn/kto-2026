<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Location;
use Inertia\Testing\AssertableInertia as Assert;

class CommunityIndexPageTest extends TestCase
{
    
    public function test_200_response(): void
    {
        
        $location = Location::inRandomOrder()->firstOrFail();

        $response = $this->get("/community-profiles/$location->id");

        $response->assertStatus(200);

    }

    public function test_invalid_location_id (): void
    {
        
        $location_id = 9999999;
        
        $response = $this->get("/community-profiles/$location_id");

        $response->assertStatus(404);
    }

    public function test_respone_data(): void
    {   


        $location = Location::inRandomOrder()->firstOrFail();

        $response = $this->get("/community-profiles/$location->id");

         $response->assertInertia(function(Assert $page){

            $page->has('location', function(Assert $location){

                $location->hasAll(['id', 'name', 'fips', 'geopolitical_id']);

            });

         });

    }
}
