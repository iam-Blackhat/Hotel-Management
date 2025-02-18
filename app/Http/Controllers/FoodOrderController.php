<?php

namespace App\Http\Controllers;


use App\Models\FoodTruckOrderItems;
use App\Models\FoodTruckOrders;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator as Validator;
use Illuminate\Validation\Rule;

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
        "orders.*.truck_id" => "required|exists:trucks,id",
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
        "metadata" => $request->orders
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
        $foodOrderItem = FoodTruckOrderItems::find($id)->first();

        return response()->json([
            'success' => true,
            'foodOrder' => $foodOrder,
            'foodOrderItem' => $foodOrderItem
        ], Response::HTTP_OK);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
