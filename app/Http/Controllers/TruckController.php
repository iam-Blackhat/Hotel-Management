<?php

namespace App\Http\Controllers;

use App\Models\Truck;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        $request->validate(
            [
                'truck_name' => 'required',
                'license_plate' => 'required|unique:trucks,license_plate,except,id',
                'location' => 'required'
            ]
            );
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
        $request->validate([
            'truck_name' => 'required',
            'license_plate' => "required|unique:trucks,license_plate,{$truck->id}",
            'location' => 'required',
        ]);
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
