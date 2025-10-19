<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Indicator;
use App\Models\Location;
use App\Models\Breakdown;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\IndicatorData>
 */
class IndicatorDataFactory extends Factory
{

    protected function getIndicatorID():array{

        return Indicator::first()->pluck('id')->toArray();
    }

    protected function getDataFormatID():array{

        return Indicator::first()->pluck('id')->toArray();

    }

    protected function getLocationID():array{

        return Location::first()->pluck('id')->toArray();
    }

    protected function getBreakdownID():array{

        return Breakdown::first()->pluck('id')->toArray();
    }
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $indicator_id = $this->getIndicatorID();

        $data_format_id = $this->getDataFormatID();

        $location_id = $this->getLocationID();

        $breakdown_id = $this->getBreakdownID();

        return [
            'data' => fake()->numberBetween(1, 100),
            'indicator_id' => $indicator_id[0],
            'data_format_id' => $data_format_id[0],
            'timeframe' => 2021,
            'location_id' => $location_id[0],
            'breakdown_id' => $breakdown_id[0],
            'is_published' => false,
            
        ];
    }
}
