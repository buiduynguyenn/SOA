<?php

namespace App\Services;

use App\Models\OrderReport;
use App\Models\ProductReport;
use Illuminate\Support\Facades\DB;

class ReportService
{
    protected $externalServices;

    public function __construct(ExternalServices $externalServices)
    {
        $this->externalServices = $externalServices;
    }

    public function createOrderReport($orderId, $token)
    {
        return DB::transaction(function () use ($orderId, $token) {
            $order = $this->externalServices->getOrder($orderId, $token);
            if (!$order) {
                throw new \Exception('Order not found');
            }

            // Tạo báo cáo đơn hàng
            $orderReport = OrderReport::create([
                'order_id' => $orderId,
                'total_revenue' => $order['total_amount'],
                'total_cost' => 0,
                'total_profit' => 0
            ]);

            $totalCost = 0;

            // Tạo báo cáo cho từng sản phẩm
            foreach ($order['items'] as $item) {
                $product = $this->externalServices->getProduct($item['product_id'], $token);
                if (!$product) continue;

                $cost = $product['cost_price'] ?? 0;
                $itemCost = $cost * $item['quantity'];
                $totalCost += $itemCost;

                ProductReport::create([
                    'order_report_id' => $orderReport->id,
                    'product_id' => $item['product_id'],
                    'total_sold' => $item['quantity'],
                    'revenue' => $item['total_price'],
                    'cost' => $itemCost,
                    'profit' => $item['total_price'] - $itemCost
                ]);
            }

            $orderReport->update([
                'total_cost' => $totalCost,
                'total_profit' => $order['total_amount'] - $totalCost
            ]);

            return $orderReport->load('productReports');
        });
    }

    public function createProductReport($productId, $token)
    {
        // Lấy thông tin sản phẩm
        $product = $this->externalServices->getProduct($productId, $token);
        if (!$product) {
            throw new \Exception('Product not found');
        }

        // Tạo một order report mới để liên kết
        $orderReport = OrderReport::create([
            'order_id' => 0, // Giá trị mặc định vì đây là báo cáo tổng hợp
            'total_revenue' => 0,
            'total_cost' => 0,
            'total_profit' => 0
        ]);

        // Tính toán từ các order reports hiện có
        $totalSold = 0;
        $totalRevenue = 0;
        $totalCost = 0;

        // Tạo báo cáo sản phẩm tổng hợp
        $productReport = ProductReport::create([
            'order_report_id' => $orderReport->id, // Thêm order_report_id
            'product_id' => $productId,
            'total_sold' => $totalSold,
            'revenue' => $totalRevenue,
            'cost' => $totalCost,
            'profit' => $totalRevenue - $totalCost
        ]);

        // Cập nhật tổng số trong order report
        $orderReport->update([
            'total_revenue' => $totalRevenue,
            'total_cost' => $totalCost,
            'total_profit' => $totalRevenue - $totalCost
        ]);

        return $productReport;
    }

    public function getProductSummary($productId)
    {
        return ProductReport::where('product_id', $productId)
            ->selectRaw('
                product_id,
                SUM(total_sold) as total_sold,
                SUM(revenue) as total_revenue,
                SUM(cost) as total_cost,
                SUM(profit) as total_profit
            ')
            ->groupBy('product_id')
            ->first();
    }

    public function getOrderSummary($orderId)
    {
        return OrderReport::where('order_id', $orderId)
            ->with('productReports')
            ->first();
    }
} 