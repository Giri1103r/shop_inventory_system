<?php

namespace App\Http\Controllers\OhcManagement\Master;

use App\Http\Controllers\Controller;
use App\Jobs\ImportMedicine;
use Illuminate\Http\Request;



use App\Models\Master\Unit;
use App\Models\Master\Location;
use App\Models\Master\Department;
use App\Models\User;
use App\Models\UploadLog;
use App\Jobs\ImportmedicineJob;
use App\Mail\Ohc\MedicineApprovalEmail;
use App\Mail\Ohc\MedicineRequestEmail;
use App\Mail\Ohc\MedicineStockRequestEmail;
use App\Models\OhcManagement\ExpireMedicine;
use App\Models\OhcManagement\Master\Medicine;
use App\Models\OhcManagement\OhcStatuslog;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\OhcManagement\Report\Inventory;

class MedicineController extends Controller
{

    private $medicine;
    private $unit;
    private $location;
    private $department;
    private $user;
    private $uploadlog;
    private $ohc_status;
    private $inventory;
    private $medicine_expire;


    public function __construct()
    {

        $this->medicine = new Medicine();
        $this->unit = new Unit();
        $this->location = new Location();
        $this->department = new Department();
        $this->user = new User();
        $this->uploadlog = new UploadLog();
        $this->ohc_status = new OhcStatuslog();
        $this->inventory = new Inventory();
        $this->medicine_expire = new ExpireMedicine();
    }


    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {

                try {

                    $data =  $this->medicine->list();

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
                        ->addColumn('expiry_date', function ($row) {
                            return Displaydateformat($row->expiry_date);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('approve_status', function ($row) {
                            if ($row->approve_status == STATUS_OHC_EHS_HEAD_APPROVAL_PENDING) {
                                $text = "<span class='badge bg-info' style='font-size: 1.0em;'>EHS Head Approval Pending</span>";
                            } else if ($row->approve_status == STATUS_OHC_EHS_HEAD_APPROVED) {
                                $text = "<span class='badge bg-success' style='font-size: 1.0em;'>EHS Head Approved</span>";
                            }
                            return $text;
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            if (CheckUserPermission('view')) {
                                $btn = '<a href="' . admin_url('ohc/medicine/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            }
                            if (CheckUserPermission('edit') && $row->status == 0) {
                                $btn .= '<a href="' . admin_url('ohc/medicine/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            }
                            if ((CheckUserRole(ROLE_SUPERADMIN) && $row->status == 0 || CheckUserRole(ROLE_EHS_HEAD) && $row->status == 0)) {
                                $btn .= '<a href="' . admin_url('ohc/medicine/approval/view/' . encryptId($row->id)) . '" class="" title="Action"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', 'unit_id', 'expiry_date', 'approve_status'])
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
        $unit = $this->unit->getunit();
        $data = array(

            'unit' => $unit
        );

        return view('ohcmanagement.master.medicine.list', $data);
    }
    // add

    public function Add(Request $request)
    {

        try {
            $unit = $this->unit->getunit();
            $data = [
                'unit' => $unit
            ];
            return view('ohcmanagement.master.medicine.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {
        try {

            $rules = [
                'medicine' => 'required',
                'pack' => 'required',
                'threshold_limit' => 'required|numeric',


            ];
            $messages = [
                'medicine.required' => 'Please enter the medicine name.',
                'pack.required' => 'Please enter the pack details.',
                'threshold_limit.required' => 'Please enter the threshold limit.',
                'threshold_limit.numeric' => 'Threshold limit contains only numeric.',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

                $data =  $this->medicine->store();

                $id =  $data->id;
                $medicine = $this->medicine->selectOne($id);
                $details = $this->medicine->selectOne($id);

                $this->ohc_status->medicinelog($id);

                if ($medicine->approve_status == STATUS_OHC_EHS_HEAD_APPROVED) {
                    $count = $this->unit->getUnitcount();
                    $this->inventory->store($details, $count);
                    $this->medicine_expire->store($medicine);
                }
                if ($medicine->approve_status == STATUS_OHC_EHS_HEAD_APPROVAL_PENDING) {
                    $mailsubject = 'Medicine is Added';
                    $user_role = ROLE_EHS_HEAD;

                    $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->pluck('id')->toArray();
                    $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();

                    if (count($users) > 0) {

                        foreach ($users as $user) {

                            $email_id = $user->email;

                            if ($email_id != '' || $email_id != null) {
                                $medicinedetails =  $this->medicine->selectone($medicine->id);
                                $details  = $medicinedetails->toArray();

                                $details['name'] = $user->name;
                                $details['email_id'] =  $email_id;
                                $details['mail_subject'] = $mailsubject;

                                Mail::to($details['email_id'])->queue(new MedicineRequestEmail($details));
                            }
                        }
                    }

                    $notificationData = array(
                        'notification_type' => 4,
                        'module_type' => 1,
                        'notification_message' => $mailsubject,
                        'mobile_notification' => json_encode(array(
                            'title' => $mailsubject,
                            'message' => $medicine->medicine . ' is added to the master submitted by ' . getUsername($medicine->created_by),
                            'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                            'id' => $medicine->id,
                            'module' => 1,
                        )),
                        'web_link' => admin_url('ohc/medicine/approval/view/' . encryptId($medicine->id)),
                        'assigned_user' => array_to_string($userids),
                        'created_by' => Auth::id(),
                    );

                    notificationSave($notificationData);
                }


                Session::flash('success',  __('common.created_msg'));
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error',  __('common.message_error'));
            }

            return redirect(admin_url('ohc/medicine/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/medicine/list'));
        }
    }
    // view
    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $medicine = $this->medicine->selectOne($id);
                $unit = $this->unit->getunit();
                $logdata =   $this->ohc_status->getmedicinestatuslog($id);
                $data = array(
                    'medicine' => $medicine,
                    'logdata' => $logdata,
                );
            }
            return view('ohcmanagement.master.medicine.view', $data);
        } catch (Exception $ex) {
        }
    }
    // approval
    public function approval(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $medicine = $this->medicine->selectOne($id);

                $unit = $this->unit->getunit();
                $data = array(
                    'medicine' => $medicine,
                );
            }
            if ($medicine->approve_status == STATUS_OHC_EHS_HEAD_APPROVAL_PENDING) {
                return view('ohcmanagement.master.medicine.approve', $data);
            } else {
                return redirect(admin_url('ohc/medicine/view/' . encryptId($medicine->id)));
            }
        } catch (Exception $ex) {
        }
    }

    public function approvalsubmit(Request $request)
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
            try {
                $action = $request->input('action');
                $approveStatus = $action == 'approve' ? STATUS_OHC_EHS_HEAD_APPROVED : STATUS_OHC_EHS_HEAD_REJECTED;
                $remarks = $request->input('remarks');
                $updateData = [
                    'remarks' => $remarks,
                    'approved_by' => Auth::id(),
                    'approve_status' =>  $approveStatus,
                ];
                $details = $this->medicine->selectOne($id);
                $ohcStatus = $this->ohc_status->medicineapproval($id, $updateData);
                $this->medicine_expire->store($details);
                $createdby = $this->medicine->where('id', $id)->value('created_by');
                $email = $this->user->where('id', $createdby)->value('email');


                if (!$email) {

                    return redirect()->back()->with('error', 'User email not found.');
                }

                $action = $request->action;
                if ($action == 'approve') {
                    $mailsubject =  'Medicine Name Has Been Approved';

                    Mail::to($email)->queue(new MedicineApprovalEmail($details));
                    $notificationData = [
                        'notification_type' => 4,
                        'module_type' => 1,
                        'notification_message' => $mailsubject,
                        'mobile_notification' => json_encode([
                            'title' => $mailsubject,
                            'message' => $details->medicine . 'has been approved by the' . Auth::user()->name,
                            'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                            'id' => $id,
                            'module' => 1,
                        ]),
                        'web_link' => admin_url('ohc/medicine/list'),
                        'assigned_user' => $createdby,
                        'created_by' => Auth::id(),
                    ];
                    notificationSave($notificationData);
                    $count = $this->unit->getUnitcount();
                    $this->inventory->store($details, $count);
                } else {
                    $mailsubject =  'Medicine Name Has Been Rejected';
                    $details['mail_subject'] =   $mailsubject;
                    Mail::to($email)->queue(new MedicineStockRequestEmail($details));
                    $notificationData = [
                        'notification_type' => 4,
                        'module_type' => 1,
                        'notification_message' => $mailsubject,
                        'mobile_notification' => json_encode([
                            'title' => $mailsubject,
                            'message' => $details->medicine . 'has been rejected by the' . $details->approver_name,
                            'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                            'id' => $id,
                            'module' => 1,
                        ]),
                        'web_link' => admin_url('ohc/medicine/list'),
                        'assigned_user' => $createdby,
                        'created_by' => Auth::id(),
                    ];
                    notificationSave($notificationData);
                }

                $this->medicine->approval($id, $updateData);

                return redirect(admin_url('ohc/medicine/list'))
                    ->with('success', 'Request has been processed successfully.');
            } catch (Exception $ex) {
                report($ex);
                return redirect(admin_url('ohc/medicine/list'))
                    ->with('error', 'Something went wrong, Please try again later.');
            }
        } catch (Exception $ex) {
            report($ex);
            return redirect(admin_url('ohc/medicine/list'))
                ->with('error', 'Something went wrong, Please try again later.');
        }
    }
    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);


            $medicine = $this->medicine->find($id);
            $unit = $this->unit->getunit();
            $data = array(
                'medicine' => $medicine,
                'unit' => $unit
            );


            return view('ohcmanagement.master.medicine.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rules = [
                'medicine' => 'required',
                'pack' => 'required',
                'threshold_limit' => 'required|numeric',


            ];
            $messages = [
                'medicine.required' => 'Please enter the medicine name.',
                'pack.required' => 'Please enter the pack details.',
                'threshold_limit.required' => 'Please enter the threshold limit.',
                'threshold_limit.numeric' => 'Threshold limit contains only numeric.',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->medicine->updates($id);


            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('ohc/medicine/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/medicine/list'));
        }
    }

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $medicine_name = $request->medicine_name;


