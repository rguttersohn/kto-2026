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
use Filament\Actions\Testing\TestAction;

class EditIndicatorDataRelationManagerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_non_admin_cannot_delete_indicator_data(): void
    {
        
        $indicator = Indicator::first();
        
        $indicator_data = IndicatorData::where('indicator_id', $indicator->id)->first();

        if(!$indicator_data){

            $indicator_data = IndicatorData::factory()->create();
        }

        $user = User::where('role_id', 2)->first();

        if(!$user){

            throw new Exception('No user with role id of 2. Did you forget to seed the user table?');
        }

        $this->actingAs($user);

        LiveWire::test(DataRelationManager::class, [
            'ownerRecord' => $indicator,
            'pageClass' => IndicatorResource::class
        ])
            ->assertActionHidden(TestAction::make('delete')->table()->bulk());
    }
}
