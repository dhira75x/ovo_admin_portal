<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;
use App\Models\Order;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $merchants = User::where('role', 'merchant')->get();
        $customers = User::where('role', 'customer')->get();
        $orders = Order::all();

        if ($merchants->isEmpty() || $customers->isEmpty()) {
            return;
        }

        $comments = [
            'Excellent service and fast shipping!',
            'Great product quality, will order again.',
            'Good communication from the seller.',
            'Item arrived as described.',
            'Very satisfied with my purchase.',
            'Fast delivery and well packaged.',
            'Amazing quality for the price!',
            'Helpful seller, quick response.',
            'Would definitely recommend this merchant.',
            'Product exceeded my expectations.',
            'Shipping took a bit long but product is good.',
            'Average experience, nothing special.',
            'Had some issues but seller resolved them.',
            'Product was okay, not great.',
            'Decent quality for the price.',
        ];

        foreach ($merchants as $merchant) {
            // Each merchant gets 2-8 reviews
            $reviewCount = rand(2, 8);
            
            for ($i = 0; $i < $reviewCount; $i++) {
                $customer = $customers->random();
                $order = $orders->where('user_id', $customer->id)->first();
                
                // Weighted random rating (more 4-5 stars)
                $ratingWeights = [5, 5, 5, 4, 4, 4, 3, 3, 2, 1];
                $rating = $ratingWeights[array_rand($ratingWeights)];
                
                Review::create([
                    'merchant_id' => $merchant->id,
                    'customer_id' => $customer->id,
                    'order_id' => $order?->id,
                    'rating' => $rating,
                    'comment' => rand(0, 2) ? $comments[array_rand($comments)] : null,
                ]);
            }
        }
    }
}
