<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

use DB;
use Str;
use Log;
use Illuminate\Support\Facades\Auth;
use Session;
use Exception;
use DataTables;

use App\Exports\UsersProfileExport;
use App\Exports\PartnersProfileExport;
use App\Exports\PartnersTeamExport;
use App\Imports\UserImport;

use App\Jobs\ImportUserJob;
use App\Jobs\SetpasswordJob;

use App\Models\User;


class UserController extends Controller
{

    public function __construct()
    {

    }

    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {

                    $data =  User::UserList();
                    $datatables = Datatables::of($data)
                        ->addIndexColumn()
                        ->addColumn('profile_status', function ($row) {
                            $text = "<span style='color:red'>In-Active<span>";
                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class= 'StatusChange' data-id='" . encryptId($row->id) . "' data-type = '1' >Active<span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class= 'StatusChange' data-id='" . encryptId($row->id) . "' data-type = '0' >In-Active<span>";
                            }
                            return $text;
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('user_management/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa  fa-eye" style="color:#0277bd;"></i></a> ';
                            $btn .= '<a href="' . admin_url('user_management/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa fa-edit" style="color:#43a047;"></i></a> ';
                            $btn .= '<a href="javascript:void(0);"  data-id="' . encryptId($row->id) . '"  class="UserDelete" title="Delete"><i class="fa fa-trash-alt" style="color:#d81821;"></i></a> ';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'profile_status'])
                        ->make(true);
                    return $datatables;
                } catch (Exception $ex) {
                    report($ex);
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }
        $user = User::UserList();
        $data = array(
            'user_details' => $user,
        );
        return view('admin.User_details_list', $data);
    }
    public function UserAdd(Request $request)
    {
        $user = $this->user->UserList();
        $country = $this->country->CountryList();
        $data = array(
            'user_details' => $user,
            'country_details' => $country
        );
        return view('admin.User_details_add', $data);
    }
    public function Useremailcheck(Request $request)
    {
        if ($request->ajax()) {
            $email = $request->email;
            $userid = $request->userid;
            if ($userid == '') {
                $user = $this->user->EmailCheck($email);
            } else {
                $user = $this->user->ExistEmailCheck($email, $userid);
            }
            if ($user->count()) {
                return Response::json(array('msg' => 'true'));
            }
            return Response::json(array('msg' => 'false'));
        }
    }
    public function UserAddSubmit(Request $request)
    {
        try {
            $rules = [
                'user_name' => 'required',
                'mobile' => 'required',
                'email' => 'required|email',
                'country' => 'required'
            ];
            $messages = [
                'user_name.required' => 'Please enter partner name',
                'mobile.required' => 'Please enter mobile',
                'email.required' => 'Please enter email address',
                'email.email' => 'Please enter a valid email address',
                'country.required' => 'Please select Country'
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $password = Str::random(12);
            $insert_data = array(
                'name' => $request->user_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'status' => 1,
                'role' => 1,
                'country' => decryptId($request->country),
                'password' => Hash::make($password),
                'is_active' => '0',
                'active_tokan' => Str::random(60),
                'created_by' => Auth::user()->id,
                'created_date' => date('Y-m-d'),
            );
            $file = $request->file('profile_image');
            if ($file != null) {
                $uploadpath = 'public/uploads/profile';
                $filenewname = time() . Str::random('10') . '.' . $file->getClientOriginalExtension();
                $fileName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                $fileMimetype = $file->getMimeType();
                $fileExt = $file->getClientOriginalExtension();
                $file->move($uploadpath, $filenewname);
                $insert_data['profile_image'] = $filenewname;
            } else {
                $insert_data['profile_image'] = '';
            }
            $userdetails = $this->user->InsertUser($insert_data);
            $link = getHost() . 'Account_Activate/' . $userdetails->active_tokan . '?email=' . urlencode($userdetails->email);
            try {
                $details = [
                    "email" => $request->email,
                    "name" => $request->name,
                    "link" => $link,
                    "expire" => get_constant('RESET_PASSWORD_EXPIRE'),
                ];
                dispatch((new SetpasswordJob($details))->onQueue('email'));
                Log::channel('user-info')->info("User Successfully Added", $userdetails->toArray());
                Session::flash('success', 'User added successfully!');
                return redirect(admin_url('user_management'));
            } catch (\Exception $e) {
                report($e);
                return redirect(admin_url('user_management'));
            }
        } catch (Exception $ex) {
            report($ex);
            Log::channel('user-info')->alert($ex);
            return redirect(admin_url('user_management'));
        }
    }
    public function UserImport(Request $request)
    {
        $data = array();
        return view('admin.User_details_Import', $data);
    }
    public function UserImportSubmit(Request $request)
    {
        try {
            $file = $request->file('user_file');
            if ($file != null) {
                $uploadpath = 'public/uploads/userdata';
                $filenewname = time() . Str::random('10') . '.' . $file->getClientOriginalExtension();
                $fileName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                $fileMimetype = $file->getMimeType();
                $fileExt = $file->getClientOriginalExtension();
                $file->move($uploadpath, $filenewname);
                $path = $uploadpath . "/" . $filenewname;
                $user_id = auth()->user()->id;
                $insert_data = array(
                    'file_path' => $path,
                    'source_path' => $path,
                    'dest-path' => $path,
                    'file_name' => $filenewname,
                    'file_orgname' => $fileName,
                    'extract_status' => 0,
                    'upload_type' => '2',
                    'created_by' => $user_id,
                );
                $insert_id = DB::table('admin_upload_log')->insertGetId($insert_data);
                $details = [
                    "user_id" => $user_id,
                    "log_id" => $insert_id,
                    "path" => $path,
                    "expire" => get_constant('RESET_PASSWORD_EXPIRE'),
                ];
                dispatch((new ImportUserJob($details))->onQueue('high'));
                // \Excel::import(new UserImport($user_id, $insert_id), $path);
            }
            $insert_data['log_id'] = $insert_id;
            $insert_data['Uploded_by'] = Auth::user()->toArray();
            Log::channel('user-info')->info("User Successfully Uploaded", $insert_data);
            Session::flash('success', 'Successfully User upload !');
            return redirect(admin_url('user_management'));
        } catch (Exception $ex) {
            Log::channel('user-info')->alert($ex);
            Session::flash('error', 'User upload failed!');
            return redirect(admin_url('user_management'));
        }
    }
    public function UserProfileDownload(Request $request)
    {

        try {

            $details = array();
            return Excel::download(new UsersProfileExport($details), 'User Details.xlsx');
        } catch (Exception $ex) {

            report($ex);
        }
    }

    public function UserView(Request $request, $userid = '')
    {
        $id = decryptId($request->userid);
        if (Auth::check()) {
            $user = $this->user->GetUser($id);
            $country = $this->country->CountryName($user);
            $data = array(
                'user_details' => $user,
                'country_details' => $country,
            );
        }
        try {
            return view('admin.User_details_view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function UserEdit(Request $request, $userid = '')
    {
        try {
            $id = decryptId($request->userid);
            $user = $this->user->GetUser($id);
            $country = $this->country->CountryList();
            $data = array(
                'user_details' => $user,
                'country_details' => $country
            );
            return view('admin.User_details_edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function UserUpdate(Request $request)
    {
        try {
            $id = decryptId($request->userid);
            $update_data = array(
                'name' => $request->user_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'country' => decryptId($request->country),
            );
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
            }
            $userdetails =   $this->user->UpdateUser($id, $update_data);
            if ($userdetails) {
                Log::channel('user-info')->info("User Successfully Updated", $update_data);
            } else {
                Log::channel('user-info')->info("User Updated Failed", $update_data);
            }
            Session::flash('success', 'User updated successfully!');
            return redirect(admin_url('user_management'));
        } catch (Exception $ex) {
            report($ex);
            Log::channel('user-info')->alert($ex);
            return "Error";
        }
    }

    public function UserStatus(Request $request)
    {
        try {
            $userid = decryptId($request->user_id);
            $type = $request->types;
            if ($type == 1) {
                $update_data = array(
                    'status' => 0,
                    'is_active' => 0,
                );
                $message = 'Your Account was In-Activated';
                $successMsg = 'Successfully user Account was In-Activated';
                $this->user->UpdateUser($userid, $update_data);
                try {
                    $user_details = $this->user->GetUser($userid);
                    if ($user_details != null) {
                        $details = array(
                            "email" => $user_details['email'],
                            "name" => $user_details['name'],
                            'message' => $message,
                        );
                        dispatch((new AccountStatusJob($details))->onQueue('email'));
                    }
                } catch (Exception $ex) {
                    report($ex);
                    return response()->json(['error' => '1', 'status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            } else {
                $update_data = array(
                    'status' => 1,
                    'is_active' => 1,
                    'active_tokan' => Str::random(60),
                );
                $message = 'Activation email successfully sent';
                $successMsg = 'Activation email successfully sent';
                $this->user->UpdateUser($userid, $update_data);
                $userdetails = $this->user->GetUser($userid);
                $link = getHost() . 'Account_Activate/' . $userdetails->active_tokan . '?email=' . urlencode($userdetails->email);
                try {
                    $details = [
                        "email" => $userdetails->email,
                        "name" => $userdetails->name,
                        "link" => $link,
                        "expire" => get_constant('RESET_PASSWORD_EXPIRE'),
                    ];
                    dispatch((new SetpasswordJob($details))->onQueue('email'));
                } catch (\Exception $e) {
                    report($e);
                    return response()->json(['error' => '2', 'status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
            return response()->json(['status' => 'success', 'msg' => $successMsg], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['error' => '2', 'status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function UserDelete(Request $request)
    {
        try {
            $userid = decryptId($request->user_id);
            $update_data = array(
                'status' => 0,
                'trash' => "YES",
            );
            $this->user->UpdateUser($userid, $update_data);
            $message = 'Your Account has been Deleted';
            try {
                $user_details = $this->user->GetUser($userid);
                if ($user_details != null) {
                    $details = array(
                        "email" => $user_details['email'],
                        "name" => $user_details['name'],
                        'message' => $message,
                    );
                    dispatch((new AccountStatusJob($details))->onQueue('email'));
                }
            } catch (Exception $ex) {
                report($ex);
                return response()->json(['error' => '1', 'status' => 'error', 'msg' => 'Please try after some time'], 406);
            }
            $update_data['user_id'] = $userid;
            $update_data['Deleted_by'] = Auth::user()->toArray();
            Log::channel('user-info')->info("User deleted successfully", $update_data);
            return response()->json(['status' => 'success', 'msg' => 'User deleted successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            Log::channel('user-info')->alert($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }



}
