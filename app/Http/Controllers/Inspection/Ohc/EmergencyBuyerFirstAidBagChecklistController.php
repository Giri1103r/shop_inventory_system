<?php

namespace App\Http\Controllers\Inspection\Ohc;

use App\Http\Controllers\Controller;
use App\Models\Inspection\Master\Shift;
use App\Models\Inspection\Ohc\Master\FirstAidEquipment;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Exception;
use Illuminate\Support\Facades\Validator;



class EmergencyBuyerFirstAidBagChecklistController extends Controller
{
    private $unit;
    private $shift;
    private $medicine;


    public function __construct()
    {
        $this->unit = new Unit();
        $this->shift = new Shift();
        $this->medicine = new FirstAidEquipment();

    }
    public function Index(Request $request){
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->weekly_first_aid->list();
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
                            $btn = '<a href="' . admin_url('ohc/first-aid-box/weekly-inspection/view/' . encryptId($row->id)) . '" class="view-icon" title="' . __('common.view') . '">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>';
                        
                            $btn .= '<a href="' . admin_url('ohc/first-aid-box/weekly-inspection/generalpdf/' . encryptId($row->id)) . '" style="margin-left: 5px;" title="PDF">
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
        // $location = $this->location->getLocationname();
        // $unit = $this->unit->getunit();
        // $data = array(
        //     'location'=>$location,
        //     'unit'=>$unit
        // );
        return view('inspection.inspection_ohc.emergency_buyer_bag_inspection.list');
    }

    public function Add(Request $request)
    {
        try {
            $unit = $this->unit->getunit();
            $shift = $this->shift->getShiftname();
            $medicines = $this->medicine->getFirstAidData();
            $data = array(
                'unit' => $unit,
                'shift' => $shift,
                'medicines' => $medicines,
            );
            
            return view('inspection.inspection_ohc.emergency_buyer_bag_inspection.add',$data);


        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {
        try {
            $rules = [
                'date_of_inspection' => 'required',
                'location_first_bag' => 'required',
                'shift' => 'required',
                'next_due_date' => 'required',
                'unit_id' => 'required',
                'frequency' => 'required',
                'medicine_id.*' => 'required',
                'freeze_quantity.*' => 'required',
                'available_quantity.*' => 'required',
                'expired_date.*' => 'required',
                'remarks.*' => 'required',
                'remark_by' => 'required',
            ];
            
            $messages = [
                'date_of_inspection.required' => 'Date of inspection is required.',
                'location_first_bag.required' => 'Location of the first aid bag is required.',
                'shift.required' => 'Shift selection is required.',
                'next_due_date.required' => 'Next due date is required.',
                'unit_id.required' => 'Unit selection is required.',
                'frequency.required' => 'Frequency is required.',
                'medicine_id.*.required' => 'Medicine ID is required.',
                'freeze_quantity.*.required' => 'Freeze quantity is required.',
                'available_quantity.*.required' => 'Available quantity is required.',
                'expired_date.*.required' => 'Expired date is required.',
                'remarks.*.required' => 'Remarks are required.',
                'remark_by.required' => 'Remark by is required.',
            ];
            
            $validator = Validator::make($request->all(), $rules, $messages);
            
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            
            
            try {
                
                Session::flash('success', 'Your data has been created successfully!');
                return redirect(admin_url('ohc/first-aid-box/weekly-inspection/list'));
                
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }
            
        } catch (Exception $ex) {
            report($ex);
        }
    }
}
