<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Order;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderItemController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        $orderItems = OrderItem::with('order')->get();
        return response()->json($orderItems);
    }

    public function show($id)
    {
        $orderItem = OrderItem::with('order')->find($id);
        if (!$orderItem) {
            return response()->json(['message' => 'Order item not found'], 404);
        }
        return response()->json($orderItem);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|integer',
            'product_name' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Kiểm tra tồn kho
        if (!$this->productService->checkProduct(
            $request->product_id,
            $request->quantity,
            $request->header('Authorization')
        )) {
            return response()->json(['message' => 'Insufficient stock'], 400);
        }

        // Tính tổng giá
        $totalPrice = $request->quantity * $request->unit_price;

        // Tạo chi tiết đơn hàng
        $orderItem = OrderItem::create([
            'order_id' => $request->order_id,
            'product_id' => $request->product_id,
            'product_name' => $request->product_name,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'total_price' => $totalPrice
        ]);

        // Cập nhật số lượng sản phẩm trong kho
        $this->productService->updateProductQuantity(
            $request->product_id,
            $request->quantity,
            $request->header('Authorization')
        );

        // Cập nhật tổng tiền đơn hàng
        $order = Order::find($request->order_id);
        $order->total_amount = $order->items->sum('total_price');
        $order->save();

        return response()->json($orderItem, 201);
    }

    public function update(Request $request, $id)
    {
        $orderItem = OrderItem::find($id);
        if (!$orderItem) {
            return response()->json(['message' => 'Order item not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'quantity' => 'sometimes|required|integer|min:1',
            'unit_price' => 'sometimes|required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Nếu số lượng thay đổi, kiểm tra tồn kho
        if (isset($request->quantity) && $request->quantity != $orderItem->quantity) {
            $quantityDiff = $request->quantity - $orderItem->quantity;
            if ($quantityDiff > 0) {
                if (!$this->productService->checkProduct(
                    $orderItem->product_id,
                    $quantityDiff,
                    $request->header('Authorization')
                )) {
                    return response()->json(['message' => 'Insufficient stock'], 400);
                }
            }

            // Cập nhật số lượng sản phẩm trong kho
            $this->productService->updateProductQuantity(
                $orderItem->product_id,
                $quantityDiff,
                $request->header('Authorization')
            );
        }

        // Cập nhật chi tiết đơn hàng
        $orderItem->update($request->only(['quantity', 'unit_price']));
        
        // Tính lại tổng giá
        $orderItem->total_price = $orderItem->quantity * $orderItem->unit_price;
        $orderItem->save();

        // Cập nhật tổng tiền đơn hàng
        $order = Order::find($orderItem->order_id);
        $order->total_amount = $order->items->sum('total_price');
        $order->save();

        return response()->json($orderItem);
    }

    public function destroy($id)
    {
        $orderItem = OrderItem::find($id);
        if (!$orderItem) {
            return response()->json(['message' => 'Order item not found'], 404);
        }

        // Hoàn trả số lượng sản phẩm vào kho
        $this->productService->updateProductQuantity(
            $orderItem->product_id,
            -$orderItem->quantity, // Số âm để tăng số lượng trong kho
            request()->header('Authorization')
        );

        // Xóa chi tiết đơn hàng
        $orderItem->delete();

        // Cập nhật tổng tiền đơn hàng
        $order = Order::find($orderItem->order_id);
        $order->total_amount = $order->items->sum('total_price');
        $order->save();

        return response()->json(['message' => 'Order item deleted successfully']);
    }
} 