<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Indicator;
use App\Models\Location;
use Faker\Factory;
use App\Models\Breakdown;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use App\Models\LocationType;


class DataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        $faker = Factory::create();

        $years = [2005, 2006, 2007, 2008, 2009, 2010, 2011, 2012, 2013, 2014,2015,2016,2017,2018,2019, 2020, 2021, 2022, 2023, 2024];

        $data_formats = [1,2,3];
        
        $breakdown_parents = Breakdown::whereNull('parent_id')->select('id')->get()->pluck('id')->all();
    
        $indicators = Indicator::select('id')->get()->toArray();

        $location_types = LocationType::all();
        $location_type_ids = $location_types->pluck('id')->toArray();
        
        // Fetch all locations once and group by type
        $all_locations = Location::select('id', 'location_type_id', 'is_uninhabited')->get()->groupBy('location_type_id');
        
        $totalInserted = 0;

        foreach($indicators as $index => $indicator){
        
            $rows = []; // Reset for each indicator - keeps memory usage constant!
            
            $data_formats_strategy = (array) Arr::random($data_formats, rand(1, 3)); 

            $breakdown_parents_strategy = Arr::random($breakdown_parents, rand(1, min(3, count($breakdown_parents))));
            
            $breakdowns = Breakdown::whereIn('parent_id', $breakdown_parents_strategy)->select('id')->get();

            $location_types_strategy = Arr::random($location_type_ids, rand(1, min(3, count($location_type_ids))));

            // Get locations from the cached collection instead of querying
            $locations = collect($location_types_strategy)
                ->flatMap(fn($type_id) => $all_locations->get($type_id, collect()))
                ->map(fn($loc) => [
                        'id' => $loc->id,
                        'is_uninhabited' => $loc->is_uninhabited
                    ])
                ->toArray();

            if($breakdowns->isEmpty()){
                
                $breakdowns_formatted = [['id' => $breakdown_parents_strategy[0]]];

            } else {

                $breakdowns_formatted = $breakdowns->toArray();

            }

            if (!collect($breakdowns_formatted)->contains('id', 1)) {
                $breakdowns_formatted[] = ['id' => 1];
            }

            $years_start_strategy = rand(0, count($years) - 1);
            $years_end_strategy = rand($years_start_strategy, count($years) - 1);
            $selected_years = array_slice($years, $years_start_strategy, $years_end_strategy - $years_start_strategy + 1);

            foreach($selected_years as $year){

                foreach($locations as $location){

                    if($location['is_uninhabited']){
                        
                        continue;

                    }

                    foreach($breakdowns_formatted as $breakdown){ 

                        foreach($data_formats_strategy as $data_format){
                        
                            $data = match ($data_format) {
                                1 => $faker->randomFloat(4, 0.01, 1.0),
                                2 => $faker->numberBetween(100, 299),             
                                3 => $faker->randomFloat(2, 1000, 50000),          
                            };

                            $now = date_create()->format('Y-m-d H:i:s');
                        
                            $rows[] = [
                                'data' => $data,
                                'timeframe' => $year,
                                'data_format_id' => $data_format,
                                'location_id' => $location['id'],
                                'indicator_id' => $indicator['id'],
                                'breakdown_id' => $breakdown['id'],
                                'is_published' => true,
                                'updated_at' => $now,
                                'created_at' => $now
                            ];

                        }
                    
                    };
                    
                };
            };
            
            // Insert this indicator's data immediately
            collect($rows)->chunk(2000)->each(function ($chunk) {
                DB::connection('supabase')->table('indicators.data')->insert($chunk->toArray());
            });
            
            $totalInserted += count($rows);
            
            // Optional: Progress logging
            if ($index % 10 === 0) {
                $this->command->info("Processed {$index} indicators, total rows: {$totalInserted}");
            }
        }
        
        $this->command->info("Complete! Total rows inserted: {$totalInserted}");
    }
}
