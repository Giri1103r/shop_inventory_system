<?php

namespace App\Http\Controllers\Inspection\Safety;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Inspection\Master\Shift;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Inspection\Safety\SafetyWalkObservation;
use App\Models\Inspection\Safety\SafetyWalkObservationDetails;
use App\Models\Master\Location;
use App\Models\Master\Unit;

class SafetyWalkObservationController extends Controller
{
    private $safety_walk;
    private $observation_details;
    private $shift;
    private $unit;
    private $location;

    public function __construct()
    {
        $this->safety_walk = new SafetyWalkObservation();
        $this->observation_details = new SafetyWalkObservationDetails();
        $this->shift = new Shift();
        $this->unit = new Unit();
        $this->location = new Location();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->safety_walk->list();
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
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('safety/safety-walk-observation/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('safety/safety-walk-observation/exportViewPdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                        <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                    </a>';
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
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', 'inspection_status', 'issue_date'])
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
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('safety/safety-walk-observation/list'));
        }
    }
}
