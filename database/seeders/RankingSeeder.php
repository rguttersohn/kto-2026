<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Location;
use App\Models\Ranking;
use Faker\Factory;
use App\Models\Category;
use App\Models\CategoryIndicator;

class RankingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = Location::whereIn('location_type_id', [3,5])->get();

        $categories = Category::whereNull('parent_id')->get();

        $years = ['2020','2021', '2022','2023', '2024'];
        
        $faker = Factory::create();

        $locations->each(function($location)use($faker, $categories, $years){

            $categories->each(function($category)use($faker, $location, $years){

                collect($years)->each(fn($year)=>Ranking::create([
                    'location_id' => $location->id,
                    'year' => $year,
                    'score' => $faker->randomFloat(4, -2.0, 2.0),
                    'indicator_category_id' => $category->id
                ]));
            
            });
            
        });

        $categories->each(function($category){

            $subcategories = Category::
                where('parent_id', $category->id)
                ->with('indicators')
                ->get();

            $subcategories->each(function($subcategory)use($category){

                CategoryIndicator::create(
                    [
                        'category_id' => $category->id,
                        'indicator_id' => $subcategory->indicators->first()->id
                    ]
                    );
            
            });

        });
    }
}
