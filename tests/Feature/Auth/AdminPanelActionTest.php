<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Indicator;
use App\Filament\Resources\Indicators\Pages\EditIndicator;
use Livewire\Livewire;

class AdminPanelActionTest extends TestCase {


    public function test_non_admin_cannot_publish_indicator():void{

        $user = User::factory()->user()->make();

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


    // public function test_non_admin_cannot_delete_indicator(){

    //     $user = User::factory()->editor();

        

    // }

}
