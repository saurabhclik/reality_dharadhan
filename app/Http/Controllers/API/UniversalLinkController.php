<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class UniversalLinkController extends Controller
{
    public function getUniversalLink(Request $request, $id)
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
            ], 401);
        }
        try
        {
            $data = DB::table('agent_universal_links')->where('unique_identifier', $id)->first();

            if($data) 
            {
                return response()->json([
                    'status' => 200,
                    'message' => 'Universal link data fetched successfully',
                    'data' => $data
                ], 200);
            }

            return response()->json([
                'status' => 404,
                'message' => 'No data found',
                'data' => []
            ], 404);

        } 
        catch(Exception $error) 
        {
            return response()->json([
                'status' => 500,
                'message' => 'Error: ' . $error->getMessage(),
                'data' => []
            ], 500);
        }
    }
}