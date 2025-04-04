<?php

namespace App\Http\Controllers\OhcManagement;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Jobs\Ohc\ImportRequisitionjob;
use App\Mail\Ohc\MedicineRequisitionEmail;
use App\Models\Master\Department;
use App\Models\Master\Employee;
use App\Models\Master\Unit;
use App\Models\OhcManagement\Master\Medicine;
use App\Models\OhcManagement\Master\Vendor;
use App\Models\OhcManagement\MedicineRequisition;
use App\Models\OhcManagement\UserMedicineRequisition;
use App\Models\UploadLog;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;
use App\Models\OhcManagement\MedicineStock;
use App\Models\OhcManagement\OhcStatuslog;
use App\Models\OhcManagement\Report\Inventory;
use App\Models\User;
use Illuminate\Support\Facades\File;

use function Ramsey\Uuid\v1;

class MedicineRequisitionController extends Controller
{
    private $medicine;
    private $vendor;
    private $user_medicine_requisition;
    private $unit;
    private $uploadlog;
    private $department;
    private $medicine_requisition;
    private $medicine_stock;
    private $ohcStatus;
    private $user;
    private $inventory;

    public function __construct()
    {
        $this->medicine = new Medicine();
        $this->vendor = new Vendor();
        $this->user_medicine_requisition = new UserMedicineRequisition();
        $this->medicine_requisition = new MedicineRequisition();
        $this->medicine_stock = new MedicineStock();
        $this->unit = new Unit();
        $this->department = new Department();
        $this->ohcStatus = new OhcStatuslog();
        $this->user = new User();
        $this->inventory = new Inventory();
        $this->uploadlog = new UploadLog();
    }
    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->user_medicine_requisition->list();
                    $datatables = DataTables::of($data['data'])
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

