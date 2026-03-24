<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Flasher\Laravel\Facade\Flasher;

class PostSaleController extends Controller
{
    public function index()
    {
        $activeFeatures = Session::get('active_features', []);
        if (!in_array('post_sale', $activeFeatures)) {
            abort(404);
        }

        try 
        {
            $postSales = DB::table('post_sales')
                ->leftJoin('leads', 'post_sales.lead_id', '=', 'leads.id')
                ->leftJoin('users as sales_persons', 'post_sales.sales_person_id', '=', 'sales_persons.id')
                ->select(
                    'post_sales.*',
                    'leads.name as lead_name',
                    'sales_persons.name as sales_person_name'
                )
                ->latest('post_sales.created_at')
                ->get();

            $leads = DB::table('leads')->get();
            $salesPersons = DB::table('users')->where('role', 'salesman')->get();
            $checklistItems = DB::table('checklist')->where('type', 'post_sale')->get();
            $projectCategories = DB::table('inv_catg')->get();

            $projects = DB::table('projects')->get();
            return view('post-sale.index', compact(
                'postSales',
                'leads',
                'salesPersons',
                'checklistItems',
                'projectCategories',
                'projects'
            ));
        } 
        catch (\Exception $e) 
        {
            Flasher::addError('Failed to load data: ' . $e->getMessage());
            return back();
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_id' => 'required|exists:leads,id',
            'sales_person_id' => 'required|exists:users,id',
            'applicant_name' => 'required|string|max:255',
            'applicant_number' => 'required|string|max:20',
            'project_name' => 'required|string|max:255',
            'unit_number' => 'required|string|max:50',
            'project_category' => 'required|string|max:255',
            'project_sub_category' => 'nullable|string|max:255',
            'dob' => 'required|date',
            'doa' => 'nullable|date|after_or_equal:dob',
            'permanent_address' => 'required|string',
            'current_address' => 'required|string',
            'email' => 'nullable|email|max:255',
            'checklist' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            $errors = '<ul style="text-align:left;margin:0;padding-left:20px;">';
            foreach ($validator->errors()->all() as $error) {
                $errors .= "<li>$error</li>";
            }
            $errors .= '</ul>';
            Flasher::addError('Please fix errors:<br>' . $errors);
            return back()->withInput();
        }

        DB::beginTransaction();
        try {
            DB::table('post_sales')->insert([
                'lead_id' => $request->lead_id,
                'sales_person_id' => $request->sales_person_id,
                'applicant_name' => $request->applicant_name,
                'applicant_number' => $request->applicant_number,
                'project_name' => $request->project_name,
                'unit_number' => $request->unit_number,
                'project_category' => $request->project_category,
                'project_sub_category' => $request->project_sub_category,
                'dob' => $request->dob,
                'doa' => $request->doa,
                'email' => $request->email,
                'permanent_address' => $request->permanent_address,
                'current_address' => $request->current_address,
                'checklist' => json_encode($request->checklist ?? []),
                'user_id' => Session::get('user_id'),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();
            Flasher::addSuccess('Customer added successfully!');
            return redirect()->route('post-sale.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Flasher::addError('Failed: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function edit($id)
    {
        $postSale = DB::table('post_sales')->find($id);
        return $postSale
            ? response()->json(['success' => 200, 'data' => $postSale])
            : response()->json(['success' => 404, 'message' => 'Not found']);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'lead_id' => 'required|exists:leads,id',
            'sales_person_id' => 'required|exists:users,id',
            'applicant_name' => 'required|string|max:255',
            'applicant_number' => 'required|string|max:20',
            'project_name' => 'required|string|max:255',
            'unit_number' => 'required|string|max:50',
            'project_category' => 'required|string|max:255',
            'project_sub_category' => 'nullable|string|max:255',
            'dob' => 'required|date',
            'doa' => 'nullable|date|after_or_equal:dob',
            'permanent_address' => 'required|string',
            'current_address' => 'required|string',
            'email' => 'nullable|email|max:255',
            'checklist' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            $errors = '<ul style="text-align:left;margin:0;padding-left:20px;">';
            foreach ($validator->errors()->all() as $error) $errors .= "<li>$error</li>";
            $errors .= '</ul>';
            Flasher::addError('Fix errors:<br>' . $errors);
            return back()->withInput();
        }

        DB::beginTransaction();
        try {
            $updated = DB::table('post_sales')->where('id', $id)->update([
                'lead_id' => $request->lead_id,
                'sales_person_id' => $request->sales_person_id,
                'applicant_name' => $request->applicant_name,
                'applicant_number' => $request->applicant_number,
                'project_name' => $request->project_name,
                'unit_number' => $request->unit_number,
                'project_category' => $request->project_category,
                'project_sub_category' => $request->project_sub_category,
                'dob' => $request->dob,
                'doa' => $request->doa,
                'email' => $request->email,
                'permanent_address' => $request->permanent_address,
                'current_address' => $request->current_address,
                'checklist' => json_encode($request->checklist ?? []),
                'updated_at' => now()
            ]);

            if (!$updated) throw new \Exception('Update failed');

            DB::commit();
            Flasher::addSuccess('Updated successfully!');
            return redirect()->route('post-sale.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Flasher::addError('Update failed: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $deleted = DB::table('post_sales')->where('id', $id)->delete();
            if (!$deleted) {
                return response()->json(['success' => 404, 'message' => 'Not found']);
            }
            DB::commit();
            return response()->json(['success' => 200, 'message' => 'Deleted!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function getSubcategories($categoryName)
    {
        $category = DB::table('inv_catg')->where('name', $categoryName)->first();
        if (!$category) {
            return response()->json(['success' => 404, 'message' => 'Category not found']);
        }

        $subcategories = DB::table('inv_subcatg')
            ->where('catg_id', $category->id)
            ->select('id', 'name')
            ->get();

        return response()->json([
            'success' => 200,
            'data' => $subcategories
        ]);
    }

    // DOCUMENTS
    public function getDocuments($postSaleId)
    {
        $documents = DB::table('post_sale_documents')
            ->where('post_sale_id', $postSaleId)
            ->select('id', 'document_name', 'file_path', 'file_type', 'file_size', 'created_at')
            ->latest()
            ->get();

        return response()->json([
            'success' => 200,
            'data' => $documents
        ]);
    }

    public function uploadDocument(Request $request, $postSaleId)
    {
        $validator = Validator::make($request->all(), [
            'document' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'document_name' => 'required|string|max:255'
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['success' => 422, 'message' => $validator->errors()->first()]);
        }

        if (!DB::table('post_sales')->where('id', $postSaleId)->exists()) 
        {
            return response()->json(['success' => 404, 'message' => 'Post sale not found']);
        }

        DB::beginTransaction();
        try 
        {
            $file = $request->file('document');
            $ext = $file->getClientOriginalExtension();
            $fileName = time() . '_' . Str::slug($request->document_name) . '.' . $ext;
            $filePath = $file->storeAs('post_sale_documents', $fileName, 'public');

            $docId = DB::table('post_sale_documents')->insertGetId([
                'post_sale_id' => $postSaleId,
                'document_name' => $request->document_name,
                'file_path' => $filePath,
                'file_type' => $ext,
                'file_size' => $file->getSize(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();
            return response()->json([
                'success' => 200,
                'message' => 'Uploaded!',
                'document_id' => $docId
            ]);
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return response()->json(['success' => 500, 'message' => 'Upload failed: ' . $e->getMessage()]);
        }
    }

    public function deleteDocument($documentId)
    {
        DB::beginTransaction();
        try 
        {
            $doc = DB::table('post_sale_documents')->find($documentId);
            if (!$doc) 
            {
                return response()->json(['success' => 404, 'message' => 'Document not found']);
            }

            Storage::disk('public')->delete($doc->file_path);
            DB::table('post_sale_documents')->where('id', $documentId)->delete();

            DB::commit();
            return response()->json(['success' => 200, 'message' => 'Deleted!']);
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return response()->json(['success' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function generateRatingLink(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:post_sales,id'
        ]);
        
        $id = $request->id;
        DB::table('shared_post_sale')
            ->where('post_sale_id', $id)
            ->delete();

        $token = Str::random(32);
        $expiresAt = now()->addDays(7);
        
        DB::table('shared_post_sale')->insert([
            'post_sale_id' => $id,
            'token' => $token,
            'expires_at' => $expiresAt,
            'created_at' => now()
        ]);

        return response()->json([
            'link' => route('rate.form', ['token' => $token])
        ]);
    }

    public function rate($token)
    {
        $shared = DB::table('shared_post_sale')
            ->where('token', $token)
            ->first();

        if (!$shared || now() > $shared->expires_at) 
        {
            return "<h2 style='text-align:center; margin:100px; color:red;'>Link expired or invalid</h2>";
        }

        $ps = DB::table('post_sales')
            ->where('id', $shared->post_sale_id)
            ->first();

        $ip = request()->ip();
        $rated = DB::table('post_sale_ratings')
            ->where('post_sale_id', $shared->post_sale_id)
            ->where('ip_address', $ip)
            ->exists();

        if (request()->method() === 'POST') 
        {
            if ($rated) 
            {
                return "<div style='text-align:center; margin:100px;'><h2 style='color:orange;'>You have already submitted a rating!</h2></div>";
            }
            $rating = request('rating');
            $comment = request('comment');

            DB::table('post_sale_ratings')->insert([
                'post_sale_id' => $shared->post_sale_id,
                'customer_name' => $ps->applicant_name ?? '', 
                'customer_email' => $ps->email ?? '',
                'rating' => $rating,
                'comments' => $comment,
                'ip_address' => $ip,
                'created_at' => now()
            ]);
            return "
            <div style='text-align:center; margin:100px; font-family:Arial;'>
                <h1 style='color:green;'>Thank You!</h1>
                <p>Your rating has been recorded.</p>
                <p><small>You can close this window now.</small></p>
            </div>";
        }
        return view('post-sale.rating', compact('ps', 'token', 'rated'));
    }
}