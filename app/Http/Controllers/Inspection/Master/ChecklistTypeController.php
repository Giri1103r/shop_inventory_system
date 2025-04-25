<?php

namespace App\Http\Controllers\Inspection\Master;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\Inspection\ImportChecklistType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Models\Inspection\Master\ChecklistFile;
use App\Models\Inspection\Master\ChecklistType;
use App\Models\Inspection\Master\ChecklistOptionType;
use App\Models\Inspection\Master\ChecklistSubType;
use App\Models\UploadLog;

class ChecklistTypeController extends Controller
{

    private $checklist_type;
    private $checklist_file;
    private $upload_log;
    private $checklist_option;
    private $checklist_sub_type;

    public function __construct()
    {
        $this->checklist_type = new ChecklistType();
        $this->checklist_file = new ChecklistFile();
        $this->checklist_option = new ChecklistOptionType();
        $this->upload_log = new UploadLog();
        $this->checklist_sub_type = new ChecklistSubType();
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
                            $btn = '<a href="' . admin_url('inspection/master/checklist-type/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            if (CheckUserRole(ROLE_SUPERADMIN)) {
                                $btn .= '<a href="' . admin_url('inspection/master/checklist-type/edit/' . encryptId($row->id)) . '" class="edit-icon " title="' . __('common.edit') . '"><i class="fa-solid fa-pen-to-square"></i> ';
                                // $btn .= '<a href="javascript:void(0);"  data-id="' . encryptId($row->id) . '"  class="recordDelete" title="' . __('common.delete') . '"><i class="fa-solid fa-trash text-danger" ></i></i></a> ';
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
                    dd($ex);
                    report($ex);
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }

        $data = array();
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

            return redirect(admin_url('inspection/master/checklist-type/list'));
        } catch (Exception $ex) {
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('inspection/master/checklist-type/list'));
        }
    }

    public function UniqueCheck(Request $request)
    {
        if ($request->ajax()) {
            $checklist_category = $request->checklist_category;
            $id = decryptId($request->id);
            if ($request->id == '') {
                $isUnique = $this->checklist_type->UniqueCheck($checklist_category);
                return response()->json($isUnique);
            } else {
                $data = [
                    'category_name' => $checklist_category,
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
                $checklist_images = $this->checklist_file->selectChecklistTypeImage($id, CHECKLIST_TYPE);

                $data = array(
                    'checklist_type' => $checklist_type,
                    'checklist_images' => $checklist_images,
                );
            }
            return view('inspection.master.checklist_type.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('inspection/master/checklist-type/list'));
        }
    }

    public function Edit($id)
    {
        try {
            $id = decryptId($id);
            $checklist_type = $this->checklist_type->selectOne($id);
            $checklist_images = $this->checklist_file->selectChecklistTypeImage($id, CHECKLIST_TYPE);
            $checklist_options = $this->checklist_option->Getall();

            $data = array(
                'checklist_type' => $checklist_type,
                'checklist_images' => $checklist_images,
                'checklist_options' => $checklist_options,
            );
            return view('inspection.master.checklist_type.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function update(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $rules = [
                'questionary_id' => 'required',
                'checklist_category' => 'required',
            ];
            $messages = [
                'checklist_category.required' => __('inspection.category_name'),
                'questionary_id.requred' => __('inspection.questionary'),
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $this->checklist_type->updates($id);
            $this->checklist_file->updates($id, CHECKLIST_TYPE);

            Session::flash('success', 'Checklist Type updated successfully!');
            return redirect(admin_url('inspection/master/checklist-type/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('inspection/master/checklist-type/list'));
        }
    }

    public function Delete(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $this->checklist_type->deleterecord($id);
            return response()->json(['status' => 'success', 'msg' => 'Checklist Type Successfully Deleted'], 200);
        } catch (Exception $ex) {
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function StatusChange(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $this->checklist_type->statuschange($id);
            // $this->checklist_sub_type->statuschange_all($id);

            return response()->json(['status' => 'success', 'msg' => 'Checklist type Status Changed Successfully!'], 200);
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
                __('inspection.checklist_type_id'),
                __('inspection.checklist_type_name'),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] = $data->category_name;
                $export[] =  $data->category_id;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;
                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Checklist Type.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('inspection/master/checklist-type/list'));
        }
    }

    public function ExportPDF()
    {
        try {

            ini_set("pcre.backtrack_limit", "5000000");

            $allData = $this->checklist_type->exportdata();

            $header = [
                __("common.sno"),
                __('inspection.checklist_type_id'),
                __('inspection.checklist_type_name'),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "CheckList Type",
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

            $view = view('inspection.master.checklist_type.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "CheckList Type Details.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('inspection/master/checklist-type/list'));
        }
    }

    public function Import(Request $request)
    {
        $data = array();
        return view('inspection.master.checklist_type.import', $data);
    }

    public function DownloadSample()
    {
        $filedetails =  exportsamplefile('inspection_checklist_type');
        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;
        return redirect(url($filePath));
    }
}
