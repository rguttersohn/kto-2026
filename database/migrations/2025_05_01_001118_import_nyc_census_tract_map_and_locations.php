<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\LocationType;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Location;
use App\Models\Geometry;

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

        $location_record = [];

        $geometry_record = [];
        
        foreach ($nyc_ct->features as $district) {
            
            $location_record[] = [
                'location_type_id' => $location_type->id,
                'fips' => $district->properties->GEOID,
                'name' => $district->properties->CTLabel,
                'valid_starting_on' => Carbon::now()
            ];

        }

        Location::insert($location_record);

        $locations = Location::where('location_type_id', $location_type->id)->get();
        
        $locations->each(function($location, $key)use($nyc_ct, $geometry_record){

            $current_tract = $nyc_ct->features[$key];

            $geometry_record[] = [
                'location_id' => $location->id,
                'type' => $current_tract->geometry->type,
                'geometry' => DB::raw("ST_GeomFromGeoJSON('".json_encode($current_tract->geometry)."')"),
                'valid_starting_on' => Carbon::now()
            ];


        });

        Geometry::insert($geometry_record);
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

