<?php

namespace App\Http\Controllers\Inspection\ohc;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Inspection\Master\Shift;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Inspection\ohc\OHCHygieneCleaningChecklist;
use App\Models\Inspection\Ohc\OhcSignature;

class OHCHygieneCleaningChecklistController extends Controller
{
    private $ohc_hygiene;
    private $shift;
    private $signature;

    public function __construct()
    {
        $this->ohc_hygiene = new OHCHygieneCleaningChecklist();
        $this->shift = new Shift();
        $this->signature = new OhcSignature();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->ohc_hygiene->list();
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
                        ->addColumn('date', function ($row) {
                            return Displaydateformat($row->issue_date);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('shift', function ($row) {
                            return getShiftname($row->shift_id);
                        })
                        ->addColumn('checklist_status', function ($row) {
                            $text = '';
                            switch ($row->checklist_status) {
                                case CLEANER_SUBMITTED_THE_CHECKLIST:
                                    $text = "<span class='badge bg-primary rounded' style='font-size: 1.0em;'>Waiting For Nursing Officer Action</span>";
                                    break;
                                case NURSING_OFFICER_SUBMITTED_THE_CHECKLIST:
                                    $text = "<span class='badge bg-success rounded' style='font-size: 1.0em;'>Inspection Completed</span>";
                                    break;
                                default:
                                    $text = "<span class='badge rounded-pill text-bg-warning'>Unknown</span>";
                            }
                            return $text;
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('ohc/ohc-hygiene-cleaning-checklist/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('ohc/ohc-hygiene-cleaning-checklist/exportViewPdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                        </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'checklist_status', 'issue_date'])
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
            $shift  = $this->shift->select('id', 'shift')->where('status', '1')->get();
            $data = array(
                'shifts' => $shift,
            );
            return view('inspection.Safety.forklift_inspection_monthly.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/ohc-hygiene-cleaning-checklist/list'));
        }
    }

    public function store(Request $request)
    {
        try {
            // dd($request->all());
            $ohc_hygiene_inspection = $this->ohc_hygiene->store();
            // $signature_update = $this->signature->signatureUpload(MONTHLY_FORKLIFT_INSPECTION, $ohc_hygiene_inspection->id);


            Session::flash('success', __('common.created_msg'));
            return redirect(admin_url('ohc/ohc-hygiene-cleaning-checklist/list'));
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/ohc-hygiene-cleaning-checklist/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $inspection_details = $this->ohc_hygiene->selectOne($id);
            $data = [
                'inspection_details' => $inspection_details,
            ];
            return view('inspection.Safety.forklift_inspection_monthly.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/ohc-hygiene-cleaning-checklist/list'));
        }
    }

    public function approvals(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $approval_type = $request->employee_type;
            $inspection_details = $this->ohc_hygiene->selectOne($id);
            $data = [
                'inspection_details' => $inspection_details,
                'approval_type' => $approval_type,
            ];
            return view('inspection.Safety.forklift_inspection_monthly.approval', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/ohc-hygiene-cleaning-checklist/list'));
        }
    }
}