            $id = $request->id;

            if (empty($id)) {
                $isUnique = $this->medicine->uniqueCheck($medicine_name);
            } else {
                $id = decryptId($id);

                $isUnique = $this->medicine->existUniqueCheck($medicine_name, $id);
            }

            if ($isUnique->count()) {
                return Response::json(false);
            }
            return Response::json(true);
        }
    }

    public function hsnNumber(Request $request)
    {
        if ($request->ajax()) {

            $hsn = $request->hsn;

            $id = $request->id;
            if (empty($id)) {
                $isUnique = $this->medicine->HsnuniqueCheck($hsn);
            } else {
                $id = decryptId($id);

                $isUnique = $this->medicine->existHsnUniqueCheck($id, $hsn);
            }

            if ($isUnique->count()) {
                return Response::json(false);
            }
            return Response::json(true);
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->medicine->statuschange($id);
            // $medicine =  $this->medicine->selectOne($id);
            // $this->user->statuschange($medicine->login_id);

            return response()->json(['status' => 'success', 'msg' => 'Your status  has changed Successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function Delete(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $location = $this->location->where('medicine_id', $id)->exists();
            $unit = $this->unit->where('medicine_id', $id)->exists();
            $department = $this->department->where('medicine_id', $id)->exists();

            if ($location || $unit || $department) {
                return response()->json(['status' => 'error', 'msg' => 'module_exits'], 406);
            }
            $this->medicine->deleterecord($id);
            return response()->json(['status' => 'success', 'msg' => 'medicine deleted successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->medicine->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __("ohc_management.medicine_name"),
                __("ohc_management.pack"),
                __("ohc_management.threshold_limit"),
                __("ohc_management.remarks"),
                __("common.from_status"),
                __("common.to_status"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->medicine;
                $export[] =  $data->pack;
                $export[] =  $data->threshold_limit;
                $export[] =  $data->remarks;
                if ($data->approve_status == STATUS_OHC_EHS_HEAD_APPROVAL_PENDING) {
                    $export[] = 'Stock Requested ';
                } elseif ($data->approve_status == STATUS_OHC_EHS_HEAD_APPROVED) {
                    $export[] = 'EHS Head approval Pending';
                } else {
                    $export[] = removeUnderScore(getStatus($data->approve_status));
                }
                if ($data->approve_status == STATUS_OHC_EHS_HEAD_APPROVAL_PENDING) {
                    $export[] = 'EHS Head approval Pending';
                } elseif ($data->approve_status == STATUS_OHC_EHS_HEAD_APPROVED) {
                    $export[] = 'EHS Head Approved';
                } else {
                    $export[] = removeUnderScore(getStatus($data->approve_status));
                }
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('medicine .xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/medicine/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->medicine->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __("ohc_management.medicine_name"),
                __("ohc_management.pack"),
                __("ohc_management.threshold_limit"),
                __("ohc_management.remarks"),
                __("common.from_status"),
                __("common.to_status"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Medicine Details",
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

            $view = view('ohcmanagement.master.medicine.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Medicine .pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/medicine/list'));
        }
    }

    public function Import(Request $request)
    {
        $data = array();
        return view('ohcmanagement.master.medicine.import', $data);
    }
    public function ImportSubmit(Request $request)
    {
        try {
            $file = $request->file('file_upload');

            $rules = [
                'file_upload' => 'required',
            ];
            $messages = [
                'file_upload.required' => 'Please upload a file',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }


            if ($file != null) {

                $uploadpath = 'public/uploads/ohc/medicine';

                $folderPath = public_path('uploads/ims/incident_type');

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
                    'upload_type' => 31,
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
                    'user' => Auth::user(),
                    "user_id" => $user_id,
                    "log_id" => $insert_id,
                    "path" => $path,
                ];

                dispatch(new ImportMedicine($details));
                // dispatch((new ImportMedicine($details))->onQueue('medicine'));
            }

            $insert_data['log_id'] = $insert_id;
            $insert_data['Uploded_by'] = Auth::user()->toArray();

            Session::flash('success', __('Incident Type uploaded sucessfully'));
            return redirect(admin_url('incident/type-master/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('Company upload failed'));
            return redirect(admin_url('incident/type-master/list'));
        }
    }

    public function DownloadSample(Request $request)
    {

        $filedetails =  exportsamplefile('medicine');
        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;

        //return Response::download($filePath, $customFileName);
        return redirect(url($filePath));
    }
}
