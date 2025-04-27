<?php

namespace App\Http\Controllers;

use App\Models\FoodItem;
use App\Models\FoodTruckOrderItems;
use App\Models\FoodTruckOrders;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator as Validator;
use Illuminate\Validation\Rule;
use App\Services\PdfReceiptService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FoodOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $foodOrders = FoodTruckOrderItems::with('truck')->get();
        return response()->json(['success' => true, 'data' => $foodOrders], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    $validator = Validator::make($request->all(), [
        "orders" => "required|array",
        "orders.*.food_id" => [
            "required",
            Rule::exists('food_items', 'id'),
        ],
        "orders.*.quantity" => "required|numeric",
        "orders.*.price" => [
        "required",
        "numeric"
        ],
        "orders.*.subtotal" => "required|numeric",
        "truck_id" => "required|exists:trucks,id",
        "payment_type" => "required|string",
        "customer_number" => "required|string",
        "status" => "required|string"
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
    }

    $total_amount = 0;
    $foodOrders = [];

    $foodOrder = FoodTruckOrderItems::create([
        "metadata" => $request->orders,
        "truck_id" => $request->truck_id
    ]);
    $foodOrders[] = $foodOrder;

    foreach ($request->orders as $order) {
        $total_amount += $order['subtotal'];
    }

    $order_id = FoodTruckOrderItems::latest()->first()->id ?? 1;

    FoodTruckOrders::create([
        "order_id" => $order_id,
        "customer_number" => $request->customer_number,
        "payment_type" => $request->payment_type,
        "total_amount" => $total_amount,
        "order_status" => $request->status
    ]);

    return response()->json(['success' => true, 'message' => 'Food order successfully placed!', 'data' => $foodOrders], Response::HTTP_CREATED);
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $foodOrder = FoodTruckOrders::where('order_id',$id)->get();
        $foodOrderItem = FoodTruckOrderItems::where('id',$id)->get();

        return response()->json([
            'success' => true,
            'foodOrder' => $foodOrder,
            'foodOrderItem' => $foodOrderItem
        ], Response::HTTP_OK);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FoodTruckOrders $foodTruckOrders)
    {
        $validator = Validator::make($request->all(),[
            "order_status" => "required|string"
        ]);

        if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
        }
        $foodTruckOrders->update([
            "order_status" => $request->order_status
        ]);
        return response()->json(['success' => true, 'message' => 'Food order status upated successfully!', 'data' => $foodTruckOrders],Response::HTTP_OK);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FoodTruckOrderItems $foodTruckOrderItems)
    {
        $foodTruckOrderItems->delete();
        return response()->json([
            'success' => true,
            'message' => 'Fodd order deleted successfully!'
        ], Response::HTTP_OK);
    }
   public function generateReceiptPdf($orderId)
    {
        // Retrieve the order
        $foodOrder = FoodTruckOrders::where('order_id', $orderId)->first();

        // Retrieve the order item
        $foodOrderItem = FoodTruckOrderItems::where('id', $orderId)->first();

        // Check if order item exists
        if (!$foodOrderItem) {
            return response()->json(['error' => 'Order item not found'], 404);
        }

        // Format date and time
        $orderDate = $foodOrderItem->created_at->format('Y-m-d'); // Format: 2025-02-23
        $orderTime = $foodOrderItem->created_at->format('H:i:s'); // Format: 14:30:15

        // Metadata is already an array (no need for json_decode)
        $metadata = $foodOrderItem->metadata;

	// Construct order data
            $order = [
                'id' => $foodOrderItem->id ? $foodOrderItem->id : "",
                'date' => Carbon::parse($foodOrder->created_at)->setTimezone('Asia/Kolkata')->format('Y-m-d'),
                'time' => Carbon::parse($foodOrder->created_at)->setTimezone('Asia/Kolkata')->format('h:i A'),
                'items' => [],
                'total' => $foodOrder->total_amount ?? 0,
                'payment_type' => $foodOrder->payment_type
            ];

        foreach ($metadata as $key => $item) {
            // Fetch food name from FoodItems table based on food_id
            $foodItem = FoodItem::where('id', $item['food_id'])->first();
            $foodName = $foodItem ? $foodItem->name : 'Unknown Item';

            $order['items'][] = [
                'index' => $key + 1, // Adding 1 to start from 1 instead of 0
                'name' => $foodName,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ];
        }

        // Generate PDF
        $pdfService = new PdfReceiptService();
        $pdf = $pdfService->generateReceipt($order);

        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="receipt.pdf"');
    }

    public function getMostOrderedFood(Request $request)
    {
        // Get the filter from request (default: 'today')
        $filter = $request->query('filter', 'today');
        // Define the date filter condition
        $dateCondition = match ($filter) {
            'today' => now()->toDateString(),
            'this_week' => now()->startOfWeek()->toDateString(),
            'this_month' => now()->startOfMonth()->toDateString(),
            default => '1970-01-01'
        };
        // dd($dateCondition);
        // Raw SQL query to fetch most ordered food items
        $mostOrderedFood = DB::select("
            SELECT
                t.food_id,
   		f.image As image_url,
                f.name AS food_name,
                SUM(t.quantity) AS total_quantity
            FROM food_truck_order_items o,
            JSON_TABLE(
                o.metadata,
                '$[*]'
                COLUMNS (
                    food_id INT PATH '$.food_id',
                    quantity INT PATH '$.quantity'
                )
            ) AS t
            JOIN food_items f ON t.food_id = f.id
            WHERE o.created_at >= ?
            GROUP BY t.food_id, f.name
            ORDER BY total_quantity DESC
        ", [$dateCondition]);

        return response()->json([
            'status' => 'success',
            'filter' => $filter,
            'data' => $mostOrderedFood
        ], 200);
    }
    public function getOrdersWithSummary(Request $request)
    {
        $filter = $request->query('filter', 'today');
        $specificDate = $request->query('date');

        // Determine current period
        if ($specificDate) {
            $dateCondition = "DATE(created_at) = ?";
            $dateParams = [$specificDate];

            // Previous day for specific date
            $prevDate = Carbon::parse($specificDate)->subDay()->toDateString();
            $prevDateCondition = "DATE(created_at) = ?";
            $prevDateParams = [$prevDate];
        } else {
            switch ($filter) {
                case 'today':
                    $currentDate = now()->toDateString();
                    $prevDate = now()->subDay()->toDateString();

                    $dateCondition = "DATE(created_at) = ?";
                    $dateParams = [$currentDate];

                    $prevDateCondition = "DATE(created_at) = ?";
                    $prevDateParams = [$prevDate];
                    break;

                case 'this_week':
                    $currentDate = now()->startOfWeek()->toDateString();
                    $prevWeekStart = now()->subWeek()->startOfWeek()->toDateString();
                    $prevWeekEnd = now()->subWeek()->endOfWeek()->toDateString();

                    $dateCondition = "created_at >= ?";
                    $dateParams = [$currentDate];

                    $prevDateCondition = "created_at BETWEEN ? AND ?";
                    $prevDateParams = [$prevWeekStart, $prevWeekEnd];
                    break;

                case 'this_month':
                    $currentDate = now()->startOfMonth()->toDateString();
                    $prevMonthStart = now()->subMonth()->startOfMonth()->toDateString();
                    $prevMonthEnd = now()->subMonth()->endOfMonth()->toDateString();

                    $dateCondition = "created_at >= ?";
                    $dateParams = [$currentDate];

                    $prevDateCondition = "created_at BETWEEN ? AND ?";
                    $prevDateParams = [$prevMonthStart, $prevMonthEnd];
                    break;

                default:
                    $dateCondition = "created_at >= ?";
                    $dateParams = ['1970-01-01'];
                    $prevDateCondition = "created_at >= ?";
                    $prevDateParams = ['1970-01-01'];
            }
        }

        // Current period summary
        $summary = DB::selectOne("
            SELECT COUNT(*) AS total_orders, COALESCE(SUM(total_amount), 0) AS total_revenue
            FROM food_truck_orders
            WHERE $dateCondition
        ", $dateParams);

        // Previous period summary
        $prevSummary = DB::selectOne("
            SELECT COUNT(*) AS total_orders, COALESCE(SUM(total_amount), 0) AS total_revenue
            FROM food_truck_orders
            WHERE $prevDateCondition
        ", $prevDateParams);

        // Calculate percentage changes
    // Calculate percentage changes with proper handling for zero previous values
    if ($prevSummary->total_orders == 0) {
        $orderChange = $summary->total_orders > 0 ? 100.00 : 0.00;
    } else {
        $orderChange = (($summary->total_orders - $prevSummary->total_orders) / $prevSummary->total_orders) * 100;
    }

    if ($prevSummary->total_revenue == 0) {
        $revenueChange = $summary->total_revenue > 0 ? 100.00 : 0.00;
    } else {
        $revenueChange = (($summary->total_revenue - $prevSummary->total_revenue) / $prevSummary->total_revenue) * 100;
    }

    // Round the values before sending the response
    $orderChange = round($orderChange, 2);
    $revenueChange = round($revenueChange, 2);

        // Fetch orders for current period
        $orders = DB::select("
            SELECT order_id, total_amount, order_status, payment_type, created_at
            FROM food_truck_orders
            WHERE $dateCondition
            ORDER BY created_at DESC
        ", $dateParams);

        return response()->json([
            'status' => 'success',
            'filter' => $filter,
            'date' => $specificDate ?? $dateParams[0],
            'summary' => [
                'total_orders' => $summary->total_orders ?? 0,
                'total_revenue' => $summary->total_revenue ?? 0,
                'order_change' => round($orderChange, 2), // percentage
                'revenue_change' => round($revenueChange, 2) // percentage
            ],
            'data' => $orders
        ], 200);
    }
}
