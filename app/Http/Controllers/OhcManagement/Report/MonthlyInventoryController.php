<?php

namespace App\Http\Controllers\OhcManagement\Report;

use App\Http\Controllers\Controller;
use App\Mail\Ohc\MedicineReceivingRequestEmail;
use App\Models\Master\Department;
use App\Models\Master\Employee;
use App\Models\Master\Unit;
use App\Models\OhcManagement\Master\Medicine;
use App\Models\OhcManagement\Master\Vendor;
use App\Models\OhcManagement\MedicineIssuance;
use App\Models\OhcManagement\MedicineReceiving;
use App\Models\OhcManagement\MedicineStock;
use App\Models\OhcManagement\OhcStatuslog;
use App\Models\OhcManagement\Report\Inventory;
use App\Models\OhcManagement\Status\ReceivingStatus;
use App\Models\OhcManagement\UserMedicineIssuance;
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

class MonthlyInventoryController extends Controller
{
    private $medicine;
    private $vendor;
    private $medicine_receiving;
    private $medicine_stock;
    private $ohc_status;
    private $unit;
    private $user;
    private $medicine_issuance;
    private $user_medicine_issuance;

    private $inventory;

    private $status;
    public function __construct()
    {
        $this->medicine = new Medicine();
        $this->vendor = new Vendor();
        $this->medicine_receiving = new MedicineReceiving();
        $this->medicine_issuance = new MedicineIssuance();
        $this->user_medicine_issuance = new UserMedicineIssuance();

        $this->medicine_stock = new MedicineStock();
        $this->ohc_status = new OhcStatuslog();
        $this->user = new User();
        $this->inventory = new Inventory();
        $this->unit = new Unit();
        $this->status = new ReceivingStatus();
    }
    public function index(Request $request)
    {
        $unit = $this->unit->getunit();
        $data = [
            'unit' => $unit,

        ];
        return view('ohcmanagement.report.monthly-inventory.list', $data);
    }

    public function medicinereport(Request $request)
    {

        $medicine = $this->medicine->getMedicineData();
        $selectedUnit = $request->input('unit_id');
        $selectedMonth = $request->input('month');
        $selectedYear = $request->input('year');

        $inventory =  $this->inventory->getmonthlyinventoryreport($selectedUnit, $selectedMonth, $selectedYear);
        $receiving = $this->medicine_receiving->getPurchaseddate($selectedYear, $selectedMonth);
        $medicineIssuing = $this->user_medicine_issuance->getunitdata($selectedYear, $selectedMonth, $selectedUnit);
        $allIssuances = [];
        foreach ($medicineIssuing as $id) {
            $allIssuances[] = $this->medicine_issuance->getissuedDate($selectedYear, $selectedMonth, [$id]);
        }

        $data = [
            'medicine' => $medicine,
            'inventory' => $inventory,
            'receiving' => $receiving,
            'issuing' => $allIssuances,
        ];
        if ($request->ajax()) {
            return response()->json($data);
        } else {
            return response()->json(['error', 'Something went wrong please try again after some time']);
        }
    }


    // public function ExportExcel(Request $request)
    // {
    //     try {
    //         dd('hi');
    //         $selectedUnit = $request->input('unit_id');
    //         $selectedMonth = $request->input('month');
    //         $selectedYear = $request->input('year');

    //         // Fetch Data
    //         $medicine = $this->medicine->getMedicineData();
    //         $inventory = $this->inventory->getmonthlyinventoryreport($selectedUnit, $selectedMonth, $selectedYear);
    //         $receiving = $this->medicine_receiving->getPurchaseddate($selectedYear, $selectedMonth);
    //         $medicineIssuing = $this->user_medicine_issuance->getunitdata($selectedYear, $selectedMonth, $selectedUnit);

    //         $allIssuances = [];
    //         foreach ($medicineIssuing as $id) {
    //             $allIssuances[] = $this->medicine_issuance->getissuedDate($selectedYear, $selectedMonth, [$id]);
    //         }

    //         // Prepare Data for Export
    //         $data = [];
    //         foreach ($inventory as $item) {
    //             $data[] = [
    //                 'Medicine Name' => $item->medicine_name ?? '',
    //                 'Total Purchased' => $item->total_purchase ?? 0,
    //                 'Total Issued' => $item->total_issue ?? 0,
    //                 'Previous Month Balance' => $item->previous_month_total ?? 0,
    //                 'Current Balance' => $item->balance ?? 0,
    //             ];
    //         }

    //         // Export as Excel
    //         return SimpleExcelWriter::streamDownload('Monthly_Inventory_Report.xlsx')
    //             ->addRows($data);

    //     } catch (Exception $ex) {
    //         report($ex);
    //         return back()->with('error', 'Failed to export Excel file.');
    //     }
    // }

}
