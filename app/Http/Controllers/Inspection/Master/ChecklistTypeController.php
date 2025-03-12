<?php

namespace App\Http\Controllers\Inspection\Master;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\Inspection\Master\ChecklistFile;
use App\Models\Inspection\Master\ChecklistType;
use App\Models\Inspection\Master\ChecklistOptionType;

class ChecklistTypeController extends Controller
{

    private $checklist_type;
    private $checklist_file;
    private $upload_log;
    private $checklist_option;

    public function __construct()
    {
        $this->checklist_type = new ChecklistType();
        $this->checklist_file = new ChecklistFile();
        $this->checklist_option = new ChecklistOptionType();
    }
    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->checklist_type->list();
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
                            $btn = '<a href="' . admin_url('inspection/checklist-type/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            if (CheckUserRole(ROLE_SUPERADMIN)) {
                                $btn .= '<a href="' . admin_url('inspection/checklist-type/edit/' . encryptId($row->id)) . '" class="edit-icon " title="' . __('common.edit') . '"><i class="fa-solid fa-pen-to-square"></i> ';
                                $btn .= '<a href="javascript:void(0);"  data-id="' . encryptId($row->id) . '"  class="recordDelete" title="' . __('common.delete') . '"><i class="fa-solid fa-trash text-danger" ></i></i></a> ';
                            }
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

        $data = array(
        );
        return view('inspection.master.checklist_type.list', $data);
    }

    public function Add(Request $request)
    {
        try {
            $checklist_options = $this->checklist_option->Getall();
            $data = array(
                'checklist_options' => $checklist_options,
            );
            return view('inspection.master.checklist_type.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {
        try {
            $rules = [
                'questionary_id' => 'required',
                'checklist_category' => 'required',
            ];
            $messages = [
                'checklist_category.required' => __('inspection.category_name'),
                'questionary_id.requred' => __('inspection.questionary'),
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            try {

                $checklist_type = $this->checklist_type->store();
                $this->checklist_file->store($checklist_type->id, CHECKLIST_TYPE);
                Session::flash('success', __('inspection.check_list_type_success'));
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', __('common.message_error'));
            }

            return redirect(admin_url('inspection/checklist-type/list'));
        } catch (Exception $ex) {
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('inspection/checklist-type/list'));
        }
    }

    public function UniqueCheck(Request $request)
    {
        if ($request->ajax()) {
            $permit_type_id = decryptId($request->permit_type_id);
            $value = $request->value;
            $type = $request->type;
            $id = decryptId($request->id);

            if ($request->id == '') {
                $data = [
                    'type' => $type,
                    'value' => $value,
                    'permit_type_id' => $permit_type_id,
                ];

                $isUnique = $this->checklist_type->UniqueCheck($data);
                return response()->json($isUnique);
            } else {
                $data = [
                    'type' => $type,
                    'value' => $value,
                    'permit_type_id' => $permit_type_id,
                    'id' => $id,
                ];

                $isUnique = $this->checklist_type->existUniqueCheck($data);
                return response()->json($isUnique);
            }
        }
    }

    public function View($id)
    {
        try {
            $id = decryptId($id);
            if (Auth::check()) {
                $checklist_type = $this->checklist_type->selectOne($id);
                $checklist_image = $this->checklist_file->selectChecklistTypeImage($id);

                $data = array(
                    'checklist_type' => $checklist_type,
                );
            }
            return view('inspection.master.checklist_type.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('inspection/checklist-type/list'));
        }
    }

    public function Edit($id)
    {
        try {
            $id = decryptId($id);
            $permit_type = $this->permit_type->get();
            $check_list_category = $this->checklist_type->find($id);

            $data = array(
                'check_list_category' => $check_list_category,
                'permit_type' => $permit_type,
            );
            return view('master.checklist_type.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $rules = [
                'ptw_checklist_type' => 'required',
                'permit_type_id' => 'required',

            ];

            $messages = [
                'ptw_checklist_type.required' => __('ptw.check_list_category_require'),
                'ptw_checklist_type.required' => __('ptw.check_list_category_require'),
                'permit_type_id.requred' => __('ptw.ptw_require'),
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                if ($validator->errors()->has('ptw_checklist_type')) {
                    $errorMessage = $validator->errors()->first('ptw_checklist_type');
                    if (str_contains($errorMessage, 'Checklist category cannot contain special characters')) {
                        Session::flash('error', 'Checklist category cannot contain special characters');
                    } else {
                        Session::flash('error', 'Checklist category is required');
                    }
                }
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->checklist_type->updates($id);

            Session::flash('success', 'Checklist Category updated successfully!');
            return redirect(admin_url('inspection/checklist-type/list'));
        } catch (Exception $ex) {

            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('inspection/checklist-type/list'));
        }
    }

    public function Delete(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $this->checklist_type->deleterecord($id);
            $this->ptw_sub_cat->delete_all($id);
            return response()->json(['status' => 'success', 'msg' => 'Checklist Category Successfully Deleted'], 200);
        } catch (Exception $ex) {
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function StatusChange(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $this->checklist_type->statuschange($id);
            $this->ptw_sub_cat->statuschange_all($id);

            return response()->json(['status' => 'success', 'msg' => 'Checklist Category status changed'], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function ExportExcel()
    {
        try {

            $allData = $this->checklist_type->exportdata();
            $header = [
                __("common.sno"),
                __("ptw.permit_to_work_type"),
                __("ptw.check_list_category"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] = $data->permit_type;
                $export[] =  $data->ptw_checklist_type;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Checklist Category.xlsx')
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

            $allData = $this->checklist_type->exportdata();

            $header = [
                __("common.sno"),
                __("ptw.permit_to_work_type"),
                __("ptw.check_list_category"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "CheckList Category",
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

            $view = view('master.checklist_type.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "CheckList Category Details.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Import(Request $request)
    {
        $data = array();

        return view('master.checklist_type.import', $data);
    }

    public function DownloadSample()
    {

        $filedetails =  exportsamplefile('checklist_type');
        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;

        return redirect(url($filePath));
    }

    public function ImportSubmit(Request $request)
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

