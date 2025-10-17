<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Exception;
use Livewire\Livewire;
use App\Filament\Resources\WellBeingScores\Pages\EditWellBeingScore;
use App\Models\WellBeingScore;

class EditWellBeingScoreTest extends TestCase {


    public function test_non_admin_cannot_publish_asset_category(){

        $user = User::where('role_id', 2)->first();

        if(!$user){

            throw new Exception('User id of 2 not in db. Did you forget to seed users?');
        }

        $this->actingAs($user);

        $well_being_score = WellBeingScore::first();
        
        if(!$well_being_score){

            $well_being_score = WellBeingScore::factory()->create();
        }

        $original_published_status = $well_being_score->is_published;

        $well_being_score->is_published = !$well_being_score->is_published;

        Livewire::test(EditWellBeingScore::class, [
                'record' => $well_being_score->getRouteKey(),
            ])
            ->fillForm([
                'is_published' => !$well_being_score->is_published,
            ])
            ->call('save')
            ->assertHasNoErrors();

            $well_being_score->refresh();

            $this->assertEquals($original_published_status, $well_being_score->is_published);

    }


    public function test_is_published_toggle_is_disabled_asset_category_page(){

        $user = User::where('role_id', 2)->first();

        if(!$user){

            throw new Exception('User id of 2 not in db. Did you forget to seed users?');
        }

        $this->actingAs($user);

        $well_being_score = WellBeingScore::first();

        if(!$well_being_score){

            $well_being_score = WellBeingScore::factory()->create();
        }

        Livewire::test(EditWellBeingScore::class, [
            'record' => $well_being_score->getRouteKey()
            ])
            ->assertFormFieldDisabled('is_published');

    }

    public function test_non_admin_cannot_see_delete_button_on_asset_category_page(){

        $user = User::where('role_id', 2)->first();

        if(!$user){

            throw new Exception('User id of 2 not in db. Did you forget to seed users?');
        }

        $this->actingAs($user);

        $well_being_score = WellBeingScore::first();

        if(!$well_being_score){

            $well_being_score = WellBeingScore::factory()->create();
        }

        Livewire::test(EditWellBeingScore::class, [
            'record' => $well_being_score->getRouteKey()
            ])
            ->assertActionHidden('delete');
    }
}
