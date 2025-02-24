<?php

namespace App\Http\Controllers\OhcManagement\Report;

use App\Http\Controllers\Controller;
use App\Mail\Ohc\MedicineReceivingRequestEmail;
use App\Models\Master\Department;
use App\Models\Master\Employee;
use App\Models\Master\Unit;
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
    private $medicine_receiving;
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
        $this->medicine_receiving = new MedicineReceiving();
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
                    $data = $this->medicine_receiving->expirelist();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->editColumn('medicine_id', function ($row) {
                            return getMedicinename($row->medicine_id);
                        })


                        ->editColumn('expire_date', function ($row) {
                            return displaydateformat($row->expire_date);
                        })


                        ->rawColumns(['action', 'expire_date', 'approve_status', 'hsn_id','pack'])
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

            $allData = $this->medicine_receiving->expireexportdata();

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

            $allData = $this->medicine_receiving->expireexportdata();

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

            dd($ex);
        }
    }
}
