<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SupportTicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'subject' => fake()->sentence(),
            'status' => fake()->randomElement(['open', 'in_progress', 'resolved', 'closed']),
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
        ];
    }
}