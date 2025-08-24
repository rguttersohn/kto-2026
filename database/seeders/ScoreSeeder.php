<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Location;
use App\Models\WellBeingScore;
use Faker\Factory;
use App\Models\Domain;
use App\Models\IndicatorCategory;
use App\Models\WellBeingDomainIndicator;

class ScoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = Location::whereIn('location_type_id', [3,6])->get();
        
        $domains = Domain::where('is_rankable', true)->get();

        $years = collect(['2020','2021', '2022','2023', '2024']);
        
        $faker = Factory::create();

        $well_being_container = [];

        $locations->each(function($location)use($faker, $domains, $years, &$well_being_container){

            $domains->each(function($domain)use($faker, $location, $years, &$well_being_container){

                $years->each(function($year)use($location, $faker, $domain, &$well_being_container){
                    
                    $well_being_container[] = [
                        'location_id' => $location->id,
                        'year' => $year,
                        'score' => $faker->randomFloat(4, -2.0, 2.0),
                        'domain_id' => $domain->id
                    ];

                });
            
            });
            
        });
        

        WellBeingScore::insert($well_being_container);

        $domain_indicator_container = [];

        $domains->each(function($domain)use(&$domain_indicator_container){

            $categories = IndicatorCategory::where('domain_id', $domain->id)->with('indicators')->get();
            
            $categories->each(function($subcategory)use($domain, &$domain_indicator_container){

                $domain_indicator_container[] = [ 
                    'domain_id' => $domain->id,
                    'indicator_id' => $subcategory->indicators->first()->id
                ];
                
            });

        });
        
        WellBeingDomainIndicator::insert($domain_indicator_container);

    }
}
