<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Exception;
use Illuminate\Support\Facades\Validator;

class LoginController extends BaseController
{
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request): JsonResponse
    {

        try {
            $rules = [
                'email' => 'required',
                'password' => 'required',
                'fcm_token' => 'required',
            ];

            $messages = [
                'email.required' => 'email is required.',
                'password.required' => 'Password is required.',
                'fcm_token.required' => 'FCM Token is required.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors(), 422);
            }

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();
                $success['token'] =  $user->createToken('karam')->accessToken;
                $success['name'] =  $user->name;


                $token = $request->fcm_token;

                if ($request->has('device_type')) {
                    $device_type = $request->device_type;
                } else {
                    $device_type = 'android';
                }

                $user->fcmTokens()->create(['token' => $token, 'device_type' => $device_type]);

                return $this->sendResponse($success, 'User login successfully.');
            } else {
                return $this->sendError('Invalid user details', ['error' => 'Unauthorised'], 406);
            }
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Invalid user details', ['error' => 'Unauthorised'], 406);
        }
    }

    public function forgotPassword(Request $request): JsonResponse
    {

        try {

            $rules = [
                'email' => 'required',
            ];

            $messages = [
                'email.required' => 'email is required.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors(), 422);
            }

            $email = $request->email;
            $userCheck = User::where('email', $email)->first();

            if ($userCheck != null && $userCheck != '') {

                if ($userCheck->email == null || $userCheck->email == '') {
                    return $this->sendError('Email id not found, please contact Admin', ['error' => 'Unauthorised'], 406);
                }

                $otp = mt_rand(100000, 999999);
                $email = $userCheck->email;

                $expire_mins = (int)env('OTP_EXPIRE', 10);

                Cache::put('otp_' . $email, $otp, now()->addMinutes($expire_mins));

                $userCheck->otp = $otp;
                $userCheck->update();

                $success = [
                    'expire' => $expire_mins,
                    'otp' => $otp
                ];

                return $this->sendResponse($success, 'OTP Sent to registered email');
            } else {
                return $this->sendError('Invalid email', ['error' => 'Unauthorised'], 406);
            }
        } catch (Exception $ex) {
            return $this->sendError('Invalid user details', ['error' => 'Unauthorised'], 406);
        }
    }

    public function passwordOtp(Request $request): JsonResponse
    {

        try {
            $rules = [
                'email' => 'required',
                'otp' => 'required',
            ];

            $messages = [
                'email.required' => 'email is required.',
                'otp.required' => 'OTP is required.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors(), 422);
            }


            $email = $request->email;
            $otp = $request->otp;
            $userCheck = User::where('email', $email)->where('otp', $otp)->first();

            if ($userCheck != null && $userCheck != '') {

                $cacheKey = 'otp_' . $email;

                if (!Cache::has($cacheKey)) {

                    return $this->sendError('OTP Expired', ['error' => 'Unauthorised'], 406);
                }

                $token = Str::random(16);

                $userCheck->otp_token = $token;
                $userCheck->update();

                $success = [
                    'token' => $token
                ];

                return $this->sendResponse($success, 'OTP Verified');
            } else {
                return $this->sendError('Invalid OTP', ['error' => 'Unauthorised'], 406);
            }
        } catch (Exception $ex) {
            return $this->sendError('Invalid user details', ['error' => 'Unauthorised'], 406);
        }
    }

    public function passwordChange(Request $request): JsonResponse
    {

        try {
            $rules = [
                'email' => 'required',
                'token' => 'required',
                'password' => 'required',
            ];

            $messages = [
                'email.required' => 'email is required.',
                'token.required' => 'Token is required.',
                'password.required' => 'Password is required.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors(), 422);
            }



            $email = $request->email;
            $token = $request->token;
            $password = $request->password;

            $userCheck = User::where('email', $email)->where('otp_token', $token)->first();

            if ($userCheck != null && $userCheck != '') {

                $userCheck->password = Hash::make($password);
                $userCheck->otp = null;
                $userCheck->otp_token = null;
                $userCheck->update();

                $success = [];

                return $this->sendResponse($success, 'Successfully password changed');
            } else {
                return $this->sendError('Invalid Token', ['error' => 'Unauthorised']);
            }
        } catch (Exception $ex) {
            return $this->sendError('Invalid user details', ['error' => 'Unauthorised'], 406);
        }
    }

    public function logout(Request $request): JsonResponse
    {

        try {
            $rules = [
                'fcm_token' => 'required',
            ];

            $messages = [
                'fcm_token.required' => 'FCM Token is required.',

            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors(), 422);
            }

            if (Auth::user()) {

                $user = Auth::user();
                $token = $request->input('fcm_token');

                $user->fcmTokens()->where('token', $token)->delete();

                $request->user()->token()->delete();
                $success = [];

                return $this->sendResponse($success, 'User logout successfully.');
            } else {
                return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
            }
        } catch (Exception $ex) {
            return $this->sendError('Invalid user details', ['error' => 'Unauthorised'], 406);
        }
    }

    public function userProfile(Request $request): JsonResponse
    {

        if (Auth::user()) {

            $user = Auth::user();

            $user_array = array(
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'employee_id' => $user->employee_id,
                'name' => $user->name,
                'role' => $user->role,
                'department' => $user->department_id,

            );

            $success = [
                'user_details' => $user_array
            ];

            return $this->sendResponse($success, 'User Details');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }
}
