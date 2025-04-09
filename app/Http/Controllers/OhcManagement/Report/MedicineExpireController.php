<?php

namespace App\Http\Controllers\OhcManagement\Report;

use App\Http\Controllers\Controller;
use App\Mail\Ohc\MedicineExpireEmail;
use App\Mail\Ohc\MedicineReceivingRequestEmail;
use App\Models\Master\Department;
use App\Models\Master\Employee;
use App\Models\Master\Unit;
use App\Models\OhcManagement\ExpireMedicine;
use App\Models\OhcManagement\Master\Medicine;
use App\Models\OhcManagement\Master\Vendor;
use App\Models\OhcManagement\MedicineReceiving;
use App\Models\OhcManagement\MedicineStock;
use App\Models\OhcManagement\OhcStatuslog;
use App\Models\OhcManagement\Report\Inventory;
use App\Models\OhcManagement\Status\ReceivingStatus;
use App\Models\OhcManagement\UserDiscard;
use App\Models\UploadLog;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Spatie\Backtrace\Arguments\ReducedArgument\ReducedArgument;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;

class MedicineExpireController extends Controller
{
    private $medicine;
    private $vendor;
    private $expire_medicine;
    private $medicine_stock;
    private $ohc_status;
    private $unit;
    private $user;
    private $inventory;
    private $status;
    private $discard;

    public function __construct()
    {
        $this->medicine = new Medicine();
        $this->vendor = new Vendor();
        $this->expire_medicine = new ExpireMedicine();
        $this->medicine_stock = new MedicineStock();
        $this->ohc_status = new OhcStatuslog();
        $this->user = new User();
        $this->inventory = new Inventory();
        $this->unit = new Unit();
        $this->status = new ReceivingStatus();
        $this->discard = new UserDiscard();
    }
    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->expire_medicine->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->editColumn('medicine', function ($row) {
                            return getMedicinename($row->medicine);
                        })

                        ->addColumn('row_class', function ($row) {
                            $expireDate = Carbon::parse($row->expire_date);
                            $today = Carbon::today();
                            $oneMonthAhead = $today->copy()->addMonth();

                            if ($expireDate->lessThanOrEqualTo($today)) {
                                return '#FF0000';
                            } elseif ($expireDate->lessThanOrEqualTo($oneMonthAhead)) {
                                return '#FFA500';
                            } else {
                                return '#FFFFFF';
                            }
                        })
                        ->addColumn('approve_status', function ($row) {
                            $text = '';
                            switch ($row->approve_status) {
                                case OHC_DISCARD_EHS_APPROVED:
                                    $text = "<span class='badge bg-success rounded' style='font-size: 1.0em;'>EHS Head Approved</span>";
                                    break;
                                case OHC_DISCARD_EHS_APPROVAL_PENDING:
                                    $text = "<span class='badge bg-primary rounded' style='font-size: 1.0em;'>EHS Head Approval Pending</span>";
                                    break;
                                default:
                                    $text = "<span class='badge rounded-pill text-bg-warning'>Unknown</span>";
                            }
                            return $text;
                        })
                        ->rawColumns(['action', 'expire_date', 'approve_status', 'hsn_id', 'pack', 'row_class'])

                        ->editColumn('expire_date', function ($row) {
                            return displaydateformat($row->expire_date);
                        })
                        ->editColumn('action', function ($row) {
                            $btn = '';
                            $expireDate = Carbon::parse($row->expire_date);
                            $today = Carbon::today();
                            $oneMonthAhead = $today->copy()->addMonth();


                            if (CheckUserPermission('view')) {
                                if ($expireDate->lessThanOrEqualTo($today)) {
                                    $btn .= '<a href="' . admin_url('ohc/medicine-expire-report/view/' . encryptId($row->id)) . '" class="me-1" title="View">
                                    <i class="fa-solid fa-eye" style="color:white;"></i>
                                 </a>';
                                } else if ($expireDate->lessThanOrEqualTo($oneMonthAhead)) {
                                    $btn .= '<a href="' . admin_url('ohc/medicine-expire-report/view/' . encryptId($row->id)) . '" class="me-1" title="View">
                                    <i class="fa-solid fa-eye" style="color:black;"></i>
                                 </a>';
                                } else {
                                    $btn .= '<a href="' . admin_url('ohc/medicine-expire-report/view/' . encryptId($row->id)) . '" class="me-1" title="View">
                                    <i class="fa-solid fa-eye" ></i>
                                 </a>';
                                }
                            }



                            if ($row->approve_status != OHC_DISCARD_EHS_APPROVED) {
                                if ($expireDate->lessThanOrEqualTo($today)) {

                                    $btn .= '<a href="javascript:void(0);" data-id="' . encryptId($row->id) . '" data-balance="' . $row->balance .  '" data-medicine="' . $row->medicine . '" data-unit="' . $row->unit .  '" class="discard me-1" title="Discard">
                                                <i class="fa-solid fa-ban" style="color:white;"></i>
                                             </a>';
                                } elseif ($expireDate->lessThanOrEqualTo($oneMonthAhead)) {

                                    $btn .= '<a href="javascript:void(0);" data-id="' . encryptId($row->id) . '" data-balance="' . $row->balance .  '" data-medicine="' . $row->medicine . '" data-unit="' . $row->unit .  '" class="discard me-1" title="Discard">
                                                <i class="fa-solid fa-ban" style="color:black;"></i>
                                             </a>';
                                }
                            }

                            if ($row->approve_status != OHC_DISCARD_EHS_APPROVED && checkUserRole(ROLE_EHS_HEAD)) {
                                if ($expireDate->lessThanOrEqualTo($today)) {
                                    $btn .= '<a href="javascript:void(0);" data-id="' . encryptId($row->id) . '" class="close me-1" title="Close" >
                                    <i class="fa fa-window-close" aria-hidden="true" style="color:white;"></i>
                                 </a>';
                                } elseif ($expireDate->lessThanOrEqualTo($oneMonthAhead)) {
                                    $btn .= '<a href="javascript:void(0);" data-id="' . encryptId($row->id) . '" class="close me-1" title="Close" >
                                    <i class="fa fa-window-close" aria-hidden="true" style="color:back;"></i>
                                 </a>';
                                }else{
                                    $btn .= '<a href="javascript:void(0);" data-id="' . encryptId($row->id) . '" class="close me-1" title="Close" >
                                    <i class="fa fa-window-close" aria-hidden="true" ></i>
                                 </a>';
                                }
                            }

                            return $btn;
                        })

