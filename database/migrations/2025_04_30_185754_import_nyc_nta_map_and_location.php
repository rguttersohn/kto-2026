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
        $nyc_nta_path = base_path('database/maps/2025/nynta2020.json');
        $nyc_nta = json_decode(file_get_contents($nyc_nta_path));

        $location_type = LocationType::create([
            'name' => 'Neighborhood Tabulation Area',
            'plural_name' => 'Neighborhood Tabulation Areas',
            'classification' => 'statistical',
        ]);

        foreach ($nyc_nta->features as $district) {
            
            $location = $location_type->locations()->create([
                'geopolitical_id' => $district->properties->CDTA2020,
                'name' => $district->properties->NTAName,
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
