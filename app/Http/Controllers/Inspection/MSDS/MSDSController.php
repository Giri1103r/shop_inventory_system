<?php

namespace App\Http\Controllers\Inspection\MSDS;

use Exception;
use App\Models\User;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use App\Models\Master\Location;
use App\Models\Master\Department;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\Inspection\MSDS\MSDSEmail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\SimpleExcel\SimpleExcelWriter;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Models\Inspection\MSDS\MSDSDetails;
use App\Models\Inspection\MSDS\MSDSCheckList;
use App\Models\Inspection\MSDS\MSDSStatusLog;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Http\Controllers\Admin\AdminController;
use App\Models\Inspection\MSDS\Master\Chemical;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\MSDS\Master\NFARating;
use App\Models\Inspection\MSDS\MSDSSignatureUpload;
use App\Models\Inspection\MSDS\Master\NFARatingValue;
use App\Models\Inspection\MSDS\MSDS;

class MSDSController extends Controller
{

    private $msdsDetails;
    private $units;
    private $departments;
    private $locations;
    private $chemicals;
    private $document_reference;
    private $nfarating;
    private $nfaratingvalue;
    private $msds;

    public function __construct()
    {
        $this->msdsDetails = new MSDSDetails();
        $this->units = new Unit();
        $this->departments = new Department();
        $this->locations = new Location();
        $this->chemicals = new Chemical();
        $this->document_reference = new InspectionStaticDocno();
        $this->nfarating = new NFARating();
        $this->nfaratingvalue = new NFARatingValue();
        $this->msds = new MSDS();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->msds->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('created_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('issue_date', function ($row) {
                            return Displaydateformat($row->issue_date);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('msds/view/' . encryptId($row->id)) . '"   class="view-icon me-1" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('msds/generalpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                                        <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                                    </a>';
                            $btn .= '<a href="' . admin_url('msds/generalExcel/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                                        <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i>
                                    </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'issue_date', 'created_by'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);
                    return $datatables;
                } catch (Exception $ex) {
                    report($ex);
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }

        $locations = $this->locations->getLocationName();
        $data = [
            'locations' => $locations,
        ];

        return view('inspection.msds.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $document_no = $this->document_reference->selectUsingName('MSDS');
            $chemicals = $this->chemicals->getChemicals();
            $locations = $this->locations->getLocationName();
            $units = $this->units->getunit();
            $departments = $this->departments->getdepartment();
            $nfaratings = $this->nfarating->getNFArating();
            $nfaratingvalues = $this->nfaratingvalue->getNFARatingValue();
            $data = [
                'document_no' => $document_no,
                'chemicals' => $chemicals,
                'locations' => $locations,
                'units' => $units,
                'departments' => $departments,
                'nfaratings' => $nfaratings,
                'nfaratingvalues' => $nfaratingvalues,
            ];
            return view('inspection.msds.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('msds/list'));
        }
    }

    public function Store(Request $request)
    {

        try {
            try {
                $msds = $this->msds->store();
                $msdsdetails = $this->msdsDetails->store($msds->id);
                Session::flash('success', __('Your data has been created successfully'));
            } catch (Exception $ex) {

                Session::flash('error',  __('common.message_error'));
                return redirect(admin_url('msds/list'));
            }
            return redirect(admin_url('msds/list'));
        } catch (Exception $ex) {

            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('msds/list'));
        }
    }

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $item_code = $request->item_code;
            $name_of_chemical = $request->name_of_chemical;

            $id = $request->id;

            if (empty($id)) {
                $isUnique = $this->msdsDetails->uniqueCheck($item_code, $name_of_chemical);
            } else {
                $id = decryptId($id);

                $isUnique = $this->msdsDetails->existUniqueCheck($item_code, $id);
            }

            if ($isUnique->count()) {
                return Response::json(false);
            }
            return Response::json(true);
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $msds = $this->msds->find($id);
                $inspection_details = $this->msdsDetails->getDetails($msds->id);
                $document_no = $this->document_reference->selectOne($msds->document_reference_id);

