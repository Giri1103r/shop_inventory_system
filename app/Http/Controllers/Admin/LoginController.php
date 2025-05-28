<?php

namespace App\Http\Controllers\Admin;

use Log;


use Exception;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\PasswordOTPEmail;
use Illuminate\Support\Carbon;
use App\Mail\PasswordResetEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Cache\RateLimiting\Limit;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

use Illuminate\Support\Facades\Cookie;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Session\Session as SessionSession;

class LoginController extends Controller
{
    protected $partner;

    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function showLoginForm()
    {

        if(Auth::check()){
            return redirect('dashboard');
        }

        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $rules = [
            'email' => 'required',
            'password' => 'required',
            'g-recaptcha-response' => 'required',
        ];
        $messages = [
            'email.required' => 'Please enter your email address!',
            'password.required' => 'Please enter your password',
            'g-recaptcha-response.required' => 'Please complete the reCAPTCHA verification',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $email = strtolower($request->email);
        $ipAddress = $request->ip();
        $throttleKey = "login_attempts:" . $email;
        if ($ipAddress) {
            if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
                $lockoutTime = RateLimiter::availableIn($throttleKey);
                $minutesLeft = ceil($lockoutTime / 60);
                Session::flash('error', "Too many failed login attempts. Try again in $minutesLeft minutes.");
                return back()->withErrors(['email' => "Too many failed login attempts. Try again in $minutesLeft minutes."]);
            }
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');



        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            RateLimiter::clear($throttleKey);

            $user = Auth::user();

            if ($user->status == 0) {
                Auth::logout();
                Session::flash('error', 'Employee no longer exists');
                return redirect()->back();
            }
            session()->put('locale', $user->language ?: env('APP_LOCALE'));

            Session::flash('success', 'Login successful');
            return redirect()->intended(admin_url('dashboard'));
        }
        RateLimiter::hit($throttleKey, 1800);

        Session::flash('error', 'Invalid Email or Password');
        return back()->withErrors(['email' => 'Email or Password is incorrect']);
    }


    public function logout(Request $request)
    {

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();


        return redirect(url('login'));
    }

    public function forgotPassword()
    {

        return view('auth.passwords.email');
    }

    public function sendOTP(Request $request)
    {
        try {
            $rules = [
                'email' => 'required',
            ];
            $messages = [
                'email.required' => 'Please enter the email',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {

                return redirect()->back()->withErrors($validator)->withInput();
            }

            $user = User::where('email', '=', $request->email)->first();



            if ($user == null) {

                Session::flash('error', 'Invalid Email');
                return redirect()->back();
            }

            if ($user->email == null || $user->email == '') {

                Session::flash('error', 'Please contact to the admin');
                return redirect()->back();
            }

            $username    = $user->username;
            $otp         = mt_rand(100000, 999999);
            $email       = $user->email;
            $expire_mins = 10;

            Cache::put('otp_' . $user->username, $otp, Carbon::now()->addMinutes($expire_mins));


            $user->otp = $otp;
            $user->update();

            $empDetails = [
                'name'     => $user->name,
                'username' => $username,
                'expire'   => $expire_mins,
                'otp'      => $otp
            ];

            Mail::to($user->email)->queue(new PasswordOTPEmail($empDetails));

            DB::table('password_resets')->where('email', $user->email)
                ->delete();

            DB::table('password_resets')->insert([
                'email'      => $user->email,
                'token'      => Str::random(60),
                'created_at' => Carbon::now()
            ]);

            $tokenData = DB::table('password_resets')
                ->where('email', $user->email)
                ->orderBy('created_at', 'Desc')
                ->first();

            $token = $tokenData->token;

            Session::flash('success', 'OTP sent to registered email Address');
            return redirect(admin_url('password/otp'))->with(['token' => $token, 'email' => $user->email]);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Please try after sometime!');
            return redirect()->back();
        }
    }


    public function resendOTP(Request $request)
    {
        $token = $request->token;
        $tokenData = DB::table('password_resets')->where('token', $token)->first();
        if (!$tokenData) {
            Session::flash('error', 'Access Denied!');
            return redirect()->back();
        }

        $user = User::where('email', $tokenData->email)->first();
        if (!$user) {
            Session::flash('error', 'Invalid Email');
            return redirect()->back();
        }

        $otp = mt_rand(100000, 999999);
        $expire_mins = 10;

        Cache::put('otp_' . $user->username, $otp, Carbon::now()->addMinutes($expire_mins));
        $user->otp = $otp;
        $user->save();

        $empDetails = [
            'name'     => $user->name,
            'username' => $user->username,
            'expire'   => $expire_mins,
            'otp'      => $otp,
        ];

        Mail::to($user->email)->queue(new PasswordOTPEmail($empDetails));

        Session::flash('success', 'OTP resent to registered email address');
        return redirect(admin_url('password/otp'))->with(['token' => $token, 'email' => $user->email]);
    }



    public function passwordOTP(Request $request)
    {

        $token = Session::get('token');
        $email = Session::get('email');
        if (!$token) {
            Session::flash('error', 'Access Denied!');
            return redirect()->back();
        }

        $expire_mins = 10;
        $newTime = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " -" . $expire_mins . " minutes"));

