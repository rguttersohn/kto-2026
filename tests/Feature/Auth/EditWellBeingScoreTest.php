<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Exception;
use Livewire\Livewire;
use App\Filament\Resources\WellBeingScores\Pages\EditWellBeingScore;
use App\Filament\Resources\WellBeingScores\Pages\ListWellBeingScores;
use App\Models\WellBeingScore;
use Filament\Actions\Testing\TestAction;



class EditWellBeingScoreTest extends TestCase {

    public function test_non_admin_cannot_publish_well_being_score(){

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
        
        $well_being_score->update([
            'is_published' => !$original_published_status
        ]);

        $well_being_score->refresh();

        $this->assertEquals($original_published_status, $well_being_score->is_published);

    }


    public function test_is_published_toggle_is_disabled_well_being_score_page(){

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

    public function test_non_admin_cannot_delete_well_being_score(){

        $user = User::where('role_id', 2)->first();

        if(!$user){

            throw new Exception('User id of 2 not in db. Did you forget to seed users?');
        }

        $this->actingAs($user);

        $well_being_score = WellBeingScore::first();

        if(!$well_being_score){

            $well_being_score = WellBeingScore::factory()->create();
        }

        $this->assertFalse($user->can('delete', $well_being_score));
    }

    public function test_non_admin_cannot_bulk_publish_score(){

        $user = User::where('role_id', 2)->first();

        if(!$user){

            $user = User::factory()->create([
                'role_id' => 2
            ]);
        }

        $this->actingAs($user);

        $scores = WellBeingScore::factory()->count(3)->create();

        Livewire::test(ListWellBeingScores::class)
            ->selectTableRecords($scores->pluck('id')->toArray())
            ->assertActionHidden(TestAction::make('set_published')->table()->bulk())
            ->assertActionHidden(TestAction::make('set_unpublished')->table()->bulk());

    }

    public function test_admin_can_bulk_publish_score(){
        
        $user = User::where('role_id', 3)->first();

        if(!$user){

            $user = User::factory()->create([
                'role_id' => 3
            ]);
        }

        $this->actingAs($user);

        $scores = WellBeingScore::factory()->count(3)->create();

        Livewire::test(ListWellBeingScores::class)
            ->selectTableRecords($scores->pluck('id')->toArray())
            ->assertActionVisible(TestAction::make('set_published')->table()->bulk())
            ->assertActionVisible(TestAction::make('set_unpublished')->table()->bulk());
    }
}
