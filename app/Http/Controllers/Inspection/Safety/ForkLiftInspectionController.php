<?php

namespace App\Http\Controllers\Inspection\Safety;

use Exception;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use App\Models\Master\Department;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Models\Inspection\Safety\SignatureUpload;
use App\Models\Inspection\Safety\ForkLiftInspection;
use App\Models\Inspection\Safety\ForkliftInspectionDetails;

class ForkLiftInspectionController extends Controller
{
    private $forklift;
    private $observation_details;
    private $unit;
    private $department;
    private $signature;

    public function __construct()
    {
        $this->forklift = new ForkLiftInspection();
        $this->observation_details = new ForkliftInspectionDetails();
        $this->unit = new Unit();
        $this->department = new Department();
        $this->signature = new SignatureUpload();
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
                                    $text = "<span class='badge bg-primary rounded' style='font-size: 1.0em;'>OBSERVATION PENDING</span>";
                                    break;
                                case OBSERVATION_REJECTED:
                                    $text = "<span class='badge bg-danger rounded' style='font-size: 1.0em;'>OBSERVATION REJECTED</span>";
                                    break;
                                case OBSERVATION_APPROVED:
                                    $text = "<span class='badge bg-success rounded' style='font-size: 1.0em;'>OBSERVATION APPROVED</span>";
                                    break;
                                default:
                                    $text = "<span class='badge rounded-pill text-bg-warning'>Unknown</span>";
                            }
                            return $text;
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('safety/forklift-inspection/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('safety/forklift-inspection/exportViewPdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                    <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                </a>';

                            if ($row->observation_status == OBSERVATION_PENDING && (isAdmin())) {
                                $btn .= '<a href="' . admin_url('safety/forklift-inspection/approval/' . encryptId($row->id)) . '" class="" title="' . __('inspection.approval') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            return $btn;
                        })
                        ->addColumn('created_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('issue_date', function ($row) {
                            return Displaydateformat($row->issue_date);
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

        $data = array();
        return view('inspection.Safety.forklift_inspection.list', $data);
    }


    public function Add(Request $request)
    {
        try {
            $unit = $this->unit->getunit();
            $departments = $this->department->getdepartment();
            $data = array(
                'unit' => $unit,
                'departments' => $departments,
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


            $forklift_observation =  $this->forklift->Store();
            $forklift_observation_details = $this->observation_details->store($forklift_observation->id);
            $signature_update = $this->signature->signatureUpload(FORKLIFT_INSPECTION, $forklift_observation->id);
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
            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
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
            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
            );

            return view('inspection.Safety.forklift_inspection.approval', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('safety/forklift-inspection/list'));
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->forklift->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Document Number',
                'Issue Date',
                'Revision Date',
                'Observation Status',
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->doc_no;
                $export[] =  displaydateformat($data->issue_date);
                $export[] = $data->rev_data;
                $export[] =  getObservationStatus($data->observation_status);
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);
                $exportData[] = $export;
                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Forklift Inspection.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
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
            }
            $header = [
                __("common.sno"),
                'Document Number',
                'Issue Date',
                'Revision Date',
                "Status",
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
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
                $last_month_inspection = $this->forklift->GetLastMonthObservation($id);
                $last_month_observation_details = $this->observation_details->GetLastMonthDetails($last_month_inspection);

                $data = [
                    'inspection_details' => $inspection_details,
                    'inspection' => $current_month_inspection,
                    'last_month_observation_details' => $last_month_observation_details,
                    'pagetitle' => "Forklift Inspection",
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
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong!');
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
            $signature_update = $this->signature->signatureUpload(FORKLIFT_INSPECTION, $id);
            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            if ($status == 1) {
                $message = 'FORKLIFT INSPECTION - OBSERVATION APPROVED';
                $to_status = OBSERVATION_APPROVED;
            } else {
                $message = 'FORKLIFT INSPECTION - OBSERVATION APPROVED';
                $to_status = OBSERVATION_REJECTED;
            }
            $web_link =   admin_url('safety/forklift-inspection/view/' . encryptId($inspection_details->id));
            $mailsubject = 'SAFETY INSPECTION';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => $message,
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $inspection_details->id,
                    'module' => 1,
                )),
                'web_link' =>  $web_link,
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);
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
}
