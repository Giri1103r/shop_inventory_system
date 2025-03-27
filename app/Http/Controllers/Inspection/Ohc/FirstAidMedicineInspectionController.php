<?php

namespace App\Http\Controllers\Inspection\Ohc;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Models\Inspection\Ohc\OhcSignature;
use App\Models\Inspection\Ohc\FirstAidMedicineInspection;
use App\Models\Inspection\Ohc\Master\FirstAidEquipment;

class FirstAidMedicineInspectionController extends Controller
{
    private $medicine_checklist;
    private $medicine;
    private $signature;

    public function __construct()
    {
        $this->medicine_checklist = new FirstAidMedicineInspection();
        $this->medicine = new FirstAidEquipment();
        $this->signature = new OhcSignature();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->medicine_checklist->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('inspection_status', function ($row) {
                            $text = '';
                            switch ($row->inspection_status) {
                                case OBSERVATION_PENDING:
                                    $text = "<span class='badge bg-primary rounded' style='font-size: 1.0em;'>OBSERVATION PENDING</span>";
                                    break;
                                case OBSERVATION_APPROVED:
                                    $text = "<span class='badge bg-success' style='font-size: 1.0em;'>OBSERVATION APPROVED</span>";
                                    break;
                                case OBSERVATION_REJECTED:
                                    $text = "<span class='badge bg-danger rounded' style='font-size: 1.0em;'>OBSERVATION REJECTED</span>";
                                    break;
                                default:
                                    $text = "<span class='badge rounded-pill text-bg-warning'>Unknown</span>";
                            }
                            return $text;
                        })
                        ->addColumn('created_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('inspection_date', function ($row) {
                            return Displaydateformat($row->inspection_date);
                        })
                        ->addColumn('next_due', function ($row) {
                            return Displaydateformat($row->next_due);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })

                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('ohc/first-aid/opd-medicine-inspection/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';

                            if ($row->inspection_status == OBSERVATION_PENDING &&  isAdmin()) {
                                $btn .= '<a href="' . admin_url('ohc/first-aid/opd-medicine-inspection/approval/' . encryptId($row->id)) . '" class="" title="Action"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }

                            $btn .= '<a href="' . admin_url('ohc/first-aid/opd-medicine-inspection/exportViewpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                        </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'inspection_status', 'inspection_date', 'next_due'])
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

        return view('inspection.inspection_ohc.first_aid_inspection.list');
    }

    public function Add(Request $request)
    {
        try {

            $medicines = $this->medicine->getFirstAidData();

            $data = array(
                'medicines' => $medicines,
            );
            return view('inspection.inspection_ohc.first_aid_inspection.add', $data);
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/first-aid/opd-medicine-inspection/list'));
        }
    }

    public function Store(Request $request)
    {
        try {
            $store = $this->medicine_checklist->store();
            $inspection_type = OHC_OPD_MEDICINE_INSPECTION;
            $inspection_details = $this->medicine_checklist->selectOne($store->id);
            $files = $this->signature->requestorsignatureUpload($inspection_type, $inspection_details->id);

            Session::flash('success', 'Your data has been added successfully');
            return redirect(admin_url('ohc/first-aid/opd-medicine-inspection/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/first-aid/opd-medicine-inspection/list'));
        }
    }

    public function View(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_details = $this->medicine_checklist->selectOne($id);
            $inspection_type = OHC_OPD_MEDICINE_INSPECTION;
            $inspection_file = $this->signature->getFilesByEmpId($inspection_details->created_by, $inspection_type);
            $inspection_data = json_decode($inspection_details->inspection_data, true);
            $verified_by = $this->signature->getFilesByEmpId($inspection_details->updated_by, $inspection_type);

            $data = array(
                'inspection_details' => $inspection_details,
                'inspection_file' => $inspection_file,
                'inspection_data' => $inspection_data,
                'verified_by' => $verified_by,
            );


            return view('inspection.inspection_ohc.first_aid_inspection.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/first-aid/opd-medicine-inspection/list'));
        }
    }

