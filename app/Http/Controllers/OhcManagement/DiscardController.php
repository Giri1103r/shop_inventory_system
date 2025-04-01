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
                            $user = Auth::user();

                            $text = "<span style='color:red'>In-Active</span>";

                            if ($row->status == 1) {
                                $text = "<span style='color:green'>Active</span>";
                            } elseif ($row->status == 0) {
                                $text = "<span style='color:red'>In-Active</span>";
                            }



                            return $text;
                        })


                        ->addColumn('approve_status', function ($row) {
                            $text = '';
                            switch ($row->approve_status) {
                                case OHC_DISCARD_EHS_APPROVED:
                                    $text = "<span class='badge bg-success rounded' style='font-size: 1.0em;'>EHS Head Approved</span>";
                                    break;

                                default:
                                    $text = "<span class='badge rounded-pill text-bg-warning'>Unknown</span>";
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
                            if (CheckUserPermission('view')) {
                                $btn .= '<a href="' . admin_url('ohc/discard/view/' . encryptId($row->id) ) . '" class="" title="View"><i class="fa-solid fa-eye"></i></a>';
                            }

                            return $btn;
                        })

                        ->rawColumns(['action', 'discard_date', 'approve_status', 'unit_id', 'department_id', 'medicine_id'])
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
            report($ex);
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

            }
            $unit = $this->unit->getunit();
            $logData = $this->ohcStatus->where('reference_id', $user_discard->expire_id)->where('type',TYPE_OHC_MEDICINE_DISCARD)->get();
            $data = array(
                'user_discard' => $user_discard,
                'logData' => $logData,


            );
            return view('ohcmanagement.discard.view', $data);
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/discard/list'));
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
                'Status',
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
                $export[] = Displaydateformat($data->discard_date);
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
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
                'Status',
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
