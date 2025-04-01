<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organizer>
 */
class OrganizerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $organizerId = User::where('role', 'organizer')->pluck('id')->toArray();


        return [
        "user_id" => $this->faker->randomElement($organizerId),
        "organization_name" => $this->faker->company(),
        "business_type"=> $this->faker->randomElement(["Sole Proprietorship", "Partnership", "Corporation"]),
        'description' => fake()->paragraph(),
        "address" => $this->faker->address(),
        "event_categories" => $this->faker->words(3, true),
        "years_of_experience" => $this->faker->numberBetween(1, 20),
        "portfolio" => $this->faker->url(),
        "bank_account_details"=> $this->faker->bankAccountNumber(),
        "verification_documents" => $this->faker->imageUrl()
        ];
    }
}
