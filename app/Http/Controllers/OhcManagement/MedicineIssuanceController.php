<?php

namespace App\Http\Controllers\OhcManagement;

use App\Http\Controllers\Controller;
use App\Jobs\Ohc\ImportIssuancejob;
use App\Mail\Ohc\MedicineRequisitionEmail;
use App\Models\Master\Department;
use App\Models\Master\Employee;
use App\Models\Master\Unit;
use App\Models\OhcManagement\Master\Medicine;
use App\Models\OhcManagement\Master\Vendor;
use App\Models\OhcManagement\UserMedicineIssuance;
use App\Models\OhcManagement\MedicineIssuance;
use App\Models\OhcManagement\MedicineReceiving;
use App\Models\OhcManagement\MedicineRequisition;
use App\Models\OhcManagement\UserMedicineRequisition;
use App\Models\UploadLog;
use Illuminate\Support\Str;
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
use App\Models\User;
use App\Models\OhcManagement\Report\Inventory;
use App\Models\OhcManagement\Status\CreatorLog;
use App\Models\OhcManagement\Status\MedicineLog;
use Illuminate\Support\Facades\File;

class MedicineIssuanceController extends Controller
{
    private $medicine;
    private $vendor;
    private $user_medicine_issuance;
    private $unit;
    private $department;
    private $medicine_issuance;
    private $medicine_receiving;
    private $medicine_stock;
    private $ohc_status;
    private $user_medicine_requisition;
    private $medicine_requisition;
    private $user;
    private $inventory;
    private $creatorlog;
    private $medicinelog;
    private $uploadlog;
    public function __construct()
    {
        $this->medicine = new Medicine();
        $this->vendor = new Vendor();
        $this->uploadlog = new UploadLog();
        $this->user_medicine_issuance = new UserMedicineIssuance();
        $this->medicine_issuance = new MedicineIssuance();
        $this->medicine_receiving = new MedicineReceiving();
        $this->medicine_stock = new MedicineStock();
        $this->ohc_status = new OhcStatuslog();
        $this->user_medicine_requisition = new UserMedicineRequisition();
        $this->medicine_requisition = new MedicineRequisition();
        $this->unit = new Unit();
        $this->department = new Department();
        $this->user = new User();
        $this->inventory = new Inventory();
        $this->creatorlog = new CreatorLog();
        $this->medicinelog = new MedicineLog();
    }
    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->user_medicine_issuance->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->editColumn('unit_id', function ($row) {
                            return getUnitname($row->unit_id);
                        })
                        ->editColumn('department_id', function ($row) {
                            return getDepartment($row->department_id);
                        })

                        ->editColumn('issue_date', function ($row) {
                            return displaydateformat($row->issue_date);
                        })
                        ->editColumn('action', function ($row) {
                            $btn = '';
                            // if (CheckUserPermission('view')) {
                            $btn .= '<a href="' . admin_url('ohc/medicine-issuance/view/' . encryptId($row->id)) . '" class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            // if (CheckUserPermission('edit')) {
                            $btn .= '<a href="' . admin_url('ohc/medicine-issuance/edit/' . encryptId($row->id)) . '" class="" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a> ';
                            // }
                            // $btn .= '<a href="javascript:void(0);" data-id="' . encryptId($row->id) . '" class="recordDelete" title="Delete"><i class="fa-solid fa-trash text-danger"></i></a> ';


                            return $btn;
                        })

                        ->rawColumns(['action', 'request_date'])
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

        return view('ohcmanagement.medicine_issuance.list', $data);
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

