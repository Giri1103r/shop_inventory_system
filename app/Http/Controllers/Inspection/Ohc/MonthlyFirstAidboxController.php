<?php

namespace App\Http\Controllers\Inspection\Ohc;

use App\Http\Controllers\Controller;
use App\Mail\Inspection\Safety\SafetyInspection;
use App\Models\Inspection\Master\ChecklistOptionType;
use App\Models\Inspection\Master\ChecklistSubTypeData;
use App\Models\Inspection\Master\ChecklistSubTypeDataName;
use App\Models\Inspection\Master\ChecklistType;
use App\Models\Inspection\Master\Frequency;
use App\Models\Inspection\Master\Shift;
use App\Models\Inspection\Ohc\InspectionOhcStatuslog;
use App\Models\Inspection\Ohc\MonthlyFirstAidbox;
use App\Models\Inspection\Ohc\MonthlyFirstAidboxChecklist;
use App\Models\Inspection\Ohc\OhcSignature;
use App\Models\Inspection\Ohc\WeeklyAmbulance;
use App\Models\Inspection\Ohc\WeeklyAmbulanceChecklist;
use App\Models\Master\Department;
use App\Models\Master\Location;
use App\Models\Master\Unit;
use App\Models\UploadLog;
use App\Models\User;
use Exception;
use Illuminate\Container\Attributes\Database;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Inspection\InspectionStaticDocno;
class MonthlyFirstAidboxController extends Controller
{

