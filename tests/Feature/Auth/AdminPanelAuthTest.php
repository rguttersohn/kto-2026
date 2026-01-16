<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AdminPanelAuthTest extends TestCase
{
   
    private function getAdminURL():string{

        return 'http://localhost/admin';
    }


    public function test_user_with_no_role_cannot_access_admin_panel(){
        
        // Attempt to access the admin panel
        $response = $this->get($this->getAdminURL());

        // Assert the user is denied access (typically 403 Forbidden)
        $response->assertStatus(302);
    
    }

    public function test_user_with_null_role_id_cannot_access_admin_panel(){

        $null_user = User::factory()->nullRole()->create();

        $this->actingAs($null_user);

        $response = $this->get($this->getAdminURL());

        $response->assertForbidden();
    }


    public function test_user_cannot_access_admin_panel(){
        // Create a user with role_id of 1
        $user = User::factory()->user()->create();

        $this->actingAs($user);

        // Attempt to access the admin panel
        $response = $this->get($this->getAdminURL());

        // Assert the user is denied access
        $response->assertForbidden();
    }

    public function test_editor_can_access_admin_panel(){

        // Adjust the role_id to whatever role should have access
        $user = User::factory()->editor()->create();

        $this->actingAs($user);

        $response = $this->get($this->getAdminURL());

        // Assert the user can access the panel
        $response->assertSuccessful();
    }

}
