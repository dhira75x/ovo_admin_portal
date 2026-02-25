<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KycSubmission;
use App\Models\User;

class KycSubmissionSeeder extends Seeder
{
    public function run(): void
    {
        $merchants = User::where('role', 'merchant')->get();

        foreach ($merchants as $merchant) {
            KycSubmission::create([
                'user_id' => $merchant->id,
                'document_type' => 'passport',
                'document_path' => 'uploads/kyc/sample.jpg', // Mock path
                'status' => 'pending',
            ]);
        }
    }
}