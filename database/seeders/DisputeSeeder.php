<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dispute;
use App\Models\User;

class DisputeSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $merchants = User::where('role', 'merchant')->get();

        if ($customers->isEmpty() || $merchants->isEmpty()) return;

        foreach ($customers as $customer) {
            Dispute::create([
                'claimant_id' => $customer->id,
                'respondent_id' => $merchants->random()->id,
                'reason' => 'Item not received',
                'status' => 'open',
            ]);
        }
    }
}