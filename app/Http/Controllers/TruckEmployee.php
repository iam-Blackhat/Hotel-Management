<?php

namespace App\Http\Controllers;

use App\Models\TruckEmployees;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class TruckEmployee extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = TruckEmployees::with('truck')->get();
        return response()->json(['success' => true, 'data' => $employees],Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'truck_id' => 'required|exists:trucks,id',
        'employee_name' => 'required',
        'employee_phone' => 'required|unique:truck_employees,employee_phone',
        'employee_email' => 'required|unique:truck_employees,employee_email',
    ], [
        'truck_id.required' => 'The truck ID is required.',
        'truck_id.exists' => 'The selected truck ID is invalid.',
        'employee_name.required' => 'The employee name is required.',
        'employee_phone.required' => 'The employee phone number is required.',
        'employee_phone.unique' => 'The employee phone number has already been taken.',
        'employee_email.required' => 'The employee email is required.',
        'employee_email.unique' => 'The employee email has already been taken.',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
    }

    $employee = TruckEmployees::create([
        'truck_id' => $request->truck_id,
        'employee_name' => $request->employee_name,
        'employee_role' => $request->employee_role,
        'employee_phone' => $request->employee_phone,
        'employee_email' => $request->employee_email,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Employee created successfully!',
        'data' => $employee,
    ]);
}
    /**
     * Display the specified resource.
     */
    public function show(TruckEmployees $employee)
    {
        return response()->json(['success' => true,'data' => $employee ],Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TruckEmployees $employee)
    {
        $validator = Validator::make($request->all(), [
        'truck_id' => 'required|exists:trucks,id',
        'employee_name' => 'required',
        'employee_phone' => 'required|unique:truck_employees,employee_phone',
        'employee_email' => 'required|unique:truck_employees,employee_email',
    ], [
        'truck_id.required' => 'The truck ID is required.',
        'truck_id.exists' => 'The selected truck ID is invalid.',
        'employee_name.required' => 'The employee name is required.',
        'employee_phone.required' => 'The employee phone number is required.',
        'employee_phone.unique' => 'The employee phone number has already been taken.',
        'employee_email.required' => 'The employee email is required.',
        'employee_email.unique' => 'The employee email has already been taken.',
    ]);
        if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
    }

    $employee = $employee->update([
        'truck_id' => $request->truck_id,
        'employee_name' => $request->employee_name,
        'employee_role' => $request->employee_role,
        'employee_phone' => $request->employee_phone,
        'employee_email' => $request->employee_email,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Employee updated successfully!',
        'data' => $employee,
    ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TruckEmployees $employee)
    {
        $employee->delete();
        return response()->json(['success' => true, 'message' => 'Employee deleted successfully!', 'data' => $employee]);

    }
}
