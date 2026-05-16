<?php

namespace Database\Factories;

use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Provider>
 */
class ProviderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'phone' => $this->faker->unique()->numerify('09########'),
            'password' => bcrypt('password'),
            'service_category_id' => \App\Models\ServiceCategory::inRandomOrder()->value('id'),
            'rating' => $this->faker->randomFloat(1, 0, 5),
            'rating_count' => $this->faker->numberBetween(0, 100),
            'is_available' => $this->faker->boolean(),
            'is_active' => $this->faker->boolean(),
            'description' => $this->faker->paragraph(),
            'email' => $this->faker->unique()->safeEmail(),
            'experience_years' => $this->faker->numberBetween(0, 30),
        ];
    }
}
