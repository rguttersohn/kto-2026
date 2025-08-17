<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\LocationType;
use App\Models\Location;
use Illuminate\Support\Carbon;
use App\Models\Geometry;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $nyc_lt = LocationType::create([
            'name' => 'New York City',
            'plural_name' => 'New York City',
            'classification' => 'administrative',
            'scope'=> 'local'
        ]);

        $nyc_lt->save();

        $nyc_path = base_path('database/maps/2025/nyc.json');
        $nyc = json_decode(file_get_contents($nyc_path));
        
        $nyc_location = Location::create([
            'fips' => 3651000,
            'name' => 'New York City',
            'location_type_id' => $nyc_lt->id,
            'valid_starting_on' => Carbon::now()
        ]);
        
        $nyc_location->save();

        $geojson = json_encode($nyc->geometries[0]);

        $geojson = json_encode($nyc->geometries[0]);

        $geometry = new Geometry();
        $geometry->location_id = $nyc_location->id;
        $geometry->valid_starting_on = Carbon::now();
        $geometry->type = 'MultiPolygon';
        $geometry->geometry = DB::raw("ST_SetSRID(ST_GeomFromGeoJSON('".$geojson."'), 4326)");
        $geometry->save();


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
