<?php

namespace App\Http\Controllers\Inspection\Ohc;

use Exception;
use App\Models\User;
use App\Models\Master\Unit;
use App\Models\Master\Work;
use Illuminate\Http\Request;
use App\Models\Master\Employee;
use App\Models\Master\Department;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Models\Inspection\Ohc\OhcSignature;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Ohc\SafetyPettyDetails;
use App\Models\Inspection\Ohc\SafetyPettyChecklist;

class SafetyPettyController extends Controller
{
    private $sfty_petty_details;
    private $sfty_petty_checklist;
    private $employee;
    private $work;
    private $unit;
    private $signature;
    private $user;
    private $document_reference;
    private $department;

    public function __construct()
    {
        $this->sfty_petty_details = new SafetyPettyDetails();
        $this->sfty_petty_checklist = new SafetyPettyChecklist();
        $this->employee = new Employee();
        $this->work = new Work();
        $this->unit = new Unit();
        $this->signature = new OhcSignature();
        $this->user = new User();
        $this->document_reference = new InspectionStaticDocno();
        $this->department = new Department();

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
                                    $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->safety_petty_id) . "' data-type = '1'>Active</span>";
                                } else if ($row->status == 0) {
                                    $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->safety_petty_id) . "' data-type = '0'>In-Active</span>";
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
                        ->addColumn('issue_date', function ($row) {
                            return Displaydateformat($row->issue_date);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('ohc/safety-petty-logbook/view/' . encryptId($row->safety_petty_id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('ohc/safety-petty-logbook/generalpdf/' . encryptId($row->safety_petty_id)) . '" style="margin-right: 5px;" title="PDF">
                                        <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                                    </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date','issue_date', 'inspection_status', 'created_by', 'status'])
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

        $units = $this->unit->getUnit();

        $data = [
            'units' => $units,
        ];
        return view('inspection.inspection_ohc.safety_petty.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $type = OHC_SAFETY_PETTY_LOGBOOK_INSPECTION;
            $unit = $this->unit->getunit();
            $document_no = $this->document_reference->selectUsingName('SafetyPettyLogbook');

            $data = [
                'unit' => $unit,
                'document_no' => $document_no,
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

            $sfty_petty_id = $sfty_petty_details[0]->id;

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
                $document_no = $this->document_reference->selectOne($sfty_petty_details->document_reference_id);

                $type = OHC_SAFETY_PETTY_LOGBOOK_INSPECTION;
                $sub_type_given = OHC_AMOUNT_GIVENBY_INSPECTION;
                $sub_type_received = OHC_AMOUNT_RECEIVEDBY_INSPECTION;

                $signature_amount_givenby = $this->signature->getGivenBy($type,$sub_type_given, $sfty_petty_details->id);

                $signature_amount_receivedby = $this->signature->getReceivedBy($type,$sub_type_received, $sfty_petty_details->id);

                $data = array(
                    'sfty_petty_details' => $sfty_petty_details,
                    'signature_amount_givenby' => $signature_amount_givenby,
                    'signature_amount_receivedby' => $signature_amount_receivedby,
                    'document_no' => $document_no,
                );

            }
            return view('inspection.inspection_ohc.safety_petty.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function StatusChange(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $this->sfty_petty_details->statuschange($id);

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
                'Employee Name',
                'Employee Code',
                'Department',
                'Unit',
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->employee_name;
                $export[] =  $data->employee_code;
                $export[] = $data->department;
                $export[] = $data->unit;
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

            $document_no = $this->document_reference->selectUsingName('SafetyPettyLogbook');

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }elseif(count($allData) > 20){
                return redirect()->back()->with('error',   __('inspection.excess_error'));
            }

            $type = OHC_SAFETY_PETTY_LOGBOOK_INSPECTION;
            $sub_type_given = OHC_AMOUNT_GIVENBY_INSPECTION;
            $sub_type_received = OHC_AMOUNT_RECEIVEDBY_INSPECTION;

            $data = array(
                'content' => $allData,
                'document_no' => $document_no,
                'type' => $type,
                'sub_type_given' => $sub_type_given,
                'sub_type_received' => $sub_type_received,
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
                $document_no = $this->document_reference->selectUsingName('SafetyPettyLogbook');

                $type = OHC_SAFETY_PETTY_LOGBOOK_INSPECTION;
                $sub_type_given = OHC_AMOUNT_GIVENBY_INSPECTION;
                $sub_type_received = OHC_AMOUNT_RECEIVEDBY_INSPECTION;

                $signature_amount_givenby = $this->signature->getGivenBy($type,$sub_type_given, $sfty_petty_details->id);

                $signature_amount_receivedby = $this->signature->getReceivedBy($type,$sub_type_received, $sfty_petty_details->id);

                $data = [
                    'sfty_petty_details' => $sfty_petty_details,
                    'signature_amount_givenby' => $signature_amount_givenby,
                    'signature_amount_receivedby' => $signature_amount_receivedby,
                    'document_no' => $document_no,
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
                $record = $this->sfty_petty_details->uniqueCheck($employee_code);
            } else {
                $id = decryptId($id);
                $record = $this->sfty_petty_details->ExistuniqueCheck($employee_code, $id);
            }
            if ($record->count()) {
                return Response::json(false);
            }
            return Response::json(true);
        }
    }

}
