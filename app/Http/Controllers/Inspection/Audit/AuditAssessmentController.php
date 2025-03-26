<?php

namespace App\Http\Controllers\Inspection\Audit;

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
use App\Models\Inspection\audit\AuditAssessment;
use App\Models\Inspection\Master\ChecklistType;
use App\Models\Inspection\Master\ChecklistSubType;
use App\Models\Inspection\Master\ChecklistSubTypeDataName;
use App\Models\Inspection\Master\ChecklistSubTypeData;
use App\Models\Inspection\Master\ChecklistOptionType;
use App\Models\Inspection\Master\Shift;

class AuditAssessmentController extends Controller
{

    private $checklist_type;
    private $checklist_subtype;
    private $checklist_subtypedata;
    private $checklist_subtypename;
    private $audit_assessment;
    private $upload_log;
    private $checklist_option;
    private $shift;

    public function __construct()
    {
        $this->audit_assessment = new AuditAssessment();
        $this->checklist_type = new ChecklistType();
        $this->checklist_subtype = new ChecklistSubType();
        $this->checklist_option = new ChecklistOptionType();
        $this->checklist_option = new ChecklistOptionType();
        $this->checklist_subtypename = new ChecklistSubTypeDataName();
        $this->checklist_subtypedata = new ChecklistSubTypeData();
        $this->shift = new Shift();
    }

    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =    $this->audit_assessment->list();
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
                            $btn = '<a href="' . admin_url('audit/assessment/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {
                                // $btn .= '<a href="' . admin_url('inspection/master/checklist-sub-type/edit/' . encryptId($row->id)) . '" class="edit-icon " title="' . __('common.edit') . '"><i class="fa-solid fa-pen-to-square"></i> ';
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

                    report($ex);
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }
        $checklist_types  = $this->checklist_type->select('id', 'category_name')->where('status', '1')->get();
        $data = array(
            'checklist_types' => $checklist_types,

        );
        return view('inspection.inspection_audit.auditAssessment.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $checklist_types  = $this->checklist_type->select('id', 'category_name')->where('status', '1')->get();
            $shift  = $this->shift->select('id', 'shift')->where('status', '1')->get();
            $checklist_details = getCheckListQuestion(CHECKLIST_AUDIT_ASSESSMENT);
            $options =  getoption(CHECKLIST_AUDIT_ASSESSMENT);
            $getoption = string_to_array($options->type);
            $data = array(
                'checklist_types' => $checklist_types,
                'shift' => $shift,
                'checklist_details' => $checklist_details,
                'getoption' => $getoption,
            );
            return view('inspection.inspection_audit.auditAssessment.add', $data);
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
        }
    }

    public function employeename(Request $request)
    {
        $name = $request->input('search');

        $employees = Employee::where('emp_name', 'like', '%' . $name . '%')
            ->orWhere('emp_id', 'like', '%' . $name . '%')
            ->where('status', 1)
            ->limit(10)
            ->get();


        return response()->json(
            $employees->map(function ($employee) {
                return [
                    'id' => encryptId($employee->id),
                    'text' => $employee->emp_name . ' - ' . $employee->emp_id,
                ];
            })
        );
    }
    public function store(Request $request)
    {

        try {
            try {

                $this->audit_assessment->store();

                Session::flash('success', __('Your data has been created successfully'));
            } catch (Exception $ex) {


                report($ex);
                Session::flash('error', __('common.message_error'));
            }

            return redirect(admin_url('audit/assessment/list'));
        } catch (Exception $ex) {
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('audit/assessment/list'));
        }
    }
    public function view($id)
    {
        try {
            $id = decryptId($id);
            if (Auth::check()) {
                $audit_assessment =   $this->audit_assessment->selectOne($id);


                $data = array(
                    'audit_assessment' => $audit_assessment,
                );
            }
            return view('inspection.inspection_audit.auditAssessment.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('audit/assessment/list'));
        }
    }

    public function edit($id)
    {
        try {
            $id = decryptId($id);
            $checklist_subtype = $this->checklist_subtype->selectOne($id);
            $checklist_sub_type =   $this->checklist_subtype->find($id);
            $checklist_types  = $this->checklist_type->select('id', 'category_name')->where('status', '1')->get();
            $data = array(
                'checklist_sub_type' => $checklist_sub_type,
                'checklist_types' => $checklist_types,
            );
            return view('inspection.inspection_audit.auditAssessment.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function update(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $rules = [
                'category_id' => 'required',
                'subcategory_name' => 'required',
            ];
            $messages = [
                'category_id.required' => 'Please enter Category',
                'subcategory_name.requred' => 'Please enter Sub category name',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $this->checklist_subtype->updates($id);


            Session::flash('success', 'Checklist Category updated successfully!');
            return redirect(admin_url('audit/assessment/list'));
        } catch (Exception $ex) {

            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('audit/assessment/list'));
        }
    }

    public function statusChange(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $this->checklist_subtype->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Checklist Category status changed'], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function exportExcel()
    {
        try {

            $allData =   $this->checklist_subtype->exportdata();
            $header = [
                __("common.sno"),
                __("Checklist Sub-Type ID"),
                __("Checklist Type Name"),
                __("Checklist Sub-Type Name"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] = $data->subcategory_id;
                $export[] =  $data->category_name;
                $export[] =  $data->subcategory_name;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Checklist Sub Type Category.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function exportPDF()
    {
        try {

            ini_set("pcre.backtrack_limit", "5000000");

            $allData =   $this->checklist_subtype->exportdata();
            $header = [
                __("common.sno"),
                __("Checklist Sub-Type ID"),
                __("Checklist Type Name"),
                __("Checklist Sub-Type Name"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];
            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Checklist Sub Type Category",
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

            $view = view('inspection.inspection_audit.auditAssessment.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Checklist Sub Type Category.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
        }
    }

    public function import(Request $request)
    {
        $data = array();

        return view('master.checklist_subtype.import', $data);
    }

    public function downloadSample()
    {

        $filedetails =  exportsamplefile('checklist_type');
        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;

        return redirect(url($filePath));
    }

    public function importSubmit(Request $request)
    {
        try {
            $file = $request->file('checklist_type_file_upload');
            $rules = [
                'checklist_type_file_upload' => 'required',
            ];
            $messages = [
                'checklist_type_file_upload.required' => 'Please upload a file',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            if ($file != null) {

                $uploadpath = 'uploads/checklist_type';

                $filenewname = time() . Str::random('16') . '.' . $file->getClientOriginalExtension();

                $fileName = $file->getClientOriginalName();
                $fileSize = $file->getSize();

                $fileExt = $file->getClientOriginalExtension();

                uploadFile($file, $uploadpath, $filenewname);

                $path = $uploadpath . "/" . $filenewname;
                $user_id = Auth::id();

                $insert_data = array(
                    'upload_type' => checklist_type_UPLOAD,
                    'upload_status' => 0,
                    'file_name' => $filenewname,
                    'file_orgname' => $fileName,
                    'file_path' => $path,
                    'file_size' => $fileSize,
                    'file_extension' => $fileExt,
                    'created_by' => $user_id,
                );

                $insert_id =  $this->upload_log->create($insert_data)->id;

                $details = [
                    "user_id" => $user_id,
                    "log_id" => $insert_id,
                    "path" => $path,
                ];

                dispatch(new ImportChecklistCategoryJob($details));
            }
            $insert_data['log_id'] = $insert_id;
            $insert_data['Uploded_by'] = Auth::user()->toArray();

            Session::flash('success', 'Permit Checklist Category Upload Successfull');
            return redirect(admin_url('inspection/checklist-type/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Permit Checklist Category failed!');
            return redirect(admin_url('inspection/checklist-type/list'));
        }
    }
}
