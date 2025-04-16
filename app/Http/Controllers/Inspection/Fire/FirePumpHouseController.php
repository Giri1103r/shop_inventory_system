<?php

namespace App\Http\Controllers\Inspection\Fire;

use Exception;
use Response;
use Illuminate\Http\Request;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\Master\Employee;
use App\Models\Inspection\Fire\DailyFireHouseInspection;
use App\Models\Inspection\Master\ChecklistType;
use App\Models\Inspection\Master\ChecklistSubType;
use App\Models\Inspection\Master\ChecklistSubTypeDataName;
use App\Models\Inspection\Master\ChecklistSubTypeData;
use App\Models\Inspection\Master\ChecklistOptionType;
use App\Models\Inspection\Master\Shift;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Master\Unit;
use App\Models\Inspection\Fire\FireSignatureUpload;

class FirePumpHouseController extends Controller
{

    private $checklist_type;
    private $checklist_subtype;
    private $checklist_subtypedata;
    private $checklist_subtypename;
    private $dailyFire;
    private $upload_log;
    private $checklist_option;
    private $shift;
    private $static_docno;
    private $unit;
    private $signature;

    public function __construct()
    {
        $this->dailyFire = new DailyFireHouseInspection();
        $this->checklist_type = new ChecklistType();
        $this->checklist_subtype = new ChecklistSubType();
        $this->checklist_option = new ChecklistOptionType();
        $this->checklist_option = new ChecklistOptionType();
        $this->checklist_subtypename = new ChecklistSubTypeDataName();
        $this->checklist_subtypedata = new ChecklistSubTypeData();
        $this->shift = new Shift();
        $this->static_docno = new InspectionStaticDocno();
        $this->unit = new Unit();
        $this->signature = new FireSignatureUpload();
    }

    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =    $this->dailyFire->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active</span>";
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {

                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->id) . "' data-type = '1'>Active</span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->id) . "' data-type = '0'>In-Active</span>";
                            }
                            // }
                            return $text;
                        })
                        ->addColumn('created_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('fire/daily-fire-pump-house-inspection/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {
                            // $btn .= '<a href="' . admin_url('inspection/master/checklist-sub-type/edit/' . encryptId($row->id)) . '" class="edit-icon " title="' . __('common.edit') . '"><i class="fa-solid fa-pen-to-square"></i> ';
                            // }

                            $btn .= '<a href="' . admin_url('fire/daily-fire-pump-house-inspection/generalpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                        </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status'])
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
        $checklist_types  = $this->checklist_type->select('id', 'category_name')->where('status', '1')->get();
        $data = array(
            'checklist_types' => $checklist_types,

        );
        return view('inspection.fire.firePumpHouse.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $checklist_types  = $this->checklist_type->select('id', 'category_name')->where('status', '1')->get();
            $shift  = $this->shift->select('id', 'shift')->where('status', '1')->get();
            $unit  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
            $checklist_details = getCheckListQuestion(CHECKLIST_FIRE_PUMP_HOUSE_INSECTION_CHECKLIST);
            $options =  getoption(CHECKLIST_FIRE_PUMP_HOUSE_INSECTION_CHECKLIST);
            $getoption = string_to_array($options->type);

            $staticDocno  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                ['type', "DailyFirePumpHouseChecklist"],
                ['status', '1']
            ])->first();
            $data = array(
                'checklist_types' => $checklist_types,
                'shift' => $shift,
                'checklist_details' => $checklist_details,
                'getoption' => $getoption,
                'staticDocno' => $staticDocno,
                'unit' => $unit,
            );
            return view('inspection.fire.firePumpHouse.add', $data);
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
        }
    }

    public function store(Request $request)
    {

        try {
            try {

                $inspection_type = DAILY_FIRE_PUMP;
                $inspection = $this->dailyFire->store();
                $id = $inspection->id;
                $inspection_file = $this->signature->dailyFirePump($inspection_type, $id);

                Session::flash('success', __('Your data has been created successfully'));
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', __('common.message_error'));
            }
            return redirect(admin_url('fire/daily-fire-pump-house-inspection/list'));
        } catch (Exception $ex) {

            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('fire/daily-fire-pump-house-inspection/list'));
        }
    }
    public function view($id)
    {
        try {
            $id = decryptId($id);
            if (Auth::check()) {
                $checklist_details = getCheckListQuestion(CHECKLIST_FIRE_PUMP_HOUSE_INSECTION_CHECKLIST);
                $options =  getoption(CHECKLIST_FIRE_PUMP_HOUSE_INSECTION_CHECKLIST);
                $getoption = string_to_array($options->type);
                $dailyFire =   $this->dailyFire->selectOne($id);


                $data = array(
                    'dailyFire' => $dailyFire,
                    'checklist_details' => $checklist_details,
                    'getoption' => $getoption,
                );
            }
            return view('inspection.fire.firePumpHouse.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/daily-fire-pump-house-inspection/list'));
        }
    }

    public function statusChange(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $this->dailyFire->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Daily Fire Pump House Inspection status changed'], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function exportExcel()
    {
        try {

            $allData =   $this->checklist_subtype->exportdata();
            $header = [
                __("common.sno"),
                __("Checklist Sub-Type ID"),
                __("Checklist Type Name"),
                __("Checklist Sub-Type Name"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] = $data->subcategory_id;
                $export[] =  $data->category_name;
                $export[] =  $data->subcategory_name;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Checklist Sub Type Category.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function exportPDF()
    {
        try {

            ini_set("pcre.backtrack_limit", "5000000");

            $allData =   $this->checklist_subtype->exportdata();
            $header = [
                __("common.sno"),
                __("Checklist Sub-Type ID"),
                __("Checklist Type Name"),
                __("Checklist Sub-Type Name"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];
            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Checklist Sub Type Category",
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

            $view = view('inspection.fire.firePumpHouse.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Checklist Sub Type Category.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
        }
    }

    public function generalpdf($id)
    {
        try {
            $id = decryptId($id);
            if (Auth::check()) {
            
                $dailyFire =   $this->dailyFire->selectOne($id);
                $audit_assessmentCkeclist = json_decode($dailyFire);
                $checklist_details = getCheckListQuestion(CHECKLIST_FIRE_PUMP_HOUSE_INSECTION_CHECKLIST);
                $options =  getoption(CHECKLIST_FIRE_PUMP_HOUSE_INSECTION_CHECKLIST);
                $getoption = string_to_array($options->type);
            }
            $data = [
                'dailyFire' => $dailyFire,
                'getoption' => $getoption,
                'pagetitle' => "Daily Fire Pump House Inspection",
            ];

            $property = [
                'tempDir' => 'public/pdf/temp/',
                'mode' => 'c',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,

            ];

            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';

            $html = view('inspection.fire.firePumpHouse.viewpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "Daily Fire Pump House Inspection.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            dd($ex);
            return redirect()->back()->withErrors(['error' => 'An error occurred while generating the PDF.']);
        }
    }

}
