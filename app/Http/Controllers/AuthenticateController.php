<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Flasher\Laravel\Facade\Flasher;
use App\Mail\ForgotMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
class AuthenticateController extends Controller
{

    public function show_login()
    {
        $logo = DB::table('settings')->where('id', 1)->value('logo');
        return view('login', compact('logo'));
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) 
        {
            Flasher::addError($validator);
            return redirect('/')->withErrors($validator)->withInput();
        }

        $user = DB::table('users')
            ->where('email', $request->email)
            ->where('is_active', 1)
            ->first();

        $software = DB::table('software_details')
            ->where('status', 'active')
            ->first();

        if (!$user) 
        {
            Flasher::addError('Invalid Credentials, please try again');
            return redirect('/')->withErrors('Invalid Credentials, please try again')->withInput();
        }

        if ($user && $user->password === $request->password) 
        {
            $token = $this->generateToken(12);
            $child_ids = [];
            $iterable = [$user->id]; 
            while (!empty($iterable)) 
            {
                $children = DB::table('users')
                    ->whereIn('tm_id', $iterable)
                    ->pluck('id')
                    ->toArray();

                foreach ($iterable as $id) 
                {
                    if (!in_array($id, $child_ids)) 
                    {
                        $child_ids[] = $id;
                    }
                }
                $iterable = array_filter($children, fn($id) => !in_array($id, $child_ids));
            }

            $activeFeatures = DB::table('software_features')
                ->where('status', 'active')
                ->whereRaw("LOWER(TRIM(feature_name)) != 'integration'")
                ->pluck('feature_name')
                ->toArray();

            $softwareInfo = DB::table('software_details')
                ->where('status', 'active')
                ->whereNotNull('apk')
                ->where('apk', '!=', '')
                ->select('software_name', 'apk', 'version')
                ->first();

            $logo_path = DB::table('settings')->value('logo');
            $hasActiveAttendance = DB::table('attendance')
                ->where('user_id', $user->id)
                ->whereDate('start_time', today())
                ->whereNull('end_time')
                ->exists();

            $softwareType = $software->software_type ?? 'real_state';

            Session::put('platform', 'web');
            Session::put('active_features', $activeFeatures);
            Session::put('child_ids', implode(', ', $child_ids));
            Session::put('software_type', $softwareType);
            Session::put([
                'user_type' => $user->role,
                'user_name' => $user->name,
                'user_id' => $user->id,
                'user_mobile' => $user->mobile,
                'user_email' => $user->email,
                'token' => $token,
                'logo'       => $logo_path,
                'last_login' => $user->last_login,
                'attendance_active' => $hasActiveAttendance,
                'software_name' => $software->software_name,
                'client_name' => $software->client_name,
                'description' => $software->description,
                'version' => $software->version,
                'status' => $software->status,
                'created_at' => $software->created_at,
                'software_info' =>  $softwareInfo
            ]);

            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'token' => $token,
                    'current_location' => $request->latitude . ',' . $request->longitude,
                    'last_login' => Carbon::now()->format('Y-m-d H:i:s'),
                ]);

            DB::table('login_logs')->insert([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
            ]);

            Flasher::addSuccess('Login successful!');
            return $this->redirectBasedOnSoftwareType($softwareType);
        } 
        else 
        {
            Flasher::addError('Invalid Credentials, please try again');
            return redirect('/')->withErrors('Invalid Credentials, please try again')->withInput();
        }
    }

    private function redirectBasedOnSoftwareType($softwareType)
    {
        switch ($softwareType) 
        {
            case 'task_management':
                return redirect()->route('task.list');
            case 'mis_management':
                return redirect()->route('mis.summary-report');
            case 'lead_management':
            case 'real_state':
            default:
                return redirect()->route('dashboard');
        }
    }

    private function generateToken($length = 12)
    {
        return bin2hex(random_bytes($length));
    }

    private function current_url()
    {
        $url = $_SERVER['REQUEST_URI'] ?? '';
        $url1 = explode("?", $url);
        return $url1[0];
    }

    public function logout()
    {
        Session::flush();
        Flasher::addSuccess('logout successfully!');
        return redirect('/')->withErrors('Logut succesfully')->withInput();
    }

    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) 
        {
            Flasher::addError('The provided email is invalid.');
            return back()->withErrors($validator)->withInput();
        }

        $token = Str::random(64);
        DB::table('users')->where('email', $request->email)->update([
            'password_reset_token' => $token,
        ]);

        $resetLink = route('password.reset', $token);
        $settings = DB::table('integration_settings')
            ->where('integration_type', 'gmail')
            ->where('status', 'active')
            ->first();

        $fromAddress = config('mail.from.address');
        $fromName    = config('mail.from.name');

        if ($settings) 
        {
            $config = json_decode($settings->settings, true);
            $fromAddress = $config['mail_from_address'] ?? $fromAddress;
            $fromName    = $config['mail_from_name'] ?? $fromName;
        }

        try 
        {
            Mail::to($request->email)->send(
                new ForgotMail($resetLink, 'Password Reset', $fromAddress, $fromName)
            );

            Flasher::addSuccess('We have emailed your password reset link!');
            return back();
        } 
        catch (\Exception $e) 
        {
            Flasher::addError('Unable to send reset link. Please try again later.');
            return back()->withInput();
        }
    }

    public function showResetForm($token)
    {
        if (!$token) 
        {
            abort(500, 'Token expired or missing.');
        }

        $user = DB::table('users')
            ->where('password_reset_token', $token)
            ->first();

        if (!$user) 
        {
            Flasher::addError('Invalid password reset link.');
            return redirect()->route('login');
        }

        return view('forgot-password', ['token' => $token, 'email' => $user->email]);
    }

    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
            'token' => 'required'
        ]);

        if ($validator->fails()) 
        {
            return back()->withErrors($validator)->withInput();
        }

        $user = DB::table('users')
            ->where('email', $request->email)
            ->where('password_reset_token', $request->token)
            ->first();

        if (!$user) 
        {
            Flasher::addError('Invalid token or email address.');
            return back()->withInput();
        }

        DB::table('users')
        ->where('email', $request->email)
        ->update([
            'password' => $request->password,
            'password_reset_token' => null,
        ]);

        Flasher::addSuccess('Your password has been reset successfully!');
        return redirect('/');
    }

}
