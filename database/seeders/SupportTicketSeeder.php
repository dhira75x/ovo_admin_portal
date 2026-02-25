<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SupportTicket;
use App\Models\TicketMessage;
use App\Models\User;

class SupportTicketSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // Create 1-3 tickets per user
            $tickets = SupportTicket::factory()->count(rand(1, 3))->create([
                'user_id' => $user->id,
                'status' => fake()->randomElement(['open', 'in_progress', 'resolved']),
                'priority' => fake()->randomElement(['low', 'medium', 'high']),
            ]);

            foreach ($tickets as $ticket) {
                // Initial message from user
                TicketMessage::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'message' => fake()->paragraph(),
                ]);

                // Randomly add an admin reply
                if (rand(0, 1)) {
                    TicketMessage::create([
                        'ticket_id' => $ticket->id,
                        'admin_id' => 1, // Assuming admin ID 1 exists
                        'message' => fake()->paragraph(),
                    ]);
                }
            }
        }
    }
}