<?php

namespace App\Http\Controllers\OhcManagement;

use App\Http\Controllers\Controller;

use App\Models\Master\Department;
use App\Models\Master\Employee;
use App\Models\Master\Unit;
use App\Models\OhcManagement\Master\Medicine;
use App\Models\OhcManagement\Master\Vendor;
use App\Models\OhcManagement\MedicineRequisition;
use App\Models\OhcManagement\MedicineStock;
use App\Models\OhcManagement\UserMedicineRequisition;
use App\Models\UploadLog;

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



    public function __construct()
    {
        $this->medicine = new Medicine();
        $this->vendor = new Vendor();
        $this->medicine_stock = new UserMedicineRequisition();
        $this->medicine_requisition = new MedicineRequisition();
        $this->medicine_stock = new MedicineStock();
        $this->unit = new Unit();
        $this->department = new Department();
    }
    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->medicine_stock->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('stock_status', function ($row) {
                            $text = "<span style='color:red'>In-Active<span>";
                            if ($row->stock_status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class= 'statusChange' data-id='" . encryptId($row->ohc_management_medicine_stock_inventory_id) . "' data-type = '1' >Active<span>";
                            } else if ($row->stock_status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class= 'statusChange' data-id='" . encryptId($row->ohc_management_medicine_stock_inventory_id) . "' data-type = '0' >In-Active<span>";
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
                        ->editColumn('action', function ($row) {
                            $btn = '';
                            // if (CheckUserPermission('view')) {
                            $btn .= '<a href="' . admin_url('ohc/medicine-stock-inventory/view/' . encryptId($row->ohc_management_medicine_stock_inventory_id)) . '" class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            // if (CheckUserPermission('edit')) {
                            $btn .= '<a href="' . admin_url('ohc/medicine-stock-inventory/edit/' . encryptId($row->ohc_management_medicine_stock_inventory_id)) . '" class="" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a> ';
                            // }
                            // $btn .= '<a href="javascript:void(0);" data-id="' . encryptId($row->id) . '" class="recordDelete" title="Delete"><i class="fa-solid fa-trash text-danger"></i></a> ';


                            return $btn;
                        })

                        ->rawColumns(['action', 'created_by', 'stock_status', 'created_at', 'medicine'])
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

                $medicine_stock = $this->medicine_stock->store();


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
            $medicineid = $this->medicine->where('id', $medicine)->select('medicine', 'threshold_limit')->first();
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
