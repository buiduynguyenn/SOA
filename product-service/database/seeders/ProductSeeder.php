<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'name' => 'iPhone 14',
            'description' => 'Latest iPhone model with advanced features',
            'price' => 999.99,
            'quantity' => 50
        ]);

        Product::create([
            'name' => 'Samsung Galaxy S23',
            'description' => 'Flagship Android smartphone',
            'price' => 899.99,
            'quantity' => 75
        ]);
    }
} 