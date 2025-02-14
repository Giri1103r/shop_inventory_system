<?php

namespace App\Http\Controllers\OhcManagement;

use App\Http\Controllers\Controller;

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
    public function __construct()
    {
        $this->medicine = new Medicine();
        $this->vendor = new Vendor();
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
                            return $row->unit_name;
                        })
                        ->editColumn('department_id', function ($row) {
                            return $row->department_name;
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
                            // $btn .= '<a href="' . admin_url('ohc/medicine-issuance/edit/' . encryptId($row->id)) . '" class="" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a> ';
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
            $medicine = $this->medicine_stock->getMedicineIssuanceStock();
            $data = array(
                'medicine' => $medicine,
                'unit' => $unit
            );

            return view('ohcmanagement.medicine_issuance.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medicine-issuance/list'));
        }
    }
    public function issue(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $unit = $this->unit->getunit();
            $departmentList = $this->department->getdepartment();

            $medicine = $this->medicine_stock->getMedicinestockdata();
            $user_medicine_requisition = $this->user_medicine_requisition->selectOne($id);
            // dd(   $user_medicine_requisition);
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
            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
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




                    foreach ($medicine_issuance as $medicine) {

                        $medicine_id = $medicine->medicine_id;
                        $unitId = $user_medicine_issuance->unit_id;
                        $issuedQuantity = $medicine->quantity;

                        $this->medicine_stock
                            ->where('id', $medicine_id)
                            ->where('unit_id', $unitId)
                            ->decrement('quantity', $issuedQuantity);
                   }

                // Notification and Email
                $id =  $user_medicine_issuance->id;
                $data = $this->user_medicine_issuance->selectOne($user_medicine_issuance->id);
                $unitId = $data->unit_id;
                $departmentId = $data->department_id;
                $mailsubject = 'Medicine Issuing to Other Unit';
                $user_role = ROLE_CERTIFIED_FIRST_AIDER;



                $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->where('unit_id', $unitId)->where('department_id', $departmentId)->pluck('id')->toArray();
                $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->where('unit_id', $unitId)->where('department_id', $departmentId)->get();


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
                        'message' => "The requested Medicine Was issued By " . getUsername($data->created_by),
                        'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('ohc/medicine-issuance/list'),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);


                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/medicine-issuance/list'));
        } catch (Exception $ex) {

            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
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

                $user_medicine_issuance = $this->user_medicine_issuance->store();
                $this->medicine_issuance->store($user_medicine_issuance);
                $id = decryptId($request->id);
                $this->user_medicine_requisition->updatestatus($id);
                $this->ohc_status->updatecloseStatus($id);
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


                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/medicine-issuance/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medicine-issuance/list'));
        }
    }
    public function edit(Request $request)
    {
        try {
            $id = decryptId($request->id);


            $user_medicine_issuance = $this->user_medicine_issuance->selectOne($id);
            $departmentList=$this->department->getdepartment();
            $medicine_issuance = $this->medicine_issuance->selectOne($id);
            $departmentList = $this->department->getdepartment();
            $unit = $this->unit->getuserunit();
            $medicine = $this->medicine_stock->getMedicineIssuanceStock();
            $data = array(
              'medicine'=>$medicine,
                'unit' => $unit,
                'departmentList' => $departmentList,
                'user_medicine_issuance' => $user_medicine_issuance,
                'medicine_issuance' => $medicine_issuance
            );
            // dd($data );
            return view('ohcmanagement.medicine_issuance.edit', $data);
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
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
                'department_id.required' => 'Please select a Deparment.',
                'unit_id.required' => 'Please select a unit.',
                'issue_date.required' => 'Please select the Issued date.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

                $user_medicine_issuance = $this->user_medicine_issuance->updates($id);
                $this->medicine_issuance->updates($id);

                $data = $this->user_medicine_issuance->selectOne($id);

                $unitId = $data->unit_id;
                $departmentId = $data->department_id;
                $mailsubject = 'Medicine Issuing to Other Unit';
                $user_role = ROLE_CERTIFIED_FIRST_AIDER;



                $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->where('unit_id', $unitId)->where('department_id', $departmentId)->pluck('id')->toArray();
                $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->where('unit_id', $unitId)->where('department_id', $departmentId)->get();


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

                $notificationData = array(
                    'notification_type' => 4,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => "The requested Medicine Was issued By " . getUsername($data->created_by),
                        'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('ohc/medicine-issuance/list'),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);
                /**
                 * Send Web notification
                 */



                Session::flash('success', 'Your data has been Updated successfully!');
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/medicine-issuance/list'));
        } catch (Exception $ex) {

            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
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
            Session::flash('error', 'Something went wrong please try again after some time');
            return redirect(admin_url('ohc/medicine-issuance/list'));
        }
    }
    public function quantity(Request $request, $quantity_id)
    {
        $id = ($quantity_id);


        $availableQuantity = $this->medicine_stock->getAvailableQuantity($id);

        return response()->json(
            ['available_quantity' => $availableQuantity->quantity]
        );
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
                'Unit Name',
                'Department Name',
                'Issue Date',
                'Created_by',
                'Created_at'
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
        }
    }

    public function delete(Request $request,$id)
    {
        try {

            $this->medicine_issuance->deleterecord($id);
            return response()->json(['status' => 'success', 'msg' => 'Deleted successfully'], 200);
        } catch (Exception $ex) {
            return response()->json(['status' => 'error', 'msg' => 'Something went wrong'], 200);
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
                'Unit Name',
                'Department Name',
                'Issue Date',
                'Created_by',
                'Created_at'
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
}
