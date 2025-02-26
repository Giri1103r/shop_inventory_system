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
use App\Models\OhcManagement\Report\Inventory;
use App\Models\OhcManagement\UserDiscard;
use App\Models\User;
use Illuminate\Support\Facades\Response;

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
    private $inventory;

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
        $this->inventory = new Inventory();
    }
    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->user_discard->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('medicine_status', function ($row) {
                            $text = "<span style='color:red'>In-Active<span>";
                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class= 'statusChange' data-id='" . encryptId($row->med_id) . "' data-type = '1' >Active<span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class= 'statusChange' data-id='" . encryptId($row->med_id) . "' data-type = '0' >In-Active<span>";
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
                            $btn .= '<a href="' . admin_url('ohc/discard/view/' . encryptId($row->discard_id) . '/' . encryptId($row->med_id)) . '" class="" title="View"><i class="fa-solid fa-eye"></i></a>';
                            $btn .= '<a href="' . admin_url('ohc/discard/edit/' . encryptId($row->discard_id) . '/' . encryptId($row->med_id)) . '" class="" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>';

                            // }

                            return $btn;
                        })

                        ->rawColumns(['action', 'discard_date', 'medicine_status', 'unit_id', 'department_id', 'medicine_id'])
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
            $medicine = $this->inventory->getmedicineUnitwise();
            $departmentList = $this->department->getunitwiseDepartment();
            $data = array(
                'medicine' => $medicine,
                'unit' => $unit,
                'departmentList' => $departmentList
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
                $medicine = $this->discard->store($user_discard);
                $user_discard_id = $this->user_discard->selectOne($user_discard->id);
                $discard = $this->discard->selectOne($user_discard->id);
                foreach ($discard as $medicine) {
                    $medicine_id = $medicine->medicine_id;
                    $unitId = $user_discard_id->unit_id;
                    $issuedQuantity = $medicine->quantity;


                    $inventory = $this->inventory
                        ->where('medicine_id', $medicine_id)
                        ->where('unit_id', $unitId)
                        ->first();

                    if ($inventory) {
                        $inventory->decrement('balance', $issuedQuantity);
                    }
                }

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
            $medicine_id = decryptId($request->medicineId);
            if (Auth::check()) {
                $user_discard = $this->user_discard->selectOne($id);
                $discard = $this->discard->firstdata($medicine_id, $id);
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

    // edit

    public function edit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $medicine_id = decryptId($request->medicineId);
// dd( $id, $medicine_id);
            if (Auth::check()) {
                $user_discard = $this->user_discard->selectOne($id);
                $discard = $this->discard->firstdata($medicine_id,$id);
            }
            $departmentList = $this->department->getdepartment();
            $unit = $this->unit->getuserunit();
            $logData = $this->ohcStatus->getMedicineRequisitionLog($id);
            $medicine = $this->inventory->dicardmedicine($user_discard->unit_id);
            $data = array(
                'user_discard' => $user_discard,
                'discard' => $discard,
                'logdata' => $logData,
                'unit' => $unit,
                'departmentList' => $departmentList,
                'medicine' =>  $medicine

            );
            return view('ohcmanagement.discard.edit', $data);
        } catch (Exception $ex) {
        }
    }
    // update
    public function update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $ids = decryptId($request->medicineid);
            $validator = Validator::make($request->all(), [
                'unit_id' => 'required',
                'department_id' => 'required',
                'discard_date' => 'required',
            ], [
                'department_id.required' => 'Please select a Department.',
                'unit_id.required' => 'Please select a unit.',
                'discard_date.required' => 'Please select the discard date.',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $user_discard = $this->user_discard->selectOne($id);
             $discard = $this->discard->medicinedetails($id,$ids);
             $newQuantity = $request->quantity;
             $oldquantity = $discard->quantity;
             if ($oldquantity > $newQuantity) {
                $difference = $oldquantity - $newQuantity;



                $this->inventory
                    ->where('medicine_id',   $discard->medicine_id)
                    ->where('unit_id', $user_discard->unit_id)
                    ->increment('balance', $difference);
            } elseif ($oldquantity < $newQuantity) {
                $difference = $newQuantity - $oldquantity;

                $this->inventory
                    ->where('medicine_id', $discard->medicine_id)
                    ->where('unit_id', $user_discard->unit_id)
                    ->decrement('balance', $difference);
            }
            $this->user_discard->updates($id, $user_discard);
            $this->discard->updates($ids, $id);

            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('ohc/discard/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something went wrong, Please try again later!');
            return redirect(admin_url('ohc/discard/list'));
        }
    }
    // General PDF


    public function StatusChange(Request $request)
    {

        try {
            // $id = decryptId($request->discard_id);
            $ids = decryptId($request->medicine_id);
            // $this->user_discard->statuschange($id);
            $this->discard->statuschange($ids);
            return response()->json(['status' => 'success', 'msg' => 'Your status  has changed Successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    // unique

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $medicine_id = decryptId($request->medicine_id);
            $id = decryptId($request->id);
            $medicineid = decryptId($request->medicineid);
// dd($medicine_id,$id, $medicineid);
            if (empty($id)) {
                $isUnique = $this->discard->uniqueCheck($medicine_id);
            } else {
              
                // dd( $unit_id );
                $isUnique = $this->discard->existUniqueCheck($medicine_id, $id,$medicineid);
            }

            if ($isUnique->count()) {
                return Response::json(false);
            }
            return Response::json(true);
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
                $export[] = $data->quantity;
                $export[] = $data->remarks;
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
        $id = decryptId($quantity_id);


        $availableQuantity = $this->inventory->getunitwiseAvailableQuantity($id);

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
}
