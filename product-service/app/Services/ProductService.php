<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function getAllProducts(): Collection
    {
        return Product::all();
    }

    public function getProductById(int $id): ?Product
    {
        return Product::find($id);
    }

    public function createProduct(array $data): Product
    {
        return Product::create($data);
    }

    public function updateProduct(int $id, array $data): ?Product
    {
        $product = Product::find($id);
        if ($product) {
            $product->update($data);
        }
        return $product;
    }

    public function deleteProduct(int $id): bool
    {
        $product = Product::find($id);
        if ($product) {
            return $product->delete();
        }
        return false;
    }
} 