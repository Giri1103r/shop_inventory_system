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


    public function driver_wise_trip_count(Request $request)
    {

        try {
            $data = $this->trip_management->driver_wise_trip_count();
            $from_date = $request->fromDate;
            $to_date = $request->toDate;

            $filter = array(
                'from_date' => $from_date,
                'to_date' => $to_date
            );

            return view('dashboards.driverwisetrip', compact('data', 'filter'));
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function company_wise_employee_count(Request $request)
    {
        try {
            $data = $this->company->companywise_emp_count();
            $from_date = $request->fromDate;
            $to_date = $request->toDate;

            $filter = array(
                'from_date' => $from_date,
                'to_date' => $to_date
            );
            return view('dashboards.companywiseemp', compact('data', 'filter'));
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function monthlytirp(Request $request)
    {
        try {

            $data = $this->trip_management_user->monthly_trip_count();
            $from_date = $request->fromDate;
            $to_date = $request->toDate;

            $filter = array(
                'from_date' => $from_date,
                'to_date' => $to_date
            );
            return view('dashboards.monthwisetrip', compact('data', 'filter'));
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function month_wise_employee_count(Request $request)
    {
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $company_id = getComapnyId($user->id);
                $data = $this->company->monthly_wise_emp_count($company_id);
                $from_date = $request->fromDate;
                $to_date = $request->toDate;

                $filter = array(
                    'from_date' => $from_date,
                    'to_date' => $to_date
                );
                return view('dashboards.monthwiseemp', compact('data', 'filter'));
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function live_track()
    {
        $key = config('constants.MAPS_KEY');
        return view('admin.activetrip', compact('key'));
    }

    public function ongoing_trip()
    {
        if (Auth::user()) {

            $tripdetails = $this->trip_management->select(
                'trip_management.id',
                'trip_management.trip_id',
                'trip_management.trip_date',
                'trip_management.trip_start_time',
                'trip_management.trip_end_time',
                'trip_management.trip_from',
                'trip_management.trip_to',
                'trip_management.driver_id',
                'trip_management.trip_initiated_time',
                'trip_management.trip_live_lat',
                'trip_management.trip_live_lan',
                'driver_management.driver_id',
                'driver_management.driver_name',
                'fleet_management.fleet_id',
                'fleet_management.fleet_model',
                'fleet_management.fleet_reg_no',
                'start.location_name as from_location',
                'end.location_name as to_location'
            )
                ->leftjoin('driver_management', 'trip_management.driver_id', '=', 'driver_management.login_id')
                ->leftjoin('fleet_management', 'trip_management.fleet_id', '=', 'fleet_management.id')
                ->leftjoin('trip_location as start', 'trip_management.trip_from', '=', 'start.id')
                ->leftjoin('trip_location as end', 'trip_management.trip_to', '=', 'end.id')
                ->when(true, function ($query) {
                    return $query->selectRaw("
                        CASE
                            WHEN trip_management.trip_from = 1 THEN 'out'
                            ELSE 'in'
                        END as trip_direction
                    ");
                })
                ->where('trip_management.trip_status', '!=', TRIP_STATUS_END)
                ->get();

            // dd($tripdetails);

            return response()->json($tripdetails);
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }
}