        $tokenData = DB::table('password_resets')
            ->where('token', $token)
            ->where('created_at', '>=', $newTime)
            ->first();

        if (!$tokenData) {
            Session::flash('error', 'Page Expired, Please try again');
            return redirect()->back();
        }

        $createdAt = Carbon::parse($tokenData->created_at);
        $remainingMinutes = now()->diffInMinutes($createdAt->addMinutes($expire_mins));
        $data = [
            'email'  => $email,
            'token'  => $token,
            'expire' => $remainingMinutes,
        ];


        return view('auth.passwords.otp', $data);
    }
    public function passwordOTPSubmit(Request $request)
    {
        $rules = [
            'otp' => 'required',
        ];
        $messages = [
            'otp.required' => 'Please enter the OTP',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $token = $request->token;

        $tokenData = DB::table('password_resets')
            ->where('token', $token)
            ->first();


        if ($tokenData == null) {
            Session::flash('error', 'Page Expired,Please try again');
            return redirect()->back();
        }

        $otp = $request->otp;
        $userCheck = User::where('email', $tokenData->email)->first();


        if ($otp != $userCheck->otp) {

            Session::flash('error', 'Invalid OTP');
            return redirect()->back()->with(['token' => $token, 'email' => $userCheck->email]);
        }

        $username = $userCheck->username;

        if ($userCheck != null && $userCheck != '') {

            $cacheKey = 'otp_' . $username;

            if (!Cache::has($cacheKey)) {
                Session::flash('error', 'OTP expired');

                return redirect(admin_url('password/forgot'));
            }

            $token = encryptId($userCheck->id) . "_" . Str::random(16);

            $userCheck->otp_token = $token;
            $userCheck->update();

            DB::table('password_resets')
                ->where('email', $userCheck->email)
                ->delete();


            return redirect(admin_url('password/finalreset/form'))->with(['otp_token' =>  $token]);
        } else {

            Session::flash('error', 'OTP expired');

            return redirect(admin_url('password/forgot'));
        }
    }


    public function passwordReset(Request $request)
    {
        try {

            $token = Session::get('otp_token');

            if (empty($token)) {
                Session::flash('error', 'Access Denied!');
                return redirect(admin_url('password/forgot'));
            }

            $expire_mins = 10;
            $userCheck   = User::where('otp_token', $token)->first();

            if (!$userCheck) {
                Session::flash('error', 'Page Expired, Please try again');
                return redirect(admin_url('password/forgot'));
            }

            return view('auth.passwords.reset', ['token' => $token]);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Please try after sometimes!');
            return redirect(admin_url('password/forgot'));
        }
    }


    public function passwordResetSubmit(Request $request)
    {

        try {

            $rules = [
                'password'        => 'required|min:8|max:20',
                'confirmpassword' => 'required|same:password',
            ];

            $messages = [
                'password.required'        => 'Password is required.',
                'password.min'             => 'Password must be at least 8 characters.',
                'password.max'             => 'Password must not exceed 20 characters.',
                'confirmpassword.required' => 'Confirm Password is required.',
                'confirmpassword.same'     => 'Confirm Password must match the Password.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {

                return redirect()->back()->with(['otp_token' => $request->token]);
            }

            $token    = $request->token;
            $password = $request->password;

            $userCheck = User::where('otp_token', $token)->first();


            if ($userCheck) {

                $userCheck->password  = Hash::make($password);
                $userCheck->otp       = null;
                $userCheck->otp_token = null;
                $userCheck->password_changed_at = now();
                $userCheck->update();


                Session::flash('success', 'Successfully password reset');
                return redirect(admin_url('login'));
            } else {

                Session::flash('error', 'Please try after sometimes');
                return redirect(admin_url('login'));
            }
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Please try after sometimes!');
            return redirect()->back();
        }
    }

    public function showResetForm()
    {
        return view('auth.passwords.reset-password');
    }

    public function resetPassword(Request $request)
    {
        $rules = [
            'password'        => 'required|min:8|max:20',
            'confirmpassword' => 'required|same:password',
        ];

        $messages = [
            'password.required'        => 'Password is required.',
            'password.min'             => 'Password must be at least 8 characters.',
            'password.max'             => 'Password must not exceed 20 characters.',
            'confirmpassword.required' => 'Confirm Password is required.',
            'confirmpassword.same'     => 'Confirm Password must match the Password.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {

            return redirect()->back()->with(['otp_token' => $request->token]);
        }
        $user = Auth::user();

        if ($user) {
            $user->password = Hash::make($request->password);
            $user->password_changed_at = now();
            $user->save();
            Session::flash('success', 'Successfully password reset');
            return redirect(admin_url('dashboard'));
        }
        Session::flash('error', 'User not found');
        return redirect()->back()->withErrors(['email' => 'User not found']);
    }

    public function privacypolicy()
    {
        return view('auth.policy');
    }
}