    public function ExportExcel()
    {
        try {
            $allData = $this->medicine_checklist->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Date of Inspection',
                'Next Due',
                'Inspection Status',
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  Displaydateformat($data->inspection_date);
                $export[] =  Displaydateformat($data->next_due);
                $export[] =  getObservationStatus($data->inspection_status);
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);
                $exportData[] = $export;
                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Monthly OHC First-Aid Medicine Inspection Checklist.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/first-aid/opd-medicine-inspection/list'));
        }
    }

    public function ExportPDF()
    {
        try {
            $allData = $this->medicine_checklist->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Date of Inspection',
                'Next Due',
                'Inspection Status',
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Monthly OHC First-Aid Medicine Inspection Checklist",
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

            $view = view('inspection.inspection_ohc.first_aid_inspection.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Monthly OHC First-Aid Medicine Inspection Checklist.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/first-aid/opd-medicine-inspection/list'));
        }
    }

    public function ExportViewPDF(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_detail = $this->medicine_checklist->selectOne($id);
            $inspection_type = OHC_OPD_MEDICINE_INSPECTION;
            $inspection_file = $this->signature->getFiles($id, $inspection_type);
            $inspection_data = json_decode($inspection_detail->inspection_data, true);
            $inspection_created_by = $this->signature->getFilesByEmpId($inspection_detail->created_by, $inspection_type);
            $inspection_updated_by = $this->signature->getFilesByEmpId($inspection_detail->updated_by, $inspection_type);

            $property = [
                'tempDir' => 'public/pdf/temp/',
                'mode' => 'c',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,

            ];

            $data = array(
                'inspection_detail' => $inspection_detail,
                'inspection_file' => $inspection_file,
                'pagetitle' => "Monthly OHC First-Aid Medicine Inspection Checklist",
                'inspection_data' => $inspection_data,
                'inspection_created_by' => $inspection_created_by,
                'inspection_updated_by' => $inspection_updated_by,
            );

            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';

            $html = view('inspection.inspection_ohc.first_aid_inspection.viewpdf', $data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "Monthly OHC First-Aid Medicine Inspection Checklist.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/first-aid/opd-medicine-inspection/list'));
        }
    }

    public function approval(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_details = $this->medicine_checklist->selectOne($id);
            $inspection_type = OHC_OPD_MEDICINE_INSPECTION;
            $inspection_file = $this->signature->getFiles($id, $inspection_type);
            $inspection_data = json_decode($inspection_details->inspection_data, true);

            $data = array(
                'inspection_details' => $inspection_details,
                'inspection_file' => $inspection_file,
                'inspection_data' => $inspection_data,
            );

            return view('inspection.inspection_ohc.first_aid_inspection.approval', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/first-aid/opd-medicine-inspection/list'));
        }
    }

    public function approvalSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->capa_remarks;
            $eye_wash_inspection = $this->medicine_checklist->approvalSubmit($id, $status, $remarks);
            $inspection_details = $this->medicine_checklist->selectOne($id);
            $signature_update = $this->signature->signatureUpload(OHC_OPD_MEDICINE_INSPECTION);
            // $ehsOfficer = GetEHSOfficer();
            // $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            // if ($status == 1) {
            //     $message = 'FORKLIFT INSPECTION - OBSERVATION APPROVED';
            //     $to_status = OBSERVATION_APPROVED;
            // } else {
            //     $message = 'FORKLIFT INSPECTION - OBSERVATION APPROVED';
            //     $to_status = OBSERVATION_REJECTED;
            // }
            // $web_link =   admin_url('ohc/first-aid/opd-medicine-inspection/view/' . encryptId($inspection_details->id));
            // $mailsubject = 'SAFETY INSPECTION';
            // $notificationData = array(
            //     'notification_type' => SAFETY_INSPECTION,
            //     'module_type' => 1,
            //     'notification_message' => $mailsubject,
            //     'mobile_notification' => json_encode(array(
            //         'title' => $mailsubject,
            //         'message' => $message,
            //         'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
            //         'id' => $inspection_details->id,
            //         'module' => 1,
            //     )),
            //     'web_link' =>  $web_link,
            //     'assigned_user' => array_to_string($ehsOfficers),
            //     'created_by' => Auth::id(),
            // );
            // notificationSave($notificationData);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('ohc/first-aid/opd-medicine-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('ohc/first-aid/opd-medicine-inspection/list'));
        }
    }
}
