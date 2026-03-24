<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CrmUserController extends Controller
{
    public function register(Request $request)
    {
        try
        {
            $token = $request->header('Authorization');
            if ($token && str_starts_with($token, 'Bearer ')) 
            {
                $token = substr($token, 7);
            }
            if (!$token || $token !== 'OXpROcBEl0JYqCO6XwW4') 
            {
                return response()->json([
                    'status' => 401,
                    'message' => 'Unauthorized: Invalid or missing token',
                    'data' => []
                ]);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'mobile' => 'required|string|unique:users,mobile',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string',
                'role' => 'required|string',
                'unique_id' => 'required|string',
            ]);

            if ($validator->fails()) 
            {
                return response()->json([
                    'status' => 200,
                    'message' => $validator->errors()->first(),
                    'data' => []
                ]);
            }
            
            $userId = DB::table('users')->insertGetId([
                'name' => $request->name,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'password' => $request->password,
                'role' => $request->role,
                'tm_id' => $request->tm_id ?? 1,
                'unique_id' => $request->unique_id,
                'is_active' => 1,
                'token' => Str::random(40),
                'created_date' => now(),
                'updated_date' => now(),
            ]);

            $user = DB::table('users')->where('id', $userId)->first();

            return response()->json([
                'status' => 200,
                'message' => 'User saved successfully',
                'data' => $user
            ]);

        } 
        catch (\Exception $e)
        {
            return response()->json([
                'status' => 500,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => []
            ]);
        }
    }
}