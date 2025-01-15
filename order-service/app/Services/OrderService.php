<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrderService
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function createOrder($data, $token)
    {
        return DB::transaction(function () use ($data, $token) {
            // Kiểm tra tồn kho cho tất cả sản phẩm
            foreach ($data['items'] as $item) {
                if (!$this->productService->checkProduct($item['product_id'], $item['quantity'], $token)) {
                    throw new \Exception('Insufficient stock for product ID: ' . $item['product_id']);
                }
            }

            // Tạo đơn hàng
            $order = Order::create([
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'total_amount' => 0,
                'status' => 'pending'
            ]);

            $totalAmount = 0;

            // Tạo chi tiết đơn hàng
            foreach ($data['items'] as $item) {
                $totalPrice = $item['quantity'] * $item['unit_price'];
                $totalAmount += $totalPrice;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $totalPrice
                ]);

                // Cập nhật số lượng sản phẩm
                $this->productService->updateProductQuantity(
                    $item['product_id'],
                    $item['quantity'],
                    $token
                );
            }

            // Cập nhật tổng tiền đơn hàng
            $order->update(['total_amount' => $totalAmount]);

            return $order->load('items');
        });
    }
} 