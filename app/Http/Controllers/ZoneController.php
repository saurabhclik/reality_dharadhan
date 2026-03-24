<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Flasher\Laravel\Facade\Flasher;
class ZoneController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $query = DB::table('zones')
                ->leftJoin('state_district', 'zones.city_id', '=', 'state_district.id')
                ->select('zones.*', 'state_district.District as district_name', 'state_district.state');

            if ($request->has('search') && !empty($request->search)) 
            {
                $search = $request->search;
                $query->where(function($q) use ($search) 
                {
                    $q->where('zones.zone_name', 'like', "%{$search}%")
                      ->orWhere('zones.sub_area', 'like', "%{$search}%")
                      ->orWhere('zones.pincode', 'like', "%{$search}%")
                      ->orWhere('state_district.District', 'like', "%{$search}%");
                });
            }

            if ($request->has('status') && $request->status !== '') 
            {
                $query->where('zones.status', $request->status);
            }

            if ($request->has('city_id') && !empty($request->city_id)) 
            {
                $query->where('zones.city_id', $request->city_id);
            }

            $sortField = $request->get('sort_field', 'zone_order');
            $sortDirection = $request->get('sort_direction', 'asc');

            if ($sortField == 'district') 
            {
                $query->orderBy('state_district.District', $sortDirection);
            } 
            else 
            {
                $query->orderBy('zones.' . $sortField, $sortDirection);
            }
            
            $zones = $query->paginate(15)->withQueryString();
            $cities = DB::table('state_district')
                ->where('state', 'Rajasthan')
                ->orderBy('District')
                ->get(['id', 'District as city_name']);
            return view('zones.index', compact('zones', 'cities'));
            
        } 
        catch (\Exception $e)
        {
            return redirect()->back()->with('error', 'Error loading zones: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try 
        {
            $cities = DB::table('state_district')
                ->where('state', 'Rajasthan')
                ->orderBy('District')
                ->get(['id', 'District as city_name']);
            
            $maxOrder = DB::table('zones')->max('zone_order') + 1;
            return view('zones.create', compact('cities', 'maxOrder'));
            
        } 
        catch (\Exception $e) 
        {
            return redirect()->back()->with('error', 'Error loading create form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city_id' => 'required|integer|exists:state_district,id',
            'zone_name' => 'required|string|max:255',
            'sub_area' => 'nullable|string|max:255',
            'pincode' => 'nullable|string|max:10|regex:/^[0-9]+$/',
            'zone_order' => 'nullable|integer|min:0',
            'status' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) 
        {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try 
        {
            DB::beginTransaction();
            $city = DB::table('state_district')
                ->where('id', $request->city_id)
                ->where('state', 'Rajasthan')
                ->first();

            if (!$city) 
            {
                return redirect()->back()
                    ->with('error', 'Invalid city/district. Only Rajasthan cities are allowed.')
                    ->withInput();
            }
            $zoneId = DB::table('zones')->insertGetId([
                'city_id' => $request->city_id,
                'zone_name' => $request->zone_name,
                'sub_area' => $request->sub_area,
                'pincode' => $request->pincode,
                'zone_order' => $request->zone_order ?? DB::table('zones')->max('zone_order') + 1,
                'status' => $request->has('status') ? 1 : 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();
            return redirect()->route('zone.index')
                ->with('success', 'Zone created successfully.');

        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error creating zone: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        try 
        {
            $zone = DB::table('zones')
                ->where('id', $id)
                ->first();

            if (!$zone) 
            {
                return redirect()->route('zone.index')
                    ->with('error', 'Zone not found.');
            }
            $cities = DB::table('state_district')
                ->where('state', 'Rajasthan')
                ->orderBy('District')
                ->get(['id', 'District as city_name']);
            
            return view('zones.edit', compact('zone', 'cities'));
            
        } 
        catch (\Exception $e) 
        {
            return redirect()->route('zone.index')
                ->with('error', 'Error loading edit form: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'city_id' => 'required|integer|exists:state_district,id',
            'zone_name' => 'required|string|max:255',
            'sub_area' => 'nullable|string|max:255',
            'pincode' => 'nullable|string|max:10|regex:/^[0-9]+$/',
            'zone_order' => 'nullable|integer|min:0',
            'status' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) 
        {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try 
        {
            DB::beginTransaction();
            $zone = DB::table('zones')
                ->where('id', $id)
                ->first();

            if (!$zone) 
            {
                return redirect()->route('zone.index')
                    ->with('error', 'Zone not found.');
            }
            $city = DB::table('state_district')
                ->where('id', $request->city_id)
                ->where('state', 'Rajasthan')
                ->first();

            if (!$city) 
            {
                return redirect()->back()
                    ->with('error', 'Invalid city/district. Only Rajasthan cities are allowed.')
                    ->withInput();
            }
            DB::table('zones')
                ->where('id', $id)
                ->update([
                    'city_id' => $request->city_id,
                    'zone_name' => $request->zone_name,
                    'sub_area' => $request->sub_area,
                    'pincode' => $request->pincode,
                    'zone_order' => $request->zone_order ?? $zone->zone_order,
                    'status' => $request->has('status') ? 1 : 0,
                    'updated_at' => now()
                ]);

            DB::commit();
            return redirect()->route('zone.index')
                ->with('success', 'Zone updated successfully.');

        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error updating zone: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try 
        {
            DB::beginTransaction();
            $zone = DB::table('zones')
                ->where('id', $id)
                ->first();

            if (!$zone) 
            {
                return redirect()->route('zone.index')
                    ->with('error', 'Zone not found.');
            }
            $userCount = DB::table('users')
                ->where('zone_id', $id)
                ->count();
            if ($userCount > 0) 
            {
                return redirect()->back()
                    ->with('error', 'Cannot delete zone because it has ' . $userCount . ' associated users.');
            }
            DB::table('zones')
                ->where('id', $id)
                ->delete();

            DB::commit();
            return redirect()->route('zone.index')
                ->with('success', 'Zone deleted successfully.');

        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error deleting zone: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        if ($validator->fails()) 
        {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $errors = [];
        $imported = 0;
        $totalRows = 0;

        try 
        {
            if (($handle = fopen($path, 'r')) !== false) 
            {
                $header = fgetcsv($handle);
                $totalRows++;
                $expectedHeaders = ['city_id', 'zone_name', 'sub_area', 'pincode', 'zone_order', 'status'];
                if ($header !== $expectedHeaders) 
                {
                    fclose($handle);
                    return redirect()->back()->with('import_errors', 
                        ['Invalid CSV format. Expected headers: ' . implode(', ', $expectedHeaders)]);
                }
                $rajasthanCityIds = DB::table('state_district')
                    ->where('state', 'Rajasthan')
                    ->pluck('id')
                    ->toArray();
                $rowNumber = 1;
                DB::beginTransaction();

                while (($data = fgetcsv($handle)) !== false)
                {
                    $rowNumber++;
                    $totalRows++;
                    if (empty(array_filter($data))) 
                    {
                        continue;
                    }
                    $rowData = array_combine($header, $data);
                    $rowValidator = Validator::make($rowData, [
                        'city_id' => 'required|integer',
                        'zone_name' => 'required|string|max:255',
                        'sub_area' => 'nullable|string|max:255',
                        'pincode' => 'nullable|string|max:10',
                        'zone_order' => 'nullable|integer|min:0',
                        'status' => 'nullable|in:0,1'
                    ]);

                    if ($rowValidator->fails()) 
                    {
                        $errors[] = "Row {$rowNumber}: " . implode(', ', $rowValidator->errors()->all());
                        continue;
                    }
                    if (!in_array($rowData['city_id'], $rajasthanCityIds)) 
                    {
                        $errors[] = "Row {$rowNumber}: City ID {$rowData['city_id']} is not a valid Rajasthan city";
                        continue;
                    }

                    try 
                    {
                        DB::table('zones')->insert([
                            'city_id' => $rowData['city_id'],
                            'zone_name' => $rowData['zone_name'],
                            'sub_area' => $rowData['sub_area'] ?? null,
                            'pincode' => $rowData['pincode'] ?? null,
                            'zone_order' => $rowData['zone_order'] ?? DB::table('zones')->max('zone_order') + 1,
                            'status' => isset($rowData['status']) ? (int)$rowData['status'] : 1,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        
                        $imported++;
                        
                    } 
                    catch (\Exception $e) 
                    {
                        $errors[] = "Row {$rowNumber}: " . $e->getMessage();
                    }
                }
                fclose($handle);

                if (empty($errors)) 
                {
                    DB::commit();
                    $message = "Successfully imported {$imported} out of " . ($totalRows - 1) . " zones.";
                    return redirect()->route('zone.index')->with('success', $message);
                } 
                else 
                {
                    DB::rollBack();
                    $message = "Imported {$imported} zones with errors. Check error list below.";
                    return redirect()->back()->with('warning', $message)->with('import_errors', $errors);
                }
            }

            return redirect()->back()->with('error', 'Could not read the CSV file.');

        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            if (isset($handle) && is_resource($handle)) 
            {
                fclose($handle);
            }
            return redirect()->back()
                ->with('error', 'Error importing zones: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        try 
        {
            DB::beginTransaction();

            $zone = DB::table('zones')
                ->where('id', $id)
                ->first();

            if (!$zone) 
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Zone not found.'
                ], 404);
            }

            $newStatus = $zone->status ? 0 : 1;

            DB::table('zones')
                ->where('id', $id)
                ->update([
                    'status' => $newStatus,
                    'updated_at' => now()
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'message' => 'Zone status updated successfully.'
            ]);

        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating zone status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getByCity($cityId)
    {
        try 
        {
            $city = DB::table('state_district')
                ->where('id', $cityId)
                ->where('state', 'Rajasthan')
                ->first();

            if (!$city) 
            {
                return response()->json([
                    'success' => false,
                    'message' => 'City not found or not a Rajasthan city.'
                ], 404);
            }

            $zones = DB::table('zones')
                ->where('city_id', $cityId)
                ->where('status', 1)
                ->orderBy('zone_order')
                ->orderBy('zone_name')
                ->get(['id', 'zone_name', 'sub_area', 'pincode']);
            
            return response()->json([
                'success' => true,
                'data' => $zones
            ]);

        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Error loading zones: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCities()
    {
        try 
        {
            $cities = DB::table('state_district')
                ->where('state', 'Rajasthan')
                ->orderBy('District')
                ->get(['id', 'District as name']);
            
            return response()->json([
                'success' => true,
                'data' => $cities
            ]);

        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Error loading cities: ' . $e->getMessage()
            ], 500);
        }
    }
}