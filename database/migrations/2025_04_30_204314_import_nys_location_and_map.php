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
        $nys_path = base_path('database/maps/2025/nys_shoreline.json');
        $nys = json_decode(file_get_contents($nys_path));

        $location_type = LocationType::create([
            'name' => 'New York State',
            'plural_name' => 'New York State',
            'classification' => 'administrative',
            'scope' => 'state',
        ]);
        
        foreach ($nys->features as $district) {
            
            $location = $location_type->locations()->create([
                'fips' => $district->properties->FIPS_CODE,
                'name' => 'New York State',
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
