<?php


namespace App\Http\Controllers\OhcManagement;

use App\Http\Controllers\Controller;

use App\Mail\Ohc\MedicineRequisitionEmail;
use App\Models\Master\Department;
use App\Models\Master\Employee;
use App\Models\Master\Unit;
use App\Models\OhcManagement\Master\Medicine;
use App\Models\OhcManagement\Master\Vendor;
use App\Models\OhcManagement\MedicineFirstAid;
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
use App\Models\OhcManagement\UserMedicineFirstAid;
use App\Models\User;

class MedicineFirstAidController extends Controller
{
    private $medicine;
    private $vendor;
    private $user_medicine_first_aid;
    private $unit;
    private $department;
    private $medicine_first_aid;
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
        $this->user_medicine_first_aid = new UserMedicineFirstAid();
        $this->medicine_first_aid = new MedicineFirstAid();
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
                    $data = $this->user_medicine_first_aid->list();
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
                            $btn .= '<a href="' . admin_url('ohc/medicine-first-aid/view/' . encryptId($row->id)) . '" class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            // if (CheckUserPermission('edit')) {
                            $btn .= '<a href="' . admin_url('ohc/medicine-first-aid/edit/' . encryptId($row->id)) . '" class="" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a> ';
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

        return view('ohcmanagement.medicine-first-aid.list', $data);
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

            return view('ohcmanagement.medicine-first-aid.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medicine-first-aid/list'));
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

                $user_medicine_first_aid = $this->user_medicine_first_aid->store();
                $medicine_first_aid = $this->medicine_first_aid->store($user_medicine_first_aid);


                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/medicine-first-aid/list'));
        } catch (Exception $ex) {

            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medicine-first-aid/list'));
        }
    }


    public function edit(Request $request)
    {
        try {
            $id = decryptId($request->id);


            $user_medicine_first_aid = $this->user_medicine_first_aid->selectOne($id);
            $departmentList=$this->department->getdepartment();
            $medicine_first_aid = $this->medicine_first_aid->selectOne($id);
            $departmentList = $this->department->getdepartment();
            $unit = $this->unit->getuserunit();
            $medicine = $this->medicine_stock->getMedicineIssuanceStock();
            $data = array(
              'medicine'=>$medicine,
                'unit' => $unit,
                'departmentList' => $departmentList,
                'user_medicine_first_aid' => $user_medicine_first_aid,
                'medicine_first_aid' => $medicine_first_aid
            );
            // dd($data );
            return view('ohcmanagement.medicine-first-aid.edit', $data);
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medicine-first-aid/list'));
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

                $user_medicine_first_aid = $this->user_medicine_first_aid->updates($id);
                $this->medicine_first_aid->updates($id);

           


                Session::flash('success', 'Your data has been Updated successfully!');
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/medicine-first-aid/list'));
        } catch (Exception $ex) {

            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medicine-first-aid/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $user_medicine_first_aid = $this->user_medicine_first_aid->selectOne($id);
                $medicine_first_aid = $this->medicine_first_aid->selectOne($id);
            }
            $unit = $this->unit->getunit();
            $data = array(
                'user_medicine_first_aid' => $user_medicine_first_aid,
                'medicine_first_aid' => $medicine_first_aid,

            );
            return view('ohcmanagement.medicine-first-aid.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong please try again after some time');
            return redirect(admin_url('ohc/medicine-first-aid/list'));
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

            $allData = $this->user_medicine_first_aid->exportdata();

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

    public function delete(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $this->medicine_first_aid->deleterecord($id);
        } catch (Exception $ex) {
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->user_medicine_first_aid->exportdata();

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

            $view = view('ohcmanagement.medicine-first-aid.pdf', $data);
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
