<?php

namespace App\Http\Controllers\Inspection\Safety;

use Exception;
use App\Models\UploadLog;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use App\Models\Master\Location;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Inspection\Master\Shift;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Inspection\Master\Frequency;
use App\Models\Inspection\Master\ChecklistFile;
use App\Models\Inspection\Master\ChecklistType;
use App\Models\Inspection\Safety\EyeWashInspectionDetails;
use App\Models\Inspection\Safety\SafetyStatusLog;
use App\Models\Inspection\Safety\MonthlyEyeWashInspection;
use Illuminate\Support\Facades\Session;

class MonthlyEyeWashInspectionController extends Controller
{
    private $eye_wash;
    private $checklist_file;
    private $upload_log;
    private $shift;
    private $location;
    private $unit;
    private $frequency;
    private $statusLog;
    private $eye_wash_details;

    public function __construct()
    {
        $this->eye_wash = new MonthlyEyeWashInspection();
        $this->checklist_file = new ChecklistFile();
        $this->upload_log = new UploadLog();
        $this->shift = new Shift();
        $this->location = new Location();
        $this->unit = new Unit();
        $this->frequency = new Frequency();
        $this->statusLog = new SafetyStatusLog();
        $this->eye_wash_details = new EyeWashInspectionDetails();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->eye_wash->list();
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
                            $btn = '<a href="' . admin_url('safety/eye-wash-inspection/monthly/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
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
        return view('inspection.Safety.eye_wash_inspection.list', $data);
    }

    public function Add(Request $request)
    {
        try {

            $location = $this->location->getLocationName();
            $unit = $this->unit->getUnit();
            $frequency = $this->frequency->getFrequency();
            $shifts = $this->shift->getShiftname();

            $data = array(
                'locations' => $location,
                'units' => $unit,
                'frequency' => $frequency,
                'shifts' => $shifts,
            );
            return view('inspection.Safety.eye_wash_inspection.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return admin_url('safety/eye-wash-inspection/monthly/list');
        }
    }

    public function Store(Request $request)
    {
        try {

            $store_eyewash_inspection = $this->eye_wash->store();
            $inspection_id = $store_eyewash_inspection->id;
            $store_inspection_details = $this->eye_wash_details->store($inspection_id);

            Session::flash('success','Monthly Eye Wash Inspection Added Successfully');
            return redirect(admin_url('safety/eye-wash-inspection/monthly/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong !');
            return admin_url('safety/eye-wash-inspection/monthly/list');
        }
    }

    public function GetLocations(Request $request)
    {
        try{
            $locations = $this->location->getLocationName();
            return response()->json($locations);
        }
        catch(Exception $ex)
        {
            report($ex);
            return response()->json(['error' => 'Please try again after sometimes'],406);
        }
    }

    public function View(Request $request)
    {
        try{
            $id = decryptId($request->id);
            $inspection = $this->eye_wash->selectOne($id);
            $inspection_details = $this->eye_wash_details->GetDetails($inspection->id);

            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
            );

            return view('inspection.Safety.eye_wash_inspection.view',$data);

        }
        catch(Exception $ex)
        {
            report($ex);
            Session::flash('error','Something went wrong !');
            return redirect(admin_url('safety/eye-wash-inspection/monthly/list'));
        }
    }
}
