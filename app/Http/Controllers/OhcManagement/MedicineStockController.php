<?php

namespace App\Http\Controllers\OhcManagement;

use App\Http\Controllers\Controller;
use App\Mail\Ohc\MedicineStockRequestEmail;
use App\Models\Master\Department;
use App\Models\Master\Employee;
use App\Models\Master\Unit;
use App\Models\OhcManagement\Master\Medicine;
use App\Models\OhcManagement\Master\Vendor;
use App\Models\OhcManagement\MedicineRequisition;
use App\Models\OhcManagement\MedicineStock;
use App\Models\OhcManagement\UserMedicineRequisition;
use App\Models\UploadLog;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;

class MedicineStockController extends Controller
{
    private $medicine;
    private $vendor;
    private $unit;
    private $department;
    private $medicine_requisition;
    private $medicine_stock;
    private $user;


    public function __construct()
    {
        $this->medicine = new Medicine();
        $this->vendor = new Vendor();
        $this->medicine_stock = new UserMedicineRequisition();
        $this->medicine_requisition = new MedicineRequisition();
        $this->medicine_stock = new MedicineStock();
        $this->unit = new Unit();
        $this->department = new Department();
        $this->user = new User();
    }
    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->medicine_stock->list();
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
                        ->editColumn('unit_id', function ($row) {
                            return $row->unit_name;
                        })
                        ->editColumn('medicine_id', function ($row) {
                            return $row->medicine;
                        })
                        ->editColumn('created_at', function ($row) {
                            return displaydateformat($row->created_at);
                        })
                        ->editColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->editColumn('expire_date', function ($row) {
                            return Displaydateformat($row->expire_date);
                        })
                        ->editColumn('hsn_number', function ($row) {
                            return $row->hsn;
                        })
                        ->editColumn('action', function ($row) {
                            $btn = '';
                            // if (CheckUserPermission('view')) {
                            $btn .= '<a href="' . admin_url('ohc/medicine-stock-inventory/view/' . encryptId($row->id)) . '" class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            // if (CheckUserPermission('edit')) {
                            $btn .= '<a href="' . admin_url('ohc/medicine-stock-inventory/edit/' . encryptId($row->id)) . '" class="" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a> ';
                            // }
                            // $btn .= '<a href="javascript:void(0);" data-id="' . encryptId($row->id) . '" class="recordDelete" title="Delete"><i class="fa-solid fa-trash text-danger"></i></a> ';
                            $btn .= '<a href="' . admin_url('ohc/medicine-stock-inventory/approval/view/' . encryptId($row->id)) . '" class="" title="Action"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';

                            return $btn;
                        })