                $data = array(
                    'msds' => $msds,
                    'inspection_details' => $inspection_details,
                    'document_no' => $document_no,
                );
            }
            return view('inspection.msds.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('msds/list'));
        }
    }


    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->msds->exportdata();
            $document_no = $this->document_reference->selectUsingName('MSDS');


            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } elseif (count($allData) > 20) {
                return redirect()->back()->with('error',   __('inspection.excess_error'));
            }

            $data = array(
                'content' => $allData,
                'document_no' => $document_no,
                'pagetitle' => "MSDS Details",
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

            $view = view('inspection.msds.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "MSDS.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('msds/list'));
        }
    }


    public function ExportExcel(Request $request)
    {
        try {
            $allData = $this->msdsDetails->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $document_no = $this->document_reference->selectUsingName('MSDS');
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $row = 1;
            $i = 1;

            foreach ($allData as $data) {

                $logoPath = public_path('assets/images/logo-dark.png');
                if (file_exists($logoPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Logo');
                    $drawing->setPath($logoPath);
                    $drawing->setCoordinates('A' . $row);
                    $drawing->setOffsetX(10);
                    $drawing->setWidth(100);
                    $drawing->setHeight(50);
                    $drawing->setWorksheet($sheet);
                }

                $sheet->mergeCells("A{$row}:B" . ($row + 2));
                $sheet->mergeCells("C{$row}:L" . ($row + 2));
                $sheet->setCellValue("C{$row}", "Chemical (MSDS) Master List PN International Pvt.Ltd.");
                $sheet->getStyle("A{$row}:O" . ($row + 2))->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                ]);

                $docLabels = [
                    'M1:N1' => 'Doc. No.',
                    'M2:N2' => 'Issue Dt.',
                    'M3:N3' => 'Rev. & Dt.',
                ];

                foreach ([0, 1, 2] as $idx) {
                    $labelRow = $row + $idx;
                    $labelCell = "M{$labelRow}";
                    $mergeRange = "M{$labelRow}:N{$labelRow}";
                    $sheet->mergeCells($mergeRange)->setCellValue($labelCell, array_values($docLabels)[$idx]);
                    $sheet->getStyle($mergeRange)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 10],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                }

                $sheet->setCellValue("O{$row}", $document_no->doc_no);
                $sheet->setCellValue("O" . ($row + 1), Displaydateformat($document_no->issue_date));
                $sheet->setCellValue("O" . ($row + 2), $document_no->rev_dt);

                $sheet->getStyle("O{$row}:O" . ($row + 2))->applyFromArray([
                    'font' => ['size' => 10],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $headerRow = $row + 3;
                $sheet->mergeCells("A{$headerRow}:C{$headerRow}")->setCellValue("A{$headerRow}", 'Sr. No');
                $sheet->mergeCells("D{$headerRow}:F{$headerRow}")->setCellValue("D{$headerRow}", 'Item Code');
                $sheet->mergeCells("G{$headerRow}:I{$headerRow}")->setCellValue("G{$headerRow}", 'Name Of Chemical');
                $sheet->mergeCells("J{$headerRow}:L{$headerRow}")->setCellValue("J{$headerRow}", 'MSDS Available Status');
                $sheet->mergeCells("M{$headerRow}:O{$headerRow}")->setCellValue("M{$headerRow}", 'Remarks');

                $sheet->getStyle("A{$headerRow}:O{$headerRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'fill' => ['fillType' => Fill::FILL_SOLID],
                ]);

                $dataRow = $headerRow + 1;

                $sheet->mergeCells("A{$dataRow}:C{$dataRow}")->setCellValue("A{$dataRow}", 1); // Always 1 for each block
                $sheet->mergeCells("D{$dataRow}:F{$dataRow}")->setCellValue("D{$dataRow}", $data->item_code ?? '');
                $sheet->mergeCells("G{$dataRow}:I{$dataRow}")->setCellValue("G{$dataRow}", $data->name_of_chemical ?? '');
                $sheet->mergeCells("J{$dataRow}:L{$dataRow}");

                $status = strtoupper($data->msds_availability_status ?? '');
                switch ($status) {
                    case '1':
                        $symbol = '✓';
                        $color = '008000';
                        break;
                    case '2':
                        $symbol = 'X';
                        $color = 'FF0000';
                        break;
                    case 'null':
                        $symbol = 'N/A';
                        $color = '808080';
                        break;
                    default:
                        $symbol = '-';
                        $color = '808080';
                        break;
                }

                $sheet->setCellValue("J{$dataRow}", $symbol);
                $sheet->getStyle("J{$dataRow}:L{$dataRow}")->applyFromArray([
                    'font' => ['name' => 'Segoe UI Symbol', 'color' => ['rgb' => $color], 'bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sheet->mergeCells("M{$dataRow}:O{$dataRow}")->setCellValue("M{$dataRow}", $data->remark ?? '');
                $sheet->getStyle("A{$dataRow}:O{$dataRow}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $row = $dataRow + 5;
                $i++;
            }

            foreach (range('A', 'O') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $fileName = 'MSDS.xlsx';
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (\Exception $e) {
            report($e);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('msds/list'));
        }
    }


    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $msds = $this->msds->selectOne($id);
                $inspection_details = $this->msdsDetails->getDetails($msds->id);
                $document_no = $this->document_reference->selectUsingName('MSDS');

                $data = [
                    'msds' => $msds,
                    'inspection_details' => $inspection_details,
                    'pagetitle' => "MSDS Details",
                    'document_no' => $document_no,
                ];
            }

            $property = [
                'tempDir' => 'public/pdf/temp/',
                'mode' => 'c',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
            ];

            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';

            $html = view('inspection.msds.generalpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "MSDS Details.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('msds/list'));
        }
    }


    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $msds = $this->msds->selectOne($id);
            $inspection_details = $this->msdsDetails->getDetails($msds->id);
            $document_no = $this->document_reference->selectUsingName('MSDS');

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $logoPath = public_path('assets/images/logo-dark.png');
            if (file_exists($logoPath)) {
                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setPath($logoPath);
                $drawing->setCoordinates('A1');
                $drawing->setOffsetX(10);
                $drawing->setWidth(100);
                $drawing->setHeight(50);
                $drawing->setWorksheet($sheet);
            }

            $sheet->mergeCells('A1:B3');
            $sheet->mergeCells('C1:L3');
            $sheet->setCellValue('C1', "Chemical (MSDS) Master List PN International Pvt.Ltd.");
            $sheet->getStyle('C1')->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $docHeaders = ['M1:N1' => 'Doc. No.', 'M2:N2' => 'Issue Dt.', 'M3:N3' => 'Rev. & Dt.'];
            foreach ($docHeaders as $cellRange => $label) {
                $startCell = explode(':', $cellRange)[0];
                $sheet->mergeCells($cellRange)->setCellValue($startCell, $label);
                $sheet->getStyle($cellRange)->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
            }
            $sheet->setCellValue("O1", $document_no->doc_no ?? '');
            $sheet->setCellValue("O2", Displaydateformat($document_no->issue_date ?? ''));
            $sheet->setCellValue("O3", $document_no->rev_dt ?? '');
            $sheet->getStyle("M1:O3")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);



            $sheet->getStyle('A4:O4')->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'D9E1F2']]
            ]);

            $sheet->mergeCells('A4:E4')->setCellValue('A4', 'Location: ' . getLocationName($msds->location_id));
            $sheet->mergeCells('F4:J4')->setCellValue('F4', 'Department: ' . getDepartment($msds->department_id));
            $sheet->mergeCells('K4:O4')->setCellValue('K4', 'Unit: ' . getUnitName($msds->unit_id));

            $sheet->getStyle('A5:O5')->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'D9E1F2']]
            ]);

            $sheet->mergeCells('A5:B5')->setCellValue('A5', 'Sr. No');
            $sheet->setCellValue('C5', 'Item Code');
            $sheet->mergeCells('D5:F5')->setCellValue('D5', 'Name Of Chemical');
            $sheet->setCellValue('G5', 'Storage Capacity');
            $sheet->setCellValue('H5', 'NPFA Rating Type');
            $sheet->setCellValue('I5', 'NPFA Rating');
            $sheet->mergeCells('J5:L5')->setCellValue('J5', 'MSDS Availability Status');
            $sheet->mergeCells('M5:O5')->setCellValue('M5', 'Remarks');

            $row = 6;
            foreach ($inspection_details as $index => $msdsDetails) {
                $sheet->mergeCells("A{$row}:B{$row}")->setCellValue("A{$row}", $index + 1);
                $sheet->setCellValue("C{$row}", $msdsDetails->item_code ?? '');
                $sheet->mergeCells("D{$row}:F{$row}")->setCellValue("D{$row}", getChemicalName($msdsDetails->name_of_chemical) ?? '');
                $sheet->setCellValue("G{$row}", $msdsDetails->storage_capacity ?? '');
                $sheet->setCellValue("H{$row}", getNFARating($msdsDetails->nfa_rating) ?? '');
                $sheet->setCellValue("I{$row}", $msdsDetails->nfa_rating_value ?? '');

                // MSDS Status
                $status = strtoupper($msdsDetails->msds_availability_status ?? '');
                switch ($status) {
                    case '1':
                    case 'YES':
                        $symbol = '✓';
                        $color = '008000';
                        break;
                    case '2':
                    case 'NO':
                        $symbol = 'X';
                        $color = 'FF0000';
                        break;
                    case 'N/A':
                    case 'NULL':
                        $symbol = 'N/A';
                        $color = '808080';
                        break;
                    default:
                        $symbol = '-';
                        $color = '808080';
                        break;
                }

                $sheet->mergeCells("J{$row}:L{$row}")->setCellValue("J{$row}", $symbol);
                $sheet->getStyle("J{$row}:L{$row}")->applyFromArray([
                    'font' => ['color' => ['rgb' => $color], 'bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sheet->mergeCells("M{$row}:O{$row}")->setCellValue("M{$row}", $msdsDetails->remark ?? '');

                $sheet->getStyle("A{$row}:O{$row}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $row++;
            }

            foreach (range('A', 'O') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $fileName = 'MSDS.xlsx';
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (\Exception $e) {
            report($e);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('msds/list'));
        }
    }


    public function GetUnit(Request $request)
    {
        $location = decryptId($request->location);
        try {
            $unit = $this->units->getUnitBasedLocation($location);
            return response()->json([
                'unit' => $unit->map(function ($unit) {
                    return [
                        'id' => encryptId($unit->id),
                        'unit' => getUnitname($unit->id),
                    ];
                })
            ]);
        } catch (Exception $ex) {
            report($ex);
            return response()->json([
                'employee' => [],
                'message' => 'Failed to retrieve Unit.',
            ], 500);
        }
    }
    public function getDepartment(Request $request)
    {
        $location = decryptId($request->location);
        $unit = decryptId($request->unit);
        try {
            $department = $this->departments->getDepartmentBasedUnit($location, $unit);
            return response()->json([
                'department' => $department->map(function ($department) {
                    return [
                        'id' => encryptId($department->id),
                        'department_name' => getDepartment($department->id),
                    ];
                })
            ]);
        } catch (Exception $ex) {
            report($ex);
            return response()->json([
                'employee' => [],
                'message' => 'Failed to retrieve Department.',
            ], 500);
        }
    }
}
