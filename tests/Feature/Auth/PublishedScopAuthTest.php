<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Indicator;
use App\Models\User;

class PublishedScopAuthTest extends TestCase {


    public function test_published_scope_not_applied_when_user_is_atleast_editor(){

        $user = User::where('role_id','>=', 2)->first();

        if(!$user){

            $user = User::factory()->editor()->create();
        }

        $this->actingAs($user);

        $indicator = Indicator::withoutGlobalScopes()->where('is_published', false)->first();

        if(!$indicator){

            $indicator = Indicator::factory()->create();
            
        }

        $indicators = Indicator::all();

        $this->assertTrue($indicators->contains('id', $indicator->id),
            'Unpublished indicator should be visible to editors'
        );
        
    }

    public function test_published_scope_is_applied_for_users(){

        $user = User::where('role_id','=', 1)->first();

        if(!$user){

            $user = User::factory()->user()->create();
        }

        $this->actingAs($user);

        $indicator = Indicator::withoutGlobalScopes()->where('is_published', false)->first();

        if(!$indicator){

            $indicator = Indicator::factory()->create();
            
        }

        $indicators = Indicator::all();

        $this->assertFalse($indicators->contains('id', $indicator->id),
            'Unpublished indicator should be visible to editors'
        );
        
    }


    public function test_published_scope_is_applied_for_unauthed_user(){

       
        $indicator = Indicator::withoutGlobalScopes()->where('is_published', false)->first();

        if(!$indicator){

            $indicator = Indicator::factory()->create();
            
        }

        $indicators = Indicator::all();

        $this->assertFalse($indicators->contains('id', $indicator->id),
            'Unpublished indicator should be visible to editors'
        );
        
    }


 
}
