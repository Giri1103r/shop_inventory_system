<?php

namespace App\Http\Controllers\Inspection\RRAA;

use App\Http\Controllers\Controller;
use App\Mail\Inspection\RRAA\RRAAEmail;
use App\Models\Inspection\Master\Frequency;
use Illuminate\Http\Request;
use App\Models\Inspection\RRAA\RRAADetails;
use App\Models\Inspection\RRAA\RRAACheckList;
use Exception;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\Master\Employee;
use App\Models\Master\Work;
use App\Models\Inspection\Master\ChecklistType;
use App\Models\Inspection\RRAA\RRAAStatusLog;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Admin\AdminController;
use App\Models\Inspection\RRAA\RRAASignatureUpload;

class RRAAController extends Controller
{

    private $rraa_details;
    private $rraa_checkList;
    private $employee;
    private $work;
    private $frequency;
    private $category;
    private $statusLog;
    private $signature;

    public function __construct()
    {
        $this->rraa_details = new RRAADetails();
        $this->rraa_checkList = new RRAACheckList();
        $this->employee = new Employee();
        $this->work = new Work();
        $this->frequency = new Frequency();
        $this->category = new ChecklistType();
        $this->statusLog = new RRAAStatusLog();
        $this->signature = new RRAASignatureUpload();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->rraa_details->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
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
                            $btn = '<a href="' . admin_url('rraa/ohc_fire_environment_compliance/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('rraa/ohc_fire_environment_compliance/generalpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                                <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                            </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'issue_date' ,'created_by'])
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

        return view('inspection.rraa.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $frequency = $this->frequency->getFrequency();
            $category = $this->category->getAll();

            $data = [
                'frequency' => $frequency,
                'category' => $category,
            ];
            return view('inspection.rraa.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {
        try {
            $rules = [
                'document_number' => 'required',
                'issue_date' => 'required',
                'revision_date' => 'required',
                'serial_number' => 'required',
                'category' => 'required',
                'ohs_compliance_index' => 'required',
                'frequency' => 'required',
                'scope' => 'required',
                'emp_id' => 'required',
                'authority' => 'required',
                'accountability' => 'required',
                'remark' => 'required',
            ];
            $messages = [
                'document_number.required' => __('Document Number is required'),
                'issue_date.required' => __('Issue Date is required'),
                'revision_date.required' => __('Revision Date is required'),
                'serial_number.required' => __('Serial Number is required'),
                'category.required' => __('category is required'),
                'ohs_compliance_index.required' => __('OHS Compliance Index is required'),
                'frequency.required' => __('Frequency is required'),
                'scope.required' => __('scope is required'),
                'emp_id.required' => __('Responsibility is required'),
                'authority.required' => __('authority is required'),
                'accountability.required' => __('accountability is required'),
                'remark.required' => __('remark is required'),
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

                $rraa = $this->rraa_details->store();
                $rraa_id = $rraa->id;
                $this->rraa_checkList->store($rraa_id);
                $this->signature->signatureStore(RRAA_INSPECTION,$rraa->id);

                Session::flash('success', __('Your data has been created successfully'));
            } catch (Exception $ex) {
                Session::flash('error', __('common.message_error'));
            }
            return redirect(admin_url('rraa/ohc_fire_environment_compliance/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('rraa/ohc_fire_environment_compliance/list'));
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
                $rraa_details = $this->rraa_details->find($id);
                $rraa_checkList = $this->rraa_checkList->selectOne($id);
                $status_log = $this->statusLog->selectOne($id);
                $inspection_details = $this->rraa_details->selectOne($id);

                $data = array(
                    'rraa_details' => $rraa_details,
                    'rraa_checkList' => $rraa_checkList  ?? [],
                    'status_log' => $status_log,
                    'inspection_details' => $inspection_details,
                );
            }
            return view('inspection.rraa.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->rraa_details->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Document Number',
                'Issue Date',
                'Revision Date',
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->document_number;
                $export[] =  $data->issue_date;
                $export[] =  $data->revision_date;
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }
            $writer = SimpleExcelWriter::streamDownload('RRAA.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('rraa/ohc_fire_environment_compliance/list'));
        }
    }

    public function ExportPdf(Request $request)
    {
        try {

            $allData = $this->rraa_details->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Document Number',
                'Issue Date',
                'Revision Date',
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "RRAA Details",
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

            $view = view('inspection.rraa.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "RRAA.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('rraa/ohc_fire_environment_compliance/list'));
        }
    }

    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $rraa_details = $this->rraa_details->find($id);
                $rraa_checkList = $this->rraa_checkList->selectOne($id);
                $status_log = $this->statusLog->selectOne($id);
                $inspection_details = $this->rraa_details->selectOne($id);

                $data = array(
                    'rraa_details' => $rraa_details,
                    'rraa_checkList' => $rraa_checkList  ?? [],
                    'status_log' => $status_log,
                    'inspection_details' => $inspection_details,
                    'pagetitle' => "RRAA Details",
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

            $html = view('inspection.rraa.generalpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "RRAA Details.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            return redirect()->back()->withErrors(['error' => 'An error occurred while generating the PDF.']);
        }
    }

}
