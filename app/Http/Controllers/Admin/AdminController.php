<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Master\Unit;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use App\Models\Master\Employee;
use App\Models\Master\Department;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Session;

use App\Models\Master\Company;


class AdminController extends Controller
{



    private $unit;
    private $department;
    private $company;


    public function __construct()
    {
        
  
        $this->unit = new Unit();
        $this->department = new Department();
        $this->company = new Company();
      
    }

    public function index(Request $request)
    {
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $data = [];

                if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_ADMIN) || CheckUserRole(ROLE_EHS_HEAD) ||  CheckUserRole(ROLE_DASHBOARD_VIEWER)) {
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


                        [
                            'link' => 'employee/list',
                            'name' => 'Employees',
                            'count' => gettotalCount('employee'),
                            'icon' => 'bx bx-message-square-detail',
                            'icon_color' => 'text-primary',
                        ],

                        [
                            'link' => 'work/list',
                            'name' => 'Workers',
                            'count' => gettotalCount('work'),
                            'icon' => 'bx bx-message-square-detail',
                            'icon_color' => 'text-primary',
                        ],

                    ];

                    $companyList  = $this->company->where('status', '1')->get();
                    $data = [
                        'masterLink' => $masterLink,
                        'companyList' => $companyList,
                    ];
                }


                if (
                    CheckUserRole(ROLE_SUPERADMIN) ||
                    CheckUserRole(ROLE_ADMIN) ||
                    CheckUserRole(ROLE_EHS_HEAD) ||
                    CheckUserRole(ROLE_DASHBOARD_VIEWER)
                ) {
                    return view('admin.dashboard', $data);
                } else {
                    return view('admin.userdashboard', $data);
                }
            }
        } catch (\Exception $ex) {
            report($ex);
            return back()->with('error', 'Failed to load heatmap incident data.');
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
            report($ex);
            return "Error";
        }
    }
    public function signatureUpload(Request $request)
    {
        try {
            $id = Auth::id();

            if ($request->hasFile('signature_image')) {
                $file = $request->file('signature_image');


                $destinationPath = 'uploads/signatureupload';

                if (!File::exists(public_path($destinationPath))) {
                    File::makeDirectory(public_path($destinationPath), 0777, true, true);
                }

                $signature_image_name = time() . '_' . $file->getClientOriginalName();

                $file->move(public_path($destinationPath), $signature_image_name);
                $signature_image_path = 'public/' . $destinationPath . '/' . $signature_image_name;

                $update_data['signature_upload'] = $signature_image_path;

                User::where('id', $id)->update($update_data);
                Employee::where('login_id', $id)->update($update_data);

                Session::flash('success', 'User Signature is updated successfully!');
            } else {
                Session::flash('error', 'No file was uploaded.');
            }

            return redirect(admin_url('profile'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong. Please try again later.');
            return redirect(admin_url('profile'));
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
            report($ex);
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
            report($ex);
            Session::flash('error', 'Please try after sometimes!');
            return redirect()->back();
        }
    }


   
}
