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
                                $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->id) . "' data-type='1'>Active</span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->id) . "' data-type='0'>In-Active</span>";
                            }
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

                            $btn .=  '<a href="' . admin_url('ohc/first-aid-box/monthly-audit/view/' . encryptId($row->id)) . '" class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a>';



                            $btn .= '<a href="' . admin_url('ohc/first-aid-box/monthly-audit/generalpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
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

        return view('inspection.inspection_ohc.monthly_first_aid_audit_checklist.list');
    }

    public function Add(Request $request)
    {
        try {
            $unit = $this->unit->getunit();
            $shift = $this->shift->getShiftname();
            $frequency = $this->frequency->getFrequency();
            $location = $this->location->getLocationname();
            $signature_upload = $this->user->getSignature();
            $data = array(
                'unit' => $unit,
                'shift' => $shift,
                'frequency' => $frequency,
                'location' => $location,
                'signature_upload' => $signature_upload,

            );
            return view('inspection.inspection_ohc.monthly_first_aid_audit_checklist.add', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }
}
