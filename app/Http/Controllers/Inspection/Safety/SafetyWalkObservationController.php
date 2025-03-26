<?php

namespace App\Http\Controllers\Inspection\Safety;

use Exception;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use App\Models\Master\Location;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Inspection\Master\Shift;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Models\Inspection\Safety\SafetyWalkObservation;
use App\Models\Inspection\Safety\SafetyWalkObservationDetails;
use App\Models\Inspection\Safety\SignatureUpload;

class SafetyWalkObservationController extends Controller
{
    private $safety_walk;
    private $observation_details;
    private $shift;
    private $unit;
    private $location;
    private $signature;

    public function __construct()
    {
        $this->safety_walk = new SafetyWalkObservation();
        $this->observation_details = new SafetyWalkObservationDetails();
        $this->shift = new Shift();
        $this->unit = new Unit();
        $this->location = new Location();
        $this->signature = new SignatureUpload();
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
                            $btn = '<a href="' . admin_url('safety/safety-walk-observation/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('safety/safety-walk-observation/exportViewPdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                        <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                    </a>';

                            if ($row->observation_status == OBSERVATION_PENDING && (isAdmin())) {
                                $btn .= '<a href="' . admin_url('safety/safety-walk-observation/approval/' . encryptId($row->id)) . '" class="" title="' . __('inspection.approval') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
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
                    dd($ex);
                    report($ex);
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }

        $data = array();
        return view('inspection.Safety.safety_walk_observation.list', $data);
    }


    public function Add(Request $request)
    {
        try {
            $shift = $this->shift->getShiftname();
            $unit = $this->unit->getunit();
            $locations = $this->location->getLocationName();
            $data = array(
                'shift' => $shift,
                'unit' => $unit,
                'locations' => $locations,
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

            $safety_walk_observation =  $this->safety_walk->Store();
            $safety_walk_observation_details = $this->observation_details->store($safety_walk_observation->id);
            $signature_update = $this->signature->signatureUpload(SAFETY_WALK_OBSERVATION, $safety_walk_observation->id);
            Session::flash('success', 'Safety Walk Observation added successfully!');
            return redirect(admin_url('safety/safety-walk-observation/list'));
        } catch (Exception $ex) {
            dd($ex);
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
            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
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
            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
            );

            return view('inspection.Safety.safety_walk_observation.approval', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('safety/safety-walk-observation/list'));
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->safety_walk->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Document Number',
                'Issue Date',
                'Revision Date',
                'Status',
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->doc_no;
                $export[] =  displaydateformat($data->issue_date);
                $export[] = $data->revision_data;
                $export[] =  getObservationStatus($data->observation_status);
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);
                $exportData[] = $export;
                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Safety Walk Observation.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('safety/safety-walk-observation/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->safety_walk->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }
            $header = [
                __("common.sno"),
                'Document Number',
                'Issue Date',
                'Revision Date',
                "Observation Status",
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
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

                $data = [
                    'inspection_details' => $inspection_details,
                    'inspection' => $current_month_inspection,
                    'last_month_observation_details' => $last_month_observation_details,
                    'pagetitle' => "Safety Walk Observation",
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

    public function approvalSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->capa_remarks;
            $eye_wash_inspection = $this->safety_walk->approvalSubmit($id, $status, $remarks);
            $inspection_details = $this->safety_walk->selectOne($id);
            $signature_update = $this->signature->signatureUpload(SAFETY_WALK_OBSERVATION, $id);
            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            if ($status == 1) {
                $message = 'FORKLIFT INSPECTION - OBSERVATION APPROVED';
                $to_status = OBSERVATION_APPROVED;
            } else {
                $message = 'FORKLIFT INSPECTION - OBSERVATION APPROVED';
                $to_status = OBSERVATION_REJECTED;
            }
            $web_link =   admin_url('safety/safety-walk-observation/view/' . encryptId($inspection_details->id));
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
            return redirect(admin_url('safety/safety-walk-observation/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('safety/safety-walk-observation/list'));
        }
    }
}
