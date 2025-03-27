<?php

namespace App\Http\Controllers\Inspection\Ohc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Inspection\Master\Shift;
use App\Models\Inspection\Ohc\DailyVitalEquipment;
use App\Models\Inspection\Ohc\OhcSignature;
use Exception;
use App\Models\Master\Unit;

class DailyVitalEquipmentController extends Controller
{
    private $daily_vital;
    private $unit;
    private $shift;
    private $signature;

    public function __construct()
    {
        $this->daily_vital = new DailyVitalEquipment();
        $this->unit = new Unit();
        $this->shift = new Shift();
        $this->signature = new OhcSignature();
    }
    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->daily_vital->list();
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
                        ->addColumn('issue_date', function ($row) {
                            return Displaydateformat($row->issue_date);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('ohc/daily-vital-equipment/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('ohc/daily-vital-equipment/exportViewPdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                        </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'status' ,'created_date', 'created_by', 'issue_date'])
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
        return view('inspection.inspection_ohc.daily_vital_equipment.list', $data);
    }

    public function Add(Request $request)
    {
        try {
            $checklistQuestions = getCheckListQuestion(OHC_DAILY_VITAL_EQUIPMENT_CHECKLIST);
            $options =  getoption(OHC_DAILY_VITAL_EQUIPMENT_CHECKLIST);
            $getoption = string_to_array($options->type);
            $shifts = $this->shift->getShiftname();
            $unit = $this->unit->getUnit();

            $data = array(
                'checklist_details' => $checklistQuestions,
                'getoption' => $getoption,
                'shifts' => $shifts,
                'units' => $unit,
            );
            return view('inspection.inspection_ohc.daily_vital_equipment.add', $data);
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/daily-vital-equipment/list'));
        }
    }

    public function Store(Request $request)
    {
        try {
            $daily_vital = $this->daily_vital->store();
            $id = $daily_vital->id;
            $inspection_type = OHC_DAILY_VITAL_EQUIPMENT_CHECKLIST;
            $signature_update = $this->signature->signatureUpload($id,$inspection_type);

            Session::flash('success', __('common.created_msg'));
            return redirect(admin_url('ohc/daily-vital-equipment/list'));
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/daily-vital-equipment/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $daily_vital = $this->daily_vital->selectOne($id);
       
            $data = [
                'daily_vital' => $daily_vital,
            ];
            return view('inspection.inspection_ohc.daily_vital_equipment.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/daily-vital-equipment/list'));
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->daily_vital->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Document Number',
                'Issue Date',
                'Revision Date',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->doc_no;
                $export[] =  $data->issue_date;
                $export[] = $data->revision_data;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);
                $exportData[] = $export;
                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Daily Vital Equipment.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/daily-vital-equipment/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->daily_vital->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }
            $header = [
                __("common.sno"),
                'Document Number',
                'Issue Date',
                'Revision Date',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Daily Vital Equipment",
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

            $view = view('inspection.inspection_ohc.daily_vital_equipment.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "OHC Daily Vital Equipment.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/daily-vital-equipment/list'));
        }
    }


    public function exportViewPdf(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $daily_vital = $this->daily_vital->selectOne($id);

                $data = [
                    'daily_vital' => $daily_vital,
                    'pagetitle' => "OHC Daily Vital Equipment",
                ];
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

            $html = view('inspection.inspection_ohc.daily_vital_equipment.viewPdf',$data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "OHC Daily Vital Equipment.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            return redirect()->back()->withErrors(['error' => 'An error occurred while generating the PDF.']);
        }
    }
}
