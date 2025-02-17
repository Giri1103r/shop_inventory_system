<?php

namespace App\Http\Controllers\OhcManagement\Opd;

use App\Http\Controllers\Controller;
use Exception;
use App\Models\OhcManagement\Opd\RoadsideFirstAid;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class RoadsideFirstAidController extends Controller
{
    private $ohc_opd_roadside_first_aid;
    public function __construct()
    {
        $this->ohc_opd_roadside_first_aid = new RoadsideFirstAid();

    }
    public function index(Request $request)
    {

        if (Auth::check()) {

            if ($request->ajax()) {

                try {

                    $data =  $this->ohc_opd_roadside_first_aid->list();

                    $datatables = Datatables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active<span>";
                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class= 'statusChange' data-id='" . encryptId($row->id) . "' data-type = '1' >Active<span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class= 'statusChange' data-id='" . encryptId($row->id) . "' data-type = '0' >In-Active<span>";
                            }
                            return $text;
                        })
                        ->addColumn('created_at', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->editColumn('department_id', function ($row) {
                            return $row->department_name;
                        })
                        ->editColumn('unit_id', function ($row) {
                            return $row->unit_name;
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })

                        ->addColumn('action', function ($row) {
                            $btn = '';
                            // if (CheckUserPermission('view')) {
                                $btn = '<a href="' . admin_url('ohc/first-aid-location/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            // if (CheckUserPermission('edit')) {
                                $btn .= '<a href="' . admin_url('ohc/first-aid-location/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            // }

                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);
                    return $datatables;
                } catch (Exception $ex) {
                  report($ex);
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }

        $data = [

        ];

        return view('ohcmanagement.ohc-opd.roadside-firstaid.list', $data);
    }

    public function add(){
        try {

            return view('ohcmanagement.ohc-opd.roadside-firstaid.add');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/roadside-first-aid/list'));
        }
    }

    public function Store(Request $request)
    {
        try {

            $rules = [
                'emp_name' => 'required',
                'emp_id' => 'required',
                'date_of_incident' => 'required',
                'time_of_incident' => 'required',
            ];

            $messages = [
                'emp_name.required' => 'Employee name is required.',
                'emp_id.required' => 'Please select an employee Id.',
                'date_of_incident.required' => 'Date of Incident is required.',
                'time_of_incident.required' => 'Time of Incident is required.',

            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {


               $this->ohc_opd_roadside_first_aid->store();


                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/roadside-first-aid/list'));
        } catch (Exception $ex) {

            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/roadside-first-aid/list'));
        }
    }

    public function edit(Request $request){
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $opd_first_aid = $this->ohc_opd_roadside_first_aid->selectOne($id);

            }
            $data = array(
              'opd_first_aid'=>$opd_first_aid,

            );
            return view('ohcmanagement.ohc-opd.roadside-firstaid.edit',$data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/roadside-first-aid/list'));
        }
    }
}
