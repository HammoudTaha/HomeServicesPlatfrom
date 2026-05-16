<?php

namespace Database\Factories;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;


class ServiceCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->word();
        $slug = \Illuminate\Support\Str::slug($name);
        return [
            'name' => $name,
            'slug' => $slug,
            'commission' => $this->faker->randomFloat(2, 0, 100),
            'is_active' => $this->faker->boolean(50),
        ];
    }
}
