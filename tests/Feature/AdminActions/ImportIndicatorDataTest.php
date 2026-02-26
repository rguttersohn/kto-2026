<?php

namespace Tests\Feature;

use App\Filament\Imports\IndicatorDataImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Breakdown;
use App\Models\LocationType;
use App\Models\DataFormat;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use App\Filament\Resources\Indicators\RelationManagers\DataRelationManager;
use App\Models\Indicator;
use App\Filament\Resources\Indicators\Pages\EditIndicator;
use App\Models\User;
use Filament\Actions\Testing\TestAction;



class ImportIndicatorDataTest extends TestCase
{
    
    public function test_import_with_top_level_breakdown_as_a_filter(): void
    {   

        $user = User::where('role_id', 3)->first();
        
        $this->actingAs($user);

        $indicator = Indicator::withoutGlobalScopes()->first();

        $top_level_breakdown = Breakdown::whereNull('parent_id')
            ->whereDoesntHave('subBreakdowns')
            ->where('name', '!=', 'All')
            ->first();

        $location_type = LocationType::with('locations')->first();

        $timeframes = [2019, 2020, 2021];

        $format = DataFormat::first();

        $rows = "Location,TimeFrame,DataFormat,Data,Fips,Breakdown\n";

        foreach ($location_type->locations as $location) {
            foreach ($timeframes as $timeframe) {
                $rows .= implode(',', [
                    "\"{$location->name}\"",
                    $timeframe,
                    "\"{$format->name}\"",
                    rand(10, 90) . '.' . rand(0, 9),
                    $location->district_id ?: $location->fips,
                    "\"{$top_level_breakdown->name}\"",
                ]) . "\n";
            }
        }

        $file = UploadedFile::fake()->createWithContent('test-import.csv', $rows);

        Livewire::test(DataRelationManager::class, [
            'ownerRecord' => $indicator,
            'pageClass' => EditIndicator::class,
        ])  
            ->assertActionExists(TestAction::make('import')->table())
            ->callAction(TestAction::make('import')->table(), [
                'file' => $file,
                'breakdown_parent_id' => $top_level_breakdown->id,
                'use_legacy_district_id' => false,
                 'columnMap' => [
                    'data' => 'Data',
                    'dataFormat' => 'DataFormat',
                    'timeframe' => 'TimeFrame',
                    'fips/district_id' => 'Fips',
                    'breakdown' => 'Breakdown',
                ],
                
            ])->assertHasNoFormErrors();
            

        $this->assertDatabaseHas('indicators.data', [
            'indicator_id' => $indicator->id,
            'breakdown_id' => $top_level_breakdown->id,
        ]);

    }
}
