<?php

namespace App\Http\Controllers\Inspection\Ohc;

use App\Http\Controllers\Controller;
use App\Models\Inspection\Master\ChecklistOptionType;
use App\Models\Inspection\Master\ChecklistSubTypeData;
use App\Models\Inspection\Master\ChecklistSubTypeDataName;
use App\Models\Inspection\Master\ChecklistType;
use App\Models\Inspection\Master\Shift;
use App\Models\Inspection\Ohc\WeeklyAmbulance;
use App\Models\Inspection\Ohc\WeeklyAmbulanceChecklist;
use App\Models\Master\Department;
use App\Models\Master\Location;
use App\Models\Master\Unit;
use App\Models\UploadLog;
use Exception;
use Illuminate\Container\Attributes\Database;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class WeeklyAmbulanceController extends Controller
{
    private $weekly_ambulance_details;
    private $weekly_ambulance_inspection_checklist;
    private $upload_log;
    private $unit;
    private $shift;
    private $department;
    private $checklist_type;
    private $sub_type_data;
    private $sub_type_data_name;
    private $questionery;

    private $location;




    public function __construct()
    {
        $this->weekly_ambulance_details = new WeeklyAmbulance();
        $this->weekly_ambulance_inspection_checklist = new WeeklyAmbulanceChecklist();
        $this->upload_log = new UploadLog();
        $this->unit = new Unit();
        $this->department = new Department();
        $this->shift = new Shift();
        $this->checklist_type = new ChecklistType();
        $this->sub_type_data = new ChecklistSubTypeData();
        $this->sub_type_data_name = new ChecklistSubTypeDataName();
        $this->questionery = new ChecklistOptionType();

        $this->location = new Location();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {

                    $data = $this->weekly_ambulance_details->list();


                    $filteredData = collect($data['data'])->where('ohc_type', 1)->values();

                    $datatables = DataTables::of($filteredData)
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
                            return '<a href="' . admin_url('ohc/weekly-ambulance/inspection/checklist/view/' . encryptId($row->id)) . '" class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a>';
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status'])
                        ->setFilteredRecords($filteredData->count())
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);

                    return $datatables;
                } catch (Exception $ex) {
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }

        return view('inspection.inspection_ohc.weekly_ambulance.list');
    }



    public function Add(Request $request)
    {
        try {
            $unit = $this->unit->getunit();
            $shift = $this->shift->getShiftname();
            $checklistQuestions = getCheckListQuestion(WEEKLY_AMBULANCE_INSPECTION_CHECKLIST);
            $questionery = $this->questionery->getQuestionery();

            $location = $this->location->getLocation();
            $data = array(
                'unit' => $unit,
                'shift' => $shift,
                'checklistQuestions'=>  $checklistQuestions,
                'location' => $location,
                'questionery' => $questionery,

            );
            return view('inspection.inspection_ohc.weekly_ambulance.add', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    public function Store(Request $request)
    {
        try {
            $rules = [
                'questionary_id' => 'required',
                'checklist_category' => 'required',
            ];
            $messages = [
                'checklist_category.required' => __('inspection.category_name'),
                'questionary_id.requred' => __('inspection.questionary'),
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            try {

                $weekly_ambulance_details = $this->weekly_ambulance_details->store();
                $weekly_ambulance_inspection_checklist = $this->weekly_ambulance_inspection_checklist->store($weekly_ambulance_details);

                Session::flash('success', __('Your data Created Successfully.!'));
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', __('common.message_error'));
            }

            return redirect(admin_url('ohc/weekly-ambulance/inspection/checklist/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/weekly-ambulance/inspection/checklist/list'));
        }
    }
}
