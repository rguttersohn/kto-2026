<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\LocationType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // $precinct_path = base_path('database/maps/2025/nypp.json');
        // $precincts = json_decode(file_get_contents($precinct_path));

        // $location_type = LocationType::create([
        //     'name' => 'New York City Police Precinct',
        //     'plural_name' => 'New York City Police Precincts',
        //     'classification' => 'administrative',
        //     'scope' => 'local',
        // ]);

        // foreach($precincts->features as $district){

        //      $location = $location_type->locations()->create([
                
        //         'district_id' => "P{$district->properties->Precinct}",
        //         'name' => $district->properties->Precinct,
        //         'valid_starting_on' => Carbon::now(),
        //         'legacy_district_id' => "P{$district->properties->Precinct}"

        //     ]);

        //     $location->save();

        //     $geometry = $location->geometry()->create([
                
        //         'location_id' => $location->id,
        //         'type' => $district->geometry->type,
        //         'geometry' => DB::raw("ST_GeomFromGeoJSON('".json_encode($district->geometry)."')"),
        //         'valid_starting_on' => Carbon::now()
                
        //     ]);

        //     $geometry->save();

        // }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
