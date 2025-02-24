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
            $medicine = $this->medicine->getMedicineData();
            $selectedUnit = $request->input('unit_id');
            $selectedYear = $request->input('year');

            $inventory = $this->inventory->getyearlyinventoryreport($selectedUnit, $selectedYear);
            $receiving = $this->medicine_receiving->getYearlyPurchaseddate($selectedYear);
            $medicineIssuing = $this->user_medicine_issuance->getYealyunitdata($selectedYear, $selectedUnit);
            $allIssuances = $this->medicine_issuance->getYearlyissuedDate($selectedYear, $medicineIssuing);

            // Create a new spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set A1 Background to White
            $sheet->getStyle('A1')->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFFFFF'] // White background for logo
                ]
            ]);

            // Insert Logo in A1
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Company Logo');
            $drawing->setPath(public_path('assets/images/logo-dark.png'));
            $drawing->setCoordinates('A1');
            $drawing->setOffsetX(5);
            $drawing->setOffsetY(5);
            $drawing->setWidth(60); 
            $drawing->setHeight(60);
            $drawing->setWorksheet($sheet);


            $sheet->mergeCells('A1:Z3');
            $sheet->setCellValue('F1', "Medical Treatment Slip\nPN International Pvt Ltd.");
            $sheet->getStyle('F1:Z3')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'], // White text
                    'size' => 14
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FF0000']
                ]
            ]);

            // Add Spacing Row
            $sheet->setCellValue('A4', '');

            // Table Headers
            $headers = ['Medicine Name', 'Unit', 'Year', 'Total Stock'];
            $columnLetter = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($columnLetter . '5', $header);
                $sheet->getStyle($columnLetter . '5')->getFont()->setBold(true);
                $sheet->getStyle($columnLetter . '5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $columnLetter++;
            }

            // Populate Data
            $rowNum = 6;
            foreach ($inventory as $item) {
                $sheet->setCellValue('A' . $rowNum, $item->medicine_name);
                $sheet->setCellValue('B' . $rowNum, $item->unit);
                $sheet->setCellValue('C' . $rowNum, $selectedYear);
                $sheet->setCellValue('D' . $rowNum, $item->total_stock);
                $rowNum++;
            }

            // Auto-size columns
            foreach (range('A', 'D') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            // Save & Download File
            $writer = new Xlsx($spreadsheet);
            $fileName = 'Monthly_Inventory_Report.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"$fileName\"");
            $writer->save('php://output');
            exit();

        } catch (Exception $ex) {
            report($ex);
            return back()->with('error', 'Failed to export Excel file.');
        }
    }




}
