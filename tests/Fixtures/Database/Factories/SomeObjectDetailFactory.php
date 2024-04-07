<?php declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Language;
use App\Enums\Region;
use App\Models\SomeObjectDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SomeObjectDetail>
 */
class SomeObjectDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'count' => fake()->numberBetween(1, 100),
            'name' => fake()->unique()->words(3, true),
            'region' => Region::getRandomValue(),
            'language' => Language::getRandomValue(),
            'description' => fake()->sentence(),
        ];
    }
}
