<?php

namespace App\Http\Controllers\OhcManagement\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Master\Company;
use App\Models\Master\Unit;
use App\Models\Master\Location;
use App\Models\Master\Department;
use App\Models\User;
use App\Models\UploadLog;
use App\Jobs\ImportCompanyJob;
use App\Models\OhcManagement\Master\CertifiedFirstAider;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Master\Employee;

class CertifiedFirstAiderController extends Controller
{

    private $certifiedfirstaider;
    private $unit;
    private $location;
    private $department;
    private $user;
    private $uploadlog;
    private $employee;


    public function __construct()
    {

        $this->certifiedfirstaider = new CertifiedFirstAider();
        $this->unit = new Unit();
        $this->location = new Location();
        $this->department = new Department();
        $this->user = new User();
        $this->uploadlog = new UploadLog();
        $this->employee = new Employee();
    }


    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {

                try {

                    $data =  $this->certifiedfirstaider->list();

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
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->editColumn('department_id', function ($row) {
                            return $row->department_name;
                        })
                        ->editColumn('unit_id', function ($row) {
                            return $row->unit_name;
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            // if (CheckUserPermission('view')) {
                            $btn = '<a href="' . admin_url('ohc/certified-first-aider/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            // if (CheckUserPermission('edit')) {
                            $btn .= '<a href="' . admin_url('ohc/certified-first-aider/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
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
        $departmentList = $this->department->getdepartment();

        $unit = $this->unit->getunit();
        $data = array(
            'unit' => $unit,
            'departmentList' => $departmentList,
        );

        return view('ohcmanagement.master.certified_first_aid.list', $data);
    }

    public function Add(Request $request)
    {

        try {

            $unit = $this->unit->getunit();
            $data = [
                'unit' => $unit
            ];
            return view('ohcmanagement.master.certified_first_aid.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/certified-first-aider/list'));
        }
    }

    public function Store(Request $request)
    {
        try {

            $rules = [
                'unit_id' => 'required',
                'department_id' => 'required',
                'emp_id' => 'required',
                'certifier_name' => 'required',
                'mobile_no' => 'required|digits_between:10,15',
                'address' => 'required|max:300',
            ];

            $messages = [
                'unit_id.required' => 'Unit ID is required.',
                'department_id.required' => 'Department ID is required.',
                'emp_id.required' => 'Employee ID is required.',
                'certifier_name.required' => 'Certifier name is required.',
                'mobile_no.required' => 'Mobile number is required.',
                'mobile_no.digits_between' => 'Mobile number must be between 10 and 15 digits.',
                'address.required' => 'Address is required.',
                'address.max' => 'Address cannot exceed 300 characters.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {


                $company = $this->certifiedfirstaider->store();


                Session::flash('success', __('common.created_msg'));
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error',  __('common.message_error'));
            }

            return redirect(admin_url('ohc/certified-first-aider/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/certified-first-aider/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $certifiedfirstaider = $this->certifiedfirstaider->selectOne($id);

                $data = array(
                    'certifiedfirstaider' => $certifiedfirstaider,
                );
            }
            return view('ohcmanagement.master.certified_first_aid.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/certified-first-aider/list'));
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);


            $certifiedfirstaider = $this->certifiedfirstaider->find($id);

            $departmentList = $this->department->getdepartment();

            $unit = $this->unit->getunit();
            $employeeList = $this->employee->getEmployeefulldata();
            $data = array(
                'certifiedfirstaider' => $certifiedfirstaider,
                'unit' => $unit,
                'departmentList' => $departmentList,
                'employeeList' => $employeeList

            );

            return view('ohcmanagement.master.certified_first_aid.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/certified-first-aider/list'));
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rules = [
                'unit_id' => 'required',
                'department_id' => 'required',
                'emp_id' => 'required',
                'certifier_name' => 'required',
                'mobile_no' => 'required|digits_between:10,15',
                'address' => 'required|max:300',
            ];

            $messages = [
                'unit_id.required' => 'Unit ID is required.',
                'department_id.required' => 'Department ID is required.',
                'emp_id.required' => 'Employee ID is required.',
                'certifier_name.required' => 'Certifier name is required.',
                'mobile_no.required' => 'Mobile number is required.',
                'mobile_no.digits_between' => 'Mobile number must be between 10 and 15 digits.',
                'address.required' => 'Address is required.',
                'address.max' => 'Address cannot exceed 300 characters.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->certifiedfirstaider->updates($id);


            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('ohc/certified-first-aider/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/certified-first-aider/list'));
        }
    }


    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $emp_id = $request->emp_id;
            $mobile_no = $request->mobile_no;
            $id = $request->id;

            if (empty($id)) {
                $isUnique = !$this->certifiedfirstaider->uniqueCheck($emp_id, $mobile_no);
            } else {
                $id = decryptId($id);
                $isUnique = !$this->certifiedfirstaider->existUniqueCheck($emp_id, $mobile_no, $id);
            }

            return Response::json($isUnique);
        }
    }


    public function StatusChange(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $this->certifiedfirstaider->statuschange($id);
            return response()->json(['status' => 'success', 'msg' => 'Your Status has Changed Successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->certifiedfirstaider->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __("common.unit"),
                __("common.department"),
                __("common.employee_or_worker_code"),
                __("ohc_management.certified_first_aider"),
                __("ohc_management.mobile_no"),
                __("ohc_management.address"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  getUnitname($data->unit_id);
                $export[] =  getDepartment($data->department_id);
                $export[] =  $data->emp_id;
                $export[] =  $data->certifier_name;
                $export[] =  $data->mobile_no;
                $export[] =  $data->address;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Certified First Aider.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/certified-first-aider/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->certifiedfirstaider->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __("common.unit"),
                __("common.department"),
                __("common.employee_or_worker_code"),
                __("ohc_management.certified_first_aider"),
                __("ohc_management.mobile_no"),
                __("ohc_management.address"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Certified First Aider Details",
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

            $view = view('ohcmanagement.master.certified_first_aid.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Certified First Aider.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/certified-first-aider/list'));
        }
    }
}
