<?php

namespace App\Http\Controllers\Inspection\Ohc;

use Exception;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Ohc\FirstAidRecordDetails;
use App\Models\OhcManagement\Master\FirstAidLocation;
use App\Models\Inspection\Ohc\FirstAidRecordChecklist;
use App\Models\Inspection\Ohc\FirstAidRecordStatusLog;
use App\Models\Inspection\Ohc\FirstAidRecordSignatureUpload;

class FirstAidRecordController extends Controller
{
    private $first_aid_details;
    private $first_aid_checklist;
    private $unit;
    private $firstAidLocation;
    private $document_reference;

    public function __construct()
    {
        $this->first_aid_details = new FirstAidRecordDetails();
        $this->first_aid_checklist = new FirstAidRecordChecklist();
        $this->unit = new Unit();
        $this->firstAidLocation = new FirstAidLocation();
        $this->document_reference = new InspectionStaticDocno();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->first_aid_details->list();
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
                            $btn = '<a href="' . admin_url('ohc/first-aid-record/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('ohc/first-aid-record/generalpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                                <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                            </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'issue_date', 'created_by', 'status'])
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

        $data = [];

        return view('inspection.inspection_ohc.first_aid_record.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $unit = $this->unit->getunit();
            $document_no = $this->document_reference->selectUsingName('FirstAidRecord');
            $data = [
                'unit' => $unit,
                'document_no' => $document_no,
            ];
            return view('inspection.inspection_ohc.first_aid_record.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }
    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $month = $request->month;
            $year = $request->year;
            $id = $request->id;

            if ($id == '') {
                $record = $this->first_aid_details->uniqueCheck($month, $year);
            } else {
                $id = decryptId($id);
                $record = $this->first_aid_details->ExistuniqueCheck($month, $year, $id);
            }

            if ($record->count()) {
                return Response::json(false);
            }

            return Response::json(true);
        }
    }

    public function getFirstAidDetails(Request $request)
    {
        $unitId = decryptId($request->unit_id);
        $departmentId = decryptId($request->department_id);

        $firstAidLocation = $this->firstAidLocation->getDetail($unitId,$departmentId);

        if ($firstAidLocation) {
            return response()->json([
                'station_number' => $firstAidLocation->station_number,
                'first_aid_box_no' => $firstAidLocation->first_aid_box_no,
            ]);
        } else {
            return response()->json([
                'station_number' => '',
                'first_aid_box_no' => '',
            ]);
        }
    }

    public function Store(Request $request)
    {
        try {

            $first_aid_details = $this->first_aid_details->store();
            $first_aid_detail_id = $first_aid_details->id;
            $this->first_aid_checklist->store($first_aid_detail_id);

            Session::flash('success', __('Your data has been created successfully'));
            return redirect(admin_url('ohc/first-aid-record/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/first-aid-record/list'));
        }
    }

    public function StatusChange(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $this->first_aid_details->statuschange($id);

            $this->first_aid_checklist->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Your status  has changed Successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $first_aid_details = $this->first_aid_details->find($id);

                $first_aid_checklist = $this->first_aid_checklist->selectOne($id);
                $document_no = $this->document_reference->selectOne($first_aid_details->document_reference_id);

                $data = array(
                    'first_aid_details' => $first_aid_details,
                    'first_aid_checklist' => $first_aid_checklist  ?? [],
                    'document_no' => $document_no,
                );
            }
            return view('inspection.inspection_ohc.first_aid_record.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function ExportExcel(Request $request)
    {
        try {

            $allData = $this->first_aid_details->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                    'Month',
                    'Year',
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->month;
                $export[] =  $data->year;
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('First Aid Record.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/first-aid-record/list'));
        }
    }

    public function ExportPdf(Request $request)
    {
        try {

            $allData = $this->first_aid_details->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                    'Month',
                    'Year',
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "First Aid Record Details",
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

            $view = view('inspection.inspection_ohc.first_aid_record.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "First Aid Record.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/first-aid-record/list'));
        }
    }

    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $first_aid_details = $this->first_aid_details->find($id);
                $first_aid_checklist = $this->first_aid_checklist->selectOne($id);
                $document_no = $this->document_reference->selectUsingName('FirstAidRecord');

                $data = array(
                    'first_aid_details' => $first_aid_details,
                    'first_aid_checklist' => $first_aid_checklist  ?? [],
                    'document_no' => $document_no,
                    'pagetitle' => "OHC FIRST AID RECORD",
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

            $html = view('inspection.inspection_ohc.first_aid_record.generalpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "First Aid Record.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            return redirect()->back()->withErrors(['error' => 'An error occurred while generating the PDF.']);
        }
    }

}
