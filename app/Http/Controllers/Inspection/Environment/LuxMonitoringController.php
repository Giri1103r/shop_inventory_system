<?php

namespace App\Http\Controllers\Inspection\Environment;

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
use App\Models\Inspection\environment\LuxMonitoring;
use App\Models\Inspection\environment\Environment;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Master\Location;
use App\Models\Master\Unit;

class LuxMonitoringController extends Controller
{
    private $location;
    private $unit;
    private $lux_monitoring;
    private $upload_log;
    private $environment;
    private $static_docno;

    public function __construct()
    {
        $this->lux_monitoring = new LuxMonitoring();
        $this->unit = new Unit();
        $this->location = new Location();
        $this->environment = new Environment();
        $this->static_docno = new InspectionStaticDocno();
    }

    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $type = LUX;
                    $data  = $this->environment->list($type);
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
                        ->addColumn('created_at', function ($row) {
                            return Displaydatetimeformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('environment/lux/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {
                           
                            // $btn .= '<a href="javascript:void(0);"  data-id="' . encryptId($row->id) . '"  class="recordDelete" title="' . __('common.delete') . '"><i class="fa-solid fa-trash text-danger" ></i></i></a> ';
                            // }
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_at', 'created_by', 'status'])
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
        $environmentList  = $this->environment->select('id', 'environment_no')->where('type', LUX)->where('status', '1')->get();

        $data = array(
            'environmentList' => $environmentList,
        );
        return view('inspection.environment.luxMonitoring.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $locationList  = $this->location->select('id', 'location_name')->where('status', '1')->get();
            $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
            $staticDocno  = $this->static_docno->select('id', 'doc_no','issue_date','rev_dt')->where([
                ['type', "Lux"],
                ['status', '1']
            ])->first();
            
            $data = array(
                'locationList' => $locationList,
                'unitList' => $unitList,
                'staticDocno' => $staticDocno,
            );
            return view('inspection.environment.luxMonitoring.add', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'lux_no' => 'required',
            ];
            $messages = [
                'lux_no.required' => "Lux No is Required",
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {
                $env_no = $request->lux_no;
                $type = LUX;
                $environment =   $this->environment->store($env_no, $type);
 
                $this->lux_monitoring->store($environment->id);

                Session::flash('success', __('Your data has been created successfully'));
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', __('common.message_error'));
            }

            return redirect(admin_url('environment/lux/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('environment/lux/list'));
        }
    }

  

    public function View($id)
    {
        try {
            $id = decryptId($id);
            if (Auth::check()) {
                $type = LUX;
                $environmentData =   $this->environment->selectOne($id,$type);
                $luxDataList = $this->lux_monitoring->selectOne($id);
                $staticDocno  = $this->static_docno->select('id', 'doc_no','issue_date','rev_dt')->where([
                    ['type', "Lux"],
                    ['status', '1']
                ])->first();
                $data = array(
                    'environmentData' => $environmentData,
                    'luxDataList' => $luxDataList,
                    'staticDocno' => $staticDocno,
                );
            }
            return view('inspection.environment.luxMonitoring.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('environment/lux/list'));
        }
    }


    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);
            $type = LUX;
            $environmentID = $this->environment->statuschange($id,$type);
            $this->lux_monitoring->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'status changed'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function ExportExcel()
    {
        try {
            $type = LUX;
            $allData =   $this->environment->exportdata($type);
            $header = [
                __("common.sno"),
                __("Lux Monitoring Id"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] = $data->environment_no;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Ambient Air Monitoring Yearly.xlsx')
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
            $type = LUX;
            $allData =   $this->environment->exportdata($type);

            $header = [
                __("common.sno"),
                __("Lux Monitoring Id"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Lux Monitoring",
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

            $view = view('inspection.environment.luxMonitoring.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Lux Monitoring Details.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            dd($ex);
        }
    }


}
