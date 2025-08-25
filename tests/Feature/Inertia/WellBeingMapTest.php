<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;


class WellBeingMapTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_200_status(): void
    {
        $response = $this->get('/well-being');

        $response->assertStatus(200);
    }


    public function test_props_data(): void {

        $response = $this->get('/well-being');

        $response->assertInertia(function(Assert $page){

            $page->has('domains');

            $page->has('location_types');

            $page->has('years');
            
        });
        

    }
}
