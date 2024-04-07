<?php declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Language;
use App\Enums\Region;
use App\Models\SomeObject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SomeObject>
 */
class SomeObjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'region' => Region::getRandomValue(),
            'language' => Language::getRandomValue(),
            'description' => fake()->sentence(),
        ];
    }
}
