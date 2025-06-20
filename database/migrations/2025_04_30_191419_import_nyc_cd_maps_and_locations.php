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
        $nyc_cd_path = base_path('database/maps/2025/nycd.json');
        $nyc_cd = json_decode(file_get_contents($nyc_cd_path));

        $location_type = LocationType::create([
            'name' => 'Community District',
            'plural_name' => 'Community Districts',
            'classification' => 'administrative',
        ]);
        
        foreach ($nyc_cd->features as $district) {
            
            $location = $location_type->locations()->create([
                'geopolitical_id' => $district->properties->BoroCD,
                'name' => $district->properties->BoroCD,
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
