<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Enums\Srid;
use App\Models\AssetCategory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Asset>
 */
class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $longitude = $this->faker->randomFloat(6, -74.25909, -73.70018);
        $latitude = $this->faker->randomFloat(6, 40.4774, 40.9176);

        return [
            'description' => $this->faker->sentence(),
            'location' => new Point($latitude, $longitude, Srid::WGS84->value),
            'category_id' => fn () => AssetCategory::factory(), 
        ];
    }
}
