<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Facades\File;

use Str;
use PDF;
use Mail;
use Illuminate\Support\Facades\Auth;
use Session;
use Exception;
use DataTables;
use Response;

use App\Models\User;

use App\Models\Administration\UserRole;


class UserRoleController extends Controller
{
    private $user_role;
    private $user;
    public function __construct()
    {
        $this->user_role = new UserRole();
        $this->user = new User();
    }


    public function index(Request $request)
    {

        if (Auth::check()) {
            if ($request->ajax()) {
                try {

                    $data =  $this->user_role->list();

                    $datatables = Datatables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active<span>";
                            if ($row->id != 1) {
                                if ($row->status == 1) {
                                    $text = "<span style='color:green;cursor:pointer' class= 'statusChange' data-id='" . encryptId($row->id) . "' data-type = '1' >Active<span>";
                                } else if ($row->status == 0) {
                                    $text = "<span style='color:red;cursor:pointer' class= 'statusChange' data-id='" . encryptId($row->id) . "' data-type = '0' >In-Active<span>";
                                }
                            } else {
                                $text = '-';
                            }

                            return $text;
                        })
                        ->addColumn('role_name', function ($row) {
                            return $row->role_name;
                        })
                        ->addColumn('created_at', function ($row) {
                            return Displaydatetimeformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })

                        ->rawColumns(['created_date', 'created_by', 'status'])
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

        $data = array();

        return view('master.role.list', $data);
    }

    public function Add(Request $request)
    {

        try {
            $data = array();
            return view('master.role.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {
        try {

            $rules = [
                'role_id' => 'required',
                'role_name' => 'required',

            ];
            $messages = [
                'role_id.required' => 'Please enter User Role ID',
                'role_name.required' => 'Please enter User Role Name',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

                $this->user_role->store();

                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {


                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('administration/role/list'));
        } catch (Exception $ex) {


            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('administration/role/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $role = $this->user_role->selectOne($id);

                $data = array(
                    'role' => $role,
                );
            }
            return view('master.role.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $role = $this->user_role->selectOne($id);

            $data = array(
                'role' => $role,
            );
            return view('master.role.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $rules = [
                'role_id' => 'required',
                'role_name' => 'required',
            ];
            $messages = [
                'role_id.required' => 'Please enter User Role ID',
                'role_name.required' => 'Please enter User Role Name',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $this->user_role->updates($id);

            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('administration/role/list'));
        } catch (Exception $ex) {

            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('administration/role/list'));
        }
    }


    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $role_name = $request->role_name;
            $id = $request->id;
            if ($id == '') {


                $record = $this->user_role->uniqueCheck($role_name);
            } else {
                $id = decryptId($id);
                $record = $this->user_role->ExistuniqueCheck($role_name, $id);
            }
            if ($record->count()) {
                return Response::json(false);
            }
            return Response::json(true);
        }
    }


    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->user_role->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'User Role status changed'], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function Delete(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $this->user_role->deleterecord($id);

            return response()->json(['status' => 'success', 'msg' => 'User Role deleted successfully'], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }


    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->user_role->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                'No.',
                'User Role ID',
                'User Role Name',
                'User Role Status',
                'Created User',
                'Created Date',
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export['No.'] =  $i;
                $export['User Role ID'] =  $data->role_id;
                $export['User Role Name'] =  $data->role_name;
                if ($data->id != 1) {
                    $export['User Role Status'] = $data->status == 1 ? 'Active' : 'In-Active';
                } else {
                    $export['User Role Status'] = '-';
                }
                $export['Created User'] =  getusername($data->created_by);
                $export['Created Date'] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('User Role.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            report($ex);
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->user_role->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                'No.',
                'User Role ID',
                'User Role Name',
                'User Role Status',
                'Created User',
                'Created Date',
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "User Role Details",
            );

            $property = [
                'tempDir' => 'public/pdf/temp/',
                'mode' => 'c',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,

            ];

            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';

            $view = view('master.role.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "User Role.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
        }
    }
}
