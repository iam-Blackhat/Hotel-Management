<?php

namespace App\Http\Controllers;


use App\Models\FoodTruckOrderItems;
use App\Models\FoodTruckOrders;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator as Validator;
use Illuminate\Validation\Rule;
use App\Services\ReceiptPrinterService;

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

    public function printReceipt($orderId)
    {
        // Simulated order data (Replace with actual database order retrieval)
        $foodOrder = FoodTruckOrders::where('order_id',$orderId)->get();
        $foodOrderItem = FoodTruckOrderItems::find($orderId)->first();
      $order = [
        'items' => [
            ['name' => 'Burger', 'quantity' => 2, 'price' => 5.00],
            ['name' => 'Fries', 'quantity' => 1, 'price' => 2.50],
        ],
        'total' => 12.50
    ];
        $printerService = new ReceiptPrinterService();
        $result = $printerService->printReceipt($order);

        return response()->json(['message' => $result]);
    }

}