                        ->rawColumns(['action', 'expire_date', 'approve_status', 'row_class', 'pack'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);

                    return response()->json($datatables->getData());
                } catch (Exception $ex) {
                    report($ex);
                    return response()->json(['status' => 'error', 'msg' => __('ohc.please_try_after_some_time')], 406);
                }
            }
        }

        $medicine = $this->medicine->getMedicineData();
        $vendor = $this->vendor->getVendordata();
        $unit = $this->unit->getunit();
        $data = [
            'medicine' => $medicine,
            'vendor' => $vendor,
            'unit' => $unit,
        ];

        return view('ohcmanagement.report.expiremedicine.list', $data);
    }


    public function approval(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $medicine = $this->discard->selectOne($id);

                $unit = $this->unit->getunit();
                $data = array(
                    'medicine' => $medicine,
                );
            }

            return view('ohcmanagement.report.expiremedicine.approve', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medicine-expire-report/list'));
        }
    }
    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $medicine = $this->expire_medicine->selectOne($id);
                $logData = $this->ohc_status->where('reference_id', $id)->where('type', TYPE_OHC_MEDICINE_DISCARD)->get();
                $unit = $this->unit->getunit();

                $data = array(
                    'medicine' => $medicine,
                    'logData' => $logData,
                );
            }

            return view('ohcmanagement.report.expiremedicine.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medicine-expire-report/list'));
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
                $approveStatus = $action == 'approve' ? OHC_DISCARD_EHS_APPROVED : OHC_DISCARD_EHS_REJECTED;
                $remarks = $request->input('remarks');
                $updateData = [
                    'remarks' => $remarks,
                    'approved_by' => Auth::id(),
                    'approve_status' =>  $approveStatus,
                ];
                $details = $this->expire_medicine->selectOne($id);
                $inventory = $this->inventory
                    ->where('medicine_id', $details->medicine_id)
                    ->where('unit_id', 1)
                    ->first();

                if ($inventory) {
                    $inventory->decrement('balance',   $details->quantity);
                }
                $ehshead =  $this->expire_medicine->ehsheadapproval($id, $updateData);
                $ehsheaddiscard =  $this->discard->ehsheadapproval($id, $updateData);
                $details = $this->expire_medicine->selectOne($id);
                $ohcStatus = $this->ohc_status->medicineexpireapproval($id, $updateData);

                $createdby = $this->expire_medicine->where('id', $id)->value('created_by');
                $email = $this->user->where('id', $createdby)->value('email');


                if (!$email) {

                    return redirect()->back()->with('error', 'User email not found.');
                }

                $action = $request->action;
                if ($action == 'approve') {
                    $mailsubject =  'Medicine  Has Been Approved for the discard';

                    Mail::to($email)->queue(new MedicineExpireEmail($details));
                    $notificationData = [
                        'notification_type' => 4,
                        'module_type' => 1,
                        'notification_message' => $mailsubject,
                        'mobile_notification' => json_encode([
                            'title' => $mailsubject,
                            'message' => getMedicinename($details->medicine_id) . 'has been approved by the' . Auth::user()->name,
                            'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                            'id' => $id,
                            'module' => 1,
                        ]),
                        'web_link' => admin_url('ohc/medicine-expire-report/list'),
                        'assigned_user' => $createdby,
                        'created_by' => Auth::id(),
                    ];
                    notificationSave($notificationData);
                    $count = $this->unit->getUnitcount();
                    $this->inventory->store($details, $count);
                } else {
                    $mailsubject =  'Medicine Name Has Been Rejected';
                    $details['mail_subject'] =   $mailsubject;
                    Mail::to($email)->queue(new MedicineExpireEmail($details));
                    $notificationData = [
                        'notification_type' => 4,
                        'module_type' => 1,
                        'notification_message' => $mailsubject,
                        'mobile_notification' => json_encode([
                            'title' => $mailsubject,
                            'message' => getMedicinename($details->medicine_id) . 'has been rejected by the' . $details->approver_name,
                            'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                            'id' => $id,
                            'module' => 1,
                        ]),
                        'web_link' => admin_url('ohc/medicine-expire-report/list'),
                        'assigned_user' => $createdby,
                        'created_by' => Auth::id(),
                    ];
                    notificationSave($notificationData);
                }



                return redirect(admin_url('ohc/medicine-expire-report/list'))
                    ->with('success', 'Request has been processed successfully.');
            } catch (Exception $ex) {
                report($ex);
                return redirect(admin_url('ohc/medicine-expire-report/list'))
                    ->with('error', 'Something went wrong, Please try again later.');
            }
        } catch (Exception $ex) {
            report($ex);
            return redirect(admin_url('ohc/medicine-expire-report/list'))
                ->with('error', 'Something went wrong, Please try again later.');
        }
    }
    public function discard(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $remarks = $request->remarks;
            $quantity = $request->quantity;
            $unit_id = $request->unit_id;

            $expire_medicine = $this->expire_medicine->selectOne($id);
            $medicinediscard = $this->expire_medicine->medicinediscard($id,  $quantity,  $remarks);
            $discard_id =  $this->discard->store($id, $remarks, $expire_medicine);
            $this->ohc_status->medicineexpire($discard_id);
            $details = $this->expire_medicine->selectOne($id);
            // Email details
            $mailsubject = getUsername($details->created_by) . 'Request the Medicine for the Discard';
            $user_role = ROLE_EHS_HEAD;

            // Fetch users with the specified role
            $users = $this->user->whereRaw('FIND_IN_SET(?, role)', [$user_role])->get();
            $userids = $users->pluck('id')->toArray();

            if ($users->isNotEmpty()) {
                foreach ($users as $user) {
                    $email_id = $user->email;

                    if (!empty($email_id)) {
                        $details = $this->expire_medicine->selectOne($id);

                        if ($details) {
                            $emailDetails = $details->toArray();
                            $emailDetails['name'] = $user->name;
                            $emailDetails['email_id'] = $email_id;
                            $emailDetails['ohc_type'] = "Request for the Medicine Dicard";


                            Mail::to($emailDetails['email_id'])->queue(new MedicineExpireEmail($emailDetails));
                        }
                    }
                }
            }



            $notificationData = array(
                'notification_type' => 4,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => getMedicinename($details->medicine_id)  . " " . "  Request For the Discard by " . getUsername($details->created_by),
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' =>    $details->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('ohc/discard/approval/view/' . encryptId($discard_id->id)),
                'assigned_user' => array_to_string($userids),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);
            return response()->json(['status' => 'success', 'msg' => __('Discards the Medicine Successfully')], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => __('ohc.Please try After Some time')], 406);
        }
    }

    public function balance(Request $request)
    {
        try {
            $unit_id = decryptId($request->unit_id);
            $medicine_id = $request->medicine_id;

            $response = $this->inventory
                ->where('unit_id', $unit_id)
                ->where('medicine_id', $medicine_id)
                ->first();

            return response()->json([
                'balance' => $response->balance ?? 0
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Invalid request'
            ], 400);
        }
    }

    public function close(Request $request)
    {

        try {

            $id = decryptId($request->id);
            $remarks = $request->remarks;
            $this->expire_medicine->closediscard($id);
            return response()->json(['status' => 'success', 'msg' => __('Closed the Discarded the Medicine Successfully')], 200);
        } catch (Exception $ex) {

            report($ex);
            return response()->json(['status' => 'error', 'msg' => __('Something Went Wrong please try again after some time')], 200);
        }
    }
    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->expire_medicine->expireexportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Medicine Name',
                'Batch Number',
                'Expiry Date',
                'Created_by',
                'Created_at'
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  getMedicinename($data->medicine_id);
                $export[] =  $data->batch_number;
                $export[] =  Displaydateformat($data->expire_date);
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Medicine Expire Details.xlsx')
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

            $allData = $this->expire_medicine->expireexportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Medicine Name',
                'Batch Number',
                'Expiry Date',
                'Created_by',
                'Created_at'
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Expire Medicine List",
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

            $view = view('ohcmanagement.report.expiremedicine.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Expire Medicine List.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
        }
    }
}
