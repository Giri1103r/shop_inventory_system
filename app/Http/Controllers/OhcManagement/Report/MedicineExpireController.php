<?php

namespace App\Http\Controllers\OhcManagement\Report;

use App\Http\Controllers\Controller;
use App\Mail\Ohc\MedicineReceivingRequestEmail;
use App\Models\Master\Department;
use App\Models\Master\Employee;
use App\Models\Master\Unit;
use App\Models\Ohcmanagement\ExpireMedicine;
use App\Models\OhcManagement\Master\Medicine;
use App\Models\OhcManagement\Master\Vendor;
use App\Models\OhcManagement\MedicineReceiving;
use App\Models\OhcManagement\MedicineStock;
use App\Models\OhcManagement\OhcStatuslog;
use App\Models\OhcManagement\Report\Inventory;
use App\Models\OhcManagement\Status\ReceivingStatus;
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
    }
    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->expire_medicine->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->editColumn('medicine_id', function ($row) {
                            return getMedicinename($row->medicine_id);
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
                                return null;
                            }
                        })
                        ->rawColumns(['action', 'expire_date', 'approve_status', 'hsn_id', 'pack', 'row_class'])

                        ->editColumn('expire_date', function ($row) {
                            return displaydateformat($row->expire_date);
                        })
                        ->editColumn('action', function ($row) {
                            $btn = '';
                            if (CheckUserPermission('view')) {
                            $btn .= '<a href="' . admin_url('ohc/medicine-expire-report/view/' . encryptId($row->discard_id) . '/' . encryptId($row->med_id)) . '" class="" title="View"><i class="fa-solid text-dark fa-eye"></i></a>';
                            }
                            $expireDate = Carbon::parse($row->expire_date);
                            $today = Carbon::today();
                            if ($expireDate->lessThanOrEqualTo($today)) {
                                $btn .= '<a href="javascript:void(0);" data-id="' . encryptId($row->id) . '" class="discard" title="discard" style="color:rgb(255, 248, 248);margin-right: 5px;"><i class="fa fa-times-circle"></i></a> ';
                            }
                            return $btn;
                        })

                        ->rawColumns(['action', 'expire_date', 'approve_status', 'row_class','pack'])
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

        $medicine = $this->medicine->getMedicineData();
        $vendor = $this->vendor->getVendordata();
        $data = [
            'medicine' => $medicine,
            'vendor' => $vendor
        ];

        return view('ohcmanagement.report.expiremedicine.list', $data);
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
    public function discard(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $remarks = $request->remarks;
            $quantity = $request->quantity;
            $expire_medicine = $this->expire_medicine->find($id);
            $this->discard->close($id, $remarks);
            return response()->json(['status' => 'success', 'msg' => __('Cancelled the OPD Patient Successfully')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('ptw.Please try After Some time')], 406);
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
