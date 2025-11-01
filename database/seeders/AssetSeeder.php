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
        
        $asset_categories = AssetCategory::select('id','parent_id','name')->get();

        $faker = Factory::create();

        $asset_categories->each(function($category)use($faker){
            
            if(!$category->parent_id){
                
                $category->load('children');

                if($category->children->isNotEmpty()){
                    
                    return;
                }
                
                
            }
            
            $max = $faker->numberBetween(30, 400);

            $asset_container = [];

            for($i=0; $i <= $max; $i++){

                $longitude = $faker->randomFloat(6, -74.25909, -73.70018);
                $latitude = $faker->randomFloat(6, 40.4774, 40.9176);

                $asset_container[] = [ 
                    'asset_category_id' => $category->id,
                    'geometry' => new Point($latitude, $longitude, Srid::WGS84->value),
                    'data' => [
                        'name' => $faker->words(),
                        'data' => $faker->numberBetween(1, 3000),
                        'name_2' => $faker->words(),
                        'category' => $faker->words()
                    ]
                ];
            }

            Asset::insert($asset_container);
            
        });
      
    }
}
