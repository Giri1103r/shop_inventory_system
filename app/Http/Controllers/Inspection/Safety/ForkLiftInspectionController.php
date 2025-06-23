<?php

namespace App\Http\Controllers\Inspection\Safety;

use Exception;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use App\Models\Master\Department;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\SimpleExcel\SimpleExcelWriter;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\Mail\Inspection\Safety\SafetyInspection;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Safety\SignatureUpload;
use App\Models\Inspection\Safety\ForkLiftInspection;
use App\Models\Inspection\Safety\ForkliftInspectionDetails;
use App\Models\Inspection\Safety\SafetyStatusLog;

class ForkLiftInspectionController extends Controller
{
    private $forklift;
    private $observation_details;
    private $unit;
    private $department;
    private $signature;
    private $document_reference;
    private $statusLog;


    public function __construct()
    {
        $this->forklift = new ForkLiftInspection();
        $this->observation_details = new ForkliftInspectionDetails();
        $this->unit = new Unit();
        $this->department = new Department();
        $this->signature = new SignatureUpload();
        $this->document_reference = new InspectionStaticDocno();
        $this->statusLog = new SafetyStatusLog();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->forklift->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('observation_status', function ($row) {
                            switch ($row->observation_status) {
                                case OBSERVATION_PENDING:
                                    $text = "<span class='badge bg-primary rounded' style='font-size: 1.0em;'>WAITING FOR EHS HEAD VERIFICATION</span>";
                                    break;
                                case OBSERVATION_REJECTED:
                                    $text = "<span class='badge bg-danger rounded' style='font-size: 1.0em;'>REJECTED BY EHS HEAD</span>";
                                    break;
                                case OBSERVATION_APPROVED:
                                    $text = "<span class='badge bg-success rounded' style='font-size: 1.0em;'>APPROVED BY EHS HEAD</span>";
                                    break;
                                default:
                                    $text = "<span class='badge rounded-pill text-bg-warning'>Unknown</span>";
                            }
                            return $text;
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('safety/forklift-inspection/view/' . encryptId($row->inspection_id)) . '"   class="view-icon me-1" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('safety/forklift-inspection/exportViewPdf/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="PDF">
                    <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                </a>';

                            $btn .= '<a href="' . admin_url('safety/forklift-inspection/generalexcel/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="EXCEL">
                <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i>
             </a>';

                            if ($row->observation_status == OBSERVATION_PENDING && (isAdmin() || CheckUserRole(ROLE_EHS_HEAD))) {
                                $btn .= '<a href="' . admin_url('safety/forklift-inspection/approval/' . encryptId($row->inspection_id)) . '" class="me-1" title="' . __('inspection.approval') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            return $btn;
                        })
                        ->addColumn('inspection_created_at', function ($row) {
                            return Displaydateformat($row->inspection_created_at);
                        })
                        ->addColumn('issue_date', function ($row) {
                            return Displaydateformat($row->issue_date);
                        })
                        ->addColumn('date_of_inspection', function ($row) {
                            return Displaydateformat($row->inspection_date);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'observation_status', 'date_of_inspection', 'issue_date'])
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
        $data = array();
        return view('inspection.Safety.forklift_inspection.list', $data);
    }


    public function Add(Request $request)
    {
        try {
            $unit = $this->unit->getunit();
            $departments = $this->department->getdepartment();
            $document_no = $this->document_reference->selectUsingName('ForliftInspectionReport');

            $data = array(
                'unit' => $unit,
                'departments' => $departments,
                'document_no' => $document_no,
            );
            return view('inspection.Safety.forklift_inspection.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('safety/forklift-inspection/list'));
        }
    }


    public function Store(Request $request)
    {
        try {

            $rules = [
                'doc_no' => 'required',
                'issue_date' => 'required',
                'inspection_date' => 'required',
                'department.*' => 'required',
                'unit.*' => 'required',
                'identification_no.*' => 'required',
                'observation.*' => 'required',
                'corrective_action.*' => 'required',
                'date_of_compliance.*' => 'required',
                'observation_status.*' => 'required',
                'remarks.*' => 'required',
                'emp_id.*' => 'required',


            ];

            $messages = [
                'doc_no.required' => 'Document number is required.',
                'issue_date.required' => 'Issue Date is Required',
                'inspection_date.required' => 'Inspection  Date is Required',
                'identification_no.required' => 'Identification Number is Required',
                'department.*' => 'Department is Required',
                'unit.*' => 'Unit is Required',
                'identification_no.*' => 'Identfication Number is Required',
                'observation.*' => 'Observation is Required',
                'corrective_action.*' => 'Coreective and Preventive Action is Required',
                'date_of_compliance.*' => 'Date of Compliance is Required',
                'observation_status.*' => 'Observation Status is Required',
                'emp_id.*' => 'Employee is Required',
                'remarks.*' => 'Remarks is Required',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $forklift_observation =  $this->forklift->Store();
            $forklift_observation_details = $this->observation_details->store($forklift_observation->id);
            // $signature_update = $this->signature->signatureUpload(FORKLIFT_INSPECTION, $forklift_observation->id);

            $ehsOfficer = GetEHSHead();
            if (!empty($ehsOfficer)) {
                $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
                $mailsubject = 'FORKLIFT INSPECTION';
                $notificationData = array(
                    'notification_type' => SAFETY_INSPECTION,
                    'module_type' => 3,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => "FORKLIFT INSPECTION - Observation Has been Created",
                        'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                        'id' => $forklift_observation->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('safety/forklift-inspection/view/' . encryptId($forklift_observation->id)),
                    'assigned_user' => array_to_string($ehsOfficers),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                $title = 'FORKLIFT INSPECTION - Observation has been Created';
                foreach ($ehsOfficers as $user) {
                    $email_id = getUseremail($user);
                    $url = admin_url('safety/forklift-inspection/approval/' . encryptId($forklift_observation->id) . '/ehs');
                    $details = array(
                        'safety_type' => 'Forklift Inspection',
                        'email' => $email_id,
                        'mail_subject' => $mailsubject,
                        'title' => $title,
                        'url' => $url,
                        'data' => $forklift_observation
                    );
                    Mail::to($email_id)->queue(new SafetyInspection($details));
                }
            }

            $insert_array = [
                'type' => FORKLIFT_INSPECTION,
                'inspection_id' => $forklift_observation->id,
                'from_status' => 0,
                'to_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'created_by' => Auth::id(),
            ];
            $this->statusLog->create($insert_array);


            Session::flash('success', 'Forklift Inspection added successfully!');
            return redirect(admin_url('safety/forklift-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('safety/forklift-inspection/list'));
        }
    }

    public function View(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_details = $this->forklift->selectOne($id);
            $inspection = $this->observation_details->GetDetails($inspection_details->id);
            $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);
            $type = FORKLIFT_INSPECTION;
            $status_log = $this->statusLog->selectOne($id, $type);
            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
                'document_no' => $document_no,
                'status_log' => $status_log
            );

            return view('inspection.Safety.forklift_inspection.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('safety/forklift-inspection/list'));
        }
    }


    public function Approval(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $inspection_details = $this->forklift->selectOne($id);
            $inspection = $this->observation_details->GetDetails($inspection_details->id);
            $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);
            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
                'document_no' => $document_no,
            );

            return view('inspection.Safety.forklift_inspection.approval', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('safety/forklift-inspection/list'));
        }
    }



    public function approvalSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->capa_remarks;
            $eye_wash_inspection = $this->forklift->approvalSubmit($id, $status, $remarks);
            $inspection_details = $this->forklift->selectOne($id);
            // $signature_update = $this->signature->signatureUpload(FORKLIFT_INSPECTION, $id);
            $created_by = [$inspection_details->created_by];
            if ($status == 1) {
                $message = 'FORKLIFT INSPECTION - OBSERVATION APPROVED';
                $to_status = OBSERVATION_APPROVED;
            } else {
                $message = 'FORKLIFT INSPECTION - OBSERVATION REJECTED';
                $to_status = OBSERVATION_REJECTED;
            }
            $web_link =   admin_url('safety/forklift-inspection/view/' . encryptId($inspection_details->id));
            $mailsubject = 'FORKLIFT INSPECTION';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => $message,
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $inspection_details->id,
                    'module' => 1,
                )),
                'web_link' =>  $web_link,
                'assigned_user' => array_to_string($created_by),
                'created_by' => Auth::id(),
            );

            notificationSave($notificationData);
            $insert_array = [
                'type' => FORKLIFT_INSPECTION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'to_status' => $to_status,
                'remarks' => $remarks,
                'approved_by' => Auth::id(),
            ];
            $this->statusLog->create($insert_array);

            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('safety/forklift-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('safety/forklift-inspection/list'));
        }
    }

    public function GetDepartment(Request $request)
    {
        try {
            $department = $this->department->getAlldepartment();
            return response()->json($department);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['error' => 'Please try again after sometimes'], 406);
        }
    }
    public function GetUnit(Request $request)
    {
        try {
            $unit = $this->unit->getAllUnit();
            return response()->json($unit);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['error' => 'Please try again after sometimes'], 406);
        }
    }

    public function ExportExcel(Request $request)
    {

        try {
            $allData = $this->forklift->exportdata();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $row = 1;

            foreach ($allData as $datas) {
                $startRow = $row;
                $firstData = $datas->first();
                $document_no = $this->document_reference->selectOne($datas[0]->document_reference_id);

                $sheet->mergeCells("A{$row}:C" . ($row + 2));
                $sheet->getStyle("A{$row}:C" . ($row + 2))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
                ]);

                $logoPath = public_path('assets/images/logo-dark.png');
                if (file_exists($logoPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Logo');
                    $drawing->setPath($logoPath);
                    $drawing->setCoordinates("B{$row}");
                    $drawing->setOffsetX(25);
                    $drawing->setOffsetY(10);
                    $drawing->setWidth(90);
                    $drawing->setHeight(50);
                    $drawing->setWorksheet($sheet);
                }

                $sheet->mergeCells("D{$row}:H" . ($row + 2));
                $sheet->setCellValue("D{$row}", 'FORKLIFT INSPECTION CHECKLIST .');
                $sheet->getStyle("D{$row}:H" . ($row + 2))->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $labelMapStart = $row;
                $labelMap = [
                    ["I{$labelMapStart}", 'Doc. No.', $document_no->doc_no],
                    ["I" . ($labelMapStart + 1), 'Issue Dt.', Displaydateformat($document_no->issue_date)],
                    ["I" . ($labelMapStart + 2), 'Rev. & Dt.', $document_no->rev_dt],
                ];

                foreach ($labelMap as [$labelCell, $label, $data]) {
                    $dataCell = str_replace('I', 'J', $labelCell);
                    $sheet->setCellValue($labelCell, $label);
                    $sheet->setCellValue($dataCell, $data);

                    $sheet->getStyle("{$labelCell}:{$dataCell}")->applyFromArray([
                        'font' => ['bold' => true],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                        'wrapText' => true,
                    ]);
                }

                $row += 3;

                $inspection_date = $firstData->inspection_date ? Displaydateformat($firstData->inspection_date) : 'Date Not Available';
                $sheet->mergeCells("A{$row}:J{$row}");
                $sheet->setCellValue("A{$row}", "Date of Inspection:- " . $inspection_date);
                $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'wrapText' => true,
                ]);
                $row++;

                $headers = [
                    'A' => 'SR.NO',
                    'B' => 'DEPARTMENT',
                    'C' => 'UNIT',
                    'D' => 'IDENTIFICATION NUMBER/SERIAL NUMBER',
                    'E' => 'OBSERVATION',
                    'F' => 'CORRECTIVE AND PREVENTIVE ACTION',
                    'G' => 'RESPONSIBILITY',
                    'H' => 'DATE OF COMPLIANCE',
                    'I' => 'STATUS',
                    'J' => 'REMARK',
                ];
                foreach ($headers as $col => $text) {
                    $sheet->setCellValue("{$col}{$row}", $text);
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'wrapText' => true,
                ]);
                $sheet->getRowDimension($row)->setRowHeight(40);
                $row++;

                $sr = 1;
                foreach ($datas as $detail) {
                    $sheet->setCellValue("A{$row}", $sr++);
                    $sheet->setCellValue("B{$row}", getDepartment($detail['department_id'] ?? ''));
                    $sheet->setCellValue("C{$row}", getUnitname($detail['unit_id'] ?? ''));
                    $sheet->setCellValue("D{$row}", $detail['identification_no'] ?? '');
                    $sheet->setCellValue("E{$row}", $detail['observation'] ?? '');
                    $sheet->setCellValue("F{$row}", $detail['correction_preventive_action'] ?? '');
                    $sheet->setCellValue("G{$row}", getUsername($detail['responsibility'] ?? ''));
                    $sheet->setCellValue("H{$row}", DBdateformat($detail['date_of_compliance'] ?? ''));
                    $status = ($detail['observation_status'] ?? '') == 1 ? 'Open' : 'Closed';
                    $sheet->setCellValue("I{$row}", $status);
                    $sheet->setCellValue("J{$row}", $detail['remarks'] ?? '');

                    $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'wrapText' => true,
                    ]);
                    $sheet->getRowDimension($row)->setRowHeight(-1);
                    $row++;
                }

                $signatureRowStart = $row;
                $sheet->getRowDimension($signatureRowStart)->setRowHeight(20);

                // $prepared_by_signature = GetSafetySignature($detail->checked_by, $detail->safety_id, FORKLIFT_INSPECTION);
                // $verified_by_signature = GetSafetySignature($detail->verified_by, $detail->safety_id, FORKLIFT_INSPECTION);

                $preparedBy = getUsername($firstData->created_by);
                $preparedByText = !empty($preparedBy) ? $preparedBy : "Inspection has not been prepared yet";

                $verifiedBy = getUsername($firstData->verified_by);
                $verifiedByText = !empty($verifiedBy) ? $verifiedBy : "Inspection has not been verified yet";

                $sheet->mergeCells("A{$signatureRowStart}:E{$signatureRowStart}");
                $sheet->getStyle("A{$signatureRowStart}:E{$signatureRowStart}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'wrapText' => true,
                ]);
                $sheet->setCellValue("A{$signatureRowStart}", "Prepared By: " . $preparedByText);

                $sheet->mergeCells("F{$signatureRowStart}:J{$signatureRowStart}");
                $sheet->getStyle("F{$signatureRowStart}:J{$signatureRowStart}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'wrapText' => true,
                ]);
                $sheet->setCellValue("F{$signatureRowStart}", "Verified By: " . $verifiedByText);


                // if (file_exists($prepared_by_signature)) {
                //     $drawing = new Drawing();
                //     $drawing->setName('Prepared By Signature');
                //     $drawing->setPath($prepared_by_signature);
                //     $drawing->setCoordinates("D{$signatureRowStart}");
                //     $drawing->setOffsetX(5);
                //     $drawing->setOffsetY(5);
                //     $drawing->setHeight(40);
                //     $drawing->setWorksheet($sheet);
                // } else {
                //     $sheet->setCellValue("A{$signatureRowStart}", "Prepared By:\nSignature not available");
                // }


                // if (file_exists($verified_by_signature)) {
                //     $drawing = new Drawing();
                //     $drawing->setName('Verified By Signature');
                //     $drawing->setPath($verified_by_signature);
                //     $drawing->setCoordinates("G{$signatureRowStart}");
                //     $drawing->setOffsetX(5);
                //     $drawing->setOffsetY(5);
                //     $drawing->setHeight(40);
                //     $drawing->setWorksheet($sheet);
                // } else {
                //     $sheet->setCellValue("F{$signatureRowStart}", "Verified By:Signature not available");
                // }

                $sheet->getStyle("A{$startRow}:P{$row}")->applyFromArray([
                    'borders' => [
                        'top'    => ['borderStyle' => Border::BORDER_THICK],
                        'bottom' => ['borderStyle' => Border::BORDER_THICK],
                        'left'   => ['borderStyle' => Border::BORDER_THICK],
                        'right'  => ['borderStyle' => Border::BORDER_THICK],
                    ],
                ]);
                $row += 5;
            }

            $fileName = 'Forklift_Inspection.xlsx';
            $filePath = storage_path("app/public/$fileName");
            $writer = new Xlsx($spreadsheet);
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('safety/forklift-inspection/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->forklift->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } else if (count($allData) > 20) {
                return redirect()->back()->with('error', __('inspection.excess_error'));
            }

            $data = array(
                'content' => $allData,
                'pagetitle' => "Forklift Inspection",
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

            $view = view('inspection.Safety.forklift_inspection.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Forklift Inspection.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('safety/forklift-inspection/list'));
        }
    }

    public function exportViewPdf(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $inspection_details = $this->forklift->selectOne($id);
                $current_month_inspection = $this->observation_details->GetDetails($inspection_details->id);
                $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);
                $type = FORKLIFT_INSPECTION;
                $status_log = $this->statusLog->selectOne($id, $type);

                $data = [
                    'inspection_details' => $inspection_details,
                    'inspection' => $current_month_inspection,
                    'pagetitle' => "Forklift Inspection",
                    'document_no' => $document_no,
                    'status_log' => $status_log,
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

            $html = view('inspection.Safety.forklift_inspection.viewPdf', $data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "Forklift Inspection.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('safety/forklift-inspection/list'));
        }
    }
    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $inspection_details = $this->forklift->selectOne($id);
            $current_month_inspection = $this->observation_details->GetDetails($inspection_details->id);
            $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);

            // $prepared_by_signature = GetSafetySignature($inspection_details->created_by, $inspection_details->id, FORKLIFT_INSPECTION);
            // $verified_by_signature = GetSafetySignature($inspection_details->updated_by, $inspection_details->id, FORKLIFT_INSPECTION);

            $sheet->mergeCells("A1:C3");
            $sheet->getStyle("A1:C3")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);
            $logoPath = public_path('assets/images/logo-dark.png');
            if (file_exists($logoPath)) {
                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setPath($logoPath);
                $drawing->setCoordinates("B1");
                $drawing->setOffsetX(25);
                $drawing->setOffsetY(10);
                $drawing->setWidth(90);
                $drawing->setHeight(50);
                $drawing->setWorksheet($sheet);
            }

            foreach (range('A', 'J') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(false);
                $sheet->getColumnDimension($col)->setWidth(25);
                $sheet->getStyle($col)->getAlignment()->setWrapText(true);
            }

            $sheet->mergeCells('D1:H3');
            $sheet->setCellValue('D1', 'FORKLIFT INSPECTION CHECKLIST .');
            $sheet->getStyle('D1:H3')->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $labelMap = [
                ['I1', 'Doc. No.', $document_no->doc_no],
                ['I2', 'Issue Dt.', Displaydateformat($document_no->issue_date)],
                ['I3', 'Rev. & Dt.', $document_no->rev_dt],
            ];

            foreach ($labelMap as [$labelCell, $label, $data]) {
                $dataCell = str_replace('I', 'J', $labelCell);
                $sheet->setCellValue($labelCell, $label);
                $sheet->setCellValue($dataCell, $data);

                $sheet->getStyle("$labelCell:$dataCell")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'wrapText' => true,
                ]);
            }

            $inspection_date = $inspection_details->inspection_date ? Displaydateformat($inspection_details->inspection_date) : 'Date Not Available';
            $sheet->mergeCells("A4:J4");
            $sheet->setCellValue("A4", "Date of Inspection:- " . $inspection_date);
            $sheet->getStyle("A4:J4")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'wrapText' => true,
            ]);

            $headers = [
                'A5' => 'SR.NO',
                'B5' => 'DEPARTMENT',
                'C5' => 'UNIT',
                'D5' => 'IDENTIFICATION NUMBER/SERIAL NUMBER',
                'E5' => 'OBSERVATION',
                'F5' => 'CORRECTIVE AND PREVENTIVE ACTION',
                'G5' => 'RESPONSIBILITY',
                'H5' => 'DATE OF COMPLIANCE',
                'I5' => 'STATUS',
                'J5' => 'REMARK',
            ];
            foreach ($headers as $cell => $text) {
                $sheet->setCellValue($cell, $text);
            }
            $sheet->getStyle("A5:J5")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'wrapText' => true,
            ]);

            $sheet->getRowDimension(5)->setRowHeight(40);


            $row = 6;
            $sr = 1;
            foreach ($current_month_inspection as $detail) {
                $sheet->setCellValue("A$row", $sr);
                $sheet->setCellValue("B$row", getDepartment($detail['department_id'] ?? ''));
                $sheet->setCellValue("C$row", getUnitname($detail['unit_id'] ?? ''));
                $sheet->setCellValue("D$row", $detail['identification_no'] ?? '');
                $sheet->setCellValue("E$row", $detail['observation'] ?? '');
                $sheet->setCellValue("F$row", $detail['correction_preventive_action'] ?? '');
                $sheet->setCellValue("G$row", getUsername($detail['responsibility'] ?? ''));
                $sheet->setCellValue("H$row", DBdateformat($detail['date_of_compliance'] ?? ''));
                $status = ($detail['observation_status'] ?? '') == 1 ? 'Open' : 'Closed';
                $sheet->setCellValue("I{$row}", $status);
                $sheet->setCellValue("J$row", $detail['remarks'] ?? '');

                $sheet->getStyle("A$row:J$row")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'wrapText' => true,
                ]);
                $sheet->getRowDimension($row)->setRowHeight(-1);
                $sr++;
                $row++;
            }

            foreach (range('A', 'J') as $col) {
                $maxLength = 0;
                foreach (range(5, $row) as $r) {
                    $cellValue = $sheet->getCell("{$col}{$r}")->getValue();
                    $maxLength = max($maxLength, strlen($cellValue));
                }
                $sheet->getColumnDimension($col)->setWidth($maxLength + 5);
            }

            $signatureRowStart = $row;
            $sheet->getRowDimension($signatureRowStart)->setRowHeight(20);

            $sheet->mergeCells("A{$signatureRowStart}:E{$signatureRowStart}");
            $sheet->getStyle("A{$signatureRowStart}:E{$signatureRowStart}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,

                    'wrapText' => true,
                ],
            ]);

            $preparedBy = getUsername($inspection_details->created_by);
            $preparedByText = !empty($preparedBy) ? $preparedBy : "Inspection has not been prepared yet";
            $sheet->setCellValue("A{$signatureRowStart}", "Prepared By:" . $preparedByText);

            $verifiedBy = getUsername($inspection_details->updated_by);
            $verifiedByText = !empty($verifiedBy) ? $verifiedBy : "Inspection has not been verified yet";
            $sheet->setCellValue("F{$signatureRowStart}", "Verified By:" . $verifiedByText);


            $sheet->mergeCells("F{$signatureRowStart}:J{$signatureRowStart}");
            $sheet->getStyle("F{$signatureRowStart}:J{$signatureRowStart}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,

                    'wrapText' => true,
                ],
            ]);
            // if (file_exists($prepared_by_signature)) {
            //     $drawing = new Drawing();
            //     $drawing->setName('Signature');
            //     $drawing->setDescription('Prepared By');
            //     $drawing->setPath($prepared_by_signature);
            //     $drawing->setCoordinates("D{$signatureRowStart}");
            //     $drawing->setOffsetX(5);
            //     $drawing->setOffsetY(5);
            //     $drawing->setHeight(40);
            //     $drawing->setWorksheet($sheet);
            // } else {
            //     $sheet->setCellValue("A{$signatureRowStart}", "Prepared By:\nSignature not available");
            // }

            // if (file_exists($verified_by_signature)) {
            //     $drawing = new Drawing();
            //     $drawing->setName('Signature');
            //     $drawing->setDescription('Verified By');
            //     $drawing->setPath($verified_by_signature);
            //     $drawing->setCoordinates("G{$signatureRowStart}");
            //     $drawing->setOffsetX(5);
            //     $drawing->setOffsetY(5);
            //     $drawing->setHeight(40);
            //     $drawing->setWorksheet($sheet);
            // } else {
            //     $sheet->setCellValue("F{$signatureRowStart}", "Verified By:\nSignature not available");
            // }

            $row++;


            $fileName = 'Forklift_Inspection.xlsx';
            $filePath = storage_path("app/public/$fileName");
            $writer = new Xlsx($spreadsheet);
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            report($e);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('safety/forklift-inspection/list'));
        }
    }
}
