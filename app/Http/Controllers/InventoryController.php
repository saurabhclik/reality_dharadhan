<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $activeFeatures = Session::get('active_features', []);
        if(in_array('inventory_management', $activeFeatures))
        {
            $user_type = Session::get('user_type');
            $project_id = $request->query('id');
            $projects = DB::table('projects')->get();
            $selectedProject = $projects->firstWhere('id', $project_id);
            if (!$selectedProject && $projects->count() > 0) 
            {
                $selectedProject = $projects->first();
                $project_id = $selectedProject->id;
            }
            $inventoryDetails = [];
            $statusCounts = (object)[
                'pending' => 0,
                'cancel' => 0,
                'hold' => 0,
                'sold' => 0,
                'total_leads' => 0
            ];

            if ($selectedProject) 
            {
                $inventoryDetails = DB::table('inventory_det')
                    ->where('inventory_id', $project_id) 
                    ->get();

                $statusCounts = DB::table('inventory_det')
                    ->where('inventory_id', $project_id)
                    ->selectRaw("
                        COUNT(*) as total_leads,
                        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                        SUM(CASE WHEN status = 'cancel' THEN 1 ELSE 0 END) as cancel,
                        SUM(CASE WHEN status = 'hold' THEN 1 ELSE 0 END) as hold,
                        SUM(CASE WHEN status = 'sold' THEN 1 ELSE 0 END) as sold
                    ")
                    ->first() ?? $statusCounts;
            }

            $salespeople = DB::table('users')->get();

            return view('inventory.index', compact(
                'inventoryDetails',
                'statusCounts',
                'salespeople',
                'project_id',
                'selectedProject',
                'user_type',
                'projects'
            ));
        }
        else
        {
            abort(404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'inventory_id' => 'required|integer|exists:projects,id',
            'unit_no' => 'required|string|max:50',
            'size' => 'required|string|max:50',
        ]);

        if ($validator->fails()) 
        {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', $validator->errors()->first());
        }

        try 
        {
            $exists = DB::table('inventory_det')
                ->where('inventory_id', $request->inventory_id)
                ->where('unit_no', $request->unit_no)
                ->exists();

            if ($exists) 
            {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Unit number already exists for this project.');
            }

            DB::table('inventory_det')->insert([
                'inventory_id' => $request->inventory_id,
                'unit_no' => $request->unit_no,
                'size' => $request->size,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Inventory unit added successfully.');
        } 
        catch (\Exception $error) 
        {
            return redirect()->back()
                ->withInput()
                ->with('error', $error->getMessage());
        }
    }

    public function updateSale(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:inventory_det,id',
            'sales_person_id' => 'required|integer|exists:users,id',
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'number' => 'required|string|max:20',
            'inv_status' => 'required|in:hold,sold,cancel',
        ]);

        DB::beginTransaction();
        try 
        {
            DB::table('inventory_det')
                ->where('id', $request->id)
                ->update([
                    'sales_person_id' => $request->sales_person_id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'number' => $request->number,
                    'status' => $request->inv_status,
                    'updated_at' => now(),
                ]);

            DB::table('inventory_history')->insert([
                'inventory_det_id' => $request->id,
                'sales_person_id' => $request->sales_person_id,
                'name' => $request->name,
                'email' => $request->email,
                'number' => $request->number,
                'status' => $request->inv_status,
                'created_at' => now(),
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Inventory sale updated successfully.');
        } 
        catch (\Exception $error) 
        {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update sale. Please try again. ' . $error->getMessage());
        }
    }

    public function getSaleHistory(Request $request)
    {
        $request->validate(['id' => 'required|integer|exists:inventory_det,id']);

        $history = DB::table('inventory_history as h')
            ->leftJoin('users as u', 'h.sales_person_id', '=', 'u.id')
            ->where('h.inventory_det_id', $request->id)
            ->orderBy('h.created_at', 'desc')
            ->select('h.*', 'u.name as sales_person_name')
            ->get();

        return response()->json($history);
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'inventory_id' => 'required|integer|exists:projects,id',
            'inventory_file' => 'required|file|mimes:csv,txt'
        ]);

        if ($validator->fails()) 
        {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', $validator->errors()->first());
        }

        try 
        {
            $file = $request->file('inventory_file');
            $projectId = $request->inventory_id;
            $imported = 0;
            $errors = [];

            if ($file->getClientOriginalExtension() == 'csv' || $file->getClientOriginalExtension() == 'txt') {
                $handle = fopen($file->getPathname(), 'r');
                $header = fgetcsv($handle);
                while (($row = fgetcsv($handle)) !== FALSE) 
                {
                    if (count($row) >= 2) 
                    {
                        $unitNo = trim($row[0]);
                        $size = trim($row[1]);
                        
                        if (!empty($unitNo) && !empty($size)) 
                        {
                            $exists = DB::table('inventory_det')
                                ->where('inventory_id', $projectId)
                                ->where('unit_no', $unitNo)
                                ->exists();
                                
                            if (!$exists) 
                            {
                                DB::table('inventory_det')->insert([
                                    'inventory_id' => $projectId,
                                    'unit_no' => $unitNo,
                                    'size' => $size,
                                    'status' => 'pending',
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                                $imported++;
                            }
                            else
                            {
                                $errors[] = "Unit {$unitNo} already exists";
                            }
                        }
                    }
                }
                fclose($handle);
            }

            $message = "Successfully imported {$imported} inventory units.";
            if (!empty($errors)) 
            {
                $message .= " Errors: " . implode(', ', array_slice($errors, 0, 5));
                if (count($errors) > 5) 
                {
                    $message .= " and " . (count($errors) - 5) . " more";
                }
            }
            return redirect()->back()->with('success', $message);
        } 
        catch (\Exception $error) 
        {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Import failed: ' . $error->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="inventory_template.csv"',
        ];

        $callback = function() 
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['unit_no', 'size']);
            fputcsv($file, ['UNIT-001', '1000 sqft']);
            fputcsv($file, ['UNIT-002', '1500 sqft']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}