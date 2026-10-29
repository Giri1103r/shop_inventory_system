<?php

namespace App\Http\Controllers\Admin\Employee\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

use App\Models\Employee\Master\EmployeeStatus;
use App\Models\User;
use App\Models\Administration\UploadLog;


class EmployeeStatusController extends Controller
{


    private $user;
    private $uploadlog;

    private $employeestatus;


    public function __construct()
    {


        $this->user = new User();
        $this->employeestatus = new EmployeeStatus();

        $this->uploadlog = new UploadLog();
    }


    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {

                try {

                    $data =  $this->employeestatus->list();

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
                            return Displaydatetimeformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            if (CheckUserPermission('view')) {
                                $btn = '<a href="' . admin_url('employee-management/master/employee-status/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            }
                            if (CheckUserPermission('edit')) {
                                $btn .= '<a href="' . admin_url('employee-management/master/employee-status/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            }
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
        $data = array();
        return view('admin.employee.master.employee_status.list', $data);
    }

    public function Add(Request $request)
    {

        try {

            $data = array();
            return view('admin.employee.master.employee_status.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('employee-management/master/employee-status/list'));
        }
    }

    public function Store(Request $request)
    {
        try {
            DB::beginTransaction();

            $rules = [
                'employee_status_name' => 'required|unique:masters_employee_status,employee_status_name',
            ];
            $messages = [
                'employee_status_name.required' => 'Please Enter Employee Status Name',
                'employee_status_name.unique' => 'Employee Status Name should be unique',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }


            $this->employeestatus->store();
            DB::commit();

            Session::flash('success', 'Your data has been created successfully!');
            return redirect(admin_url('employee-management/master/employee-status/list'));
        } catch (Exception $ex) {

            report($ex);
            DB::rollBack();
            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('employee-management/master/employee-status/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $employeestatus = $this->employeestatus->selectOne($id);

            if (!$employeestatus) {
                Session::flash('error', __('common.error_msg'));
                return redirect(admin_url('employee-management/master/employee-status/list'));
            }

            $data = array(
                'employeestatus' => $employeestatus,
            );

            return view('admin.employee.master.employee_status.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('employee-management/master/employee-status/list'));
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $employeestatus = $this->employeestatus->find($id);

            if (!$employeestatus) {
                Session::flash('error', __('common.error_msg'));
                return redirect(admin_url('employee-management/master/employee-status/list'));
            }

            $data = array(
                'employeestatus' => $employeestatus,
            );

            return view('admin.employee.master.employee_status.edit', $data);
        } catch (Exception $error) {
            report($error);

            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('employee-management/master/employee-status/list'));
        }
    }

    public function Update(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = decryptId($request->id);
            $rules = [
                'employee_status_name' => 'required|unique:masters_employee_status,employee_status_name,' . $id . ',id',
            ];
            $messages = [
                'employee_status_name.required' => 'Please Enter Employee Status Name',
                'employee_status_name.unique' => 'Employee Status Name should be unique',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->employeestatus->updates($id);
            DB::commit();

            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('employee-management/master/employee-status/list'));
        } catch (Exception $ex) {
            report($ex);
            DB::rollBack();
            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('employee-management/master/employee-status/list'));
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->employeestatus->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Employee Status status changed'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function Delete(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $this->employeestatus->deleterecord($id);

            return response()->json(['status' => 'success', 'msg' => 'Employee Status deleted successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }


    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->employeestatus->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Employee Status ID',
                'Employee Status Name',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $exportData = [];
            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->est_code;
                $export[] =  $data->employee_status_name;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Employee Status Master.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('employee-management/master/employee-status/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->employeestatus->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Employee Status ID',
                'Employee Status Name',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Employee Status Details",
            );

            $property = [
                'tempDir' => public_path('pdf/temp'),
                'mode' => 'c',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,

            ];

            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';

            $view = view('admin.employee.master.employee_status.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Employee Status Master.pdf";

            /* Hand the PDF back as a response instead of echoing it: mPDF sends
               its own Content-Length and then returns, so letting the action fall
               through made the framework append a second body to the download. */
            return response($mpdf->Output($filename, \Mpdf\Output\Destination::STRING_RETURN), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('employee-management/master/employee-status/list'));
        }
    }

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {

            $employee_status_name = $request->employee_status_name;
            $id = $request->id;

            if ($id == '') {

                $record = $this->employeestatus->uniqueCheck($employee_status_name);
            } else {

                $id = decryptId($id);
                $record = $this->employeestatus->ExistuniqueCheck($employee_status_name, $id);
            }
            if ($record->count()) {
                return Response::json(false);
            }
            return Response::json(true);
        }
    }

    public function list(Request $request)
    {
        $id = decryptId($request->id);
        $employeestatuss = $this->employeestatus->ajaxList($id);

        return response()->json($employeestatuss);
    }
}
