<?php

namespace Tests\Feature;

use App\Filament\Resources\Indicators\IndicatorResource;
use Tests\TestCase;
use App\Models\IndicatorData;
use App\Models\Indicator;
use App\Models\User;
use Livewire\Livewire;
use App\Filament\Resources\Indicators\RelationManagers\DataRelationManager;
use Exception;

class EditIndicatorDataRelationManagerTest extends TestCase
{
    
    public function test_non_admin_cannot_publish_indicator_data():void{

        $user = User::where('role_id', 2)->first();

        if(!$user){

            throw new Exception('User id of 2 not in db. Did you forget to seed users?');
        }

        $this->actingAs($user);

        $indicator = Indicator::first();

        $indicator_data = IndicatorData::withoutGlobalScopes()->where('indicator_id', $indicator->id)->first();

        if(!$indicator_data){

            $indicator_data = IndicatorData::factory()->create([
                'indicator_id' => $indicator->id
            ]);
        }

        $original_published_status = $indicator->is_published;


        $indicator_data->update([
            'is_published' => !$original_published_status
        ]);

        $indicator_data->refresh();

        $this->assertEquals($original_published_status, $indicator->is_published);

    }

    public function test_non_admin_cannot_delete_indicator_data(){

        $user = User::where('role_id', 2)->first();

        $this->actingAs($user);

        if(!$user){

            throw new Exception('User id of 2 not in db. Did you forget to seed users?');
        }

        $indicator = Indicator::first();

        $indicator_data = IndicatorData::withoutGlobalScopes()->where('indicator_id', $indicator->id)->first();

        if(!$indicator_data){

            $indicator_data = IndicatorData::factory()->create([
                'indicator_id' => $indicator->id
            ]);
        }

        $indicator_data->delete();

        $this->assertDatabaseHas('indicators.data', [
            'indicator_id' => $indicator->id
        ]);

    }


    public function test_is_published_toggle_is_disabled_indicator_page(){

        $user = User::where('role_id', 2)->first();

        if(!$user){

            throw new Exception('User id of 2 not in db. Did you forget to seed users?');
        }

        $this->actingAs($user);

        $indicator = Indicator::first();

        $indicator_data = IndicatorData::withoutGlobalScopes()->where('indicator_id', $indicator->id)->first();

        if(!$indicator_data){

            $indicator_data = IndicatorData::factory()->create([
                'indicator_id' => $indicator->id
            ]);

        }

        LiveWire::test(DataRelationManager::class, [
            'ownerRecord' => $indicator,
            'pageClass' => IndicatorResource::class
        ])
            ->mountTableAction('edit', $indicator_data)
            ->assertFormFieldDisabled('is_published');

    }

}
