<?php

namespace App\Http\Controllers\Inspection\Ohc;

use App\Http\Controllers\Controller;
use App\Models\Master\Employee;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use Exception;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\Master\Work;
use App\Models\Inspection\Ohc\OhcSignature;
use App\Models\Inspection\Ohc\SafetyPettyChecklist;
use App\Models\Inspection\Ohc\SafetyPettyDetails;
use App\Models\User;
use Illuminate\Support\Facades\Response;

class SafetyPettyController extends Controller
{
    private $sfty_petty_details;
    private $sfty_petty_checklist;
    private $employee;
    private $work;
    private $unit;
    private $signature;
    private $user;

    public function __construct()
    {
        $this->sfty_petty_details = new SafetyPettyDetails();
        $this->sfty_petty_checklist = new SafetyPettyChecklist();
        $this->employee = new Employee();
        $this->work = new Work();
        $this->unit = new Unit();
        $this->signature = new OhcSignature();
        $this->user = new User();

    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->sfty_petty_details->list();
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
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('ohc/safety-petty-logbook/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('ohc/safety-petty-logbook/generalpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                                <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                            </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'inspection_status', 'created_by', 'status'])
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
        return view('inspection.inspection_ohc.safety_petty.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $type = OHC_SAFETY_PETTY_LOGBOOK_INSPECTION;
            $unit = $this->unit->getunit();

            $data = [
                'unit' => $unit,
            ];
            return view('inspection.inspection_ohc.safety_petty.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function getSignature(Request $request)
    {
        $loginId = $request->input('login_id');

        if ($loginId) {
            $user = User::where('id', $loginId)->first();
            if ($user && $user->signature_upload) {
                return response()->json([
                    'signature_upload' => $user->signature_upload
                ]);
            }
        }

        return response()->json([
            'signature_upload' => null
        ]);
    }

    public function Store(Request $request)
    {
        try {

            $sfty_petty_details = $this->sfty_petty_details->store();
            $sfty_petty_id = $sfty_petty_details->id;
            $this->sfty_petty_checklist->store($sfty_petty_id);
            $empId =  Auth::user()->id;

            $this->signature->signatureLogUpload(
                $empId, $sfty_petty_id ,
                OHC_AMOUNT_GIVENBY_INSPECTION,
                'signature_givenby_image'
            );
            $this->signature->signatureLogUpload(
                $empId, $sfty_petty_id ,
                OHC_AMOUNT_RECEIVEDBY_INSPECTION,
                'signature_receivedby_image'
            );

            Session::flash('success', __('Your data has been created successfully'));
            return redirect(admin_url('ohc/safety-petty-logbook/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/safety-petty-logbook/list'));
        }
    }

    public function employeeid(Request $request)
    {
        $name = $request->input('search');

        $employee_code = $this->employee->where('emp_id', 'like', '%' . $name . '%')
            ->where('status', 1)
            ->limit(10)
            ->get();

        return response()->json(
            $employee_code->map(function ($employee) {
                return [
                    'id' => $employee->login_id,
                    'text' => $employee->emp_id . ' - ' . $employee->emp_name,
                ];
            })
        );
    }

    public function view(Request $request)
    {
       
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $sfty_petty_details = $this->sfty_petty_details->find($id);
                $sfty_petty_checklist = $this->sfty_petty_checklist->selectOne($id);

                $type = OHC_SAFETY_PETTY_LOGBOOK_INSPECTION;
                $sub_type_given = OHC_AMOUNT_GIVENBY_INSPECTION;
                $sub_type_received = OHC_AMOUNT_RECEIVEDBY_INSPECTION;

                $signature_amount_givenby = $this->signature->getGivenBy($type,$sub_type_given, $sfty_petty_checklist->amount_given_by);

                $signature_amount_receivedby = $this->signature->getReceivedBy($type,$sub_type_received, $sfty_petty_checklist->amount_received_by);
                
                $data = array(
                    'sfty_petty_details' => $sfty_petty_details,
                    'sfty_petty_checklist' => $sfty_petty_checklist,
                    'signature_amount_givenby' => $signature_amount_givenby,
                    'signature_amount_receivedby' => $signature_amount_receivedby,
                );
              
            }
            return view('inspection.inspection_ohc.safety_petty.view', $data);
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
        }
    }

    public function StatusChange(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $this->sfty_petty_details->statuschange($id);

            $this->sfty_petty_checklist->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Your status  has changed Successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function ExportExcel(Request $request)
    {
        try {
            $allData = $this->sfty_petty_details->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Document Number',
                'Issue Date',
                'Revision Data',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->document_number;
                $export[] =  $data->issue_date;
                $export[] = $data->revision_date;
                $export[] = getInspectionStatus($data->inspection_status);
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Safety Petty Logbook.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/safety-petty-logbook/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->sfty_petty_details->exportdata();

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
                'pagetitle' => "Safety Petty Logbook Details",
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

            $view = view('inspection.inspection_ohc.safety_petty.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Safety Petty Logbook.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/safety-petty-logbook/list'));
        }
    }

    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $sfty_petty_details = $this->sfty_petty_details->find($id);
                $sfty_petty_checklist = $this->sfty_petty_checklist->selectOne($id);

                $type = OHC_SAFETY_PETTY_LOGBOOK_INSPECTION;
                $sub_type_given = OHC_AMOUNT_GIVENBY_INSPECTION;
                $sub_type_received = OHC_AMOUNT_RECEIVEDBY_INSPECTION;

                $signature_amount_givenby = $this->signature->getGivenBy($type,$sub_type_given, $sfty_petty_checklist->amount_given_by);

                $signature_amount_receivedby = $this->signature->getReceivedBy($type,$sub_type_received, $sfty_petty_checklist->amount_received_by);

                $data = [
                    'sfty_petty_details' => $sfty_petty_details,
                    'sfty_petty_checklist' => $sfty_petty_checklist,
                    'signature_amount_givenby' => $signature_amount_givenby,
                    'signature_amount_receivedby' => $signature_amount_receivedby,
                    'pagetitle' => "Safety Petty Logbook Details",
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

            $html = view('inspection.inspection_ohc.safety_petty.generalpdf', $data)->render();

            $mpdf->WriteHTML($html);

            $filename = "Safety Petty Logbook Details.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            return redirect()->back()->withErrors(['error' => 'An error occurred while generating the PDF.']);
        }
    }

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $employee_code = $request->employee_code;
            $id = $request->id;
            if ($id == '') {
                $record = $this->sfty_petty_checklist->uniqueCheck($employee_code);
            } else {
                $id = decryptId($id);
                $record = $this->sfty_petty_checklist->ExistuniqueCheck($employee_code, $id);
            }
            if ($record->count()) {
                return Response::json(false);
            }
            return Response::json(true);
        }
    }

}
