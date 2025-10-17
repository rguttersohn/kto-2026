<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Indicator;
use App\Filament\Resources\Indicators\Pages\EditIndicator;
use Exception;
use Livewire\Livewire;

class EditIndicatorTest extends TestCase {

    public function test_non_admin_cannot_publish_indicator():void{

        $user = User::where('role_id', 2)->first();

        if(!$user){

            throw new Exception('User id of 2 not in db. Did you forget to seed users?');
        }

        $this->actingAs($user);

        $indicator = Indicator::first();

        $original_published_status = $indicator->is_published;

        $indicator->is_published = !$indicator->is_published;

        Livewire::test(EditIndicator::class, [
            'record' => $indicator->getRouteKey(),
            ])
            ->fillForm([
            'is_published' => !$indicator->is_published,
            ])
            ->call('save')
            ->assertHasNoErrors();

            $indicator->refresh();

            $this->assertEquals($original_published_status, $indicator->is_published);

    }


    public function test_is_published_toggle_is_disabled_indicator_page(){

        $user = User::where('role_id', 2)->first();

        if(!$user){

            throw new Exception('User id of 2 not in db. Did you forget to seed users?');
        }

        $this->actingAs($user);

        $indicator = Indicator::first();

        Livewire::test(EditIndicator::class, [
            'record' => $indicator->getRouteKey()
            ])
            ->assertFormFieldDisabled('is_published');

    }


    public function test_non_admin_cannot_see_delete_button_on_indicator_page(){

        $user = User::where('role_id', 2)->first();

        $this->actingAs($user);

        if(!$user){

            throw new Exception('User id of 2 not in db. Did you forget to seed users?');
        }

        $indicator = Indicator::first();


        Livewire::test(EditIndicator::class, [
            'record' => $indicator->getRouteKey()
        ])
            ->assertActionHidden('delete');
    }

}
