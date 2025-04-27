<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    //Registation Method
    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|email|unique:users,email,except,id',
            'password'=>'required',
            'c_password'=>'required|same:password'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $user=User::create($data);
        $response['name'] = $user->name;
        $response['message'] = "User Register Successfully";
        return response()->json($response,200);

    }
    public function login(Request $request){
        if(Auth::attempt(['email'=>$request->input('email'),'password'=>$request->input('password')])){
            $user = Auth::user();
            $response['token']=$user->createToken('Testing');
            $response['name']=$user->name;
            return response()->json($response,200);
        }
        else{
            return response()->json(['message'=>'invalid credentials error'],401);
        }
}
    public function detail(){
        $user = Auth::user();
        $response['user']=$user;
        return response()->json($response,200);
    }

    public function logout(Request $request){
        $user = Auth::user();
	$accessToken = $request->user()->token();
        $accessToken->revoke();
        $response['user'] = $user->name;
        $response['message'] = "Logout Successfully";
        return response()->json($response,200);
    }
}
