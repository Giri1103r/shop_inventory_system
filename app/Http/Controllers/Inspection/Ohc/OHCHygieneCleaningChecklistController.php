<?php

namespace App\Http\Controllers\Inspection\ohc;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Inspection\Master\Shift;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Models\Inspection\Ohc\OhcSignature;
use App\Models\Inspection\ohc\OHCHygieneCleaningChecklist;

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
                            if ($row->checklist_status == CLEANER_SUBMITTED_THE_CHECKLIST) {
                                $btn .= '<a href="' . admin_url('ohc/ohc-hygiene-cleaning-checklist/approval/' . encryptId($row->id)) . '" class="" title="' . __('inspection.ehs_officer_verify') . '"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }
                            $btn .= '<a href="' . admin_url('ohc/ohc-hygiene-cleaning-checklist/generalpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
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
                    dd($ex);
                    report($ex);
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }
        $shift  = $this->shift->select('id', 'shift')->where('status', '1')->get();

        $data = array(
            'shifts' => $shift,
        );
        return view('inspection.inspection_ohc.ohc_hygiene_checklist.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $shift  = $this->shift->select('id', 'shift')->where('status', '1')->get();
            $data = array(
                'shifts' => $shift,
            );
            return view('inspection.inspection_ohc.ohc_hygiene_checklist.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/ohc-hygiene-cleaning-checklist/list'));
        }
    }

    public function store(Request $request)
    {
        try {

            $rules = [
                'issue_date' => 'required',
                'shift_id' => 'required',
                'inspection' => 'required',
                'remarks' => 'required',
                'signature_image' => [
                    function ($attribute, $value, $fail) {
                        $user = Auth::user();
                        if (is_null($user->signature_upload)) {
                            $fail('Signature is required.');
                        }
                    }
                ],
            ];

            $messages = [
                'issue_date.required' => 'Issue Date is required.',
                'shift_id.required' => 'Shift ID is required.',
                'inspection.required' => 'Inspection is required.',
                'remarks.required' => 'Remarks is required.',
                'signature_image' => 'Signature is required.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $ohc_hygiene_inspection = $this->ohc_hygiene->store();
            $signature_update = $this->signature->requestorsignatureUpload(DAILY_OHC_HYGIENE_CLEANING_CHECKLIST, $ohc_hygiene_inspection->id);
            Session::flash('success', __('common.created_msg'));
            return redirect(admin_url('ohc/ohc-hygiene-cleaning-checklist/list'));
        } catch (Exception $ex) {
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
            $cleaner_signature = GetOHCSignature($inspection_details->created_by, $inspection_details->id, DAILY_OHC_HYGIENE_CLEANING_CHECKLIST);
            $nursing_signature = GetOHCSignature($inspection_details->updated_by, $inspection_details->id, DAILY_OHC_HYGIENE_CLEANING_CHECKLIST);
            $data = [
                'inspection_details' => $inspection_details,
                'cleaner_signature' => $cleaner_signature,
                'nursing_signature' => $nursing_signature,
            ];
            return view('inspection.inspection_ohc.ohc_hygiene_checklist.view', $data);
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/ohc-hygiene-cleaning-checklist/list'));
        }
    }
    public function approval(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $inspection_details = $this->ohc_hygiene->selectOne($id);
            $cleaner_signature = GetOHCSignature($inspection_details->created_by, $inspection_details->id, DAILY_OHC_HYGIENE_CLEANING_CHECKLIST);
            $nursing_signature = GetOHCSignature($inspection_details->updated_by, $inspection_details->id, DAILY_OHC_HYGIENE_CLEANING_CHECKLIST);
            $data = [
                'inspection_details' => $inspection_details,
                'cleaner_signature' => $cleaner_signature,
                'nursing_signature' => $nursing_signature,
            ];
            return view('inspection.inspection_ohc.ohc_hygiene_checklist.approval', $data);
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/ohc-hygiene-cleaning-checklist/list'));
        }
    }

    public function approvalSubmit(Request $request)
    {
        try {
            $data = $this->ohc_hygiene->approvalSubmit();
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('ohc/ohc-hygiene-cleaning-checklist/list'));
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/ohc-hygiene-cleaning-checklist/list'));
        }
    }

    public function ExportExcel(Request $request)
    {

        try {
            $allData = $this->ohc_hygiene->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Issue Date',
                'Shift',
                'Checklist Status',
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {
                $export = [];
                $export[] =  $i;
                $export[] =  $data->issue_date;
                $export[] =  getShiftname($data->shift_id);
                $export[] = $data->checklist_status == '1' ? 'Waiting For Nursing Officer Action' : 'Inspection Completed';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);
                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('OHC HYGIENE CLEANING CHECKLIST.xlsx')
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

            $allData = $this->ohc_hygiene->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }


            $header = [
                __("common.sno"),
                'Issue Date',
                'Shift',
                'Checklist Status',
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "OHC HYGIENE CLEANING CHECKLIST",
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

            $view = view('inspection.inspection_ohc.ohc_hygiene_checklist.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "OHC HYGIENE CLEANING CHECKLIST.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
        }
    }


    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $inspection_details = $this->ohc_hygiene->selectone($id);
                $cleaner_signature = GetOHCSignature($inspection_details->created_by, $inspection_details->id, DAILY_OHC_HYGIENE_CLEANING_CHECKLIST);
                $nursing_signature = GetOHCSignature($inspection_details->updated_by, $inspection_details->id, DAILY_OHC_HYGIENE_CLEANING_CHECKLIST);

                $data = [
                    'cleaner_signature' => $cleaner_signature,
                    'nursing_signature' => $nursing_signature,
                    'inspection_details' => $inspection_details,
                    'pagetitle' => "OHC Hygiene Inspection Checklist",
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

            $html = view('inspection.inspection_ohc.ohc_hygiene_checklist.generalPdf', $data)->render();

            $mpdf->WriteHTML($html);

            $filename = "OHC Hygiene Inspection Checklist.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            return redirect()->back()->withErrors(['error' => 'An error occurred while generating the PDF.']);
        }
    }
}
