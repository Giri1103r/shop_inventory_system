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
use App\Models\OhcManagement\MedicalFitnessCertificate;
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


class MedicalFitnessCertificateController extends Controller
{

    private $medical_fitness_certificate;


    public function __construct()
    {

        $this->medical_fitness_certificate = new MedicalFitnessCertificate();

    }

    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->medical_fitness_certificate->list();
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


                            if ($row->approve_status == STATUS_OHC_MEDICAL_DOCTOR_APPROVAL_PENDING) {
                                $text = "<span class='badge bg-info' style='font-size: 1.0em;'>Doctor Approval Pending</span>";
                            } else if ($row->approve_status == STATUS_OHC_MEDICAL_DOCTOR_APPROVED) {
                                $text = "<span class='badge bg-success' style='font-size: 1.0em;'>Doctor Approved</span>";
                            } else if ($row->approve_status == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVAL_PENDING) {
                                $text = "<span class='badge bg-info' style='font-size: 1.0em;'>EHS Head Approval Pending</span>";
                            } else if ($row->approve_status == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVED) {
                                $text = "<span class='badge bg-success' style='font-size: 1.0em;'>EHS Head Approved</span>";
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
                            // if (CheckUserPermission('view')) {
                            $btn .= '<a href="' . admin_url('ohc/medical-fitness/view/' . encryptId($row->id)) . '" class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            if (CheckUserPermission('edit') && $row->approve_status == STATUS_OHC_PARAMEDICS_APPROVAL_PENDING) {
                            $btn .= '<a href="' . admin_url('ohc/medical-fitness/edit/' . encryptId($row->id)) . '" class="" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a> ';
                            }
                            if ((checkUserRole(ROLE_SUPERADMIN) && $row->approve_status == STATUS_OHC_PARAMEDICS_APPROVAL_PENDING) || (checkUserRole(ROLE_PARAMEDICS) && $row->approve_status == STATUS_OHC_PARAMEDICS_APPROVAL_PENDING)) {
                                $btn .= '<a href="' . admin_url('ohc/medical-fitness/approval/view/' . encryptId($row->id)) . '" class="" title="Action"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }


                            $btn .= '<a href="' . admin_url('ohc/medical-fitness/generalpdf/' . encryptId($row->id)) . '" class="" title="PDF"> <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i></a> ';
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


        $data = array(

        );

        return view('ohcmanagement.medical_fitness_certificate.list', $data);
    }

