<?php

namespace Database\Seeders;

use App\Models\AssetCategory;
use Illuminate\Database\Seeder;
use App\Models\Asset;
use Faker\Factory;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Enums\Srid;
use App\Models\AssetSchema;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $asset_categories = AssetCategory::select('id','parent_id','name')->withoutGlobalScopes()->get();

        $faker = Factory::create();

        $asset_categories->each(function($category)use($faker){
            
            if(!$category->parent_id){
                
                $category->load('children');

                if($category->children->isNotEmpty()){
                    
                    return;
                }
                
                
            }
            
            $max = $faker->numberBetween(30, 400);


            for($i=0; $i <= $max; $i++){

                $longitude = $faker->randomFloat(6, -74.25909, -73.70018);
                $latitude = $faker->randomFloat(6, 40.4774, 40.9176);

                Asset::create([ 
                    'asset_category_id' => $category->id,
                    'geometry' => new Point($latitude, $longitude, Srid::WGS84->value),
                    'data' => [
                        'name' => $faker->word(),
                        'data' => $faker->numberBetween(1, 3000),
                        'name_2' => $faker->word(),
                        'category' => $faker->word()
                    ]
                ]);

            
            }

            $schema = [

                'name' => 'string',
                'data' => 'numeric',
                'name_2' => 'string',
                'category' => 'string'
            
            ];

            AssetSchema::create([
                'asset_category_id' => $category->id,
                'schema' => $schema
            ]);
            
            
        });
      
    }
}
