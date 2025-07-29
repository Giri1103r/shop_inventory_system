<?php

namespace App\Http\Controllers\Inspection\Safety;

use Exception;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use App\Models\Master\Location;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Inspection\Master\Shift;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Mail\Inspection\Safety\SafetyInspection;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Safety\SafetyStatusLog;
use App\Models\Inspection\Safety\SignatureUpload;
use App\Models\Inspection\Safety\SafetyWalkObservation;
use App\Models\Inspection\Safety\SafetyWalkObservationDetails;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

use function PHPSTORM_META\type;

class SafetyWalkObservationController extends Controller
{
    private $safety_walk;
    private $observation_details;
    private $shift;
    private $unit;
    private $location;
    private $signature;
    private $document_reference;
    private $statusLog;



    public function __construct()
    {
        $this->safety_walk = new SafetyWalkObservation();
        $this->observation_details = new SafetyWalkObservationDetails();
        $this->shift = new Shift();
        $this->unit = new Unit();
        $this->location = new Location();
        $this->signature = new SignatureUpload();
        $this->document_reference = new InspectionStaticDocno();
        $this->statusLog = new SafetyStatusLog();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->safety_walk->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('observation_status', function ($row) {
                            switch ($row->observation_status) {
                                case OBSERVATION_PENDING:
                                    $text = "<span class='badge bg-primary rounded' style='font-size: 1.0em;'>WAITING FOR EHS OFFICER VERIFICATION</span>";
                                    break;
                                case OBSERVATION_REJECTED:
                                    $text = "<span class='badge bg-danger rounded' style='font-size: 1.0em;'>REJECTED BY EHS OFFICER</span>";
                                    break;
                                case OBSERVATION_APPROVED:
                                    $text = "<span class='badge bg-success rounded' style='font-size: 1.0em;'>APPROVED BY EHS OFFICER</span>";
                                    break;
                                default:
                                    $text = "<span class='badge rounded-pill text-bg-warning'>Unknown</span>";
                            }
                            return $text;
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('safety/safety-walk-observation/view/' . encryptId($row->inspection_id)) . '"   class="view-icon me-1" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('safety/safety-walk-observation/exportViewPdf/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="PDF">
                                        <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                                    </a>';
                            $btn .= '<a href="' . admin_url('safety/safety-walk-observation/generalExcel/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="EXCEL">
                                        <i class="fas fa-file-excel" style="color: #1D6F42;" aria-hidden="true"></i>
                                    </a>';

                            if (($row->observation_status == OBSERVATION_PENDING && (isAdmin())) || ($row->observation_status == OBSERVATION_PENDING && (CheckUserRole(ROLE_EHS_OFFICER)))) {
                                $btn .= '<a href="' . admin_url('safety/safety-walk-observation/approval/' . encryptId($row->inspection_id)) . '" class="" title="' . __('inspection.approval') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            return $btn;
                        })
                        ->addColumn('inspection_created_at', function ($row) {
                            return Displaydateformat($row->inspection_created_at);
                        })
                        ->addColumn('issue_date', function ($row) {
                            return Displaydateformat($row->issue_date);
                        })
                        ->addColumn('inspection_date', function ($row) {
                            return Displaydateformat($row->date);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'observation_status', 'inspection_status', 'issue_date'])
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

        $location = $this->location->getLocationName();
        $unit = $this->unit->getUnit();
        $shifts = $this->shift->getShiftname();

        $data = array(
            'locations' => $location,
            'units' => $unit,
            'shifts' => $shifts,
        );

        return view('inspection.Safety.safety_walk_observation.list', $data);
    }


    public function Add(Request $request)
    {
        try {
            $shift = $this->shift->getShiftname();
            $unit = $this->unit->getunit();
            $locations = $this->location->getLocationName();
            $document_no = $this->document_reference->selectUsingName('SafetyWalkObservationSheet');

            $data = array(
                'shift' => $shift,
                'unit' => $unit,
                'locations' => $locations,
                'document_no' => $document_no,
            );

            return view('inspection.Safety.safety_walk_observation.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('safety/safety-walk-observation/list'));
        }
    }


    public function Store(Request $request)
    {
        try {
            $rules = [
                'doc_no' => 'required',
                'issue_date' => 'required',
                'inspection_date' => 'required',
                'shift_id' => 'required',
                'month' => 'required',
                'unit' => 'required',
                'safety_walk_taken_by' => 'required',
                'unit' => 'required',
                'location.*' => 'required',
                'exact_location.*' => 'required',
                'date_of_observation.*' => 'required',
                'observation.*' => 'required',
                'checklist_file.*' => 'required',
                'recomended_action.*' => 'required',
                'date_of_compliance.*' => 'required',
                'observation_status.*' => 'required',

                'emp_id.*' => 'required',


            ];

            $messages = [
                'doc_no.required' => 'Document number is required.',
                'issue_date.required' => 'Issue date is required.',
                'inspection_date.required' => 'Inspection date is required.',
                'shift_id.required' => 'Shift ID is required.',
                'month.required' => 'Month is required.',
                'unit.required' => 'Unit is required.',
                'safety_walk_taken_by.required' => 'Safety walk taken by is required.',
                'unit.*.required' => 'Unit is required.',
                'location.*.required' => 'Location is required.',
                'exact_location.*.required' => 'Location is required.',
                'date_of_observation.*.required' => 'Date of observation is required.',
                'observation.*.required' => 'Observation is required.',
                'checklist_file.*.required' => 'Image is required.',
                'recomended_action.*.required' => 'Recommended action is required.',
                'date_of_compliance.*.required' => 'Date of compliance is required.',
                'observation_status.*.required' => 'Observation status is required.',
                'emp_id.*.required' => 'Employee ID is required.',

            ];


            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $safety_walk_observation =  $this->safety_walk->Store();
            $inspection_id = $safety_walk_observation->id;
            $safety_walk_observation_details = $this->observation_details->store($safety_walk_observation->id);
            // $signature_update = $this->signature->signatureUpload(SAFETY_WALK_OBSERVATION, $safety_walk_observation->id);

            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            $mailsubject = 'Safety Walk Observation';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
                'module_type' => 7,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "Safety Walk Observation - Observation Has been Created",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $safety_walk_observation->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('safety/safety-walk-observation/approval/' . encryptId($safety_walk_observation->id)),
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'SAFETY INSPECTION - Observation has been Created';
            foreach ($ehsOfficers as $user) {
                $email_id = getUseremail($user);
                $url = admin_url('safety/safety-walk-observation/approval/' . encryptId($safety_walk_observation->id));
                $details = array(
                    'safety_type' => 'Safety Walk Observation',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $safety_walk_observation_details
                );
                Mail::to($email_id)->queue(new SafetyInspection($details));
            }

            $insert_array = [
                'type' => SAFETY_WALK_OBSERVATION,
                'inspection_id' => $inspection_id,
                'from_status' => 0,
                'to_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'created_by' => Auth::id(),
            ];
            $this->statusLog->create($insert_array);

            Session::flash('success', 'Safety Walk Observation added successfully!');
            return redirect(admin_url('safety/safety-walk-observation/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('safety/safety-walk-observation/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $inspection_details = $this->safety_walk->selectOne($id);
            $inspection = $this->observation_details->GetDetails($inspection_details->id);
            $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);
            $type =  SAFETY_WALK_OBSERVATION;
            $status_log = $this->statusLog->selectOne($id, $type);

            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
                'document_no' => $document_no,
                'status_log' => $status_log,
            );
            return view('inspection.Safety.safety_walk_observation.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('safety/safety-walk-observation/list'));
        }
    }


    public function Approval(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $inspection_details = $this->safety_walk->selectOne($id);
            $inspection = $this->observation_details->GetDetails($inspection_details->id);
            $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);

            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
                'document_no' => $document_no,
            );

            return view('inspection.Safety.safety_walk_observation.approval', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('safety/safety-walk-observation/list'));
        }
    }

    public function approvalSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->capa_remarks;
            $eye_wash_inspection = $this->safety_walk->approvalSubmit($id, $status, $remarks);
            $inspection_details = $this->safety_walk->selectOne($id);
            // $signature_update = $this->signature->signatureUpload(SAFETY_WALK_OBSERVATION, $id);
            $ehsOfficer = [$inspection_details->created_by];

            if ($status == 1) {
                $message = 'SAFETY WALK OBSERVATION APPROVED';
                $to_status = OBSERVATION_APPROVED;
            } else {
                $message = 'SAFETY WALK OBSERVATION REJECTED';
                $to_status = OBSERVATION_REJECTED;
            }
            $web_link =   admin_url('safety/safety-walk-observation/view/' . encryptId($inspection_details->id));
            $mailsubject = 'Safety Walk Observation';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
                'module_type' => 7,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => $message,
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $inspection_details->id,
                    'module' => 1,
                )),
                'web_link' =>  $web_link,
                'assigned_user' => array_to_string($ehsOfficer),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'SAFETY INSPECTION - Observation Status';
            $email_id = getUseremail($ehsOfficer);
            $url = admin_url('safety/safety-walk-observation/view/' . encryptId($inspection_details->id));
            $details = array(
                'safety_type' => 'Safety Walk Observation',
                'email' => $email_id,
                'mail_subject' => $mailsubject,
                'title' => $title,
                'url' => $url,
                'data' => $inspection_details
            );
            Mail::to($email_id)->queue(new SafetyInspection($details));

            $insert_array = [
                'type' => SAFETY_WALK_OBSERVATION,
                'inspection_id' => $inspection_details->id,
                'from_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'to_status' => $to_status,
                'remarks' => $remarks,
                'approved_by' => Auth::id(),
            ];
            $this->statusLog->create($insert_array);

            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('safety/safety-walk-observation/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('safety/safety-walk-observation/list'));
        }
    }

    public function ExportExcel()
    {
        try {
            $allData = $this->safety_walk->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $row = 1;

            foreach ($allData as $inspection_details) {

                $inspection_details = $inspection_details->first();
                $current_month_inspection = $this->observation_details->GetDetails($inspection_details->safety_id);

                $last_month_inspection = $this->safety_walk->GetLastMonthObservation($inspection_details->safety_id);
                $last_month_observation_details = $this->observation_details->GetLastMonthDetails($last_month_inspection);
                $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);
                // $prepared_by_signature = GetSafetySignature($inspection_details->created_by, $inspection_details->safety_id, SAFETY_WALK_OBSERVATION);
                // $verified_by_signature = GetSafetySignature($inspection_details->verified_by, $inspection_details->safety_id, SAFETY_WALK_OBSERVATION);

                $titleRow = $row;

                foreach (range('A', 'J') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                $sheet->mergeCells("A{$row}:B" . ($row + 2));
                $leftLogo = public_path('assets/images/logo-dark.png');
                if (file_exists($leftLogo)) {
                    $drawing = new Drawing();
                    $drawing->setPath($leftLogo);
                    $drawing->setCoordinates("A{$row}");
                    $drawing->setHeight(60);
                    $drawing->setWorksheet($sheet);
                }
                $sheet->getStyle("A{$row}:B" . ($row + 2))->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $sheet->mergeCells("C{$row}:F" . ($row + 2));
                $sheet->setCellValue("C{$row}", "Safety Walk Observation Sheet\n ");
                $sheet->getStyle("C{$row}:F" . ($row + 2))->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $sheet->mergeCells("G{$row}:H" . ($row + 2));
                $rightLogo = public_path('assets/images/safety_walk_logo.jpg');
                if (file_exists($rightLogo)) {
                    $drawing = new Drawing();
                    $drawing->setPath($rightLogo);
                    $drawing->setCoordinates("H{$row}");
                    $drawing->setOffsetX(5);
                    $drawing->setOffsetY(5);
                    $drawing->setHeight(50);
                    $drawing->setWorksheet($sheet);
                }

                $sheet->getStyle("F{$row}:H" . ($row + 2))->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $sheet->setCellValue("I{$row}", 'Document No.');
                $sheet->setCellValue("I" . ($row + 1), 'Issue Date');
                $sheet->setCellValue("I" . ($row + 2), 'Rev . No');

                $sheet->setCellValue("J{$row}", $document_no->doc_no ?? '');
                $sheet->setCellValue("J" . ($row + 1), $document_no->issue_date ?? '');
                $sheet->setCellValue("J" . ($row + 2), $document_no->rev_dt ?? '');

                $sheet->mergeCells("J{$row}:K{$row}");
                $sheet->mergeCells("J" . ($row + 1) . ":K" . ($row + 1));
                $sheet->mergeCells("J" . ($row + 2) . ":K" . ($row + 2));


                $sheet->getStyle("I{$row}:K" . ($row + 2))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $row += 3;

                $sheet->mergeCells("A{$row}:D{$row}")->setCellValue("A{$row}", "Date of Inspection: " . Displaydateformat($inspection_details->date));
                $sheet->mergeCells("E{$row}:F{$row}")->setCellValue("E{$row}", "Shift: " . getShift($inspection_details->shift_id));
                $sheet->mergeCells("G{$row}:J" . ($row + 1))->setCellValue("G{$row}", "Safety Walk Taken By:- " .  getUsername($inspection_details->safety_walk_taken_by));
                $sheet->getRowDimension($row)->setRowHeight(20);
                $row++;
                $sheet->mergeCells("A{$row}:D{$row}")->setCellValue("A{$row}", "Month: " . $inspection_details->month);
                $sheet->mergeCells("E{$row}:F{$row}")->setCellValue("E{$row}", "Unit: " . getUnitname($inspection_details->unit));
                $sheet->getRowDimension($row)->setRowHeight(20);

                $sheet->getStyle("A" . ($row - 1) . ":J{$row}")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $row++;

                $headers = ['Sr. No.', 'Location', 'Exact Location','Observation Date', 'Observation', 'Picture', 'Recommended Action', 'Responsibility', 'Date of Compliance', 'Status', 'Remarks'];
                $sheet->fromArray($headers, null, "A{$row}");
                $sheet->getStyle("A{$row}:K{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFEFEFEF']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(20);
                $row++;

                if (!empty($last_month_observation_details)) {
                    $sheet->mergeCells("A{$row}:K{$row}")->setCellValue("A{$row}", 'Previous Month Observations');
                    $sheet->getStyle("A{$row}:K{$row}")->applyFromArray([
                        'font' => ['bold' => true],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                    $sheet->getRowDimension($row)->setRowHeight(30);

                    $row++;

                    $sr = 1;
                    foreach ($last_month_observation_details as $group) {
                        foreach ($group as $observation) {
                            $sheet->setCellValue("A{$row}", $sr);
                            $sheet->setCellValue("B{$row}", getLocationName($observation->location));
                            $sheet->setCellValue("C{$row}", ($observation->exact_location));
                            $sheet->setCellValue("D{$row}", Displaydateformat($observation->observation_date));
                            $sheet->setCellValue("E{$row}", $observation->observation);
                            $imagePath = GetSafetyWalkImage($observation->id);
                            if (file_exists($imagePath)) {
                                $drawing = new Drawing();
                                $drawing->setPath($imagePath);
                                $drawing->setCoordinates("F{$row}");
                                $drawing->setHeight(60);
                                $drawing->setOffsetX(5);
                                $drawing->setWidth(80);
                                $drawing->setOffsetY(20);
                                $drawing->setWorksheet($sheet);
                                $sheet->getRowDimension($row)->setRowHeight(90);
                                $sheet->getColumnDimension('F')->setWidth(20);
                            } else {
                                $sheet->setCellValue("F{$row}", 'No Image');
                            }
                            $sheet->setCellValue("G{$row}", $observation->recomended_action);
                            $sheet->setCellValue("H{$row}", getUsername($observation->responsibility));
                            $sheet->setCellValue("I{$row}", $observation->date_of_compliance);
                            $sheet->setCellValue("J{$row}", $observation->observation_status == 1 ? 'Active' : ($observation->observation_status == 0 ? 'Deactive' : 'Unknown'));
                            $sheet->setCellValue("K{$row}", $observation->remarks);

                            $sheet->getStyle("A{$row}:K{$row}")->applyFromArray([
                                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                            ]);

                            $row++;
                            $sr++;
                        }
                    }
                }

                if (!empty($current_month_inspection)) {
                    $sheet->mergeCells("A{$row}:K{$row}")->setCellValue("A{$row}", 'Current Month Observations');
                    $sheet->getStyle("A{$row}:K{$row}")->applyFromArray([
                        'font' => ['bold' => true],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                    $sheet->getRowDimension($row)->setRowHeight(30);

                    $row++;

                    $sr = 1;
                    foreach ($current_month_inspection as $observation) {
                        $sheet->setCellValue("A{$row}", $sr);
                        $sheet->setCellValue("B{$row}", getLocationName($observation->location));
                        $sheet->setCellValue("C{$row}", ($observation->exact_location));
                        $sheet->setCellValue("D{$row}", Displaydateformat($observation->observation_date));
                        $sheet->setCellValue("E{$row}", $observation->observation);
                        $imagePath = GetSafetyWalkImage($observation->id);
                        if (file_exists($imagePath)) {
                            $drawing = new Drawing();
                            $drawing->setPath($imagePath);
                            $drawing->setCoordinates("F{$row}");
                            $drawing->setHeight(60);
                            $drawing->setOffsetX(5);
                            $drawing->setWidth(80);
                            $drawing->setOffsetY(20);
                            $drawing->setWorksheet($sheet);
                            $sheet->getRowDimension($row)->setRowHeight(90);
                            $sheet->getColumnDimension('F')->setWidth(20);
                        } else {
                            $sheet->setCellValue("F{$row}", 'No Image');
                        }
                        $sheet->setCellValue("G{$row}", $observation->recomended_action);
                        $sheet->setCellValue("H{$row}", getUsername($observation->responsibility));
                        $sheet->setCellValue("I{$row}", $observation->date_of_compliance);
                        $sheet->setCellValue("J{$row}", $observation->observation_status == 1 ? 'Active' : ($observation->observation_status == 0 ? 'Deactive' : 'Unknown'));
                        $sheet->setCellValue("K{$row}", $observation->remarks);
                        $sheet->getStyle("A{$row}:K{$row}")->applyFromArray([
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        ]);
                        $row++;
                        $sr++;
                    }
                }
                $signatureRowStart = $row;
                $sheet->getRowDimension($signatureRowStart)->setRowHeight(20);
                // $sheet->mergeCells("A{$signatureRowStart}:E{$signatureRowStart}");
                // if (file_exists($prepared_by_signature)) {
                //     $drawing = new Drawing();
                //     $drawing->setPath($prepared_by_signature);
                //     $drawing->setCoordinates("C{$signatureRowStart}");
                //     $drawing->setOffsetX(50);
                //     $drawing->setOffsetY(5);
                //     $drawing->setHeight(60);
                //     $drawing->setWorksheet($sheet);
                // }
                $sheet->setCellValue("A{$signatureRowStart}", "Prepared By:- " . getUsername($inspection_details->created_by));
                $sheet->mergeCells("A{$row}:E{$row}");
                $sheet->getStyle("A{$signatureRowStart}:E{$signatureRowStart}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_BOTTOM],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $sheet->mergeCells("F{$row}:K{$row}");
                $sheet->setCellValue("F{$signatureRowStart}", "Verified By:- "  . (
                    !empty(getUserName($inspection_details->verified_by))
                    ? getUserName($inspection_details->verified_by)
                    : "Inspection has not been verified yet"
                ));
                // $sheet->mergeCells("F{$signatureRowStart}:J{$signatureRowStart}");
                // if (file_exists($verified_by_signature)) {
                //     $drawing = new Drawing();
                //     $drawing->setPath($verified_by_signature);
                //     $drawing->setCoordinates("H{$signatureRowStart}");
                //     $drawing->setOffsetX(5);
                //     $drawing->setOffsetY(5);
                //     $drawing->setHeight(60);
                //     $drawing->setWorksheet($sheet);
                // } else {
                //     $sheet->setCellValue("F{$signatureRowStart}", "Not Verified Yet");
                // }
                $sheet->getStyle("F{$signatureRowStart}:K{$signatureRowStart}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_BOTTOM],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $row += 5;

                $lastRow = $signatureRowStart;

                $sheet->getStyle("A{$titleRow}:K{$lastRow}")->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_THICK,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
            }

            $writer = new Xlsx($spreadsheet);
            $fileName = 'Safety Walk Observations.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"{$fileName}\"");
            $writer->save('php://output');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect()->back();
        }
    }

    public function ExportPdf(Request $request)
    {
        try {

            $allData = $this->safety_walk->exportdata();
            $document_no = $this->document_reference->selectUsingName('SafetyWalkObservationSheet');

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $data = array(
                'content' => $allData,
                'document_no' => $document_no,
                'pagetitle' => "Safety Walk Observation",
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

            $view = view('inspection.Safety.safety_walk_observation.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Safety Walk Observation.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('safety/safety-walk-observation/list'));
        }
    }

    public function exportViewPdf(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $inspection_details = $this->safety_walk->selectOne($id);
                $current_month_inspection = $this->observation_details->GetDetails($inspection_details->id);
                $last_month_inspection = $this->safety_walk->GetLastMonthObservation($id);
                $last_month_observation_details = $this->observation_details->GetLastMonthDetails($last_month_inspection);
                $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);
                $type =  SAFETY_WALK_OBSERVATION;
                $status_log = $this->statusLog->selectOne($id, $type);

                $data = [
                    'inspection_details' => $inspection_details,
                    'inspection' => $current_month_inspection,
                    'last_month_observation_details' => $last_month_observation_details,
                    'pagetitle' => "Safety Walk Observation",
                    'document_no' => $document_no,
                    'status_log' => $status_log
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

            $html = view('inspection.Safety.safety_walk_observation.viewPdf', $data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "Safety Walk Observation.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('safety/safety-walk-observation/list'));
        }
    }

    public function generalExcel(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $inspection_details = $this->safety_walk->selectOne($id);
            $current_month_inspection = $this->observation_details->GetDetails($inspection_details->id);
            $last_month_inspection = $this->safety_walk->GetLastMonthObservation($id);
            $last_month_observation_details = $this->observation_details->GetLastMonthDetails($last_month_inspection);
            $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);
            // $prepared_by_signature = GetSafetySignature($inspection_details->created_by, $inspection_details->id, SAFETY_WALK_OBSERVATION,);
            // $verified_by_signature = GetSafetySignature($inspection_details->updated_by, $inspection_details->id, SAFETY_WALK_OBSERVATION,);


            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            foreach (range('A', 'J') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $row = 1;

            $logoPath = public_path('assets/images/logo-dark.png');
            if (file_exists($logoPath)) {
                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Company Logo');
                $drawing->setPath($logoPath);
                $drawing->setCoordinates('A1');
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawing->setHeight(60);
                $drawing->setWorksheet($sheet);
            }
            $sheet->mergeCells('A1:B3');
            $sheet->getStyle('A1:B3')->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $sheet->mergeCells('C1:F3');
            $sheet->setCellValue('C1', "Safety Walk Observation Sheet\n");
            $sheet->getStyle('C1:F3')->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $rightlogoPath = public_path('assets/images/safety_walk_logo.jpg');
            if (file_exists($rightlogoPath)) {
                $drawing = new Drawing();
                $drawing->setName('Safety Walk Logo');
                $drawing->setDescription('Safety Walk Logo');
                $drawing->setPath($rightlogoPath);
                $drawing->setCoordinates('H1');
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawing->setHeight(50);
                $drawing->setWorksheet($sheet);
            }
            $sheet->mergeCells('G1:H3');
            $sheet->getStyle('F1:H3')->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $sheet->setCellValue('I1', 'Document No.');
            $sheet->setCellValue('I2', 'Issue Date');
            $sheet->setCellValue('I3', 'Rev . No');
            $sheet->setCellValue('J1', $document_no->doc_no ?? '');
            $sheet->setCellValue('J2', $document_no->issue_date ?? '');
            $sheet->setCellValue('J3', $document_no->rev_dt ?? '');

            $sheet->mergeCells('J1:K1');
            $sheet->mergeCells('J2:K2');
            $sheet->mergeCells('J3:K3');

            $sheet->getStyle('J1:K3')->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DOUBLE]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
            ]);

            $sheet->mergeCells('A4:D4')->setCellValue('A4', "Date of Inspection: " . Displaydateformat($inspection_details->date));
            $sheet->mergeCells('E4:F4')->setCellValue('E4', "Shift: " . getShift($inspection_details->shift_id));
            $sheet->mergeCells('G4:J5')->setCellValue('G4', "Safety Walk Taken By:- " . getUsername($inspection_details->safety_walk_taken_by));
            $sheet->getRowDimension(4)->setRowHeight(20);

            $row = 5;
            $sheet->mergeCells('A5:D5')->setCellValue('A5', "Month: " . $inspection_details->month);
            $sheet->mergeCells('E5:F5')->setCellValue('E5', "Unit: " . getUnitname($inspection_details->unit));
            $sheet->getRowDimension(
                5
            )->setRowHeight(20);

            $sheet->getStyle('A4:K5')->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $row += 1;
            $headers = ['Sr. No.', 'Location', 'Exact Location', 'Observation Date', 'Observation', 'Picture', 'Recommended Action', 'Responsibility', 'Date of Compliance', 'Status', 'Remarks'];
            $sheet->fromArray($headers, null, "A{$row}");
            $sheet->getStyle("A{$row}:k{$row}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 12],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFEFEFEF']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(20);

            $row++;


            if (!empty($last_month_observation_details)) {
                $sheet->mergeCells("A{$row}:K{$row}")->setCellValue("A{$row}", 'Previous Month Observations');
                $sheet->getStyle("A{$row}:K{$row}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(20);
                $row++;

                $sr = 1;
                foreach ($last_month_observation_details as $observationGroup) {
                    foreach ($observationGroup as $observation) {
                        $sheet->setCellValue("A{$row}", $sr++);
                        $sheet->setCellValue("B{$row}", getLocationName($observation->location));
                        $sheet->setCellValue("C{$row}", getLocationName($observation->exact_location));
                        $sheet->setCellValue("D{$row}", Displaydateformat($observation->observation_date));
                        $sheet->setCellValue("E{$row}", $observation->observation);

                        $imagePath = GetSafetyWalkImage($observation->id);
                        if (file_exists($imagePath)) {
                            $drawing = new Drawing();
                            $drawing->setPath($imagePath);
                            $drawing->setCoordinates("F{$row}");
                            $drawing->setHeight(60);
                            $drawing->setOffsetX(5);
                            $drawing->setWidth(80);
                            $drawing->setOffsetY(5);
                            $sheet->getRowDimension($row)->setRowHeight(90);
                            $sheet->getColumnDimension('F')->setWidth(20);

                            $drawing->setWorksheet($sheet);
                        } else {
                            $sheet->setCellValue("F{$row}", 'No Image');
                        }

                        $sheet->setCellValue("G{$row}", $observation->recomended_action);
                        $sheet->setCellValue("H{$row}", getUsername($observation->responsibility));
                        $sheet->setCellValue("I{$row}", $observation->date_of_compliance);
                        $sheet->setCellValue("J{$row}", $observation->observation_status == 1 ? 'Active' : ($observation->observation_status == 0 ? 'Deactive' : 'Unknown'));
                        $sheet->setCellValue("K{$row}", $observation->remarks);

                        $sheet->getStyle("A{$row}:K{$row}")->applyFromArray([
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        ]);
                        $row++;
                    }
                }
            }

            if (!empty($current_month_inspection)) {
                $sheet->mergeCells("A{$row}:K{$row}")->setCellValue("A{$row}", 'Current Month Observations');
                $sheet->getStyle("A{$row}:K{$row}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(20);
                $row++;

                $sr = 1;
                foreach ($current_month_inspection as $observation) {
                    $sheet->setCellValue("A{$row}", $sr++);
                    $sheet->setCellValue("B{$row}", getLocationName($observation->location));
                    $sheet->setCellValue("C{$row}", ($observation->exact_location));
                    $sheet->setCellValue("D{$row}", Displaydateformat($observation->observation_date));
                    $sheet->setCellValue("E{$row}", $observation->observation);

                    $imagePath = GetSafetyWalkImage($observation->id);
                    if (file_exists($imagePath)) {
                        $drawing = new Drawing();
                        $drawing->setPath($imagePath);
                        $drawing->setCoordinates("F{$row}");
                        $drawing->setHeight(60);
                        $drawing->setWidth(80);

                        $drawing->setOffsetX(5);
                        $drawing->setOffsetY(5);
                        $sheet->getRowDimension($row)->setRowHeight(90);
                        $sheet->getColumnDimension('F')->setWidth(20);
                        $drawing->setWorksheet($sheet);
                    } else {
                        $sheet->setCellValue("F{$row}", 'No Image');
                    }

                    $sheet->setCellValue("G{$row}", $observation->recomended_action);
                    $sheet->setCellValue("H{$row}", getUsername($observation->responsibility));
                    $sheet->setCellValue("I{$row}", $observation->date_of_compliance);
                    $sheet->setCellValue("J{$row}", $observation->observation_status == 1 ? 'Active' : ($observation->observation_status == 0 ? 'Deactive' : 'Unknown'));
                    $sheet->setCellValue("K{$row}", $observation->remarks);

                    $sheet->getStyle("A{$row}:K{$row}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $row++;
                }
            }

            $signatureRowStart = $row;
            $sheet->getRowDimension($signatureRowStart)->setRowHeight(20);

            $sheet->mergeCells("A{$signatureRowStart}:E{$signatureRowStart}");
            $sheet->getStyle("A{$signatureRowStart}:E{$signatureRowStart}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_BOTTOM,
                    'wrapText' => false,
                ],
            ]);

            $sheet->setCellValue("A{$signatureRowStart}", "Prepared By:- \n" . getUsername($inspection_details->created_by));

            // if (file_exists($prepared_by_signature)) {
            //     $drawing = new Drawing();
            //     $drawing->setName('Signature');
            //     $drawing->setDescription('Prepared By');
            //     $drawing->setPath($prepared_by_signature);
            //     $drawing->setCoordinates("C{$signatureRowStart}");
            //     $drawing->setOffsetX(50);
            //     $drawing->setOffsetY(5);
            //     $drawing->setHeight(60);
            //     $drawing->setWorksheet($sheet);
            // } else {

            //     $sheet->setCellValue("A{$signatureRowStart}", "Prepared By:- \n" . getUsername($inspection_details->created_by));
            // }

            $sheet->mergeCells("F{$signatureRowStart}:K{$signatureRowStart}");
            $sheet->getStyle("F{$signatureRowStart}:K{$signatureRowStart}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_BOTTOM,
                    'wrapText' => false,
                ],
            ]);
            $sheet->setCellValue(
                "F{$signatureRowStart}",
                "Verified By:- \n" . (
                    !empty(getUserName($inspection_details->verified_by))
                    ? getUserName($inspection_details->verified_by)
                    : "Inspection has not been verified yet"
                )
            );


            // if (file_exists($verified_by_signature)) {
            //     $drawing = new Drawing();
            //     $drawing->setName('Signature');
            //     $drawing->setDescription('Verified By');
            //     $drawing->setPath($verified_by_signature);
            //     $drawing->setCoordinates("H{$signatureRowStart}");
            //     $drawing->setOffsetX(5);
            //     $drawing->setOffsetY(5);
            //     $drawing->setHeight(60);
            //     $drawing->setWorksheet($sheet);
            // } else {
            //     $sheet->setCellValue("F{$signatureRowStart}", "Not Verified Yet");
            // }

            $writer = new Xlsx($spreadsheet);
            $fileName = 'Safety Walk Observation Sheet.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"{$fileName}\"");
            $writer->save('php://output');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('safety/safety-walk-observation/list'));
        }
    }
}
