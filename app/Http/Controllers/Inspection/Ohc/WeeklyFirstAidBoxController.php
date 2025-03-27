<?php

namespace App\Http\Controllers\Inspection\Ohc;

use App\Http\Controllers\Controller;
use App\Models\Inspection\Master\Shift;
use App\Models\Inspection\Ohc\Master\FirstAidEquipment;
use App\Models\Inspection\Ohc\OhcSignature;
use App\Models\Inspection\Ohc\WeeklyFirstAidBox;
use App\Models\Master\Location;
use App\Models\Master\Unit;
use App\Models\OhcManagement\Master\CertifiedFirstAider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class WeeklyFirstAidBoxController extends Controller
{

    private $unit;
    private $certified_First_aid;
    private $location;
    private $shift;
    private $medicine;
    private $weekly_first_aid;
    private $signature;
    private $user;


    public function __construct()
    {
        $this->unit = new Unit();
        $this->shift = new Shift();
        $this->location = new Location();
        $this->certified_First_aid = new CertifiedFirstAider();
        $this->medicine = new FirstAidEquipment();
        $this->weekly_first_aid = new WeeklyFirstAidBox();
        $this->signature = new OhcSignature();
        $this->user = new User();



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
        // dd($request->all());
        try {
            
            try {

                $weekly_first_aid = $this->weekly_first_aid->store();
                $weekly_first_aid_id = $weekly_first_aid->id;
                $inspection_type = OHC_TYPE_WEEEKLY_FIRST_AID_MEDICINE_STORE;
                $inspection_details = $this->weekly_first_aid->selectOne($weekly_first_aid_id);
                $files = $this->signature->requestorsignatureUpload($inspection_type, $inspection_details->id);

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

    public function View(Request $request)
    {
        try {
            
            $id = decryptId($request->id);
            $inspection_details = $this->weekly_first_aid->selectOne($id);
            $inspection_type = OHC_TYPE_WEEEKLY_FIRST_AID_MEDICINE_STORE;
            $inspection_file = $this->signature->getFiles($inspection_details->created_by, $inspection_type);
            $inspection_data = json_decode($inspection_details->inspection_data, true);
            $creater_signature = $this->user->where('id',$inspection_details->created_by)->first();

            $data = array(
                'inspection_details' => $inspection_details,
                'inspection_file' => $inspection_file,
                'inspection_data' => $inspection_data,
                'creater_signature' => $creater_signature,
            );


            return view('inspection.inspection_ohc.weekly_first_aid.view',$data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/monthly-medicine-store/inspection/list'));
        }
    }

    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);
            
            if (Auth::check()) {
                $id = decryptId($request->id);
                $inspection_details = $this->weekly_first_aid->selectOne($id);
                $inspection_type = OHC_TYPE_WEEEKLY_FIRST_AID_MEDICINE_STORE;
                $inspection_created_by = $this->signature->getFilesByEmpId($inspection_details->created_by, $inspection_type);
                $inspection_data = json_decode($inspection_details->inspection_data, true);

                $data = array(
                    'inspection_details' => $inspection_details,
                    'inspection_created_by' => $inspection_created_by,
                    'inspection_data' => $inspection_data,
                );
                
            }
            $property = [
                'tempDir' => 'public/pdf/temp/',
                'mode' => 'c',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,

            ];

            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';

            $html = view('inspection.inspection_ohc.weekly_first_aid.generalpdf',$data)->render();
            $mpdf->WriteHTML($html);

            $filename = "Weekly First Aid Details.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            return redirect()->back()->withErrors(['error' => 'An error occurred while generating the PDF.']);
        }
    }
}