    public function add()
    {
        try {

            return view('ohcmanagement.medical_fitness_certificate.add');
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function store(Request $request)
    {
        try {

            $rules = [
                'emp_id' => 'required',
                'emp_name' => 'required',
                'date' => 'required',
                'remarks' => 'required',


            ];


            $messages = [
                'emp_id.required' => 'Employee Id is required',
                'emp_name.required' => 'Emp Name is required',
                'date.required' => 'Date is required',
                'remarks.required' => 'remarks is required',

            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {


                $medicinefitness =  $this->medical_fitness_certificate->store();

                // $this->ohc_status->medicinestockstore($id);
                // $mailsubject = 'Medicine Request for the Stock';
                // $user_role = ROLE_EHS_OFFICER;


                // $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->pluck('id')->toArray();
                // $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();

                // if (count($users) > 0) {

                //     foreach ($users as $user) {

                //         $email_id = $user->email;

                //         if ($email_id != '' || $email_id != null) {
                //             $data = $this->medicine_receiving->selectOne($id);
                //             $permitrray  = $data->toArray();

                //             $data['name'] = $user->name;
                //             $data['email_id'] =  $email_id;
                //             $data['mail_subject'] = $mailsubject;

                //             Mail::to($data['email_id'])->queue(new MedicineReceivingRequestEmail($data));
                //         }
                //     }
                // }


                // /**
                //  * Send Web notification
                //  */

                // $notificationData = array(
                //     'notification_type' => 4,
                //     'module_type' => 1,
                //     'notification_message' => $mailsubject,
                //     'mobile_notification' => json_encode(array(
                //         'title' => $mailsubject,
                //         'message' => getMedicinename($data->medicine_id) . 'Has requested the medicine for the stock by' . getUsername($data->created_by),
                //         'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                //         'id' => $data->id,
                //         'module' => 1,
                //     )),
                //     'web_link' =>  admin_url('ohc/medical-fitness/medicineapproval/view/' . encryptId($data->id)),
                //     'assigned_user' => array_to_string($userids),
                //     'created_by' => Auth::id(),
                // );
                // notificationSave($notificationData);

                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/medical-fitness/list'));
        } catch (Exception $ex) {

            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medical-fitness/ist'));
        }
    }

    // public function Edit(Request $request)
    // {
    //     try {
    //         $id = decryptId($request->id);

    //         $unit = $this->unit->getunit();
    //         $medicine_receiving = $this->medicine_receiving->find($id);
    //         $vendor = $this->vendor->getVendordata();
    //         $pack = $this->medicine->getMedicineData();
    //         $medicineStock  = $this->inventory->getmedicinedata();

    //         $hsn = $this->medicine->where('id',   $medicine_receiving->medicine_id)->select('medicine', 'hsn', 'pack')->first();


    //         $existingMedicineIds = $this->medicine_receiving
    //         ->where('status', 1)
    //         ->pluck('medicine_id')
    //         ->toArray();
    //         $data = [
    //             'medicineStock' => $medicineStock,
    //             'vendor' => $vendor,
    //             'medicine_receiving' => $medicine_receiving,
    //             'hsn' => $hsn,
    //             'unitList'=>$unit,
    //             'pack'=>$pack


    //         ];

    //         return view('ohcmanagement.medicine_receiving.edit', $data);
    //     } catch (Exception $ex) {
    //         report($ex);
    //         Session::flash('error', 'Something went wrong, Please try after sometimes!');
    //         return redirect(admin_url('ohc/medical-fitness/list'));
    //     }
    // }
    // public function update(Request $request)
    // {
    //     try {
    //         $id = decryptId($request->id);

    //         $rules = [
    //             'medicine_id' => 'required',
    //             'vendor_id' => 'required',
    //             'quantity' => 'required',
    //             'batch_number' => 'required',
    //             'expire_date' => 'required',
    //             // 'hsn_id' => 'required',
    //             'rate' => 'required',
    //             'pack_id' => 'required',

    //         ];


    //         $messages = [
    //             'medicine_id.required' => 'Medicine Name is required',
    //             'vendor_id.required' => 'Vendor Name is required',
    //             'quantity.required' => 'Quantity is required',
    //             'batch_number.required' => 'Quantity is required',
    //             'expire_date.required' => 'Expire Date is required',
    //             // 'hsn_id.required' => 'HSN Numner is required',
    //             'rate.required' => 'Rate is required',
    //             'pack_id.required => Pack Details is required',
    //         ];

    //         $validator = Validator::make($request->all(), $rules, $messages);
    //         if ($validator->fails()) {
    //             dd($validator->errors());
    //             return redirect()->back()->withErrors($validator)->withInput();
    //         }

    //         try {

    //             $hsn = $this->medicine->where('hsn',$request->hsn_display)->first();
    //             $pack = $this->medicine->where('pack',$request->pack_display)->first();


    //             $this->medicine_receiving->updates($id,$hsn, $pack);

    //             $mailsubject = 'Medicine Request for the Stock';
    //             $user_role = ROLE_EHS_OFFICER;


    //             $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->pluck('id')->toArray();
    //             $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();

    //             if (count($users) > 0) {

    //                 foreach ($users as $user) {

    //                     $email_id = $user->email;

    //                     if ($email_id != '' || $email_id != null) {
    //                         $data = $this->medicine_receiving->selectOne($id);
    //                         $permitrray  = $data->toArray();

    //                         $data['name'] = $user->name;
    //                         $data['email_id'] =  $email_id;
    //                         $data['mail_subject'] = $mailsubject;

    //                         Mail::to($data['email_id'])->queue(new MedicineReceivingRequestEmail($data));
    //                     }
    //                 }
    //             }


    //             /**
    //              * Send Web notification
    //              */

    //             $notificationData = array(
    //                 'notification_type' => 4,
    //                 'module_type' => 1,
    //                 'notification_message' => $mailsubject,
    //                 'mobile_notification' => json_encode(array(
    //                     'title' => $mailsubject,
    //                     'message' => getMedicinename($data->medicine_id) . 'Has requested the medicine for the stock' . getUsername($data->created_by),
    //                     'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
    //                     'id' => $data->id,
    //                     'module' => 1,
    //                 )),
    //                 'web_link' =>  admin_url('ohc/medical-fitness/approval/view/' . encryptId($data->id)),
    //                 'assigned_user' => array_to_string($userids),
    //                 'created_by' => Auth::id(),
    //             );
    //             notificationSave($notificationData);
    //             Session::flash('success', 'Your data has been updated successfully!');
    //         } catch (Exception $ex) {
    //             report($ex);
    //             Session::flash('error', 'Something went wrong, Please try after sometimes!');
    //         }

    //         return redirect(admin_url('ohc/medical-fitness/list'));
    //     } catch (Exception $ex) {

    //         report($ex);
    //         Session::flash('error', 'Something went wrong, Please try after sometimes!');
    //         return redirect(admin_url('ohc/medical-fitness/list'));
    //     }
    // }
}
