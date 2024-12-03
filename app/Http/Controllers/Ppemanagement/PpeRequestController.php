<?php

namespace App\Http\Controllers\Ppemanagement;

use App\Http\Controllers\Controller;
use App\Mail\PpeRequestEmail;
use App\Models\Master\Employee;
use App\Models\Master\PpeRequest;
use App\Models\Master\PpeType;
use App\Models\Master\PpeTypeMaster;
use App\Models\UploadLog;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;

class PpeRequestController extends Controller
{
    private $ppetypemaster;
    private $ppetype;
    private $pperequest;
    private $employee;
    private $uploadlog;

    public function __construct()
    {
        $this->ppetypemaster = new PpeTypeMaster();
        $this->ppetype = new PpeType();
        $this->uploadlog = new UploadLog();
        $this->pperequest = new PpeRequest();
        $this->employee = new Employee();
    }
    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->pperequest->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active</span>";
                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->id) . "' data-type='1'>Active</span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->id) . "' data-type='0'>In-Active</span>";
                            }
                            return $text;
                        })
                        ->addColumn('created_at', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('ppe_type', function ($row) {
                            return getPpeType($row->ppe_type);
                        })
                        ->addColumn('ppe_name', function ($row) {
                            return getPpename($row->ppe_name);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            if(CheckUserRole(ROLE_EHS_OFFICER) && CheckUserRole(ROLE_SUPERADMIN) && CheckUserRole(ROLE_ADMIN)){
                                $btn .= '<a href="' . admin_url('ppe_request/view/' . encryptId($row->id)) . '" class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            }
                            // $btn .= '<a href="' . admin_url('ppe_request/edit/' . encryptId($row->id)) . '" class="" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a> ';
                            // $btn .= '<a href="javascript:void(0);" data-id="' . encryptId($row->id) . '" class="recordDelete" title="Delete"><i class="fa-solid fa-trash text-danger"></i></a> ';
                            // $btn .= '<a href="' . admin_url('ppe_request/approval/' . encryptId($row->id)) . '" class="" title="Approve"><i class="fa-solid fa-pen-to-square"></i></a> ';

                            // $btn .= '<a href="' . admin_url('ppe_request/view/pdf/' . encryptId($row->id)) . '" class=" " title="Pdf"> <i class="fa-solid fa-file-pdf" style="color: #e67265;"></i></i> </a>';


                            return $btn;
                        })
                        ->rawColumns(['action', 'created_at', 'created_by', 'status'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);

                    return response()->json($datatables->getData());
                } catch (Exception $ex) {
                    return response()->json(['status' => 'error', 'msg' => __('ppe.please_try_after_some_time')], 406);
                }
            }
        }

        $ppetype = $this->ppetype->getPpetypedata();
        $ppename = $this->ppetypemaster->getppetypemaster();
        $data = [
            'ppetype' => $ppetype,
            'ppename' => $ppename,
        ];

        return view('ppemanagement.pperequest.list', $data);
    }

    public function add(Request $request)
    {
        $employee = $this->employee->getEmployeedata();
        $ppetypedata = $this->ppetype->getPpetypedata();
        $ppetypemaster = $this->ppetypemaster->getppetypemaster();
        $data = [
            'employee' => $employee,
            'ppetypedata' => $ppetypedata,
            'ppetypemaster' => $ppetypemaster,

        ];
        // dd($data);
        return view('ppemanagement.pperequest.add', $data);
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'ppe_type' => 'required',
                'ppe_name' => 'required',

            ];
            $messages = [

                'ppe_name.required' => __('PPE Name is required'),
                'ppe_type.required' => __('PPE Type  is required'),

            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $lastPPERequest = $this->pperequest->lastPpeRequest();

            if ($lastPPERequest) {
                $lastRequestDate = Carbon::parse($lastPPERequest->created_at)->addYear();
                $currentDate = Carbon::now();

                if (!($lastRequestDate->lt($currentDate))) {
                    Session::flash('error', __('PPE Request is not allowed within one year'));
                    return redirect(admin_url('ppe_request/list'));
                }
            }

            try {

                $this->pperequest->store();
                Session::flash('success', __('PPE Request is taken  added successfully'));
                return redirect(admin_url('ppe_request/list'));
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', __('common.message_error'));
            }
            return redirect(admin_url('ppe_request/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ppe_request/list'));
        }
    }

    public function view(Request $request){

        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $pperequest = $this->pperequest->selectOne($id);
            }
            $data =[
              'pperequest'=>  $pperequest,
              'encryptid' => $request->id,
            ];
            return view('ppemanagement.pperequest.view' ,$data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function viewpdf(Request $request){

        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $pperequest = $this->pperequest->selectOne($id);
            }
            $data =[
              'pperequest'=>  $pperequest,
              'encryptid' => $request->id,
            ];
            return view('ppemanagement.pperequest.pdf.logsheet' ,$data);
        } catch (Exception $ex) {
            report($ex);
        }
    }


    public function ApprovalReject(Request $request)
    {
        $rules = [
            'remarks' => 'required|min:3|max:255|regex:/^[a-zA-Z].*/',
        ];
        $messages = [
            'remarks.required' => 'Remarks Field is Mandatory',
            'remarks.min' => 'Minimum 3 characters are required',
            'remarks.max' => 'Maximum limit is 255 characters',
            'remarks.regex' => 'First character should be an alphabet',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $id = decryptId($request->id);

        try {
            $emp_details = $this->pperequest->find($id);
            $departmentId = $this->pperequest->findDepartment($id)->department;
            $action = $request->input('action');
            $remarks = $request->input('remarks');
            $approved_at = $request->input('date');

            $updateData = [
                'approve_msg' => $remarks,
                'approved_by' => Auth::id(),
                'approve_status' => $action == 'approve' ? 'Approved' : 'Rejected',
                'approved_at' => DBdatetimeformat($approved_at),
            ];

            $emp_details->updateapproval($updateData, $id);
            $details = [
                'emp_id' => $emp_details->emp_id,
                'emp_name' => $emp_details->emp_name,
                'reason' => $emp_details->approve_msg,
                'status' => $emp_details->approve_status,
                'approved_by' => $emp_details->approved_by,
            ];
            $empId = $emp_details->emp_id;
            $requestor = $this->pperequest->getrequestemail($empId);
            $hod = $this->pperequest->getdepartmenthod($departmentId);
            $storemanager = $this->pperequest->getstoremanager();

            if ($action == 'approve') {
                $recipients = array_filter([$requestor, $hod, $storemanager]);
                Mail::to($recipients)->send(new PpeRequestEmail($details));
            } else {
                Mail::to($requestor)->send(new PpeRequestEmail($details));
            }

            Session::flash('success', 'PPE Request has successfully responded');
            return redirect(admin_url('ppe_request/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', __('common.message_error'));
            return redirect(admin_url('ppe_request/list'));
        }
    }



    public function edit(Request $request){
        try{
            $id = decryptId($request->id);
            $pperequest = $this->pperequest->find($id);
            $employee = $this->employee->getEmployeedata();
            $ppetypedata = $this->ppetype->getPpetypedata();
            $ppetypemaster = $this->ppetypemaster->getppetypemaster();
            $data = [
                'employee' => $employee,
                'ppetypedata' => $ppetypedata,
                'ppetypemaster' => $ppetypemaster,
                'encryptid' => $request->id,
                'pperequest' => $pperequest,

            ];
            return view('ppemanagement.pperequest.edit', $data);
        }
        catch(Exception $ex){
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ppe_request/list'));
        }
    }

    public function update(Request $request){
        try {
            $id = decryptId($request->id);
            $rules = [
                'ppe_type' => 'required',
                'ppe_name' => 'required',

            ];
            $messages = [

                'ppe_name.required' => __('PPE Name is required'),
                'ppe_type.required' => __('PPE Type  is required'),

            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

                $this->pperequest->updates($id);
                Session::flash('success', __('PPE Request is taken updated successfully'));
                return redirect(admin_url('ppe_request/list'));
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', __('common.message_error'));
            }
            return redirect(admin_url('ppe_request/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ppe_request/list'));
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->pperequest->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => __('PPE Request  to be taken status changed')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('administration.please_try_after_some_time')], 406);
        }
    }

    public function Delete(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $this->pperequest->deleterecord($id);

            return response()->json(['status' => 'success', 'msg' => __('PPE Request to be taken deleted successfully')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('administration.please_try_after_some_time')], 406);
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->pperequest->exportdata();

            $header = [
                __("common.sno"),
                __("Emp Id"),
                __('Emp Name'),
                __("PPE Type"),
                __("PPE Name"),
                __("Department"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->emp_id;
                $export[] =  $data->emp_name;
                $export[] = getPpeType($data->ppe_type );
                $export[] =  getPpename($data->ppe_name);
                $export[] =  getDepartment($data->department);
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('PPE Request to be taken .xlsx')
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

            ini_set("pcre.backtrack_limit", "5000000");

            $allData = $this->pperequest->exportdata();
            $header = [
                __("common.sno"),
                __("Emp Id"),
                __('Emp Name'),
                __("PPE Type"),
                __("PPE Name"),
                __("Department"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "PPE Request ",
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

            $view = view('ppemanagement.pperequest.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Precation to be takens Details.pdf";
            $mpdf->Output($filename, 'I');
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
        }
    }
}
