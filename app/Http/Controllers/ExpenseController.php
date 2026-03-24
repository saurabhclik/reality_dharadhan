<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $activeFeatures = Session::get('active_features', []);
        if(in_array('expense_management', $activeFeatures))
        {
            $status = $request->input('status');
            $fromDt = $request->input('fromDt');
            $toDt = $request->input('toDt');
            $userId = $request->input('users');
            $category = $request->input('excategory');
            $childIds = explode(',', Session::get('child_ids') ?? '');

            $query = DB::table('expenses as a')
                ->join('users as b', 'a.user_id', '=', 'b.id')
                ->select('a.*', 'b.name as users')
                ->whereIn('a.user_id', $childIds)
                ->orderBy('a.exp_date', 'desc');

            if (!empty($status)) 
            {
                $query->where('a.status', $status);
            }

            if (!empty($fromDt)) 
            {
                $query->whereDate('a.exp_date', '>=', $fromDt);
            }

            if (!empty($toDt)) 
            {
                $query->whereDate('a.exp_date', '<=', $toDt);
            }

            if (!empty($userId)) 
            {
                $query->where('a.user_id', $userId);
            }

            if (!empty($category)) 
            {
                $query->where('a.category', $category);
            }

            $expenses = $query->get();
            $totalAmount = $expenses->sum('amount');

            $users = DB::table('users')
                ->whereIn('id', $childIds)
                ->get();

            return view('expense.index', [
                'expenses' => $expenses,
                'totalAmount' => $totalAmount,
                'status' => $status,
                'fromDt' => $fromDt,
                'toDt' => $toDt,
                'userId' => $userId,
                'category' => $category,
                'users' => $users,
            ]);
        }
        else
        {
            abort(404);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'exp_date' => 'required|date',
            'comment' => 'required|string',
            'files.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        DB::transaction(function () use ($request) 
        {
            $expenseId = DB::table('expenses')->insertGetId([
                'user_id' => Session::get('user_id'),
                'title' => $request->title,
                'category' => $request->category,
                'amount' => $request->amount,
                'comments' => $request->comment,
                'exp_date' => $request->exp_date,
                'status' => 'pending',
            ]);

            if ($request->hasFile('files')) 
            {
                foreach ($request->file('files') as $file) 
                {
                    $path = $file->store('expense_images', 'public');
                    
                    DB::table('expense_img')->insert([
                        'expense_id' => $expenseId,
                        'img_url' => $path,
                    ]);
                }
            }
        });

        return redirect()->route('expense.index')->with('success', 'Expense saved successfully');
    }

    public function accept($id)
    {
        if (Session::get('user_type') !== 'super_admin') 
        {
            return back()->with('error', 'Unauthorized action');
        }

        DB::table('expenses')
            ->where('id', $id)
            ->update([
                'status' => 'accepted',
                'exp_date' => now()
            ]);
            
        return back()->with('success', 'Expense accepted successfully');
    }

    public function reject($id)
    {
        if (Session::get('user_type') !== 'super_admin') 
        {
            return back()->with('error', 'Unauthorized action');
        }

        DB::table('expenses')
            ->where('id', $id)
            ->update([
                'status' => 'rejected',
                'exp_date' => now()
            ]);
            
        return back()->with('success', 'Expense rejected successfully');
    }

    public function clear($id)
    {
        if(Session::get('user_type') != 'super_admin')
        {
            return back()->with('error', 'Unauthorized action');
        }
        DB::table('expenses')
            ->where('id', $id)
            ->update([
                'status' => 'clear',
                'exp_date' => now()
            ]);
            
        return back()->with('success', 'Expense clear successfully');
    }
    public function bulkAccept(Request $request)
    {
        if (Session::get('user_type') !== 'super_admin') 
        {
            return back()->with('error', 'Unauthorized action');
        }

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:expenses,id'
        ]);

        DB::table('expenses')
            ->whereIn('id', $request->ids)
            ->update([
                'status' => 'accepted',
                'exp_date' => now()
            ]);
            
        return back()->with('success', 'Selected expenses accepted successfully');
    }

    public function bulkReject(Request $request)
    {
        if (Session::get('user_type') !== 'admin') 
        {
            return back()->with('error', 'Unauthorized action');
        }

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:expenses,id'
        ]);

        DB::table('expenses')
            ->whereIn('id', $request->ids)
            ->update([
                'status' => 'rejected',
                'exp_date' => now()
            ]);
            
        return back()->with('success', 'Selected expenses rejected successfully');
    }

    public function getImages($id)
    {
        $images = DB::table('expense_img')
            ->where('expense_id', $id)
            ->get()
            ->map(function ($img) 
            {
                return asset('storage/' . $img->img_url);
            });

        return response()->json(['images' => $images]);
    }
}