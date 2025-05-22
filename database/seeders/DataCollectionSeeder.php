<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DataCollection;

class DataCollectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $stop_and_frisk_path = base_path('database/collections/2025/stop_and_frisk_under_18_only.json');

        $stop_and_frisk_data = file_get_contents($stop_and_frisk_path);

        DataCollection::create([
            'name' => 'Stop, Frisk, and Question of New York City Children',
            'description' => 'A table showing the stop, frisk, and questioning of the New York City Population under 18',
            'data' => $stop_and_frisk_data
        ]);

        
    }
}
