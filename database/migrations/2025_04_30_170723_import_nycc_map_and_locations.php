<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\LocationType;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {   

        $city_council_path = base_path('database/maps/2025/nycc.json');

        $city_council = json_decode(file_get_contents($city_council_path));

        $location_type = LocationType::create([
            'name' => 'City Council District',
            'plural_name' => 'City Council Districts',
            'classification' => 'political',
        ]);

        foreach($city_council->features as $district){

            $location = $location_type->locations()->create([
                'geopolitical_id' => $district->properties->CounDist,
                'name' => $district->properties->CounDist,
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
        
    }
};
