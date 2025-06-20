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
        $nyc_ct_path = base_path('database/maps/2025/nyct2020.json');
        $nyc_ct = json_decode(file_get_contents($nyc_ct_path));

        $location_type = LocationType::create([
            'name' => 'Census Tract',
            'plural_name' => 'Census Tracts',
            'classification' => 'statistical',
            'scope' => 'local',
        ]);
        
        foreach ($nyc_ct->features as $district) {
            $location = $location_type->locations()->create([
                'fips' => $district->properties->GEOID,
                'name' => $district->properties->CTLabel,
                'valid_starting_on' => Carbon::now()
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

