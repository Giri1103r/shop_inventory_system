<?php

namespace App\Http\Controllers\Inspection\Ohc;

use Exception;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use App\Models\Master\Location;
use App\Models\Master\Department;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Inspection\Master\Shift;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Inspection\Master\Frequency;
use App\Models\Inspection\Ohc\FloorStretcher;
use App\Models\Inspection\Ohc\FloorStretcherFiles;
use App\Models\Inspection\Ohc\FloorStretcherDetails;

class FloorStretcherController extends Controller
{
    private $floor_strecther;
    private $floor_details;
    private $floor_files;
    private $frequency;
    private $unit;
    private $shift;
    private $location;
    private $department;
    

    public function __construct()
    {
        $this->floor_strecther = new FloorStretcher();
        $this->floor_details = new FloorStretcherDetails();
        $this->floor_files = new FloorStretcherFiles();
        $this->frequency = new Frequency();
        $this->unit = new Unit();
        $this->shift = new Shift();
        $this->location = new Location();
        $this->department = new Department();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->floor_strecther->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active</span>";
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {
                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->checklist_id) . "' data-type = '1'>Active</span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->checklist_id) . "' data-type = '0'>In-Active</span>";
                            }
                            // }
                            return $text;
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
                        
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('fire/monthly-fire-pump-house-inspection/view/' . encryptId($row->checklist_id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            
                            $btn .= '<a href="' . admin_url('fire/monthly-fire-pump-house-inspection/exportViewPdf/' . encryptId($row->checklist_id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                        </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by','issue_date'])
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

        return view('inspection.ohc.floor_stretcher.list');

    }

    public function Add(Request $request)
    {
        try {
           
            $shifts = $this->shift->getShiftname();
            $frequency = $this->frequency->getFrequency();
            $unit = $this->unit->getUnit();
            $data = array(
                
                'shifts' => $shifts,
                'units' => $unit,
                'frequency' => $frequency,
            );
            return view('inspection.ohc.floor_stretcher.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/monthly-fire-pump-house-inspection/list'));
        }
    }
}
