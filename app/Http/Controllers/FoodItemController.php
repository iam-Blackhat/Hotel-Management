<?php

namespace App\Http\Controllers;

use App\Models\FoodItem;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class FoodItemController extends Controller
{
    public function index()
    {
        $foodItems = FoodItem::with('category')->get();
        return response()->json(['success' => true, 'data' => $foodItems], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'truck_id' => 'required|exists:categories,truck_id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);


        if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
        }

        $foodItem = FoodItem::create([
            'name' => $request->name,
            'truck_id' => $request->truck_id,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $request->image,
            'category_id' => $request->category_id,
            'is_available' => $request->is_available?$request->is_available:false,
        ]);

        return response()->json(['success' => true, 'message' => 'Food item added successfully!', 'data' => $foodItem], Response::HTTP_CREATED);
    }

    public function show(FoodItem $foodItem)
    {
        return response()->json(['success' => true, 'data' => $foodItem], Response::HTTP_OK);
    }

    public function update(Request $request, FoodItem $foodItem)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        if ($request->hasFile('image')) {
            if ($foodItem->image) {
                Storage::disk('public')->delete($foodItem->image);
            }
            $imagePath = $request->file('image')->store('food-items', 'public');
            $foodItem->update(['image' => $imagePath]);
        }

        if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
        }

        $foodItem->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'is_available' => $request->has('is_available'),
        ]);

        return response()->json(['success' => true, 'message' => 'Food item updated successfully!', 'data' => $foodItem], Response::HTTP_OK);
    }

    public function destroy(FoodItem $foodItem)
    {
        if ($foodItem->image) {
            Storage::disk('public')->delete($foodItem->image);
        }

        $foodItem->delete();
        return response()->json(['success' => true, 'message' => 'Food item deleted successfully!'], Response::HTTP_OK);
    }
}
