<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdvertisementController extends Controller
{
    public function deactivate(Request $request)
    {
        try 
        {
            $advertisementId = $request->input('advertisement_id');
             
            DB::table('advertisements')
                ->where('id', $advertisementId)
                ->update([
                    'is_active' => 0,
                    'updated_at' => now()
                ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Advertisement deactivated successfully'
            ]);
            
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Failed to deactivate advertisement',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}