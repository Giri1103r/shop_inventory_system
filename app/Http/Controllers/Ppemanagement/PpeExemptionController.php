<?php

namespace App\Http\Controllers\Ppemanagement;
use App\Models\Master\Employee;
use App\Models\Master\PpeRequest;
use App\Models\Master\PpeType;
use App\Models\Master\PpeTypeMaster;
use App\Models\UploadLog;
use App\Http\Controllers\Controller;
use App\Mail\PpeExemptionEmail;
use App\Models\Master\PpeExemption;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;

class PpeExemptionController extends Controller
{

    private $ppeexemption;
    private $user;
    private $pperequest;
    private $uploadlog;

    public function __construct()
    {
        $this->ppeexemption = new PpeExemption();
        $this->uploadlog = new UploadLog();
        $this->user = new User();
        $this->pperequest = new PpeRequest();

    }
    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->ppeexemption->list();
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
                        ->editColumn('department', function ($row) {
                            return $row->department_name;
                        })
                        ->editColumn('unit', function ($row) {
                            return $row->unit_name;
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn .= '<a href="' . admin_url('ppe_exemption/view/' . encryptId($row->id)) . '" class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('ppe_exemption/edit/' . encryptId($row->id)) . '" class="" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a> ';
                            $btn .= '<a href="javascript:void(0);" data-id="' . encryptId($row->id) . '" class="recordDelete" title="Delete"><i class="fa-solid fa-trash text-danger"></i></a> ';
                            if ((CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_EHS_OFFICER)) && $row->approve_status == 'PENDING') {
                            $btn .= '<a href="' . admin_url('ppe_exemption/approval/view/' . encryptId($row->id)) . '" class="" title="Approval"><i class="fa-solid fa-check-to-slot text-warning"></i></a> ';
                            }
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

        return view('ppemanagement.ppeexemption.list');
    }


    public function add(){

         $userData = $this->user->getUserdata();
        $data = [
          'userData'=>$userData,
        ];
        return view('ppemanagement.ppeexemption.add' ,$data);
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'from_date' => 'required|date_format:d-m-Y',
                'to_date' => 'required|date_format:d-m-Y|after_or_equal:from_date',
                'reason' => ['required', 'regex:/^[a-zA-Z0-9\-_\'"()\s]+$/'],
            ];


