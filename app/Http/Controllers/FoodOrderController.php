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
            'id' => $foodOrderItem->id?$foodOrderItem->id:"",
            'date' => $orderDate,
            'time' => $orderTime,
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
        // Get the filter from request (default: 'today')
        $filter = $request->query('filter', 'today');
         $specificDate = $request->query('date');

        // If custom date range is provided, use it, otherwise apply predefined filters
        if ($specificDate) {
            $dateCondition = "DATE(created_at) = ?";
            $dateParams = [$specificDate];
        } else {
            $dateCondition = "created_at >= ?";
            $dateParams = [match ($filter) {
                'today' => now()->toDateString(),
                'this_week' => now()->startOfWeek()->toDateString(),
                'this_month' => now()->startOfMonth()->toDateString(),
                default => '1970-01-01'
            }];
        }

        // Fetch total orders count and total revenue
        $summary = DB::select("
            SELECT
                COUNT(*) AS total_orders,
                COALESCE(SUM(total_amount), 0) AS total_revenue
            FROM food_truck_orders
            WHERE $dateCondition
        ", $dateParams);

        // Fetch individual orders with required details
        $orders = DB::select("
            SELECT
                order_id,
                total_amount,
                order_status,
                payment_type,
                created_at
            FROM food_truck_orders
            WHERE $dateCondition
            ORDER BY created_at DESC
        ", $dateParams);

        return response()->json([
            'status' => 'success',
            'filter' => $filter,
            'date' => $specificDate ?? $dateParams[0],
            'summary' => [
                'total_orders' => $summary[0]->total_orders ?? 0,
                'total_revenue' => $summary[0]->total_revenue ?? 0
            ],
            'data' => $orders
        ], 200);
    }

}
