<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Location;
use App\Models\WellBeingRanking;
use Faker\Factory;
use App\Models\Domain;
use App\Models\IndicatorCategory;
use App\Models\WellBeingDomainIndicator;

class RankingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = Location::whereIn('location_type_id', [3,5])->get();

        $domains = Domain::where('is_rankable', true)->get();

        $years = ['2020','2021', '2022','2023', '2024'];
        
        $faker = Factory::create();

        $locations->each(function($location)use($faker, $domains, $years){

            $domains->each(function($domain)use($faker, $location, $years){

                collect($years)->each(fn($year)=>WellBeingRanking::create([
                    'location_id' => $location->id,
                    'year' => $year,
                    'score' => $faker->randomFloat(4, -2.0, 2.0),
                    'domain_id' => $domain->id
                ]));
            
            });
            
        });

        $domains->each(function($domain){

            $categories = IndicatorCategory::where('domain_id', $domain->id)->with('indicators')->get();
            
            $categories->each(function($subcategory)use($domain){

                WellBeingDomainIndicator::create(
                        [
                            'domain_id' => $domain->id,
                            'indicator_id' => $subcategory->indicators->first()->id
                        ]
                    );
            
            });

        });
    }
}
