<?php


namespace App\Http\Controllers\OhcManagement;

use App\Http\Controllers\Controller;
use App\Mail\Ohc\MedicineRequisitionEmail;
use App\Models\Master\Department;
use App\Models\Master\Employee;
use App\Models\Master\Unit;
use App\Models\OhcManagement\Discard;
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
use App\Models\OhcManagement\UserDiscard;
use App\Models\User;

class DiscardController extends Controller
{
    private $medicine;
    private $vendor;
    private $user_discard;
    private $unit;
    private $department;
    private $discard;
    private $medicine_stock;
    private $ohcStatus;
    private $user;

    public function __construct()
    {
        $this->medicine = new Medicine();
        $this->vendor = new Vendor();
        $this->user_discard = new UserDiscard();
        $this->discard = new Discard();
        $this->medicine_stock = new MedicineStock();
        $this->unit = new Unit();
        $this->department = new Department();
        $this->ohcStatus = new OhcStatuslog();
        $this->user = new User();
    }
    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->user_discard->list();
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


                        ->editColumn('discard_date', function ($row) {
                            return displaydateformat($row->discard_date);
                        })
                        ->editColumn('medicine_id', function ($row) {
                            return getMedicinename($row->medicine_id);
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
                            $btn .= '<a href="' . admin_url('ohc/discard/view/' . encryptId($row->discard_id)) . '" class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }

                           return $btn;
                        })

                        ->rawColumns(['action', 'discard_date', 'approve_status','unit_id','department_id','medicine_id'])
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
        $data = array(

            'unit' => $unit
        );

        return view('ohcmanagement.discard.list', $data);
    }

    public function add()
    {
        try {
            $unit = $this->unit->getunit();
            $medicine = $this->medicine_stock->getMedicinestockdata();
            $departmentList = $this->department->getunitwiseDepartment();
            $data = array(
                'medicine' => $medicine,
                'unit' => $unit,
                'departmentList'=>$departmentList
            );

            return view('ohcmanagement.discard.add', $data);
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/discard/list'));
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'unit_id' => 'required',
                'department_id' => 'required',
                'discard_date' => 'required',

            ];
            $messages = [
                'department_id.required' => 'Please select a Deparment.',
                'unit_id.required' => 'Please select a unit.',
                'discard_date.required' => 'Please select the expiry date.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {
                // Store user medicine requisition
                $user_discard = $this->user_discard->store();
                $medicineRequisition = $this->discard->store($user_discard);


                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/discard/list'));
        } catch (Exception $ex) {

            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/discard/list'));
        }
    }


    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $user_discard = $this->user_discard->selectOne($id);
                $discard = $this->discard->selectOne($id);
            }
            $unit = $this->unit->getunit();
            $logData = $this->ohcStatus->getMedicineRequisitionLog($id);
            $data = array(
                'user_discard' => $user_discard,
                'discard' => $discard,
                'logdata' => $logData,

            );
            return view('ohcmanagement.discard.view', $data);
        } catch (Exception $ex) {
        }
    }

    public function approvalview(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $user_discard = $this->user_discard->selectOne($id);
                $discard = $this->discard->selectOne($id);
            }
            if ($user_discard->approve_status != STATUS_OHC_PARAMEDICS_APPROVAL_PENDING) {
                return redirect(admin_url('ohc/discard/list'))
                    ->with('error', 'You have already responded to this request !.');
            }

            $data = array(
                'user_discard' => $user_discard,
                'discard' => $discard,
            );

            return view('ohcmanagement.discard.approve', $data);
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
                    'approve_status' => STATUS_OHC_PARAMEDICS_APPROVED,
                ];
            } else {
                $data = [
                    'approve_status' => STATUS_OHC_PARAMEDICS_REJECTED,
                ];
            }

            $this->user_discard->approvereject($id, $data);
            $this->ohcStatus->paramedicsapprove($id, $data);
            $createdBy = $this->user_discard->where('id', $id)->pluck('created_by');
            $user = $this->user->where('id', $createdBy)->where('status', 1)->first();
            if ($request->action == 'approve') {
                $mailsubject = 'Paramedics Approved the medicine';
                $email_id = $user->email;

                if (!empty($email_id)) {
                    $details = $this->user_discard->selectOne($id);
                    $medicineDetails = $this->discard->selectOne($id);

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
                    'web_link' =>  admin_url('ohc/discard/list' ),
                    'assigned_user' => $details->created_by,
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);
            }else{
                $mailsubject = 'Paramedics Rejected the medicine';
                $email_id = $user->email;

                if (!empty($email_id)) {
                    $details = $this->user_discard->selectOne($id);
                    $medicineDetails = $this->discard->selectOne($id);

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
                    'web_link' =>  admin_url('ohc/discard/list' ),
                    'assigned_user' => $details->created_by,
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);
            }

            return redirect(admin_url('ohc/discard/list'))
                ->with('success', 'Request has been processed successfully.');
        } catch (Exception $ex) {
            dd($ex);
            return redirect(admin_url('ohc/discard/list'))
                ->with('error', 'Something went wrong, Please try again later.');
        }
    }

    // General PDF

    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $user_discard = $this->user_discard->selectOne($id);
                $discard = $this->discard->selectOne($id);
            }
            $logData = $this->ohcStatus->getMedicineRequisitionLog($id);

            $data = [
                'user_discard' => $user_discard,
                'discard' => $discard,
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

            $html = view('ohcmanagement.discard.generalpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "Medicine Receiving Stock Details.pdf";
            return $mpdf->Output($filename, 'I');
        } catch (Exception $ex) {
            dd($ex);
            return redirect()->back()->withErrors(['error' => 'An error occurred while generating the PDF.']);
        }
    }
    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->user_discard->statuschange($id);
            $this->discard->statuschange($id);


            return response()->json(['status' => 'success', 'msg' => 'Your status  has changed Successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->user_discard->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Unit Name',
                'Department Name',
                'Medicine Name',
                'Quantity',
                'Remarks',
                'Discard Date',
                'Created by',
                'Created at'
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  getUnitname($data->unit_id);
                $export[] =  getDepartment($data->department_id);
                $export[] =  getMedicinename($data->medicine_id);
                $export[] =$data->quantity;
                $export[] =$data->remarks;
                $export[] = Displaydateformat($data->discard_date);;
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Expired Medicines.xlsx')
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

            $allData = $this->user_discard->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Unit Name',
                'Department Name',
                'Medicine Name',
                'Quantity',
                'Remarks',
                'Discard Date',
                'Created by',
                'Created at'
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Expired Medicines",
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

            $view = view('ohcmanagement.discard.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Expired Medicines.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
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


}
