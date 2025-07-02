<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class IndexPageTest extends TestCase
{

    public function test_response_speed():void{

        $start = microtime(true);

        $this->get('/');

        $duration = microtime(true) - $start;

        dump("response time: $duration");

        $this->assertLessThan(0.5, $duration, "Respone too slow. Response time: $duration");

    }
    
    public function test_index_page_returns_200()
    {
        $response = $this->get('/');

        $response->assertStatus(200);

    }
}
