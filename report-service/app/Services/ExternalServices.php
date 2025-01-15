<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ExternalServices
{
    public function getOrder($orderId, $token)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->get(env('ORDER_SERVICE_URL') . "/api/orders/{$orderId}");

            if ($response->failed()) {
                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getProduct($productId, $token)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->get(env('PRODUCT_SERVICE_URL') . "/api/products/{$productId}");

            if ($response->failed()) {
                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            return null;
        }
    }
} 