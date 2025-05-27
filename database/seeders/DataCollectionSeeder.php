<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Collection;
use App\Models\DataCollection;
use Illuminate\Support\Facades\DB;
use Faker\Factory;


class DataCollectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $stop_and_frisk_path = base_path('database/collections/2025/stop_and_frisk_under_18_only.json');

        $stop_and_frisk_data = json_decode(file_get_contents($stop_and_frisk_path));

        $faker = Factory::create();

        $data_collection = Collection::create([
            'name' => 'Stop, Frisk, and Question of New York City Children',
            'description' => 'A table showing the stop, frisk, and questioning of the New York City Population under 18',
            'is_published' => true
        ]);


        foreach($stop_and_frisk_data as $d){

            DataCollection::create([
                'collection_id' => $data_collection->id,
                'geometry' => DB::raw("st_transform(st_setsrid(st_makepoint({$d->STOP_LOCATION_X},{$d->STOP_LOCATION_Y}), 2263), 4326)"),
                'data' => $d,
                'is_published' => $faker->boolean(95)
            ]);
            
        }


        $student_homelessness_path = base_path('database/collections/2025/students_in_temporary_housing_2021.json');

        $student_homelessness_data = json_decode(file_get_contents($student_homelessness_path));

        $sh_data_collection = Collection::create([
            'name' => 'Student in Temporary Housing per New York City School',
            'description' => 'Students in temporary housing (STH) are defined as students experiencing housing instability at any point, for any length of time, during the school year (from the first day of school to 7/2). This includes students and families that are "doubled up" (sharing the housing of others due to economic hardship), living in shelter (including NYC Department of Homeless Services family shelters or Human Resources Administration domestic violence shelters), or living in some other unstable, temporary housing. There were approximately 87,000 New York City district school students who resided in temporary housing in the 2020-21 school year, with about two thirds of them residing in doubled up living arrangements. Approximately 9,500 of those 87,000 students were residing in the DHS shelter system on any given night. The DOE works in close partnership with the Department of Homeless Services to provide streamlined support for students in shelter throughout each day.',
            'is_published' => true
        ]);


        foreach($student_homelessness_data as $d){
          
            $d->year = 2021;

            DataCollection::create([
                'collection_id' => $sh_data_collection->id,
                'geometry' => $d->x ? DB::raw("st_transform(st_setsrid(st_makepoint({$d->x},{$d->y}), 26918), 4326)") : null,
                'data' => $d,
                'is_published' => $faker->boolean(95)
            ]);
            
        }



        
    }
}
