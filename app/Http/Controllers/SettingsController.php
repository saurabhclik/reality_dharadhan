<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Flasher\Laravel\Facade\Flasher;
use Carbon\Carbon;
class SettingsController extends Controller
{
    public function profile()
    {
        $user = DB::table('users')
            ->where('id', Session::get('user_id'))
            ->first();

        return view('settings.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.Session::get('user_id'),
            'phone' => 'nullable|string|max:20'
        ]);

        if ($validator->fails()) 
        {
            foreach ($validator->errors()->all() as $error) 
            {
                Flasher::addError($error);
            }
            return redirect()->back()->withInput();
        }

        DB::table('users')
            ->where('id', Session::get('user_id'))
            ->update([
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->phone,
                'updated_date' => now()
            ]);

        Flasher::addSuccess('Profile updated successfully');
        return back();
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:4',
            'password_confirmation' => 'required|same:password'
        ]);

        if ($validator->fails()) 
        {
            foreach ($validator->errors()->all() as $error) 
            {
                Flasher::addError($error);
            }
            return redirect()->back()->withInput();
        }

        DB::table('users')
            ->where('id', Session::get('user_id'))
            ->update([
                'password' => $request->password,
                'updated_date' => now()
            ]);

        Flasher::addSuccess('Password updated successfully');
        return back();
    }

    public function change_logo()
    {
        $user_role = session()->get('user_type');
        if ($user_role !== 'super_admin' && $user_role !== 'divisional_head' ) 
        {
            abort(404); 
        }
        return view('settings.change-logo');
    }

    public function update_logo(Request $request)
    {
        $user_type = Session::get('user_type'); 
        if ($user_type != 'super_admin') 
        {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'file' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) 
        {
            foreach ($validator->errors()->all() as $error) 
            {
                Flasher::addError($error);
            }
            return redirect()->back()->withInput();
        }

        $upload_dir = 'uploads/';
        $file = $request->file('file');
        $pic_name = $file->getClientOriginalName();
        $random_name = rand(1000, 1000000) . "-" . $pic_name;
        $upload_name = $upload_dir . strtolower($random_name);
        $upload_name = preg_replace('/\s+/', '-', $upload_name);

        try 
        {
            $file->move(public_path($upload_dir), $upload_name);
            DB::table('settings')->update(['logo' => $upload_name]);
            
            Flasher::addSuccess('Logo updated successfully');
            return redirect()->route('setting.logo');
        } 
        catch (\Exception $e) 
        {
            Flasher::addError('Error while uploading logo: ' . $e->getMessage());
            return redirect()->route('setting.logo');
        }
    }

    public function login_log()
    {
        $user_role = session()->get('user_type');
        if ($user_role !== 'super_admin' && $user_role !== 'divisional_head' ) 
        {
            abort(404); 
        }
        $user_type = Session::get('user_type');
        if ($user_type != 'super_admin') 
        {
            abort(403, 'Unauthorized action.');
        }

        $logs = DB::table('login_logs as a')
            ->join('users as b', 'a.user_id', '=', 'b.id')
            ->select('a.*', 'b.name', 'b.mobile')
            ->orderBy('a.login_date', 'desc')
            ->paginate(10);

        return view('settings.login-logs', compact('logs'));
    }

    public function index()
    {
        $notifications = DB::table('user_notification')
            ->where('UserId', session()->get('user_id'))
            ->orderBy('CreatedDate', 'desc')
            ->paginate(20);

        foreach ($notifications as $noti) 
        {
            $noti->time_diff = Carbon::parse($noti->CreatedDate)->diffForHumans();
        }

        return view('partial.notification', compact('notifications'));
    }
    
    public function markAllAsRead()
    {
        DB::table('user_notification')
            ->where('UserId', session()->get('user_id'))
            ->update(['ack' => 1]);
        
        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    protected function getTimeDifference($datetime)
    {
        $now = new \DateTime();
        $created = new \DateTime($datetime);
        $diff = $now->diff($created);

        if ($diff->y > 0) return $diff->y . ' year(s) ago';
        if ($diff->m > 0) return $diff->m . ' month(s) ago';
        if ($diff->d > 0) return $diff->d . ' day(s) ago';
        if ($diff->h > 0) return $diff->h . ' hour(s) ago';
        if ($diff->i > 0) return $diff->i . ' minute(s) ago';
        return 'just now';
    }

    public function ratings()
    {
        $user_role = session()->get('user_type');
        if ($user_role !== 'super_admin' && $user_role !== 'divisional_head') 
        {
            abort(404);
        }

        $ratings = DB::table('post_sale_ratings as r')
            ->join('post_sales as s', 'r.post_sale_id', '=', 's.id')
            ->select(
                'r.id',
                'r.rating',
                'r.comments',
                'r.ip_address',
                'r.created_at',
                's.applicant_name',
                's.applicant_number',
                's.project_name',
                's.unit_number'
            )
            ->orderBy('r.created_at', 'desc')
            ->paginate(10);

        return view('settings.ratings', compact('ratings'));
    }
}