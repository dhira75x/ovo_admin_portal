<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 10 Customers
        User::factory()->count(10)->create(['role' => 'customer']);

        // 5 Merchants
        User::factory()->count(5)->create(['role' => 'merchant']);
        
        // Specific users for testing
        User::create([
            'name' => 'Test Customer',
            'email' => 'customer@test.com',
            'password' => 'password',
            'role' => 'customer'
        ]);
        
        User::create([
            'name' => 'Test Merchant',
            'email' => 'merchant@test.com',
            'password' => 'password',
            'role' => 'merchant'
        ]);
    }
}