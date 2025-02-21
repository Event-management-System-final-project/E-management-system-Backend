<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Testiomonial>
 */
class TestimonialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
    
            return [
                'user_id' => User::factory(),
                'rating' => fake()->numberBetween(1,5),
                'content' => fake()->paragraph(), 
            ];
        
    }
}
