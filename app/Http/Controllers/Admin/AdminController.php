<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use DB;
use Exception;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

use App\Models\User;

class AdminController extends Controller
{


    public function __construct() {}

    public function index()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $data = [];
            if ((in_array(ROLE_SUPERADMIN, getUserRoleId(Auth::id())) || in_array(ROLE_ADMIN, getUserRoleId(Auth::id())))) {
                $masterLink = [
                    [
                        'link' => 'company/list',
                        'name' => 'Company',
                        'count' => gettotalCount('company'),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],
                    [
                        'link' => 'location/list',
                        'name' => 'Location',
                        'count' => gettotalCount('location'),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],
                    [
                        'link' => 'unit/list',
                        'name' => 'Unit',
                        'count' => gettotalCount('unit'),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],

                    [
                        'link' => 'department/list',
                        'name' => 'Department',
                        'count' => gettotalCount('department'),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],

                ];

                $data = [
                    'masterLink' => $masterLink,
                ];
            }
            return view('admin.dashboard', $data);
        }
    }


    public function profileView()
    {
        $id = Auth::user()->id;
        $page_data['user_detail'] = Auth::user();
        $page_data['country_detail'] = Auth::user();
        return view('admin.user_profile', $page_data);
    }

    public function profileUpdate(Request $request)
    {
        try {
            $id = Auth::id();

            $file = $request->file('profile_image');
            if ($file != null) {
                $uploadpath = 'public/uploads/profile';
                $filenewname = time() . Str::random('10') . '.' . $file->getClientOriginalExtension();
                $fileName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                $fileMimetype = $file->getMimeType();
                $fileExt = $file->getClientOriginalExtension();
                $file->move($uploadpath, $filenewname);
                $update_data['profile_image'] = $filenewname;

                User::where('id', $id)->update($update_data);
            }



            Session::flash('success', 'User profile is updated successfully!');
            return redirect(admin_url('profile'));
        } catch (Exception $ex) {

            return "Error";
        }
    }
    public function Update(Request $request)
    {
        try {
            $id = Auth::id();
            $request->validate([
                'name' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
            ]);
            $update_data = array(
                'name' => $request->name,
                'first_name' => $request->name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'mobile' => $request->phone,
                'updated_by' => Auth::id()
            );

            User::where('id', $id)->update($update_data);
            Session::flash('success', 'User profile is updated successfully!');
            return redirect(admin_url('profile'));
        } catch (Exception $ex) {
            dd($ex);
            return "Error";
        }
    }
    public function changeProfilePassword(Request $request)
    {

        try {

            $user = Auth::user();

            if (Auth::attempt(array('username' => Auth::user()->username, 'password' => $request->old_password))) {

                if ($request->password != null && $request->confirm_password != null) {
                    if ($request->password == $request->confirm_password) {
                        $password = $request->password;
                        $user->password = Hash::make($password);

                        $user->save();

                        Session::flash('success', 'Password updated successfully!');
                    } else {
                        Session::flash('error', 'Password missmatch!');
                    }
                }
            } else {
                Session::flash('error', 'Invalid old password');
            }

            return redirect(admin_url('profile'));
        } catch (Exception $ex) {

            Session::flash('error', 'Please try after sometimes!');
            return redirect()->back();
        }
    }
}
