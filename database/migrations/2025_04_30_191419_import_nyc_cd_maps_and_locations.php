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
        $cd_labels_path = base_path('database/maps/2025/cd-labels.json');

        $nyc_cd = json_decode(file_get_contents($nyc_cd_path));

        $cd_labels = json_decode(file_get_contents($cd_labels_path));
        
        $location_type = LocationType::create([
            'name' => 'Community District',
            'plural_name' => 'Community Districts',
            'classification' => 'administrative',
            'is_rankable' => true,
            'has_community_profile' => true
        ]);

        function formatCDIDFromCDTAMap(object $properties): string
        {
            return $properties->BoroCode . substr($properties->CDTA2020, 2);
        }
        
        foreach ($nyc_cd->features as $district) {

            $cd_label = array_find($cd_labels, fn($label)=>$label->CD_NAME === $district->properties->CDTA2020);
            dump($cd_label);
            $location = $location_type->locations()->create([
                'district_id' => $cd_label ? $cd_label->CD : formatCDIDFromCDTAMap($district->properties),
                'name' => $cd_label ? $cd_label->Location : 'Park/Uninhabited',
                'valid_starting_on' => Carbon::now(),
                'legacy_district_id' => $cd_label ? $cd_label->CD : formatCDIDFromCDTAMap($district->properties),
                'is_uninhabited' => $district->properties->CDTAType !== '0' ? true : false

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
