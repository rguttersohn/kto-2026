<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Exception;
use Livewire\Livewire;
use App\Filament\Resources\Users\Pages\ListUsers;

class EditUserTest extends TestCase
{
    public function test_nonadmin_cannot_delete_user(){
        
        $user = User::where('role_id', 2)->first();
        $target_user = User::factory()->create();

        if(!$user){

            throw new Exception('User id of 2 not in db. Did you forget to seed users?');
        };

        $this->actingAs($user);

        $result = $user->can('delete', $user);

        $this->assertFalse($user->can('delete', $target_user));

    }

    public function test_admin_can_delete_user(){

        $user = User::where('role_id', '>', 2)->first();

        $target = User::where('role_id', 1)->first();

        if(!$user){

            throw new Exception('Admin user not in db. Did you forget to seed users?');
        };

        $this->actingAs($user);

        $this->assertTrue($user->can('delete', $target));

    }

    public function test_nonadmin_cannot_update_user(){

        $user = User::where('role_id', 2)->first();

        if(!$user){

            throw new Exception('User id of 2 not in db. Did you forget to seed users?');
        }

        $this->actingAs($user);

        $this->assertFalse($user->can('update', $user));
    
    }

    public function test_nonadmin_cannot_see_user_list(){

        $user = User::where('role_id', 2)->first();

        if(!$user){

            throw new Exception('User id of 2 not in db. Did you forget to seed users?');
        }

        $this->actingAs($user);

        $this->assertFalse($user->can('viewAny', User::class));

        Livewire::test(ListUsers::class)
            ->assertForbidden();

    }
}
