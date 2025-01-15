<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ProductService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('PRODUCT_SERVICE_URL');
    }

    public function checkProduct($productId, $quantity, $token)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->get("{$this->baseUrl}/api/products/{$productId}");

            if ($response->failed()) {
                return false;
            }

            $product = $response->json();
            return $product['quantity'] >= $quantity;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateProductQuantity($productId, $quantity, $token)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->get("{$this->baseUrl}/api/products/{$productId}");

            if ($response->failed()) {
                return false;
            }

            $product = $response->json();
            $newQuantity = $product['quantity'] - $quantity;

            $updateResponse = Http::withHeaders([
                'Authorization' => $token
            ])->put("{$this->baseUrl}/api/products/{$productId}", [
                'quantity' => $newQuantity
            ]);

            return $updateResponse->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
} 