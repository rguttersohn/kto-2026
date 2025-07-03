<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AssetsMapPageTest extends TestCase
{
    
    public function test_200_response(): void
    {
        $response = $this->get('/community-assets');

        $response->assertStatus(200);

    }
    
}
