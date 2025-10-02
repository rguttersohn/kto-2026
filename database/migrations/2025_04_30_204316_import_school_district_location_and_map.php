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
        $sd_path = base_path('database/maps/2025/nysd.json');
        $school_districts = json_decode(file_get_contents($sd_path));

        $location_type = LocationType::create([
            'name' => 'New York City School District',
            'plural_name' => 'New York City School Districts',
            'classification' => 'administrative',
            'scope' => 'local',
        ]);


        foreach($school_districts->features as $district){

            $location = $location_type->locations()->create([
                
                'district_id' => "SD{$district->properties->SchoolDist}",
                'name' => $district->properties->SchoolDist,
                'valid_starting_on' => Carbon::now(),
                'legacy_district_id' => "SD{$district->properties->SchoolDist}"

            ]);

            $location->save();

            $geometry = $location->geometry()->create([
                
                'location_id' => $location->id,
                'type' => $district->geometry->type,
                'geometry' => DB::raw("ST_GeomFromGeoJSON('".json_encode($district->geometry)."')"),
                'valid_starting_on' => Carbon::now()

            ]);

            $geometry->save();

        }

        

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
