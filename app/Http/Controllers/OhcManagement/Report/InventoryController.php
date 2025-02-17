<?php

namespace App\Http\Controllers\OhcManagement\Report;


use App\Http\Controllers\Controller;
use App\Mail\Ohc\MedicineRequisitionEmail;
use App\Models\Master\Department;
use App\Models\Master\Employee;
use App\Models\Master\Unit;
use App\Models\OhcManagement\Discard;
use App\Models\OhcManagement\Master\Medicine;
use App\Models\OhcManagement\Master\Vendor;
use App\Models\OhcManagement\MedicineRequisition;
use App\Models\OhcManagement\UserMedicineRequisition;
use App\Models\UploadLog;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;
use App\Models\OhcManagement\MedicineStock;
use App\Models\OhcManagement\OhcStatuslog;
use App\Models\OhcManagement\Report\Inventory;
use App\Models\OhcManagement\UserDiscard;
use App\Models\User;

class InventoryController extends Controller
{
    private $inventory;


    public function __construct()
    {
        $this->inventory = new Inventory();

    }
    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->inventory->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active<span>";
                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class= 'statusChange' data-id='" . encryptId($row->id) . "' data-type = '1' >Active<span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class= 'statusChange' data-id='" . encryptId($row->id) . "' data-type = '0' >In-Active<span>";
                            }
                            return $text;
                        })


                        ->editColumn('discard_date', function ($row) {
                            return displaydateformat($row->request_date);
                        })
                        ->editColumn('unit_id', function ($row) {
                            return getUnitname($row->unit_id);
                        })
                        ->editColumn('department_id', function ($row) {
                            return getDepartment($row->department_id);
                        })
                        ->editColumn('action', function ($row) {
                            $btn = '';
                            // if (CheckUserPermission('view')) {
                            $btn .= '<a href="' . admin_url('ohc/discard/view/' . encryptId($row->id)) . '" class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            // if (CheckUserPermission('edit')) {
                            // $btn .= '<a href="' . admin_url('ohc/discard/edit/' . encryptId($row->id)) . '" class="" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a> ';
                            // }
                            if ((checkUserRole(ROLE_SUPERADMIN) && $row->approve_status == STATUS_OHC_PARAMEDICS_APPROVAL_PENDING) || (checkUserRole(ROLE_PARAMEDICS) && $row->approve_status == STATUS_OHC_PARAMEDICS_APPROVAL_PENDING)) {
                                $btn .= '<a href="' . admin_url('ohc/discard/approval/view/' . encryptId($row->id)) . '" class="" title="Action"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            if ((checkUserRole(ROLE_SUPERADMIN) && $row->approve_status == STATUS_OHC_PARAMEDICS_APPROVED) || (checkUserRole(ROLE_PARAMEDICS) && $row->approve_status == STATUS_OHC_PARAMEDICS_APPROVED)) {
                                $btn .= '<a href="' . admin_url('ohc/medicine-issuance/add/' . encryptId($row->id)) . '" class="" title="Action"><i class="fas fa-share-square " style="color: #0013ff;"></i></a> ';
                            }


                            $btn .= '<a href="' . admin_url('ohc/discard/generalpdf/' . encryptId($row->id)) . '" class="" title="PDF"> <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i></a> ';
                            return $btn;
                        })

                        ->rawColumns(['action', 'request_date', 'approve_status','unit_id','department_id'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);

                    return response()->json($datatables->getData());
                } catch (Exception $ex) {
                    report($ex);
                    return response()->json(['status' => 'error', 'msg' => __('ppe.please_try_after_some_time')], 406);
                }
            }
        }


        $data = array(


        );

        return view('ohcmanagement.report.inventory.list', $data);
    }

}