    private $OhcDetails;
    private $user;
    private $monthly_first_aid;
    private $frequency;
    private $upload_log;
    private $unit;
    private $shift;
    private $department;
    private $checklist_type;
    private $sub_type_data;
    private $sub_type_data_name;
    private $questionery;
    private $signature;
    private $location;
    private $inspection_ohc_status_log;
    private $monthly_first_aid_audit_checklist;
    private $document_reference;
    public function __construct()
    {

        $this->upload_log = new UploadLog();
        $this->unit = new Unit();
        $this->department = new Department();
        $this->shift = new Shift();
        $this->location = new Location();
        $this->monthly_first_aid = new MonthlyFirstAidbox();
        $this->monthly_first_aid_audit_checklist = new MonthlyFirstAidboxChecklist();
        $this->user = new User();
        $this->frequency = new Frequency();
        $this->signature = new OhcSignature();
        $this->inspection_ohc_status_log = new InspectionOhcStatuslog();
        $this->document_reference = new InspectionStaticDocno();
    }
    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {

                    $data = $this->monthly_first_aid->list();

                    $datatables = Datatables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active</span>";
                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->inspection_id) . "' data-type='1'>Active</span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->inspection_id) . "' data-type='0'>In-Active</span>";
                            }
                            return $text;
                        })
                        ->addColumn('created_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('shift', function ($row) {
                            return getShift($row->shift);
                        })
                        ->addColumn('frequency', function ($row) {
                            return getFrequencyname($row->frequency);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })

                        ->addColumn('action', function ($row) {
                            $btn = '';

                            $btn .=  '<a href="' . admin_url('ohc/first-aid-box/monthly-audit/view/' . encryptId($row->inspection_id)) . '" class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a>';



                            $btn .= '<a href="' . admin_url('ohc/first-aid-box/monthly-audit/generalpdf/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                        </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', 'approve_status'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);

                    return $datatables;
                } catch (Exception $ex) {
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }
        $shift = $this->shift->getShiftname();
        $frequency = $this->frequency->getFrequency();
        $data = array(

            'shift' => $shift,
            'frequency' => $frequency,


        );
        return view('inspection.inspection_ohc.monthly_first_aid_audit_checklist.list',$data);
    }

    public function Add(Request $request)
    {
        try {
            $unit = $this->unit->getunit();
            $shift = $this->shift->getShiftname();
            $frequency = $this->frequency->getFrequency();
            $location = $this->location->getLocationname();
            $signature_upload = $this->user->getSignature();
            $document_no = $this->document_reference->selectUsingName('MonthlyFirstAidBoxAuditChecklist');
            $data = array(
                'unit' => $unit,
                'shift' => $shift,
                'frequency' => $frequency,
                'location' => $location,
                'document_no' => $document_no,
                'signature_upload' => $signature_upload,

            );
            return view('inspection.inspection_ohc.monthly_first_aid_audit_checklist.add', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }
    // store
    public function Store(Request $request)
    {
        try {

            $store = $this->monthly_first_aid->store();
            $inspection_type = OHC_TYPE_MONTHLY_FIRST_AID_BOX_AUDIT_INSPECTION_CHECKLIST;
            $monthly_first_aid_audit_checklist = $this->monthly_first_aid_audit_checklist->store($store);
            $id = $store->id;
            $files = $this->signature->requestorsignatureUpload($inspection_type, $id);

            Session::flash('success', 'Your data has been added successfully');
            return redirect(admin_url('ohc/first-aid-box/monthly-audit/list'));
        } catch (Exception $ex) {

            dd($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/first-aid-box/monthly-audit/list'));
        }
    }
    // view

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $monthly_first_aid = $this->monthly_first_aid->selectOne($id);
                $monthly_first_aid_audit_checklist = $this->monthly_first_aid_audit_checklist->selectOne($id);
            }

            $requestorsignature =  $monthly_first_aid->created_by;

            $type = OHC_TYPE_MONTHLY_FIRST_AID_BOX_AUDIT_INSPECTION_CHECKLIST;
            $requestor_signature = $this->signature->requestorSignature($id, $requestorsignature, $type);
            $signatureview = $this->user->where('id', $requestorsignature)->first();

            $document_no = $this->document_reference->selectUsingName('MonthlyFirstAidBoxAuditChecklist');
            $data = [
                'monthly_first_aid' => $monthly_first_aid,
                'monthly_first_aid_audit_checklist' => $monthly_first_aid_audit_checklist,
                'signatureview' => $signatureview,
                'document_no' => $document_no,
                'pagetitle' => "Monthly First Aid Audit Checklist Inspection",
            ];
            return view('inspection.inspection_ohc.monthly_first_aid_audit_checklist.view', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }
    // general pdf

    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $monthly_first_aid = $this->monthly_first_aid->selectOne($id);
                $monthly_first_aid_audit_checklist = $this->monthly_first_aid_audit_checklist->selectOne($id);
            }
            $requestorsignature =  $monthly_first_aid->created_by;

            $type = OHC_TYPE_MONTHLY_FIRST_AID_BOX_AUDIT_INSPECTION_CHECKLIST;
            $requestor_signature = $this->signature->requestorSignature($id, $requestorsignature, $type);
            $signatureview = $this->user->where('id', $requestorsignature)->first();

            $document_no = $this->document_reference->selectUsingName('MonthlyFirstAidBoxAuditChecklist');
            $data = [
                'monthly_first_aid' => $monthly_first_aid,
                'monthly_first_aid_audit_checklist' => $monthly_first_aid_audit_checklist,
                'signatureview' => $signatureview,
                'document_no' => $document_no,
                'pagetitle' => "Monthly First Aid Audit Checklist Inspection",
            ];

            $property = [
                'tempDir' => storage_path('app/public/pdf/temp/'), // Corrected path
                'mode' => 'c',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
            ];

            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';

            // Load HTML from the Blade view
            $html = view('inspection.inspection_ohc.monthly_first_aid_audit_checklist.viewpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "Monthly First Aid Audit Checklist Inspection.pdf";

            return $mpdf->Output($filename, 'D');
        } catch (\Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/first-aid-box/monthly-audit/list'));
        }
    }

    // Excel
    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->monthly_first_aid->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Document Number',
                'Review date',
                'Issued Date',
                'Date of Inspection',
                'Shift',
                'frequency',
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->doc_no;
                $export[] =  $data->revision_date;
                $export[] =  Displaydateformat($data->issue_date);
                $export[] =  Displaydateformat($data->date_of_inspection);
                $export[] =  getShift($data->shift);
                $export[] =  getFrequencyname($data->frequency);
                $export[] =  getusername($data->inspection_created_by);
                $export[] =  Displaydateformat($data->inspection_created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Monthly First Aid Audit Checklist Inspection.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/first-aid-box/monthly-audit/list'));
        }
    }
    // pdf
    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->monthly_first_aid->exportdata();
            $header = [
                __("common.sno"),
                'Document Number',
                'Review date',
                'Issued Date',
                'Date of Inspection',
                'Shift',
                'frequency',
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Monthly First Aid Audit Checklist Inspection",
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

            $view = view('inspection.inspection_ohc.monthly_first_aid_audit_checklist.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Monthly First Aid Audit Checklist Inspection.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/first-aid-box/monthly-audit/list'));
        }
    }
}
