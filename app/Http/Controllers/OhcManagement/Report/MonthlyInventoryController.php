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
use DateTime;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

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
        $selectedYear = $request->input('year');
        $formattedMonth = $request->input('month');
        $date = DateTime::createFromFormat('F', $formattedMonth);
        $selectedMonth = $date ? $date->format('m') : null;
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




    public function ExportExcel(Request $request)
    {
        try {
            $selectedUnit = $request->input('unit_id');
            $selectedYear = $request->input('year');
            $selectedMonth = $request->input('month');

            // Fetch data
            $medicine = $this->inventory->getUnitwise($selectedUnit);
            $inventory = $this->inventory->getmonthlyinventoryreport($selectedUnit, $selectedMonth, $selectedYear);
            $receiving = $this->medicine_receiving->getPurchaseddate($selectedYear, $selectedMonth);
            $medicineIssuing = $this->user_medicine_issuance->getunitdata($selectedYear, $selectedMonth, $selectedUnit);
            $allIssuances = $this->medicine_issuance->getissuedDate($selectedYear, $selectedMonth, $medicineIssuing);
            $receivingData = [];
            foreach ($receiving as $item) {
                $date = Carbon::parse($item->approved_date ?? $item->created_at)->format('j');
                $receivingData[$item->medicine_name][$date] = $item->quantity;
            }

            $inventoryData = [];
            foreach ($inventory as $inv) {
                $inventoryData[$inv->medicine_name] = [
                    'total_purchase' => $inv->total_purchase ?? 0,
                    'total_issue' => $inv->total_issue ?? 0,
                    'previous_month_total' => $inv->previous_month_total ?? 0,
                    'balance' => $inv->balance ?? 0
                ];
            }
            $issuanceData = [];
            foreach ($allIssuances as $issue) {
                $date = Carbon::parse($issue->approved_date ?? $issue->created_at)->format('j');
                $issuanceData[$issue->medicine_name][$date] = $issue->quantity;
            }

            $monthName = DateTime::createFromFormat('!m', $selectedMonth)->format('F');
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $selectedMonth, $selectedYear);
            $dayHeaders = range(1, $daysInMonth);

            // Create Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->getStyle('A1:B3')->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']]
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

            $titleEndColumn = 'BR';
            $sheet->mergeCells("F1:$titleEndColumn" . "3");

            // Set the title text
            $financialStartYear = $selectedYear - 1;
            $financialEndYear = $selectedYear;


        $financialYear =  "April $financialStartYear - March $financialEndYear";

        // Set Excel Header with Financial Year
        $sheet->setCellValue("F1", "Occupational Health Center Inventory Record \nPN International Pvt Ltd \n Financial Year($financialYear)");


            // Apply styling
            $sheet->getStyle("C1:$titleEndColumn" . "3")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FF0000']]
            ]);


            // **Header Row**
            $headerStart = 'A4';
            $headerEnd = 'BR4';
            $sheet->setCellValue('A4', 'Month:');
            $sheet->setCellValue('B4', " $monthName -$selectedYear");
            $range = "$headerStart:$headerEnd";
            $sheet->getStyle($range)->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E1ACC9']]
            ]);
            // Define column ranges
            $purchaseStart = 'C';
            $purchaseEnd = Coordinate::stringFromColumnIndex(2 + $daysInMonth);
            $issueStart = Coordinate::stringFromColumnIndex(4 + $daysInMonth);
            $issueEnd = Coordinate::stringFromColumnIndex(3 + (2 * $daysInMonth));
            $grandStart = Coordinate::stringFromColumnIndex(4 + (2 * $daysInMonth));
            $grandEnd = Coordinate::stringFromColumnIndex(7 + (2 * $daysInMonth));

            // Apply Merging and Values
            $sheet->mergeCells("$purchaseStart" . "4:$purchaseEnd" . "4");
            $sheet->setCellValue("$purchaseStart" . "4", 'Purchased Medicine Quantity (Day Wise)');

            $itemCol = Coordinate::stringFromColumnIndex(3 + $daysInMonth);
            $sheet->setCellValue("$itemCol" . "4", 'Name of the Item');

            $sheet->mergeCells("$issueStart" . "4:$issueEnd" . "4");
            $sheet->setCellValue("$issueStart" . "4", 'Issued Medicine Quantity (Day Wise)');

            $sheet->mergeCells("$grandStart" . "4:$grandEnd" . "4");
            $sheet->setCellValue("$grandStart" . "4", 'Grant');

            // **Column Headers**

            $headerRowStart = 'A5';
            $headerRowEnd = 'BR5';
            $sheet->setCellValue('A5', 'ID');
            $sheet->setCellValue('B5', 'Name of Item');
            $range = "$headerRowStart:$headerRowEnd";
            $sheet->getStyle($range)->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '7ECFA1']]
            ]);
            $colIndex = 3;
            foreach ($dayHeaders as $day) {
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . "5", $day);
                $colIndex++;
            }

            $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . "5", 'Name of Item');
            $colIndex++;

            foreach ($dayHeaders as $day) {
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . "5", $day);
                $colIndex++;
            }

            $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . "5", 'Total Purchase');
            $colIndex++;
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . "5", 'Total Issue');
            $colIndex++;
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . "5", 'Previous Month Total');
            $colIndex++;
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . "5", 'Balance');

            // **Data Population**
            $row = 6;
            $id = 1;
            foreach ($medicine as $med) {
                $sheet->setCellValue("A$row", $id);
                $sheet->setCellValue("B$row", $med->medicine_name);
                $colIndex = 3;

                foreach ($dayHeaders as $day) {
                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $receivingData[$med->medicine_name][$day] ?? 0);
                    $colIndex++;
                }

                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $med->medicine_name);
                $colIndex++;

                foreach ($dayHeaders as $day) {
                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $issuanceData[$med->medicine_name][$day] ?? 0);
                    $colIndex++;
                }

                $totalPurchase = $inventoryData[$med->medicine_name]['total_purchase'] ?? 0;
                $totalIssue = $inventoryData[$med->medicine_name]['total_issue'] ?? 0;
                $previousMonthTotal = $inventoryData[$med->medicine_name]['previous_month_total'] ?? 0;
                $balance = $inventoryData[$med->medicine_name]['balance'] ?? 0;
                // Fill calculated columns
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $totalPurchase);
                $colIndex++;
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $totalIssue);
                $colIndex++;
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $previousMonthTotal);
                $colIndex++;
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $balance);

                $row++;
                $id++;
            }

            // **Export Excel**
            $fileName = 'Monthly_Inventory_Report.xlsx';
            return response()->streamDownload(function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            }, $fileName, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
        } catch (Exception $ex) {
            return back()->with('error', 'Failed to export Excel file. ' . $ex->getMessage());
        }
    }
}
