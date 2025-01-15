<?php

namespace App\Http\Controllers;

use App\Models\OrderReport;
use App\Models\ProductReport;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function getOrderReports()
    {
        $reports = OrderReport::with('productReports')->get();
        return response()->json($reports);
    }

    public function getOrderReport($id)
    {
        $report = OrderReport::with('productReports')->find($id);
        if (!$report) {
            return response()->json(['message' => 'Report not found'], 404);
        }
        return response()->json($report);
    }

    public function getProductReports()
    {
        $reports = ProductReport::with('orderReport')->get();
        return response()->json($reports);
    }

    public function getProductReport($id)
    {
        $report = ProductReport::with('orderReport')->find($id);
        if (!$report) {
            return response()->json(['message' => 'Report not found'], 404);
        }
        return response()->json($report);
    }

    public function createOrderReport(Request $request)
    {
        try {
            $report = $this->reportService->createOrderReport(
                $request->order_id,
                $request->header('Authorization')
            );
            return response()->json($report, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function deleteOrderReport($id)
    {
        $report = OrderReport::find($id);
        if (!$report) {
            return response()->json(['message' => 'Report not found'], 404);
        }

        $report->delete();
        return response()->json(['message' => 'Report deleted successfully']);
    }

    public function deleteProductReport($id)
    {
        $report = ProductReport::find($id);
        if (!$report) {
            return response()->json(['message' => 'Report not found'], 404);
        }

        $report->delete();
        return response()->json(['message' => 'Report deleted successfully']);
    }

    public function createProductReport(Request $request)
    {
        try {
            $report = $this->reportService->createProductReport(
                $request->product_id,
                $request->header('Authorization')
            );
            return response()->json($report, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getProductSummary($productId)
    {
        $summary = $this->reportService->getProductSummary($productId);
        if (!$summary) {
            return response()->json(['message' => 'No reports found for this product'], 404);
        }
        return response()->json($summary);
    }

    public function getOrderSummary($orderId)
    {
        $summary = $this->reportService->getOrderSummary($orderId);
        if (!$summary) {
            return response()->json(['message' => 'No reports found for this order'], 404);
        }
        return response()->json($summary);
    }
} 