            return view('ohcmanagement.medicine_issuance.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('common.message_error'));
            return redirect(admin_url('ohc/medicine-issuance/list'));
        }
    }
    public function issue(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $unit = $this->unit->getunit();
            $departmentList = $this->department->getdepartment();

            $medicine = $this->inventory->getstockdata();
            $user_medicine_requisition = $this->user_medicine_requisition->selectOne($id);
            $medicine_requisition = $this->medicine_requisition->selectOne($id);

            $data = array(
                'medicine' => $medicine,
                'unit' => $unit,
                'departmentList' => $departmentList,
                'user_medicine_requisition' => $user_medicine_requisition,
                'medicine_requisition' => $medicine_requisition
            );

            return view('ohcmanagement.medicine_issuance.issue', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('common.message_error'));
            return redirect(admin_url('ohc/medicine-issuance/list'));
        }
    }
    public function store(Request $request)
    {
        try {
            $rules = [
                'unit_id' => 'required',
                'department_id' => 'required',
                'issue_date' => 'required',

            ];
            $messages = [
                'department_id.required' => 'Please select a Deparment.',
                'unit_id.required' => 'Please select a unit.',
                'issue_date.required' => 'Please select the Issued date.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

                $user_medicine_issuance = $this->user_medicine_issuance->store();
                $medicine_issuance = $this->medicine_issuance->store($user_medicine_issuance);
                $user_medicine_issuance_unit = $this->creatorlog->store($user_medicine_issuance);
                $medicine_issuance_unit = $this->medicinelog->store($user_medicine_issuance_unit);

                foreach ($medicine_issuance as $medicine) {

                    $medicine_id = $medicine->medicine_id;
                    $unitId = $user_medicine_issuance->unit_id;
                    $issuedQuantity = $medicine->quantity;
                    $data = $this->inventory
                        ->where('medicine_id', $medicine_id)
                        ->where('unit_id', $unitId)
                        ->first();
                    $this->inventory
                        ->where('medicine_id', $medicine_id)
                        ->where('unit_id', 1)
                        ->decrement('balance', $issuedQuantity);
                    $this->inventory
                        ->where('medicine_id', $medicine_id)
                        ->where('unit_id', 1)
                        ->increment('total_issue', $issuedQuantity);

                    $this->inventory
                        ->where('medicine_id', $medicine_id)
                        ->where('unit_id',  $unitId)
                        ->increment('balance', $issuedQuantity);
                    $this->inventory
                        ->where('medicine_id', $medicine_id)
                        ->where('unit_id',  $unitId)
                        ->increment('total_received', $issuedQuantity);
                }

                // Notification and Email
                $id =  $user_medicine_issuance->id;
                $data = $this->user_medicine_issuance->selectOne($user_medicine_issuance->id);
                $unitId = $data->unit_id;
                $departmentId = $data->department_id;
                $mailsubject = 'Medicine Issuing to Other Unit';
                $user_role = ROLE_EHS_OFFICER;



                $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->pluck('id')->toArray();
                $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();


                if (count($users) > 0) {
                    foreach ($users as $user) {

                        $email_id = $user->email;

                        if ($email_id != '' || $email_id != null) {
                            $details = $this->user_medicine_issuance->selectOne($id);
                            $medicineDetails = $this->medicine_issuance->selectOne($id);
                            $emailDetails = $details->toArray();
                            $emailDetails['name'] = $user->name;
                            $emailDetails['email_id'] = $email_id;
                            $emailDetails['mail_subject'] = $mailsubject;

                            Mail::to($emailDetails['email_id'])->queue(new MedicineRequisitionEmail($emailDetails, $medicineDetails));
                        }
                    }
                }


                /**
                 * Send Web notification
                 */

                $notificationData = array(
                    'notification_type' => 4,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => "The  Medicine Was issued By " . getUsername($data->created_by),
                        'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('ohc/medicine-issuance/list'),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);


                Session::flash('success', __('common.created_msg'));
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', __('common.message_error'));
            }

            return redirect(admin_url('ohc/medicine-issuance/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', __('common.message_error'));
            return redirect(admin_url('ohc/medicine-issuance/list'));
        }
    }

    public function issuestore(Request $request)
    {
        try {

            $rules = [
                'unit_id' => 'required',
                'department_id' => 'required',
                'issue_date' => 'required',

            ];
            $messages = [
                'department_id.required' => 'Please select a Deparment.',
                'unit_id.required' => 'Please select a unit.',
                'issue_date.required' => 'Please select the Issued date.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

                $id = decryptId($request->id);
                $user_medicine_requisition = $this->user_medicine_requisition->selectOne($id);
                // issue store
                $user_medicine_issuance = $this->user_medicine_issuance->issuestore($user_medicine_requisition);

                $medicineissuance =  $user_medicine_issuance->id;

                $issuance =  $this->medicine_issuance->store($user_medicine_issuance);
                $user_medicine_issuance_unit = $this->creatorlog->store($user_medicine_issuance);
                $medicine_issuance_unit = $this->medicinelog->store($user_medicine_issuance_unit);


                $medicinedata =  $this->medicine_issuance->where('reference_id', $medicineissuance)->get();
                // requisition status update

                $this->user_medicine_requisition->updatestatus($id);
                $this->ohc_status->updatecloseStatus($id);

                // stock update
                $this->inventory->issuestockupdate($user_medicine_issuance, $medicinedata);
                $this->inventory->otherunit($user_medicine_requisition, $medicinedata);
                // notification
                $mailsubject = 'Medicine Issuing to the Unit';

                $details =  $this->user_medicine_requisition->selectOne($id);
                $createdby = $details->created_by;
                $userEmail = $this->user->where('id', $createdby)->pluck('email');

                if ($userEmail != '' || $userEmail != null) {
                    $Details = $this->user_medicine_requisition->selectOne($id);

                    $medicineDetails = $this->medicine_requisition->selectOne($id);
                    $emailDetails = $Details->toArray();
                    $emailDetails['mail_subject'] = "Medicine Issued";
                    Mail::to($userEmail)->queue(new MedicineRequisitionEmail($emailDetails, $medicineDetails));
                }


                $notificationData = array(
                    'notification_type' => 4,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Requested Medicine was issued By ' . getUsername($user_medicine_issuance->created_by),
                        'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $user_medicine_issuance->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('ohc/medicine-requisition/list'),
                    'assigned_user' => $createdby,
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);


                Session::flash('success', __('common.created_msg'));
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', __('common.message_error'));
            }

            return redirect(admin_url('ohc/medicine-issuance/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', __('common.message_error'));
            return redirect(admin_url('ohc/medicine-issuance/list'));
        }
    }
    public function edit(Request $request)
    {
        try {
            $id = decryptId($request->id);


            $user_medicine_issuance = $this->user_medicine_issuance->selectOne($id);
            $departmentList = $this->department->getdepartment();
            $medicine_issuance = $this->medicine_issuance->selectOne($id);
            $departmentList = $this->department->getdepartment();
            $unit = $this->unit->getuserunit();
            $medicine =  $this->inventory->getstockdata();
            $data = array(
                'medicine' => $medicine,
                'unit' => $unit,
                'departmentList' => $departmentList,
                'user_medicine_issuance' => $user_medicine_issuance,
                'medicine_issuance' => $medicine_issuance
            );

            return view('ohcmanagement.medicine_issuance.edit', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('common.message_error'));
            return redirect(admin_url('ohc/medicine-issuance/list'));
        }
    }

    public function update(Request $request)
    {
        try {
            $id = decryptId($request->id);


            $rules = [
                'unit_id' => 'required',
                'department_id' => 'required',
                'issue_date' => 'required',
            ];
            $messages = [
                'department_id.required' => 'Please select a Department.',
                'unit_id.required' => 'Please select a unit.',
                'issue_date.required' => 'Please select the Issued date.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

                $user_medicine_issuance = $this->user_medicine_issuance->selectOne($id);
                $medicine_issuance = $this->medicine_issuance->selectOne($id);
                $creatorlog =  $this->creatorlog->selectOne($id);
                $deletedPages = json_decode($request->deletedPage, true);
                $deletedMedicine = json_decode($request->deletedMedicine, true);


                if (!empty($deletedPages)) {
                    foreach ($deletedPages as $encryptedId) {
                        $medicineIds = decryptId($encryptedId);

                        $medicine_issuance = $this->medicine_issuance->firstdata($medicineIds);

                        $this->inventory->where('unit_id', 1)
                            ->where('medicine_id', $medicine_issuance->medicine_id)
                            ->decrement('total_issue', $medicine_issuance->quantity);

                        $this->inventory->where('unit_id', 1)
                            ->where('medicine_id', $medicine_issuance->medicine_id)
                            ->increment('balance', $medicine_issuance->quantity);

                        $this->inventory->where('unit_id', $user_medicine_issuance->unit_id)
                            ->where('medicine_id', $medicine_issuance->medicine_id)
                            ->decrement('total_received', $medicine_issuance->quantity);

                        $this->inventory->where('unit_id', $user_medicine_issuance->unit_id)
                            ->where('medicine_id', $medicine_issuance->medicine_id)
                            ->decrement('balance', $medicine_issuance->quantity);

                        $update_data = $this->medicine_issuance
                            ->where('id', $medicineIds)->where('reference_id', $id)
                            ->update([
                                'status' => 0,
                                'trash'  => 'Yes'
                            ]);
                    }
                }

                if (!empty($deletedMedicine)) {

                    foreach ($deletedMedicine as $encryptedMedicineId) {
                        $medicineDatas = ($encryptedMedicineId);

                        $update_data = $this->medicinelog
                            ->where('medicine_id', $medicineDatas)->where('creator_id', $creatorlog->id)
                            ->update([
                                'status' => 0,
                                'trash'  => 'Yes'
                            ]);
                    }
                }
                foreach ($request->medicine_id as $index => $medicine_id) {
                    $medicine_record = $medicine_issuance->where('medicine_id', $medicine_id)->first();


                    if ($medicine_record) {
                        $oldquantity = $medicine_record->quantity;
                        $newquantity = $request->quantity[$index];

                        if ($oldquantity > $newquantity) {
                            $difference = $oldquantity - $newquantity;

                            $this->inventory
                                ->where('medicine_id', $medicine_id)
                                ->where('unit_id', $user_medicine_issuance->unit_id)
                                ->decrement('total_received', $difference);

                            $this->inventory
                                ->where('medicine_id', $medicine_id)
                                ->where('unit_id', $user_medicine_issuance->unit_id)
                                ->decrement('balance', $difference);
                            $this->inventory
                                ->where('medicine_id', $medicine_id)
                                ->where('unit_id', 1)
                                ->decrement('total_issue', $difference);

                            $this->inventory
                                ->where('medicine_id', $medicine_id)
                                ->where('unit_id', 1)
                                ->increament('balance', $difference);
                        } elseif ($oldquantity < $newquantity) {
                            $difference = $newquantity - $oldquantity;

                            $this->inventory
                                ->where('medicine_id', $medicine_id)
                                ->where('unit_id', $user_medicine_issuance->unit_id)
                                ->increment('total_received', $difference);

                            $this->inventory
                                ->where('medicine_id', $medicine_id)
                                ->where('unit_id', $user_medicine_issuance->unit_id)
                                ->increment('balance', $difference);
                            $this->inventory
                                ->where('medicine_id', $medicine_id)
                                ->where('unit_id', 1)
                                ->increment('total_issue', $difference);

                            $this->inventory
                                ->where('medicine_id', $medicine_id)
                                ->where('unit_id', 1)
                                ->decrement('balance', $difference);
                        }
                    } else {
                        foreach ($request->medicine_id as $index =>  $encrypt_medicine_id) {
                            $medicine_id = ($encrypt_medicine_id);

                            $unitId = $user_medicine_issuance->unit_id;
                            $issuedQuantity =  $request->quantity[$index];
                            $data = $this->inventory
                                ->where('medicine_id', $medicine_id)
                                ->where('unit_id', $unitId)
                                ->first();
                            $this->inventory
                                ->where('medicine_id', $medicine_id)
                                ->where('unit_id', 1)
                                ->decrement('balance', $issuedQuantity);
                            $this->inventory
                                ->where('medicine_id', $medicine_id)
                                ->where('unit_id', 1)
                                ->increment('total_issue', $issuedQuantity);
                            $this->inventory
                                ->where('medicine_id', $medicine_id)
                                ->where('unit_id',  $unitId)
                                ->increment('total_received', $issuedQuantity);
                            $this->inventory
                                ->where('medicine_id', $medicine_id)
                                ->where('unit_id',  $unitId)
                                ->increment('balance', $issuedQuantity);
                        }
                    }
                }

                $this->user_medicine_issuance->updates($id);
                $updatedMedicines = $this->medicine_issuance->updates($id);

                $this->creatorlog->updates($id);
                $creatorlog =  $this->creatorlog->selectOne($id);

                $updatedMedicines = $this->medicinelog->updates($creatorlog);
                $mailsubject = 'Medicine Issuing to Other Unit';
                $user_role = ROLE_EHS_OFFICER;

                // Fetch all EHS Officers for notification
                $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();
                $userids = $users->pluck('id')->toArray();

                // Send email notifications to all users
                foreach ($users as $user) {
                    if (!empty($user->email)) {
                        $details = $this->user_medicine_issuance->selectOne($id);
                        $medicineDetails = $this->medicine_issuance->selectOne($id);
                        $emailDetails = $details->toArray();
                        $emailDetails['name'] = $user->name;
                        $emailDetails['email_id'] = $user->email;
                        $emailDetails['mail_subject'] = $mailsubject;

                        Mail::to($user->email)->queue(new MedicineRequisitionEmail($emailDetails, $medicineDetails));
                    }
                }


                $notificationData = [
                    'notification_type' => 4,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode([
                        'title' => $mailsubject,
                        'message' => "The requested Medicine was issued by " . getUsername($user_medicine_issuance->created_by),
                        'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $id,
                        'module' => 1,
                    ]),
                    'web_link' => admin_url('ohc/medicine-issuance/list'),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                ];
                notificationSave($notificationData);

                Session::flash('success', __('common.updated_msg'));
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', __('common.message_error'));
                return redirect()->back();
            }

            return redirect(admin_url('ohc/medicine-issuance/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('common.message_error'));
            return redirect(admin_url('ohc/medicine-issuance/list'));
        }
    }


    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $user_medicine_issuance = $this->user_medicine_issuance->selectOne($id);
                $medicine_issuance = $this->medicine_issuance->selectOne($id);
            }
            $unit = $this->unit->getunit();
            $data = array(
                'user_medicine_issuance' => $user_medicine_issuance,
                'medicine_issuance' => $medicine_issuance,

            );
            return view('ohcmanagement.medicine_issuance.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('common.message_error'));
            return redirect(admin_url('ohc/medicine-issuance/list'));
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


    public function import()
    {
        return view('ohcmanagement.medicine_issuance.import');
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
                    'upload_type' => 18,
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
                $medicnieissuance = $this->user_medicine_issuance->store();


                // dispatch(new ImportIssuancejob($details, $medicnieissuance));
                dispatch((new ImportIssuancejob($details, $medicnieissuance))->onQueue('medicine_issuance'));
            }

            $insert_data['log_id'] = $insert_id;
            $insert_data['Uploded_by'] = Auth::user()->toArray();

            Session::flash('success', __('ohc_management.file_upload_msg'));
            return redirect(admin_url('ohc/medicine-issuance/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('ohc_management.file_upload_error_msg'));
            return redirect(admin_url('ohc/medicine-issuance/list'));
        }
    }
    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->user_medicine_issuance->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __("common.unit"),
                __("common.department"),
                __("ohc_management.issued_date"),
                __("common.created_by"),
                __("common.created_date"),

            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  getUnitname($data->unit_id);
                $export[] =  getDepartment($data->department_id);
                $export[] =  Displaydateformat($data->issue_date);
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Medicine Issuance.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', __('common.message_error'));
            return redirect(admin_url('ohc/medicine-issuance/list'));
        }
    }




    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->user_medicine_issuance->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __("common.unit"),
                __("common.department"),
                __("ohc_management.issued_date"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Medicine Issuance",
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

            $view = view('ohcmanagement.medicine_issuance.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Medicine Issuance.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', __('common.message_error'));
            return redirect(admin_url('ohc/medicine-issuance/list'));
        }
    }

    public function medicineDetails(Request $request, $unit_id)
    {
        $unit_id = decryptId($unit_id);
        $medicineData = $this->medicine_stock->unitwisemedicineData($unit_id);


        return response()->json($medicineData->map(function ($medicine) {
            return [
                'id' => $medicine->id,
                'name' => $medicine->medicine_id,
            ];
        }));
    }

    public function editquantity(Request $request, $quantity_id)
    {
        $id = ($quantity_id);

        $availableQuantity = $this->inventory->getAvailableQuantity($id);

        return response()->json(
            ['available_quantity' => $availableQuantity->balance]
        );
    }

    public function DownloadSample(Request $request)
    {

        $filedetails =  exportsamplefile('medicineissuance');

        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;

        //return Response::download($filePath, $customFileName);
        return redirect(url($filePath));
    }
}
