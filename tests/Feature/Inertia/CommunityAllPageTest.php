<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class CommunityAllPageTest extends TestCase
{
    
    public function test_200_response(): void
    {
        $response = $this->get('/community-profiles');

        $response->assertStatus(200);
    }

    public function test_response_data(){

        $response = $this->get('/community-profiles');

        $response->assertInertia(function(Assert $page){

            $page->has('location_types.0', function(Assert $location){

                $location->hasAll(['id', 'name', 'plural_name', 'scope', 'classification']);
            });

        });
        
    }
}

