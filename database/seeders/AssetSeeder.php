<?php

namespace Database\Seeders;

use App\Models\AssetCategory;
use Illuminate\Database\Seeder;
use App\Models\Asset;
use Faker\Factory;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Enums\Srid;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $asset_categories = AssetCategory::select('id')->get();

        $faker = Factory::create();

        $asset_categories->each(function($category)use($faker){

            for($i=0; $i < 1000; $i++){

                $longitude = $faker->randomFloat(6, -74.25909, -73.70018);
                $latitude = $faker->randomFloat(6, 40.4774, 40.9176);

                Asset::create([
                    'asset_category_id' => $category->id,
                    'location' => new Point($latitude, $longitude, Srid::WGS84->value),
                    'description' => $faker->text(100)
                ]);
            }
        });
      
    }
}
