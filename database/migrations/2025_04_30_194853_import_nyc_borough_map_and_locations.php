<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\LocationType;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $nyc_bb_path = base_path('database/maps/2025/nybb.json');
        $nyc_bb = json_decode(file_get_contents($nyc_bb_path));

        $location_type = LocationType::create([
            'name' => 'Borough',
            'plural_name' => 'Boroughs',
            'classification' => 'administrative',
        ]);
        
        foreach ($nyc_bb->features as $district) {
            
            $location = $location_type->locations()->create([
                
                'district_id' => "bb{$district->properties->BoroCode}",
                'name' => $district->properties->BoroName,
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
