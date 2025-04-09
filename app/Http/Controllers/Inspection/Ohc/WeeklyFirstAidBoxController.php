<?php

namespace App\Http\Controllers\Inspection\Ohc;

use App\Http\Controllers\Controller;
use App\Models\Inspection\InspectionStaticDocno;
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
use Spatie\SimpleExcel\SimpleExcelWriter;


class WeeklyFirstAidBoxController extends Controller
{

    private $unit;
    private $certified_First_aid;
    private $location;
    private $shift;
    private $medicine;
    private $weekly_first_aid;
    private $signature;
    private $document_reference;
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
        $this->document_reference = new InspectionStaticDocno();
        $this->user = new User();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->weekly_first_aid->list();
                    // dd($data);
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
        $location = $this->location->getLocationname();
        $unit = $this->unit->getunit();
        $shift = $this->shift->getShiftname();
        $First_aid = $this->certified_First_aid->getFirsaid();

        $data = array(
            'location' => $location,
            'unit' => $unit,
            'shift' => $shift,
            'First_aid' => $First_aid,

        );

        return view('inspection.inspection_ohc.weekly_first_aid.list', $data);
    }


    public function Add(Request $request)
    {
        try {
            $unit = $this->unit->getunit();
            $shift = $this->shift->getShiftname();
            $First_aid = $this->certified_First_aid->getFirsaid();
            $medicines = $this->medicine->getFirstAidData();
            $location = $this->location->getLocationname();
            $document_no = $this->document_reference->selectUsingName('WeeklyFirstAidBoxInspectionChecklist');

            $data = array(
                'unit' => $unit,
                'shift' => $shift,
                'First_aid' => $First_aid,
                'location' => $location,
                'medicines' => $medicines,
                'document_no' => $document_no,
            );

            return view('inspection.inspection_ohc.weekly_first_aid.add', $data);
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
            dd($inspection_details);
            $inspection_type = OHC_TYPE_WEEEKLY_FIRST_AID_MEDICINE_STORE;
            $inspection_data = json_decode($inspection_details->inspection_data, true);
            $inspection_file = GetOHCSignature($inspection_details->created_by, $inspection_details->id, $inspection_type);
            $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);



            $data = array(
                'inspection_details' => $inspection_details,
                'inspection_file' => $inspection_file,
                'inspection_data' => $inspection_data,
                'document_no' => $document_no,

            );
            // dd($data);

            return view('inspection.inspection_ohc.weekly_first_aid.view', $data);
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
                $inspection_created_by = GetOHCSignature($inspection_details->created_by, $inspection_details->id, $inspection_type);
                $inspection_data = json_decode($inspection_details->inspection_data, true);
                $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);


                $data = array(
                    'inspection_details' => $inspection_details,
                    'inspection_created_by' => $inspection_created_by,
                    'inspection_data' => $inspection_data,
                    'document_no' => $document_no,

                );
                // dd($data);
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

            $html = view('inspection.inspection_ohc.weekly_first_aid.generalpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "Weekly First Aid Details.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            return redirect()->back()->withErrors(['error' => 'An error occurred while generating the PDF.']);
        }
    }

    public function ExportExcel(Request $request)
    {

        try {
            $allData = $this->weekly_first_aid->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'First Aid Box No',
                'Shift',
                'Location',
                'Unit',
                'First Aider Name',
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {
                $export = [];
                $export[] =  $i;
                $export[] =  $data->first_aid_box_no;
                $export[] =  getShift($data->shift_id);
                $export[] =  getLocationname($data->location);
                $export[] =  getUnitname($data->unit);
                $export[] =  getFirstAider($data->first_aider);
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);
                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Weekly First Aid.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            ini_set("pcre.backtrack_limit", "5000000");

            $allData = $this->weekly_first_aid->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'First Aid Box No',
                'Shift',
                'Location',
                'Unit',
                'First Aider Name',
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "First Aid Name",
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

            $view = view('inspection.inspection_ohc.weekly_first_aid.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Weekly First Aid.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->weekly_first_aid->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => __('First Aid  Status is changed')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => 'Something went wrong, Please try after sometimes!'], 406);
        }
    }
}
