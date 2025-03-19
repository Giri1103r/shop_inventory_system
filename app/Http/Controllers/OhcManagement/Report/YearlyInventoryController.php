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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class YearlyInventoryController extends Controller
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
        return view('ohcmanagement.report.yearly-inventory.list', $data);
    }

    public function medicinereport(Request $request)
    {
        $medicine = $this->medicine->getMedicineData();
        $selectedUnit = $request->input('unit_id');
        $selectedYear = $request->input('year');

        $inventory = $this->inventory->getyearlyinventoryreport($selectedUnit, $selectedYear);
        $receiving = $this->medicine_receiving->getYearlyPurchaseddate($selectedYear); // Ensure this method exists
        $medicineIssuing = $this->user_medicine_issuance->getYealyunitdata($selectedYear, $selectedUnit);


        $allIssuances = $this->medicine_issuance->getYearlyissuedDate($selectedYear, $medicineIssuing);
      
        $data = [
            'medicine' => $medicine,
            'inventory' => $inventory,
            'receiving' => $receiving,
            'issuing' => $allIssuances,
        ];

        if ($request->ajax()) {
            return response()->json($data);
        } else {
            return response()->json(['error' => 'Something went wrong, please try again later']);
        }
    }

    public function ExportExcel(Request $request)
    {
        try {
            $selectedUnit = $request->input('unit_id');
            $selectedYear = $request->input('year');

            // Fetch data
            $medicine = $this->inventory->getUnitwise($selectedUnit);
            $inventory = $this->inventory->getyearlyinventoryreport($selectedUnit, $selectedYear);
            $receiving = $this->medicine_receiving->getYearlyPurchaseddate($selectedYear);
            $medicineIssuing = $this->user_medicine_issuance->getYealyunitdata($selectedYear, $selectedUnit);
            $allIssuances = $this->medicine_issuance->getYearlyissuedDate($selectedYear, $medicineIssuing);

            // Organizing data by medicine ID for easy lookup
            $inventoryData = [];
            foreach ($inventory as $inv) {
                $inventoryData[$inv->medicine_name] = [
                    'balance' => $inv->balance ?? 0,
                    'total_purchase' => $inv->total_purchase ?? 0,
                    'total_issue' => $inv->total_issue ?? 0,
                ];
            }

            $receivingData = [];
            foreach ($receiving as $rec) {
                $month = date('M', strtotime($rec->approved_date));
                $receivingData[$rec->medicine_name][$month] = $rec->quantity;
            }

            $issuanceData = [];
            foreach ($allIssuances as $issue) {
                $month = date('M', strtotime($issue->created_at));
                $issuanceData[$issue->medicine_name][$month] = $issue->quantity;
            }

            // Initialize Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->getStyle('A1:B3')->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFFFFF']
                ]
            ]);

            // Insert Logo
            $logoPath = public_path('assets/images/logo-dark.png');
            if (file_exists($logoPath)) {
                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Company Logo');
                $drawing->setPath($logoPath);
                $drawing->setCoordinates('A1');
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawing->setWidth(60);
                $drawing->setHeight(60);
                $drawing->setWorksheet($sheet);
            }

            // Title Styling for C1:AC3 (Red)
            $sheet->mergeCells('C1:AC3');
            $sheet->setCellValue('F1', "Medical Treatment Slip\nPN International Pvt Ltd.");
            $sheet->getStyle('C1:AC3')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '000000'], 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FF0000']
                ]
            ]);

            // Year Selection
            $sheet->setCellValue('A4', 'Year:');
            $sheet->setCellValue('B4', $selectedYear);
            $sheet->getStyle('A4:B4')->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E1ACC9']]
            ]);

            // Section Titles
            $sheet->mergeCells('C4:L4')->setCellValue('C4', 'Material Available (Month Wise)');
            $sheet->mergeCells('M4:X4')->setCellValue('M4', 'Issued Medicine Quantity (Month Wise)');
            $sheet->mergeCells('Y4:AC4')->setCellValue('Y4', 'Grant');

            foreach (['C4:L4', 'M4:X4', 'Y4:AC4'] as $range) {
                $sheet->getStyle($range)->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E1ACC9']]
                ]);
            }

            // Column Headers
            $sheet->getStyle('A5:AC5')->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '7ECFA1']]
            ]);

            // Column Names
            $sheet->setCellValue('A5', 'ID');
            $sheet->setCellValue('B5', 'Name of Item');

            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $col = 'C';

            foreach ($months as $month) {
                $sheet->setCellValue($col . '5', $month);
                $col++;
            }
            foreach ($months as $month) {
                $sheet->setCellValue($col . '5', $month);
                $col++;
            }

            $sheet->setCellValue($col . '5', 'Total Purchase');
            $col++;
            $sheet->setCellValue($col . '5', 'Total Issue');
            $col++;
            $sheet->setCellValue($col . '5', 'Balance');

            // Data Population
            $row = 6;
            $id = 1;
            foreach ($medicine as $med) {
                $sheet->setCellValue('A' . $row, $id);
                $sheet->setCellValue('B' . $row, $med->medicine_name);
                $col = 'C';

                foreach ($months as $month) {
                    $sheet->setCellValue($col . $row, $receivingData[$med->medicine_name][$month] ?? 0);
                    $col++;
                }

                foreach ($months as $month) {
                    $sheet->setCellValue($col . $row, $issuanceData[$med->medicine_name][$month] ?? 0);
                    $col++;
                }

                $sheet->setCellValue($col . $row, $inventoryData[$med->medicine_name]['total_purchase'] ?? 0);
                $col++;
                $sheet->setCellValue($col . $row, $inventoryData[$med->medicine_name]['total_issue'] ?? 0);
                $col++;
                $sheet->setCellValue($col . $row, $inventoryData[$med->medicine_name]['balance'] ?? 0);

                $row++;
                $id++;
            }

            // Save and Export Excel
            $fileName = 'Yearly_Inventory_Report.xlsx';
            return response()->streamDownload(function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            }, $fileName, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
        } catch (Exception $ex) {
            return back()->with('error', 'Failed to export Excel file. ' . $ex->getMessage());
        }
    }
}
