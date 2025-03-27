<?php

namespace App\Http\Controllers\Inspection\Ohc;

use App\Http\Controllers\Controller;
use App\Models\Inspection\Master\Shift;
use App\Models\Inspection\Ohc\Master\FirstAidEquipment;
use App\Models\Master\Location;
use App\Models\Master\Unit;
use App\Models\OhcManagement\Master\CertifiedFirstAider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class WeeklyFirstAidBoxController extends Controller
{

    private $unit;
    private $certified_First_aid;
    private $location;
    private $shift;
    private $medicine;




    public function __construct()
    {
        $this->unit = new Unit();
        $this->shift = new Shift();
        $this->location = new Location();
        $this->certified_First_aid = new CertifiedFirstAider();
        $this->medicine = new FirstAidEquipment();



    }

    public function Index(Request $request){
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->health_instrument_calibration->list();
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
                        ->addColumn('created_at', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('issue_date', function ($row) {
                            return Displaydateformat($row->issue_date);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })

                        ->addColumn('action', function ($row) {
                            $btn = '<a href="' . admin_url('ohc/health-instrument/calibration-track-sheet/view/' . encryptId($row->id)) . '" class="view-icon" title="' . __('common.view') . '">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>';
                        
                            $btn .= '<a href="' . admin_url('ohc/health-instrument/calibration-track-sheet/generalpdf/' . encryptId($row->id)) . '" style="margin-left: 5px;" title="PDF">
                                        <i class="fas fa-file-pdf" style="color: #e67265;" aria-hidden="true"></i>
                                    </a>';
                        
                            return $btn;
                        })
                        
                        ->rawColumns(['action', 'issue_date', 'created_by', 'status', 'created_at'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);

                    return $datatables;
                } catch (Exception $ex) {
                    dd($ex);
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }
        return view('inspection.inspection_ohc.weekly_first_aid.list');
    }


    public function Add(Request $request)
    {
        try {
            $unit = $this->unit->getunit();
            $shift = $this->shift->getShiftname();
            $First_aid = $this->certified_First_aid->getFirsaid();
            $medicines = $this->medicine->getFirstAidData();
            $location = $this->location->getLocationname();
            $data = array(
                'unit' => $unit,
                'shift' => $shift,
                'First_aid' => $First_aid,
                'location' => $location,
                'medicines' => $medicines,
            );
            
            return view('inspection.inspection_ohc.weekly_first_aid.add',$data);

        } catch (Exception $ex) {
            report($ex);
        }
    }


    public function Store(Request $request)
    {
        dd($request->all());
        try {
            // $rules = [
            //     'document_no' => 'required',
            //     'issue_date' => 'required',
            //     'review_date' => 'required',
            //     'health_instrument.*.sr_no' => 'required',
            //     'health_instrument.*.instrument_name' => 'required',
            //     'health_instrument.*.resource_code' => 'required',
            //     'health_instrument.*.exact_location' => 'required',
            //     'health_instrument.*.unit_id' => 'required',
            //     'health_instrument.*.instrument_serial_no' => 'required',
            //     'health_instrument.*.make' => 'required',
            //     'health_instrument.*.model' => 'required',
            //     'health_instrument.*.instrument_range' => 'required',
            //     'health_instrument.*.calibration_frequency' => 'required',
            //     'health_instrument.*.date_of_calibration' => 'required',
            //     'health_instrument.*.due_date_of_calibration' => 'required',
            //     'health_instrument.*.instrument_remarks' => 'required',
            // ];
            
            // $messages = [
            //     'document_no.required' => 'Document number is required.',
            //     'issue_date.required' => 'Issue date is required.',
            //     'review_date.required' => 'Review date is required.',
            //     'health_instrument.*.sr_no.required' => 'Serial number is required.',
            //     'health_instrument.*.instrument_name.required' => 'Please enter the instrument name.',
            //     'health_instrument.*.resource_code.required' => 'Please enter the resource code.',
            //     'health_instrument.*.exact_location.required' => 'Please specify the exact location.',
            //     'health_instrument.*.unit_id.required' => 'Please select a unit.',
            //     'health_instrument.*.instrument_serial_no.required' => 'Instrument serial number is required.',
            //     'health_instrument.*.make.required' => 'Please enter the make of the instrument.',
            //     'health_instrument.*.model.required' => 'Please enter the model of the instrument.',
            //     'health_instrument.*.instrument_range.required' => 'Instrument range is required.',
            //     'health_instrument.*.calibration_frequency.required' => 'Calibration frequency is required.',
            //     'health_instrument.*.date_of_calibration.required' => 'Please select the date of calibration.',
            //     'health_instrument.*.due_date_of_calibration.required' => 'Please select the due date of calibration.',
            //     'health_instrument.*.instrument_remarks.required' => 'Remarks are required.',
            // ];
            
            // $validator = Validator::make($request->all(), $rules, $messages);
            
            
            // if ($validator->fails()) {
            //     return redirect()->back()->withErrors($validator)->withInput();
            // }  
            try {

                $health_instrument = $this->health_instrument_calibration->store();
                $id = $health_instrument->id;

                $health_instrument_details = $this->health_instrument_calibration_details->store($id);

                Session::flash('success', 'Your data has been created successfully!');
                return redirect(admin_url('ohc/health-instrument/calibration-track-sheet/list'));

            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

        } catch (Exception $ex) {
            report($ex);
        }
    }
}
