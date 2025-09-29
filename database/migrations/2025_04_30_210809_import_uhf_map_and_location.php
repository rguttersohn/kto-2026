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
        $uhf_path = base_path('database/maps/2025/uhf.json');
        $uhf = json_decode(file_get_contents($uhf_path));

        $location_type = LocationType::create([
            'name' => 'United Hospital Fund',
            'plural_name' => 'United Hospital Fund Districts',
            'classification' => 'administrative',
            'scope' => 'local',
        ]);
        
        foreach ($uhf->features as $district) {
            
            $location = $location_type->locations()->create([
                'district_id' => "uhf{$district->properties->UHFCODE}",
                'name' => $district->properties->UHF_NEIGH,
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