                        ->addColumn('approve_status', function ($row) {

                            if ($row->created_by == Auth::id()) {
                                if ($row->approve_status == STATUS_OHC_REQUISITION_EHS_HEAD_APPROVED) {
                                    $text = "<span class='badge bg-info' style='font-size: 1.0em;'>Open</span>";
                                }
                            }

                            if ($row->approve_status == STATUS_OHC_REQUISITION_EHS_HEAD_APPROVAL_PENDING) {
                                $text = "<span class='badge bg-info' style='font-size: 1.0em;'>EHS Head Approval Pending</span>";
                            } else if ($row->approve_status == STATUS_OHC_REQUISITION_EHS_HEAD_APPROVED) {
                                $text = "<span class='badge bg-success' style='font-size: 1.0em;'>EHS Head Approved</span>";
                            } else if ($row->approve_status == STATUS_OHC_REQUISITION_EHS_HEAD_REJECTED) {
                                $text = "<span class='badge bg-danger' style='font-size: 1.0em;'>EHS Head Rejected</span>";
                            } else if ($row->approve_status == STATUS_OHC_OPEN) {
                                $text = "<span class='badge bg-info' style='font-size: 1.0em;'>Open</span>";
                            } else if ($row->approve_status == STATUS_OHC_CLOSE) {
                                $text = "<span class='badge bg-success' style='font-size: 1.0em;'>Close</span>";
                            }
                            return $text;
                        })
                        ->editColumn('request_date', function ($row) {
                            return displaydateformat($row->request_date);
                        })
                        ->editColumn('unit_id', function ($row) {
                            return getUnitname($row->unit_id);
                        })
                        ->editColumn('department_id', function ($row) {
                            return getDepartment($row->department_id);
                        })
                        ->editColumn('action', function ($row) {
                            $btn = '';
                            if (CheckUserPermission('view')) {
                            $btn .= '<a href="' . admin_url('ohc/medicine-requisition/view/' . encryptId($row->id)) . '" class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            }
                            if (CheckUserPermission('edit') && $row->approve_status == STATUS_OHC_REQUISITION_EHS_HEAD_APPROVAL_PENDING && $row->created_by == Auth::id()) {
                            $btn .= '<a href="' . admin_url('ohc/medicine-requisition/edit/' . encryptId($row->id)) . '" class="" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a> ';
                            }
                            if ((checkUserRole(ROLE_SUPERADMIN) && $row->approve_status == STATUS_OHC_REQUISITION_EHS_HEAD_APPROVAL_PENDING) || (checkUserRole(ROLE_EHS_HEAD) && $row->approve_status == STATUS_OHC_REQUISITION_EHS_HEAD_APPROVAL_PENDING)) {
                                $btn .= '<a href="' . admin_url('ohc/medicine-requisition/approval/view/' . encryptId($row->id)) . '" class="" title="Action"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ((checkUserRole(ROLE_SUPERADMIN) && $row->approve_status == STATUS_OHC_REQUISITION_EHS_HEAD_APPROVED) || (checkUserRole(ROLE_EHS_HEAD) && $row->approve_status == STATUS_OHC_REQUISITION_EHS_HEAD_APPROVED)) {
                                $btn .= '<a href="' . admin_url('ohc/medicine-issuance/add/' . encryptId($row->id)) . '" class="" title="Action"><i class="fas fa-share-square " style="color: #0013ff;"></i></a> ';
                            }


                            $btn .= '<a href="' . admin_url('ohc/medicine-requisition/generalpdf/' . encryptId($row->id)) . '" class="" title="PDF"> <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i></a> ';
                            return $btn;
                        })

                        ->rawColumns(['action', 'request_date', 'approve_status', 'unit_id', 'department_id'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);

                    return response()->json($datatables->getData());
                } catch (Exception $ex) {
                    report($ex);
                    return response()->json(['status' => 'error', 'msg' => __('ppe.please_try_after_some_time')], 406);
                }
            }
        }

        $unit = $this->unit->getunit();
        $data = array(

            'unit' => $unit
        );

        return view('ohcmanagement.medicine_requisition.list', $data);
    }

    public function add()
    {
        try {
            $unit = $this->unit->getuserunit();
            $medicine = $this->inventory->getstockdata();
            $data = array(
                'medicine' => $medicine,
                'unit' => $unit
            );

            return view('ohcmanagement.medicine_requisition.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medicine-requisition/list'));
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'unit_id' => 'required',
                'department_id' => 'required',
                'request_date' => 'required',

            ];
            $messages = [
                'department_id.required' => 'Please select a Deparment.',
                'unit_id.required' => 'Please select a unit.',
                'request_date.required' => 'Please select the expiry date.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {
                // Store user medicine requisition
                $user_medicine_requisition = $this->user_medicine_requisition->store();
                $medicineRequisition = $this->medicine_requisition->store($user_medicine_requisition);
                $id = $user_medicine_requisition->id;
                $this->ohcStatus->storeMedicineRecevingdata($id);
                $details = $this->user_medicine_requisition->selectOne($user_medicine_requisition->id);
                // Email details
                $mailsubject = getUsername($details ->created_by) .'Request the Medicine for the stock';
                $user_role = ROLE_EHS_HEAD;

                // Fetch users with the specified role
                $users = $this->user->whereRaw('FIND_IN_SET(?, role)', [$user_role])->get();
                $userids = $users->pluck('id')->toArray();

                if ($users->isNotEmpty()) {
                    foreach ($users as $user) {
                        $email_id = $user->email;

                        if (!empty($email_id)) {
                            $details = $this->user_medicine_requisition->selectOne($user_medicine_requisition->id);
                            $medicineDetails = $this->medicine_requisition->selectOne($user_medicine_requisition->id);

                            if ($details && $medicineDetails) {
                                $emailDetails = $details->toArray();
                                $emailDetails['name'] = $user->name;
                                $emailDetails['email_id'] = $email_id;
                                $emailDetails['mail_subject'] = $mailsubject;

                                Mail::to($emailDetails['email_id'])->queue(new MedicineRequisitionEmail($emailDetails, $medicineDetails));
                            }
                        }
                    }
                }


                /**
                 * Send Web notification
                 */
                $details = $this->user_medicine_requisition->selectOne($user_medicine_requisition->id);
                $notificationData = array(
                    'notification_type' => 4,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => "Request For the Medicine by " . getUsername($details->created_by),
                        'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' =>    $details->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('ohc/medicine-requisition/approval/view/' . encryptId($details->id)),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/medicine-requisition/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medicine-requisition/list'));
        }
    }

    public function edit(Request $request)
    {
            try {
                $id = decryptId($request->id);


                $user_medicine_requisition = $this->user_medicine_requisition->selectOne($id);
                $departmentList = $this->department->getdepartment();
                $unit = $this->unit->getunit();
                $medicine = $this->inventory->getstockdata();
                $medicine_requisition = $this->medicine_requisition->selectOne($id);

                $data = array(
                    'medicine' => $medicine,
                    'unit' => $unit,
                    'departmentList' => $departmentList,
                    'user_medicine_requisition' => $user_medicine_requisition,
                    'medicine_requisition' => $medicine_requisition

                );
                return view('ohcmanagement.medicine_requisition.edit', $data);
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
                return redirect(admin_url('ohc/medicine-requisition/list'));
            }

    }
    public function update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rules = [
                'unit_id' => 'required',
                'department_id' => 'required',
                'request_date' => 'required',

            ];

            $messages = [
                'department_id.required' => 'Please select a Deparment.',
                'unit_id.required' => 'Please select a unit.',
                'request_date.required' => 'Please select the expiry date.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

                $user_medicine_requisition = $this->user_medicine_requisition->updates($id);


                $deletedPages = json_decode($request->deletedPage, true);

                if (!empty($deletedPages)) {
                    foreach ($deletedPages as $encryptedId) {
                        $medicineIds = decryptId($encryptedId);

                        $update_data = $this->medicine_requisition
                            ->where('id', $medicineIds)->where('req_id',$id)
                            ->update([
                                'status' => 0,
                                'trash'  => 'Yes'
                            ]);
                    }
                }
                $this->medicine_requisition->updates($id);


                $mailsubject = 'Paramedics Request the Medicine';
                $user_role = ROLE_EHS_HEAD;

                // Fetch users with the specified role
                $users = $this->user->whereRaw('FIND_IN_SET(?, role)', [$user_role])->get();
                $userids = $users->pluck('id')->toArray();

                if ($users->isNotEmpty()) {
                    foreach ($users as $user) {
                        $email_id = $user->email;

                        if (!empty($email_id)) {
                            $details = $this->user_medicine_requisition->selectOne($id);
                            $medicineDetails = $this->medicine_requisition->selectOne($id);

                            if ($details && $medicineDetails) {
                                $emailDetails = $details->toArray();
                                $emailDetails['name'] = $user->name;
                                $emailDetails['email_id'] = $email_id;
                                $emailDetails['mail_subject'] = $mailsubject;

                                Mail::to($emailDetails['email_id'])->queue(new MedicineRequisitionEmail($emailDetails, $medicineDetails));
                            }
                        }
                    }
                }


                /**
                 * Send Web notification
                 */
                $details = $this->user_medicine_requisition->selectOne($id);
                $notificationData = array(
                    'notification_type' => 4,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => "Request For the Medicine by " . getUsername(  $details->created_by),
                        'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('ohc/medicine-requisition/approval/view/' . encryptId($id)),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                Session::flash('success', 'Your data has been updated successfully!');
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/medicine-requisition/list'));
        } catch (Exception $ex) {

            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medicine-requisition/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $user_medicine_requisition = $this->user_medicine_requisition->selectOne($id);
                $medicine_requisition = $this->medicine_requisition->selectOne($id);
            }
            $unit = $this->unit->getunit();
            $logData = $this->ohcStatus->getMedicineRequisitionLog($id);
            $data = array(
                'user_medicine_requisition' => $user_medicine_requisition,
                'medicine_requisition' => $medicine_requisition,
                'logdata' => $logData,

            );
            return view('ohcmanagement.medicine_requisition.view', $data);
        } catch (Exception $ex) {
        }
    }

    public function approvalview(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $user_medicine_requisition = $this->user_medicine_requisition->selectOne($id);
                $medicine_requisition = $this->medicine_requisition->selectOne($id);
            }
            if ($user_medicine_requisition->approve_status != STATUS_OHC_REQUISITION_EHS_HEAD_APPROVAL_PENDING) {
                return redirect(admin_url('ohc/medicine-requisition/view/' . encryptId($id)))
                 ;
            }

            $data = array(
                'user_medicine_requisition' => $user_medicine_requisition,
                'medicine_requisition' => $medicine_requisition,
            );

            return view('ohcmanagement.medicine_requisition.approve', $data);
        } catch (Exception $ex) {
        }
    }
    public function apporvalsubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $rules = [
                'approver_name' => 'required',
                'remarks' => 'required',
            ];
            $messages = [
                'approver_name.required' => 'Approver name is required.',
                'remarks.required' => 'Remarks are required.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            if ($request->action == 'approve') {
                $data = [
                    'approve_status' => STATUS_OHC_REQUISITION_EHS_HEAD_APPROVED,
                ];
            } else {
                $data = [
                    'approve_status' => STATUS_OHC_REQUISITION_EHS_HEAD_REJECTED,
                ];
            }

            $this->user_medicine_requisition->approvereject($id, $data);
            $this->ohcStatus->paramedicsapprove($id, $data);
            $createdBy = $this->user_medicine_requisition->where('id', $id)->pluck('created_by');
            $user = $this->user->where('id', $createdBy)->where('status', 1)->first();
            if ($request->action == 'approve') {
                $mailsubject = 'EHS Head Approved the medicine';
                $email_id = $user->email;

                if (!empty($email_id)) {
                    $details = $this->user_medicine_requisition->selectOne($id);
                    $medicineDetails = $this->medicine_requisition->selectOne($id);

                    if ($details && $medicineDetails) {
                        $emailDetails = $details->toArray();
                        $emailDetails['name'] = $user->name;
                        $emailDetails['email_id'] = $email_id;
                        $emailDetails['mail_subject'] = $mailsubject;

                        Mail::to($emailDetails['email_id'])->queue(new MedicineRequisitionEmail($emailDetails, $medicineDetails));
                    }
                }
                $notificationData = array(
                    'notification_type' => 4,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => "The requested Medicine Was approved By " . getUsername($details->approved_by),
                        'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('ohc/medicine-requisition/list'),
                    'assigned_user' => $details->created_by,
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);
            } else {
                $mailsubject = 'EHS Head Rejected the medicine';
                $email_id = $user->email;

                if (!empty($email_id)) {
                    $details = $this->user_medicine_requisition->selectOne($id);
                    $medicineDetails = $this->medicine_requisition->selectOne($id);

                    if ($details && $medicineDetails) {
                        $emailDetails = $details->toArray();
                        $emailDetails['name'] = $user->name;
                        $emailDetails['email_id'] = $email_id;
                        $emailDetails['mail_subject'] = $mailsubject;

                        Mail::to($emailDetails['email_id'])->queue(new MedicineRequisitionEmail($emailDetails, $medicineDetails));
                    }
                }
                $notificationData = array(
                    'notification_type' => 4,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => "The requested Medicine Was Rejected By " . getUsername($details->approved_by),
                        'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('ohc/medicine-requisition/list'),
                    'assigned_user' => $details->created_by,
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);
            }

            return redirect(admin_url('ohc/medicine-requisition/list'))
                ->with('success', 'Request has been processed successfully.');
        } catch (Exception $ex) {
            report($ex);
            return redirect(admin_url('ohc/medicine-requisition/list'))
                ->with('error', 'Something went wrong, Please try again later.');
        }
    }

    // General PDF

    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $user_medicine_requisition = $this->user_medicine_requisition->selectOne($id);
                $medicine_requisition = $this->medicine_requisition->selectOne($id);
            }
            $logData = $this->ohcStatus->getMedicineRequisitionLog($id);

            $data = [
                'user_medicine_requisition' => $user_medicine_requisition,
                'medicine_requisition' => $medicine_requisition,
                'logdata' => $logData,
                'pagetitle' => "Medicine Requisition",
            ];

            $property = [
                'tempDir' => 'public/pdf/temp/',
                'mode' => 'c',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,

            ];

            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';

            $html = view('ohcmanagement.medicine_requisition.generalpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "Medicine Receiving Stock Details.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            return redirect()->back()->withErrors(['error' => 'An error occurred while generating the PDF.']);
        }
    }
    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->user_medicine_requisition->statuschange($id);
            $this->medicine_requisition->statuschange($id);


            return response()->json(['status' => 'success', 'msg' => 'Your status  has changed Successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    // delete

    public function delete(Request $request,$id){
        try{
            $ids = decryptId($id);
            // dd( $ids);
            $this->medicine_requisition->deleterecord($ids);
            return response()->json(['status' => 'success', 'msg' => 'Deleted successfully'], 200);
        }
    catch (Exception $ex) {
        report($ex);
        return response()->json(['status' => 'error', 'msg' => 'Something went wrong'], 200);
    }

    }

    // import

    public function import()
    {
        return view('ohcmanagement.medicine_requisition.import');
    }

    public function ImportSubmit(Request $request)
    {
        try {
            $file = $request->file('medicine_upload');

            $rules = [
                'medicine_upload' => 'required',
            ];
            $messages = [
                'medicine_upload.required' => 'Please upload a file',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }


            if ($file != null) {

                $uploadpath = 'public/uploads/medicine';

                $folderPath = public_path('uploads/medicine');

                if (!File::exists($folderPath)) {

                    File::makeDirectory($folderPath, 0755, true);
                }

                $filenewname = time() . Str::random('10') . '.' . $file->getClientOriginalExtension();

                $fileName = $file->getClientOriginalName();
                $fileSize = $file->getSize();

                $fileExt = $file->getClientOriginalExtension();

                $file->move($uploadpath, $filenewname);

                $path = $uploadpath . "/" . $filenewname;
                $user_id = Auth::id();

                $insert_data = array(
                    'upload_type' => 17,
                    'upload_status' => 0,
                    'file_name' => $filenewname,
                    'file_orgname' => $fileName,
                    'file_path' => $path,
                    'file_size' => $fileSize,
                    'file_extension' => $fileExt,
                    'created_by' => $user_id,
                );

                $insert_id =  $this->uploadlog->create($insert_data)->id;



                $details = [
                    "user_id" => $user_id,
                    "log_id" => $insert_id,
                    "path" => $path,
                ];
                $medicnieRequisition = $this->user_medicine_requisition->store();
                $id = $medicnieRequisition->id;
                $this->ohcStatus->storeMedicineRecevingdata($id);
                // dispatch(new ImportRequisitionjob($details, $medicnieRequisition));
                   dispatch((new ImportRequisitionjob($details, $medicnieRequisition))->onQueue('medicine_requisition'));
            }

            $insert_data['log_id'] = $insert_id;
            $insert_data['Uploded_by'] = Auth::user()->toArray();

            Session::flash('success', __('Medicine uploaded sucessfully'));
            return redirect(admin_url('ohc/medicine-requisition/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('Medicine  upload failed'));
            return redirect(admin_url('ohc/medicine-requisition/list'));
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->user_medicine_requisition->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Requisition ID',
                'Unit Name',
                'Department Name',
                'Request Date',
                'From Status',
                'To Status',
                'Created by',
                'Created at'
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->req_id;
                $export[] =  getUnitname($data->unit_id);
                $export[] =  getDepartment($data->department_id);
                $export[] =  Displaydateformat($data->request_date);
                if ($data->approve_status == STATUS_OHC_REQUISITION_EHS_HEAD_APPROVAL_PENDING) {
                    $export[] = 'Stock Requested ';
                } elseif ($data->approve_status == STATUS_OHC_REQUISITION_EHS_HEAD_APPROVAL_PENDING) {
                    $export[] = 'Paramedics approval Pending';
                } elseif ($data->approve_status == STATUS_OHC_REQUISITION_EHS_HEAD_APPROVED) {
                    $export[] = 'Paramedics approval Pending';
                } elseif ($data->approve_status == STATUS_OHC_REQUISITION_EHS_HEAD_REJECTED) {
                    $export[] = 'Paramedics approval Pending';
                } elseif ($data->approve_status == STATUS_OHC_CLOSE) {
                    $export[] = 'Open';
                } else {
                    $export[] = removeUnderScore(getStatus($data->approve_status));
                }
                if ($data->approve_status == STATUS_OHC_REQUISITION_EHS_HEAD_APPROVAL_PENDING) {
                    $export[] = 'Paramedics approval Pending ';
                } elseif ($data->approve_status == STATUS_OHC_REQUISITION_EHS_HEAD_APPROVED) {
                    $export[] = 'Paramedics Approved';
                } elseif ($data->approve_status == STATUS_OHC_REQUISITION_EHS_HEAD_REJECTED) {
                    $export[] = 'Paramedics Rejected';
                } elseif ($data->approve_status == STATUS_OHC_CLOSE) {
                    $export[] = 'closed';
                } else {
                    $export[] = removeUnderScore(getStatus($data->approve_status));
                }
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Medicine Requisition.xlsx')
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

            $allData = $this->user_medicine_requisition->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Requisition ID',
                'Unit Name',
                'Department Name',
                'Request Date',
                'From Status',
                'To Status',
                'Created by',
                'Created at'
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Medicine Requisition",
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

            $view = view('ohcmanagement.medicine_requisition.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Medicine Requisition.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
        }
    }

    public function quantity(Request $request, $quantity_id)
    {
        $id = decryptId($quantity_id);


        $availableQuantity = $this->inventory->getAvailableQuantity($id);

        return response()->json(
            ['available_quantity' => $availableQuantity->balance]
        );
    }

    public function stockdata(Request $request)
    {
        $name = $request->input('search');

        $medicineData = $this->medicine_stock->getMedicineRequisitionStock();

        $results = $medicineData->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->medicine_id,
            ];
        });

        return response()->json(['results' => $results]);
    }
    public function DownloadSample(Request $request)
    {

        $filedetails =  exportsamplefile('medicinerequisition');

        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;

        //return Response::download($filePath, $customFileName);
        return redirect(url($filePath));
    }
}
