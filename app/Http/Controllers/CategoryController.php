<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    // List all categories
    public function index()
    {
        $categories = Category::all();
        return response()->json(['success' => true, 'data' => $categories], Response::HTTP_OK);
    }

    // Store a new category
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'truck_id' => 'required|exists:trucks,id',
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
        }

        $category = Category::create([
            'truck_id' => $request->truck_id,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully!',
            'data' => $category
        ], Response::HTTP_CREATED);
    }

    // Show details for a single category
    public function show(Category $category)
    {
        return response()->json([
            'success' => true,
            'data' => $category
        ], Response::HTTP_OK);
    }

    // Update an existing category
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => "required|string|max:255|unique:categories,name,{$category->id}",
            'description' => 'nullable|string',
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully!',
            'data' => $category
        ], Response::HTTP_OK);
    }

    // Delete a category
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully!'
        ], Response::HTTP_OK);
    }
}