            $messages = [
                'from_date.required' => __('From Date is required'),
                'from_date.date_format' => __('From Date must be in the format Y-m-d'),
                'to_date.required' => __('To Date is required'),
                'to_date.date_format' => __('To Date must be in the format Y-m-d'),
                'to_date.after_or_equal' => __('To Date must be on or after From Date'),
                'reason.required' => __('Reason is required'),
                'reason.regex' => __('Reason should be alphanumeric and can include -, _, \', ", (, ).'),
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

                $this->ppeexemption->store();
                Session::flash('success', __('PPE Exemption is taken  added successfully'));
                return redirect(admin_url('ppe_exemption/list'));
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', __('common.message_error'));
            }
            return redirect(admin_url('ppe_exemption/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ppe_exemption/list'));
        }
    }

    public function view(Request $request){

        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $ppeexemption = $this->ppeexemption->selectOne($id);
            }
            $data =[
              'ppeexemption'=>  $ppeexemption,
              'encryptid' => $request->id,
            ];
            return view('ppemanagement.ppeexemption.view' ,$data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function edit(Request $request){
        try{
            $id = decryptId($request->id);
            $ppeexemption = $this->ppeexemption->find($id);
            $userData = $this->user->getUserdata();

            $data = [
              'userData'=>$userData,
              'ppeexemption'=>$ppeexemption,
              'encryptid' => $request->id,
            ];

            return view('ppemanagement.ppeexemption.edit', $data);
        }
        catch(Exception $ex){
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ppe_exemption/list'));
        }
    }

    public function update(Request $request){
        try {
            $id = decryptId($request->id);
            $rules = [
                'from_date' => 'required|date_format:d-m-Y',
                'to_date' => 'required|date_format:d-m-Y|after_or_equal:from_date',
                'reason' => ['required', 'regex:/^[a-zA-Z0-9\-_\'"()\s]+$/'],
            ];


            $messages = [
                'from_date.required' => __('From Date is required'),
                'from_date.date_format' => __('From Date must be in the format Y-m-d'),
                'to_date.required' => __('To Date is required'),
                'to_date.date_format' => __('To Date must be in the format Y-m-d'),
                'to_date.after_or_equal' => __('To Date must be on or after From Date'),
                'reason.required' => __('Reason is required'),
                'reason.regex' => __('Reason should be alphanumeric and can include -, _, \', ", (, ).'),
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

                $this->ppeexemption->updates($id);
                Session::flash('success', __('PPE Exemption is taken updated successfully'));
                return redirect(admin_url('ppe_exemption/list'));
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', __('common.message_error'));
            }
            return redirect(admin_url('ppe_exemption/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ppe_exemption/list'));
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->ppeexemption->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => __('PPE Exemption  to be taken status changed')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('administration.please_try_after_some_time')], 406);
        }
    }

    public function Delete(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $this->ppeexemption->deleterecord($id);

            return response()->json(['status' => 'success', 'msg' => __('PPE Exemption to be taken deleted successfully')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('administration.please_try_after_some_time')], 406);
        }
    }

    public function ApprovalReject(Request $request){
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $ppeexemption = $this->ppeexemption->selectOne($id);
            }
            $data =[
              'ppeexemption'=>  $ppeexemption,
              'encryptid' => $request->id,
            ];
            return view('ppemanagement.ppeexemption.approvereject' ,$data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function storeapprovereject(Request $request){

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
            $emp_details = $this->ppeexemption->find($id);
            $department =  $emp_details->department;
            $departmentId = $this->ppeexemption->findDepartment( $department)->department;
            $action = $request->input('action');
            $remarks = $request->input('remarks');
            $approved_at = $request->input('date');

            $updateData = [
                'remarks' => $remarks,
                'approved_by' => Auth::id(),
                'approve_status' => $action == 'approve' ? 'APPROVED' : 'REJECT',
                'approved_at' => DBdatetimeformat($approved_at),
            ];

            $emp_details->updateapproval($updateData, $id);
            $details = [
                'emp_id' => $emp_details->emp_id,
                'emp_name' => $emp_details->emp_name,
                'remarks' =>$updateData['remarks'],
                'status' =>$updateData['approve_status'],
                'department' => $emp_details->department,
                'unit' => $emp_details->unit,
                'approved_by' => $emp_details->approved_by,
            ];
            $empId = $emp_details->emp_id;
            $requestor = $this->pperequest->getrequestemail($empId);
            $hod = $this->pperequest->getdepartmenthod($departmentId);

            if ($action == 'approve' || $action == 'reject') {
                $recipients = array_filter([$requestor, $hod]);
                Mail::to($recipients)->send(new PpeExemptionEmail($details));
            }

            Session::flash('success', 'PPE Exemption has successfully responded');
            return redirect(admin_url('ppe_exemption/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', __('common.message_error'));
            return redirect(admin_url('ppe_exemption/list'));
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->ppeexemption->exportdata();

            $header = [
                __("common.sno"),
                __("Emp Id"),
                __('Emp Name'),
                __("Department"),
                __("Unit"),
                __("From Date"),
                __("To Date"),
                __("Reason"),
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
                $export[] = getDepartment($data->department );
                $export[] =  getUnitname($data->unit);
                $export[] =  Displaydateformat($data->from_date);
                $export[] =  Displaydateformat($data->to_date);
                $export[] =  $data->reason;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('PPE Exemption to be taken .xlsx')
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

            $allData = $this->ppeexemption->exportdata();
            $header = [
                __("common.sno"),
                __("Emp Id"),
                __('Emp Name'),
                __("Department"),
                __("Unit"),
                __("From Date"),
                __("To Date"),
                __("Reason"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "PPE Exemption ",
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

            $view = view('ppemanagement.ppeexemption.pdf', $data);
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
