<?php

namespace App\Http\Controllers;

use App\Models\Truck;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator as Validator;

class TruckController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tucks =  Truck::all();
        return response()->json(['success' => true, 'data' => $tucks],Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
                'truck_name' => 'required',
                'license_plate' => 'required|unique:trucks,license_plate,except,id',
                'location' => 'required'
            ]
            );
        if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
        }

        $truck = Truck::create([
            'truck_name' => $request->truck_name,
            'license_plate' => $request->license_plate,
            'location' => $request->location,
        ]);

        return response()->json(['success' => true, 'message' => 'Truck added successfully!', 'data' => $truck ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     */
    public function show(Truck $truck)
    {
        return response()->json(['success' => true, 'data' => $truck ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Truck $truck)
    {
        $validator = Validator::make($request->all(),[
            'truck_name' => 'required',
            'license_plate' => "required|unique:trucks,license_plate,{$truck->id}",
            'location' => 'required',
        ]);

        if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
    }

        $truck->update([
            'truck_name' => $request->truck_name,
            'license_plate' => $request->license_plate,
            'location' => $request->location
        ]);
        return response()->json(['success' => true, 'message' => 'Truck data upated successfully!', 'data' => $truck],Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Truck $truck)
    {
        $truck->delete();
        return response()->json([
            'success' => true,
            'message' => 'Truck deleted successfully!'
        ], Response::HTTP_OK);
    }
}