                        ->rawColumns(['action', 'created_by', 'status', 'created_at', 'medicine'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);

                    return response()->json($datatables->getData());
                } catch (Exception $ex) {
                    dd($ex);
                    return response()->json(['status' => 'error', 'msg' => __('ppe.please_try_after_some_time')], 406);
                }
            }
        }

        $unit = $this->unit->getunit();
        $medicine = $this->medicine->getMedicineData();
        $data = array(
            'medicine' => $medicine,
            'unit' => $unit
        );
        return view('ohcmanagement.medicine-stock.list', $data);
    }

    public function add()
    {
        try {
            $unit = $this->unit->getunit();
            $medicine = $this->medicine->getMedicineData();
            $data = array(
                'medicine' => $medicine,
                'unit' => $unit
            );

            return view('ohcmanagement.medicine-stock.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medicine-stock-inventory/list'));
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'unit_id' => 'required',


            ];
            $messages = [

                'unit_id.required' => 'Please select a unit.',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {
                //    dd($request->all());
                $medicine_stock = $this->medicine_stock->store();

                $medicineId = $medicine_stock->id;
                $medicine = $this->medicine->find($medicineId);

                $mailsubject = 'Medicine is Added';
                $user_role = ROLE_EHS_HEAD;

                $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->pluck('id')->toArray();
                $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();

                if (count($users) > 0) {

                    foreach ($users as $user) {

                        $email_id = $user->email;

                        if ($email_id != '' || $email_id != null) {
                            $medicinedetails =  $this->medicine_stock->selectone($medicine_stock->id);
                            $details  = $medicinedetails->toArray();

                            $details['name'] = $user->name;
                            $details['email_id'] =  $email_id;
                            $details['mail_subject'] = $mailsubject;

                            Mail::to($details['email_id'])->queue(new MedicineStockRequestEmail($details));
                        }
                    }
                }

                $notificationData = array(
                    'notification_type' => 4,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => $medicine->medicine . ' is added to the Stock inventory submitted by ' . getUsername($medicine->created_by),
                        'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $medicine->id,
                        'module' => 1,
                    )),
                    'web_link' => admin_url('ohc/medicine-stock-inventory/approval/view/' . encryptId($medicine->id)),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                );

                notificationSave($notificationData);

                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/medicine-stock-inventory/list'));
        } catch (Exception $ex) {

            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medicine-stock-inventory/list'));
        }
    }

    public function edit(Request $request)
    {
        try {
            $id = decryptId($request->id);


            $medicine_stock = $this->medicine_stock->find($id);
            $medicine =   $medicine_stock->medicine_id;
            $thresholdlimit = $this->medicine->where('id', $medicine)->select('threshold_limit')->first();
            $unit = $this->unit->getunit();
            $medicine = $this->medicine->getMedicineData();


            $data = array(
                'medicine' => $medicine,
                'unit' => $unit,
                'thresholdlimit' => $thresholdlimit,
                'medicine_stock' => $medicine_stock,


            );
            return view('ohcmanagement.medicine-stock.edit', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medicine-stock-inventory/list'));
        }
    }
    public function update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rules = [
                'unit_id' => 'required',

            ];
            $messages = [

                'unit_id.required' => 'Please select a unit.',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

                $medicine_stock = $this->medicine_stock->updates($id);
                $medicinedata = $this->medicine_stock->selectone($id);
                $mailsubject = 'Medicine is Updated';
                $user_role = ROLE_EHS_HEAD;

                $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->pluck('id')->toArray();
                $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();

                if (count($users) > 0) {

                    foreach ($users as $user) {

                        $email_id = $user->email;

                        if ($email_id != '' || $email_id != null) {
                            $medicinedetails =  $this->medicine->selectone($id);
                            $details  = $medicinedetails->toArray();

                            $details['name'] = $user->name;
                            $details['email_id'] =  $email_id;
                            $details['mail_subject'] = $mailsubject;

                            Mail::to($details['email_id'])->queue(new MedicineStockRequestEmail($details));
                        }
                    }
                }

                $notificationData = array(
                    'notification_type' => 4,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => $medicinedata->medicine . ' is Updated to the Stock Inventory submitted by ' . getUsername($medicinedata->created_by),
                        'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $id,
                        'module' => 1,
                    )),
                    'web_link' => admin_url('ohc/medicine-stock-inventory/approval/view/' . encryptId($id)),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                );

                notificationSave($notificationData);

                Session::flash('success', 'Your data has been updated successfully!');
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/medicine-stock-inventory/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medicine-stock-inventory/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $medicine_stock = $this->medicine_stock->selectOne($id);
            }
            $medicine =    $medicine_stock->medicine_id;
            $medicineid = $this->medicine->where('id', $medicine)->select('medicine', 'hsn')->first();
            $unit = $this->unit->getunit();
            $data = array(
                'medicine_stock' => $medicine_stock,
                'medicine' => $medicineid


            );
            return view('ohcmanagement.medicine-stock.view', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }
    public function approvalview(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $medicine_stock = $this->medicine_stock->selectOne($id);
            }
            $medicine =    $medicine_stock->medicine_id;
            $medicineid = $this->medicine->where('id', $medicine)->select('medicine', 'hsn')->first();
            $unit = $this->unit->getunit();
            $data = array(
                'medicine_stock' => $medicine_stock,
                'medicine' => $medicineid


            );
            return view('ohcmanagement.medicine-stock.approve', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    public function requestsubmit(Request $request)
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

            $details = $this->medicine_stock->selectOne($id);
            if (!$details) {
                return redirect()->back()->with('error', 'Invalid request.');
            }

            $createdby = $this->medicine_stock->where('id', $id)->value('created_by');
            $email = $this->user->where('id', $createdby)->value('email');

            if (!$email) {
                return redirect()->back()->with('error', 'User email not found.');
            }

            $action = $request->action;
            $mailsubject = ($action == 'approve') ? 'Medicine Name Has Been Approved' : 'Medicine Name Has Been Rejected';

           
            Mail::to($email)->queue(new MedicineStockRequestEmail($details));


            $notificationData = [
                'notification_type' => 4,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode([
                    'title' => $mailsubject,
                    'message' => getMedicinename($details->medicine_id) .
                                ' has ' . ($action == 'approve' ? 'approved' : 'rejected') .
                                ' by the EHS Head ',
                    'icon' => admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $id,
                    'module' => 1,
                ]),
                'web_link' => admin_url('ohc/medicine-stock-inventory/list'),
                'assigned_user' => $createdby,
                'created_by' => Auth::id(),
            ];

            notificationSave($notificationData);


            $this->medicine_stock->approvalupdate($id);

            if ($action == 'reject') {
                $this->medicine->where('id', $details->medicine_id)->update(['trash' => 'YES']);
            }

            return redirect(admin_url('ohc/medicine-stock-inventory/list'))
                ->with('success', 'Request has been processed successfully.');

        } catch (Exception $ex) {
           report($ex);
            return redirect(admin_url('ohc/medicine-stock-inventory/list'))
                ->with('error', 'Something went wrong, Please try again later.');
        }
    }

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $medicine_name = decryptId($request->medicine_id);
            $id = $request->id;

            if (empty($id)) {
                $isUnique = !$this->medicine_stock->uniqueCheck($medicine_name);
            } else {
                $id = decryptId($id);
                $isUnique = !$this->medicine_stock->existUniqueCheck($medicine_name, $id);
            }

            return Response::json($isUnique);
        }
    }

    public function list(Request $request, $unit_id)
    {
        $unit_id = decryptId($unit_id);
        $medicine = $this->medicine->ajaxList($unit_id);

        return response()->json($medicine);
    }
    public function stocklist(Request $request, $medicine_id)
    {
        $medicineId = decryptId($medicine_id);
        $medicine = $this->medicine->stocklist($medicineId);

        if (!$medicine) {
            return response()->json(['error' => 'Medicine not found'], 404);
        }

        return response()->json([
            'expire_date' => displaydateformat($medicine->expiry_date),
            'hsn' => $medicine->hsn,
            'threshold_limit' => $medicine->threshold_limit,
            'id' => encryptId($medicine->id),
        ]);
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->medicine_stock->statuschange($id);


            return response()->json(['status' => 'success', 'msg' => 'Your status  has changed Successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->medicine_stock->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Unit Name',
                'Department Name',
                'request Date',
                'status',
                'Created_by',
                'Created_at'
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->req_id;
                $export[] =  getUnitname($data->unit_id);
                $export[] =  $data->medicine;
                $export[] =  $data->threshold_limit;
                $export[] =  $data->quantity;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Medicine Stock data.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            dd($ex);
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->medicine_stock->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),

                'Unit Name',
                'Medicine Name',
                'Threshold Limit',
                'Quantity',
                'status',
                'Created_by',
                'Created_at'
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Medicine Stock data",
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

            $view = view('ohcmanagement.medicine-stock.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Medicine Stock data.xlsx.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            dd($ex);
        }
    }

    public function thresholdlimit(Request $request)
    {
        $medicineID = decryptId($request->medicine_id);
        $threshold = $this->medicine->hsnajaxData($medicineID);

        if ($threshold) {
            return response()->json([
                'id' => $threshold->id,
                'text' => $threshold->threshold_limit,
                'encrypted_id' => encrypt($threshold->id),
            ]);
        }

        return response()->json(['error' => 'No Threshold limit found'], 404);
    }
}
