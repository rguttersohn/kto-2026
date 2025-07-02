<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class IndicatorAllPageTest extends TestCase
{
    
    public function test_response_speed():void{

        $start = microtime(true);

        $this->get('/indicators');

        $duration = microtime(true) - $start;

        dump("response time: $duration");

        $this->assertLessThan(0.5, $duration, "Respone too slow. Response time: $duration");

    }


    public function test_indicator_all_data(): void
    {
        
        $response = $this->get('/indicators');
        
        $response->assertStatus(200);

        $response->assertInertia(fn (Assert $page) =>
            $page->has('indicators.0', function(Assert $indicator){
                $indicator->hasAll(['id', 'name']);
            })
        );
    }
}
