<?php

namespace App\Http\Controllers\Inspection\Ohc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Department;
use App\Models\Master\Location;
use App\Models\Master\Unit;
use App\Models\Inspection\Master\Shift;

use App\Models\Inspection\Ohc\MedicineRequistionFdoChecklist;
use App\Models\Inspection\Ohc\OhcDetails;
use App\Models\OhcManagement\Report\Inventory;
use App\Models\UploadLog;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class MedicalRequisitionSlipSecurityGateController extends Controller
{

    private $upload_log;
    private $unit;
    private $shift;
    private $department;
    private $OhcDetails;
    private $user;
    private $medicine_requisition_fdo_checklist;

    private $inventory;


    private $location;
    public function __construct()
    {

        $this->upload_log = new UploadLog();
        $this->unit = new Unit();
        $this->department = new Department();
        $this->shift = new Shift();
        $this->location = new Location();
        $this->OhcDetails = new OhcDetails();
        $this->medicine_requisition_fdo_checklist = new MedicineRequistionFdoChecklist();
        $this->inventory = new Inventory();
        $this->user = new User();

    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {

                    $data = $this->OhcDetails->list();
                    $datatables = DataTables::of($data['data']);
                    $filteredData = collect($data['data'])->where('ohc_type', OHC_TYPE_MEDICINE_REQUISTION_FDO)->values();

                    $filteredCount = $filteredData->count();
                    $totalCount = count($data['data']);


                    return DataTables::of($filteredData)
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
                        ->addColumn('issue_date', function ($row) {
                            return Displaydateformat($row->issue_date);
                        })
                        ->addColumn('action', function ($row) {
                            return '<a href="' . admin_url('ohc/medical-requisition-slip/fdo-security-gate/view/' . encryptId($row->id)) . '" class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a>';
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status','issue_date'])
                        ->setFilteredRecords($filteredCount)
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);

                    return $datatables;
                } catch (Exception $ex) {
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }

        return view('inspection.inspection_ohc.medical_requisition_slip_security_gate.list');
    }

    public function Add(Request $request)
    {
        try {
            $unit = $this->unit->getunit();
            $shift = $this->shift->getShiftname();
            $medicine = $this->inventory->getstockdata();
            $signature_upload = $this->user->getSignature();
            $location = $this->location->getLocation();
            $data = array(
                'unit' => $unit,
                'shift' => $shift,
                'medicine' => $medicine,
                'signature_upload' => $signature_upload,

            );
            return view('inspection.inspection_ohc.medical_requisition_slip_security_gate.add', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'unit_id' => 'required',
                'department_id' => 'required',
                'review_date' => 'required',

            ];
            $messages = [
                'department_id.required' => 'Please select a Deparment.',
                'unit_id.required' => 'Please select a unit.',
                'review_date.required' => 'Please select the expiry date.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {
                // Store user medicine requisition
                $OhcDetails = $this->OhcDetails->store();

                $signature = $this->OhcDetails->signatureupload();
                $medicine_requisition_fdo_checklist = $this->medicine_requisition_fdo_checklist->store(  $OhcDetails);


                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/medical-requisition-slip/fdo-security-gate/list'));
        } catch (Exception $ex) {

            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medical-requisition-slip/fdo-security-gate/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $medicinerequisition = $this->OhcDetails->Selectone($id);
                $medicine_requisition_fdo_checklist = $this->medicine_requisition_fdo_checklist->Selectone($id);

                $data = array(
                    'medicinerequisition' => $medicinerequisition,
                    'medicine_requisition_fdo_checklist' => $medicine_requisition_fdo_checklist,
                );
            }
            return view('inspection.inspection_ohc.medical_requisition_slip_security_gate.view', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }
}
