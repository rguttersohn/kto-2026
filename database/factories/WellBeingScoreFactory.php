<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Services\WellBeingService;
use App\Models\Location;
use App\Models\Domain;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WellBeingScore>
 */
class WellBeingScoreFactory extends Factory
{   
    protected function getLocationIDs(): array 
    {
        $location_types = WellBeingService::queryRankableLocationTypes();
        $location_type_ids = $location_types->pluck('id')->toArray();
        
        return Location::whereIn('location_type_id', $location_type_ids)
            ->pluck('id')
            ->toArray();
    }

    protected function getDomainIDs(): array
    {
        return Domain::where('is_rankable', true)
            ->pluck('id')
            ->toArray();
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $location_ids = $this->getLocationIDs();
        $domain_ids = $this->getDomainIDs();

        return [
            'domain_id' => $domain_ids[array_rand($domain_ids)], 
            'score' => fake()->randomFloat(2, -1, 1),
            'location_id' => $location_ids[array_rand($location_ids)],
            'timeframe' => 2021,
            'is_published' => false,
        ];
    }
}