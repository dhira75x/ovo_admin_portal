<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();

        if ($customers->isEmpty()) {
            return;
        }

        $products = [
            ['name' => 'Wireless Headphones', 'price' => 79.99],
            ['name' => 'Smart Watch', 'price' => 199.99],
            ['name' => 'Laptop Stand', 'price' => 45.00],
            ['name' => 'USB-C Hub', 'price' => 59.99],
            ['name' => 'Mechanical Keyboard', 'price' => 129.99],
            ['name' => 'Webcam 4K', 'price' => 89.99],
            ['name' => 'Monitor Light Bar', 'price' => 35.00],
            ['name' => 'Desk Mat', 'price' => 24.99],
        ];

        $statuses = ['pending', 'processing', 'completed', 'cancelled'];
        $paymentMethods = ['Credit Card', 'PayPal', 'Bank Transfer', 'Stripe'];

        foreach ($customers as $customer) {
            // Create 1-3 orders per customer
            $orderCount = rand(1, 3);
            
            for ($i = 0; $i < $orderCount; $i++) {
                $order = Order::create([
                    'user_id' => $customer->id,
                    'total_amount' => 0, // Will update after items
                    'status' => $statuses[array_rand($statuses)],
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'shipping_address' => $this->generateAddress(),
                    'billing_address' => rand(0, 1) ? $this->generateAddress() : null,
                    'tracking_number' => rand(0, 1) ? 'TRK' . strtoupper(uniqid()) : null,
                    'shipped_at' => rand(0, 1) ? now()->subDays(rand(1, 10)) : null,
                ]);

                // Add 1-4 items per order
                $itemCount = rand(1, 4);
                $totalAmount = 0;

                for ($j = 0; $j < $itemCount; $j++) {
                    $product = $products[array_rand($products)];
                    $quantity = rand(1, 3);
                    $subtotal = $product['price'] * $quantity;
                    $totalAmount += $subtotal;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_name' => $product['name'],
                        'quantity' => $quantity,
                        'unit_price' => $product['price'],
                        'subtotal' => $subtotal,
                    ]);
                }

                // Update order total
                $order->update(['total_amount' => $totalAmount]);
            }
        }
    }

    private function generateAddress(): string
    {
        $streets = ['123 Main St', '456 Oak Ave', '789 Pine Rd', '321 Elm St', '654 Maple Dr'];
        $cities = ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix'];
        $states = ['NY', 'CA', 'IL', 'TX', 'AZ'];

        return sprintf(
            "%s\n%s, %s %s",
            $streets[array_rand($streets)],
            $cities[array_rand($cities)],
            $states[array_rand($states)],
            rand(10000, 99999)
        );
    }
}
