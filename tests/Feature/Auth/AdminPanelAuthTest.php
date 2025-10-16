<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AdminPanelAuthTest extends TestCase
{
   

    public function test_user_with_no_role_cannot_access_admin_panel(){
    
        // Attempt to access the admin panel
        $response = $this->get('/admin');

        // Assert the user is denied access (typically 403 Forbidden)
        $response->assertStatus(302);
    }


    public function test_user_with_role_id_one_cannot_access_admin_panel(){
        // Create a user with role_id of 1
        $user = User::where('role_id', 1)->first();

        $this->actingAs($user);

        // Attempt to access the admin panel
        $response = $this->get('/admin');

        // Assert the user is denied access
        $response->assertForbidden();
    }

    public function test_authorized_user_can_access_admin_panel(){

        // Adjust the role_id to whatever role should have access
        $user = User::where('role_id', 2)->first();

        $this->actingAs($user);

        $response = $this->get('/admin');

        // Assert the user can access the panel
        $response->assertSuccessful();
    }

}
