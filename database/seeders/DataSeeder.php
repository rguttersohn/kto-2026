<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Indicator;
use App\Models\Location;
use Faker\Factory;
use App\Models\Breakdown;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use FFI\Exception;


class DataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   

        $faker = Factory::create();

        $years = [2020, 2021, 2022, 2023, 2024];

        $data_formats = [1,2,3];
        
        $breakdown_parents = Breakdown::whereNull('parent_id')->select('id')->get()->pluck('id')->all();
      

        $indicators = Indicator::select('id')->get()->toArray();

        $locations = Location::whereIn('location_type_id', [1,2,5])->select('id')->get()->toArray();
        
        $rows = [];

        foreach($indicators as $indicator){
           

            $data_formats_strategy = (array) Arr::random($data_formats, rand(1, 3)); 
           
            $breakdown_parents_strategy = Arr::random($breakdown_parents, rand(1, min(3, count($breakdown_parents))));
            
            $breakdowns = Breakdown::whereIn('parent_id', $breakdown_parents_strategy)->select('id')->get();

            if($breakdowns->isEmpty()){
                
                $breakdowns_formatted = [['id' => $breakdown_parents_strategy[0]]];

            } else {

                $breakdowns_formatted = $breakdowns->toArray();

            }

            foreach($years as $year){

                foreach($locations as $location){

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
            
        }
        
        collect($rows)->chunk(1000)->each(function ($chunk) {
           
            DB::connection('supabase')->table('indicators.data')->insert($chunk->toArray());
           
        });
        
    }
}
