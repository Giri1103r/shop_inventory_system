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
use App\Models\OhcManagement\Report\Inventory;

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
    private $inventory;
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
        $this->inventory = new Inventory();
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

                        ->rawColumns(['action', 'issue_date'])
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
            $departmentList = $this->department->getunitwiseDepartment();
            $medicine = $this->inventory->getmedicineUnitwise();
            $data = array(
                'medicine' => $medicine,
                'unit' => $unit,
                'departmentList' => $departmentList
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
                'unit_id.required' => 'Please select the Unit name.',
                'department_id.required' => 'Please select the Department Name.',
                'issue_date.required' => 'Please select the request date.',

            ];


            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

                $user_medicine_first_aid = $this->user_medicine_first_aid->store();
                $medicine_first_aid = $this->medicine_first_aid->store($user_medicine_first_aid);


                foreach ($medicine_first_aid as $medicine) {
                    $medicine_id = $medicine->medicine_id;
                    $unitId = $user_medicine_first_aid->unit_id;
                    $issuedQuantity = $medicine->quantity;


                    $inventory = $this->inventory
                        ->where('medicine_id', $medicine_id)
                        ->where('unit_id', $unitId)
                        ->first();

                    if ($inventory) {
                        $inventory->increment('total_first_aid', $issuedQuantity);
                        $inventory->decrement('balance', $issuedQuantity);
                    }
                }


                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', 'Something went wrong, Please try again later!');
            }
            return redirect(admin_url('ohc/medicine-first-aid/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try again later!');
            return redirect(admin_url('ohc/medicine-first-aid/list'));
        }
    }



    public function edit(Request $request)
    {
        try {
            $id = decryptId($request->id);


            $user_medicine_first_aid = $this->user_medicine_first_aid->selectOne($id);
            $departmentList = $this->department->getdepartment();
            $medicine_first_aid = $this->medicine_first_aid->selectOne($id);
            $departmentList = $this->department->getdepartment();
            $unit = $this->unit->getuserunit();
            $medicine = $this->inventory->getmedicineUnitwise();
            $data = array(
                'medicine' => $medicine,
                'unit' => $unit,
                'departmentList' => $departmentList,
                'user_medicine_first_aid' => $user_medicine_first_aid,
                'medicine_first_aid' => $medicine_first_aid
            );

            return view('ohcmanagement.medicine-first-aid.edit', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medicine-first-aid/list'));
        }
    }

    public function update(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $validator = Validator::make($request->all(), [
                'unit_id' => 'required',
                'department_id' => 'required',
                'issue_date' => 'required',
            ], [
                'department_id.required' => 'Please select a Department.',
                'unit_id.required' => 'Please select a unit.',
                'issue_date.required' => 'Please select the Issued date.',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $user_medicine_first_aid = $this->user_medicine_first_aid->selectOne($id);
            $medicine_first_aid = $this->medicine_first_aid->selectOne($id);

            $deletedPages = json_decode($request->deletedPage, true);

            if (!empty($deletedPages)) {
                foreach ($deletedPages as $encryptedId) {
                    $medicineIds = decryptId($encryptedId);

                    $medicine_first_aid_medicine = $this->medicine_first_aid->firstdata($medicineIds);

                    $this->inventory->where('unit_id', $user_medicine_first_aid->unit_id)
                        ->where('medicine_id', $medicine_first_aid_medicine->medicine_id)
                        ->decrement('total_first_aid', $medicine_first_aid_medicine->quantity);

                    $this->inventory->where('unit_id', $user_medicine_first_aid->unit_id)
                        ->where('medicine_id', $medicine_first_aid_medicine->medicine_id)
                        ->increment('balance', $medicine_first_aid_medicine->quantity);

                    $update_data = $this->medicine_first_aid
                        ->where('id', $medicineIds)->where('reference_id', $id)
                        ->update([
                            'status' => 0,
                            'trash'  => 'Yes'
                        ]);
                }
            }

            foreach ($request->medicine_id as $index => $medicine_id) {
                $medicineRecord = $medicine_first_aid->where('medicine_id', $medicine_id)->where('reference_id', $id)->first();

                if (!$medicineRecord) {

                    continue;
                }

                $newQuantity = $request->quantity[$index];
                $oldquantity = $medicineRecord->quantity;

                if ($oldquantity > $newQuantity) {
                    $difference = $oldquantity - $newQuantity;

                    $this->inventory
                        ->where('medicine_id', $medicine_id)
                        ->where('unit_id', $user_medicine_first_aid->unit_id)
                        ->decrement('total_first_aid', $difference);

                    $this->inventory
                        ->where('medicine_id', $medicine_id)
                        ->where('unit_id', $user_medicine_first_aid->unit_id)
                        ->increment('balance', $difference);
                } elseif ($oldquantity < $newQuantity) {
                    $difference = $newQuantity - $oldquantity;

                    $this->inventory
                        ->where('medicine_id', $medicine_id)
                        ->where('unit_id', $user_medicine_first_aid->unit_id)
                        ->increment('total_first_aid', $difference);

                    $this->inventory
                        ->where('medicine_id', $medicine_id)
                        ->where('unit_id', $user_medicine_first_aid->unit_id)
                        ->decrement('balance', $difference);
                }
            }

            $this->user_medicine_first_aid->updates($id);
            $this->medicine_first_aid->updates($id, $user_medicine_first_aid);


            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('ohc/medicine-first-aid/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something went wrong, Please try again later!');
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
        $id = decryptId($quantity_id);

        $availableQuantity = $this->inventory->getunitwiseAvailableQuantity($id);

        return response()->json(
            ['available_quantity' => $availableQuantity->balance]
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

    public function delete(Request $request, $id)
    {
        try {


            $ids = decryptId($id);

            $data =    $this->medicine_first_aid->firstdata($ids);

            $referenceId =   $data->reference_id;
            $user_medicine_first_aid = $this->user_medicine_first_aid->selectOne($referenceId);

            $this->inventory->where('unit_id', $user_medicine_first_aid->unit_id)
                ->where('medicine_id', $data->medicine_id)
                ->decrement('total_first_aid', $data->quantity);

            $this->inventory->where('unit_id', $user_medicine_first_aid->unit_id)
                ->where('medicine_id', $data->medicine_id)
                ->increment('balance', $data->quantity);
            $this->medicine_first_aid->deleterecord($ids);
            return response()->json(['status' => 'success', 'msg' => 'Deleted successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Something went wrong'], 200);
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

    public function editquantity(Request $request, $quantity_id)
    {
        $id = ($quantity_id);

        $availableQuantity = $this->inventory->getunitwiseAvailableQuantity($id);

        return response()->json(
            ['available_quantity' => $availableQuantity->balance]
        );
    }
}
