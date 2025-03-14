<?php

namespace App\Http\Controllers\Inspection\Safety;

use Exception;
use App\Models\UploadLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inspection\Master\Frequency;
use Illuminate\Support\Facades\Auth;
use App\Models\Inspection\Master\Shift;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Inspection\Safety\MonthlyForkLiftInspection;
use App\Models\Master\ForkLiftType;
use App\Models\Master\Location;
use App\Models\Master\Unit;

class MonthlyForkLiftInspectionController extends Controller
{
    private $forklift;
    private $forklift_type;
    private $upload_log;
    private $shift;
    private $location;
    private $unit;
    private $frequency;

    public function __construct()
    {
        $this->forklift = new MonthlyForkLiftInspection();
        $this->upload_log = new UploadLog();
        $this->shift = new Shift();
        $this->location = new Location();
        $this->unit = new Unit();
        $this->frequency = new Frequency();
        $this->forklift_type = new ForkLiftType();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->forklift->list();
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
                            $btn = '<a href="' . admin_url('inspection/master/checklist-type/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            if (CheckUserRole(ROLE_SUPERADMIN)) {
                                $btn .= '<a href="' . admin_url('inspection/master/checklist-type/edit/' . encryptId($row->id)) . '" class="edit-icon " title="' . __('common.edit') . '"><i class="fa-solid fa-pen-to-square"></i> ';
                                // $btn .= '<a href="javascript:void(0);"  data-id="' . encryptId($row->id) . '"  class="recordDelete" title="' . __('common.delete') . '"><i class="fa-solid fa-trash text-danger" ></i></i></a> ';
                            }
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

        $data = array();
        return view('inspection.Safety.forklift_inspection_monthly.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $checklistQuestions = getCheckListQuestion(FORKLIFT_INSPECTION_MONTHLY_CHECKLIST);
            $options =  getoption(FORKLIFT_INSPECTION_MONTHLY_CHECKLIST);
            $shift  = $this->shift->select('id', 'shift')->where('status', '1')->get();
            $getoption = string_to_array($options->type);
            $location = $this->location->getLocation();
            $unit = $this->unit->getUnit();
            $frequency = $this->frequency->getFrequency();
            $forklifts = $this->forklift_type->getForkLift();
            $data = array(
                'checklist_details' => $checklistQuestions,
                'shift' => $shift,
                'getoption' => $getoption,
                'locations' => $location,
                'units' => $unit,
                'frequency' => $frequency,
                'forklifts' => $forklifts,
            );
            return view('inspection.Safety.forklift_inspection_monthly.add', $data);
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('safety/forklift-inspection/monthly/list'));
        }
    }

    public function store(Request $request){
        dd($request->all());
    }
}
