<?php

namespace App\Http\Controllers\Inspection\Audit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\Master\Unit;
use App\Models\Master\Department;
use App\Models\Inspection\audit\AuditAnalysis;
use App\Models\Inspection\audit\AuditAnalysisChecklist;
use App\Models\Inspection\MSDSCheckList;
use Exception;
use App\Models\Inspection\InspectionStaticDocno;
use Spatie\SimpleExcel\SimpleExcelWriter;

class AuditAnalysisController extends Controller
{
    private $unit;
    private $department;
    private $auditAnalysis;
    private $auditAnalysisCheckList;
    private $static_docno;

    public function __construct()
    {
        $this->auditAnalysis = new AuditAnalysis();
        $this->auditAnalysisCheckList = new AuditAnalysisChecklist();
        $this->department = new Department();
        $this->unit = new Unit();
        $this->static_docno = new InspectionStaticDocno();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->auditAnalysis->list();
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
                            $btn = '<a href="' . admin_url('audit/6s-analysis/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {
                            // $btn .= '<a href="' . admin_url('audit/6s-analysis/edit/' . encryptId($row->id)) . '" class="edit-icon " title="' . __('common.edit') . '"><i class="fa-solid fa-pen-to-square"></i> ';
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
                    report($ex);
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }

        $auditAnalysisList  = $this->auditAnalysis->select('id', 'audit_analysis_id')->where('status', '1')->get();

        $data = array(
            'auditAnalysisList' => $auditAnalysisList,
        );
        return view('inspection.inspection_audit.auditAnalysis.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $departmentList  = $this->department->select('id', 'department_name')->where('status', '1')->get();
            $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
            $staticDocno  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                ['type', "6SAuditAnalysis"],
                ['status', '1']
            ])->first();

            $data = array(
                'departmentList' => $departmentList,
                'unitList' => $unitList,
                'staticDocno' => $staticDocno,
            );
            return view('inspection.inspection_audit.auditAnalysis.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {

        try {

            try {
                $auditAnalysis = $this->auditAnalysis->store();
                $auditanalysis_id = $auditAnalysis->id;
                $this->auditAnalysisCheckList->store($auditanalysis_id);

                Session::flash('success', __('Your data has been created successfully'));
            } catch (Exception $ex) {

                dd($ex);
                Session::flash('error', __('common.message_error'));
            }
            return redirect(admin_url('audit/6s-analysis/list'));
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('audit/6s-analysis/list'));
        }
    }
    public function Uniquecheck(Request $request)
    {
        $ids = decryptId($request->input('id'));
        $departmentId = decryptId($request->input('departmentId'));
        $unitId = decryptId($request->input('unitId'));
        $year = (new \DateTime($request->year))->format('Y');
        $month = (new \DateTime($request->month))->format('m');

        if (empty($ids)) {
            $conflicts = $this->auditAnalysisCheckList->getUniqueSchedule($departmentId, $unitId, $year, $month);
        } else {
            $conflicts = $this->auditAnalysisCheckList->getExistUniqueSchedule($departmentId, $unitId, $year, $month, $ids);
        }

        return response()->json(['conflicts' => $conflicts]);
    }


    public function StatusChange(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $this->auditAnalysis->statuschange($id);

            $this->auditAnalysisCheckList->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Your status  has changed Successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function View($id)
    {
        try {
            $id = decryptId($id);
            if (Auth::check()) {
                $auditData =   $this->auditAnalysis->selectOne($id);
                $auditAnalysisData =   $this->auditAnalysisCheckList->selectOne($id);
                $staticDocno  = $this->static_docno->select('id', 'doc_no', 'issue_date', 'rev_dt')->where([
                    ['type', "6SAuditAnalysis"],
                    ['status', '1']
                ])->first();
                $data = array(
                    'auditData' => $auditData,
                    'auditAnalysisData' => $auditAnalysisData,
                    'staticDocno' => $staticDocno,
                );
            }
            return view('inspection.inspection_audit.auditAnalysis.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('audit/6s-analysis/list'));
        }
    }


    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->auditAnalysis->exportdata();

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
                $export[] =  $data->document_number;
                $export[] =  $data->issue_date;
                $export[] = $data->revision_date;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('MSDS.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('audit/6s-analysis/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->auditAnalysis->exportdata();

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
                'pagetitle' => "MSDS Details",
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

            $view = view('inspection.inspection_audit.auditAnalysis.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "MSDS.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('audit/6s-analysis/list'));
        }
    }

    public function edit($id)
    {
        try {
            $id = decryptId($id);

            $msdsDetails = $this->auditAnalysis->find($id);

            $msdsCheckList = $this->msdsCheckList->selectOne($id);

            $data = [
                'msdsDetails' => $msdsDetails,
                'msdsCheckList' => $msdsCheckList  ?? [],
            ];

            return view('inspection.inspection_audit.auditAnalysis.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function update(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $rules = [
                'document_number' => 'required',
                'issue_date' => 'required',
                'revision_date' => 'required',
                'item_code' => 'required',
                'name_of_chemical' => 'required',
                'msds_availability_status' => 'required',
                'remark' => 'required',
            ];
            $messages = [
                'document_number.required' => __('Document Number is required'),
                'issue_date.required' => __('Issue Date is required'),
                'revision_date.required' => __('Revision Date is required'),
                'item_code.required' => __('Item Code is required'),
                'name_of_chemical.required' => __('Name of Chemical is required'),
                'msds_availability_status.required' => __('MSDS Availability Status is required'),
                'remark.required' => __('Remark is required'),
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

                $msds = $this->auditAnalysis->updates($id);
                $msds_details = $this->auditAnalysis->selectOne($id);
                $msdsId = $msds_details->id;

                $this->msdsCheckList->updates($msdsId);

                Session::flash('success', __('Your data has been updated successfully'));
            } catch (Exception $ex) {
                Session::flash('error', __('common.message_error'));
            }
            return redirect(admin_url('audit/6s-analysis/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('audit/6s-analysis/list'));
        }
    }
}
