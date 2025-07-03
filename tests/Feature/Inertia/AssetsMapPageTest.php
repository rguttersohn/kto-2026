<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class AssetsMapPageTest extends TestCase
{
    
    public function test_200_response(): void
    {
    $response = $this->get('/community-assets');

        $response->assertStatus(200);

    }

    public function test_response_data():void {

        $response = $this->get('/community-assets');

        $response->assertInertia(function(Assert $page){

            $page->has('asset_categories.0', function(Assert $category){
                $category->hasAll(['id', 'group_name', 'subcategories']);
            });
            
        });
    }
    
}
