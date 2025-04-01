<?php

namespace App\Http\Controllers\Inspection\Fire;

use Exception;
use Response;
use Illuminate\Http\Request;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\Master\Employee;
use App\Models\Inspection\Fire\FireSafetyEquipment;
use App\Models\Inspection\Fire\Fire;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Master\Department;
use App\Models\Master\Unit;

class FireSafetyEquipmentsController extends Controller
{
    private $unit;
    private $fire_safety_equipment;
    private $upload_log;
    private $fire;
    private $static_docno;

    public function __construct()
    {
        $this->fire_safety_equipment = new FireSafetyEquipment();
        $this->unit = new Unit();
        $this->fire = new Fire();
        $this->static_docno = new InspectionStaticDocno();
    }

    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $type = 3;
                    $data  = $this->fire->list($type);
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
                            return Displaydatetimeformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('fire/fire-safety/equipments/code-sheet/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {
                           
                            // $btn .= '<a href="javascript:void(0);"  data-id="' . encryptId($row->id) . '"  class="recordDelete" title="' . __('common.delete') . '"><i class="fa-solid fa-trash text-danger" ></i></i></a> ';
                            // }
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status'])
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
        $fireList  = $this->fire->select('id', 'fire_no')->where('type', 3)->where('status', '1')->get();

        $data = array(
            'fireList' => $fireList,
        );
        return view('inspection.fire.fireSafetyEquipment.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
            $staticDocno  = $this->static_docno->select('id', 'doc_no','issue_date','rev_dt')->where([
                ['type', "FireSafetyEquipment"],
                ['status', '1']
            ])->first();
            
            $data = array(
                'unitList' => $unitList,
                'staticDocno' => $staticDocno,
            );
            return view('inspection.fire.fireSafetyEquipment.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'fire_safety_equipment_no' => 'required',
            ];
            $messages = [
                'fire_safety_equipment_no.required' => "Certified Fire Fighter No is Required",
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {
                $fire_no = $request->fire_safety_equipment_no;
                $type = 3;
                $fire =   $this->fire->store($fire_no, $type);
 
                $this->fire_safety_equipment->store($fire->id);

                Session::flash('success', __('Your data has been created successfully'));
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', __('common.message_error'));
            }

            return redirect(admin_url('fire/fire-safety/equipments/code-sheet/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('fire/fire-safety/equipments/code-sheet/list'));
        }
    }

  

    public function View($id)
    {
        try {
            $id = decryptId($id);
            if (Auth::check()) {
                $type = 3;
                $fireData =   $this->fire->selectOne($id,$type);
                $fireSafetyEquipmentDataList = $this->fire_safety_equipment->selectOne($id);
                $staticDocno  = $this->static_docno->select('id', 'doc_no','issue_date','rev_dt')->where([
                    ['type', "FireSafetyEquipment"],
                    ['status', '1']
                ])->first();
                $data = array(
                    'fireData' => $fireData,
                    'fireSafetyEquipmentDataList' => $fireSafetyEquipmentDataList,
                    'staticDocno' => $staticDocno,
                );
            }
            return view('inspection.fire.fireSafetyEquipment.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('fire/fire-safety/equipments/code-sheet/list'));
        }
    }


    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);
            $type = 3;
            $fireID = $this->fire->statuschange($id,$type);
            $this->fire_safety_equipment->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'status changed'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function ExportExcel()
    {
        try {
            $type = 3;
            $allData =   $this->fire->exportdata($type);
            $header = [
                __("common.sno"),
                __("Certified Fire Fighter Monitoring Id"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] = $data->fire_no;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Certified Fire Fighter Monitoring.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function ExportPDF()
    {
        try {

            ini_set("pcre.backtrack_limit", "5000000");
            $type = 3;
            $allData =   $this->fire->exportdata($type);

            $header = [
                __("common.sno"),
                __("Certified Fire Fighter Monitoring Id"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Certified Fire Fighter Monitoring",
            );

            $property = [
                'tempDir' => 'public/pdf/temp/',
                'mode' => 'c',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,

            ];

            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';

            $view = view('inspection.fire.fireSafetyEquipment.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Certified Fire Fighter Monitoring Details.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            dd($ex);
        }
    }


}
