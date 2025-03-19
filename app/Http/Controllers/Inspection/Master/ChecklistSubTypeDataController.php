<?php

namespace App\Http\Controllers\Inspection\Master;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\Inspection\Master\ChecklistType;
use App\Models\Inspection\Master\ChecklistSubType;
use App\Models\Inspection\Master\ChecklistSubTypeData;
use App\Models\Inspection\Master\ChecklistSubTypeDataName;

class ChecklistSubTypeDataController extends Controller
{

    private $checklist_type;
    private $checklist_subtype;
    private $upload_log;
    private $checklist_subtype_data;
    private $checklist_subtype_dataName;

    public function __construct()
    {
        $this->checklist_type = new ChecklistType();
        $this->checklist_subtype = new ChecklistSubType();
        $this->checklist_subtype_data = new ChecklistSubTypeData();
        $this->checklist_subtype_dataName = new ChecklistSubTypeDataName();
    }
    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =    $this->checklist_subtype_data->list();
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
                            $btn = '<a href="' . admin_url('inspection/master/checklist-sub-type-data/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {
                            $btn .= '<a href="' . admin_url('inspection/master/checklist-sub-type-data/edit/' . encryptId($row->id)) . '" class="edit-icon " title="' . __('common.edit') . '"><i class="fa-solid fa-pen-to-square"></i> ';
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
                    report($ex);
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }

        $checklistTypeList  = $this->checklist_type->select('id', 'category_name')->where('status', '1')->get();
        $data = array(
            'checklistTypeList' => $checklistTypeList,
        );
        return view('inspection.master.checklist_subtype_data.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $checklistTypeList  = $this->checklist_type->select('id', 'category_name')->where('status', '1')->get();
            $data = array(
                'checklistTypeList' => $checklistTypeList,
            );
            return view('inspection.master.checklist_subtype_data.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'checklist_type_id' => 'required',
                'checklist_sub_type_id' => 'required',
            ];
            $messages = [
                'checklist_type_id.required' => "Checklist Type Name is Required",
                'checklist_sub_type_id.requred' => "Checklist Sub Type Name is Required",
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            try {

                $checklist_sub_type_data =   $this->checklist_subtype_data->store();
                $SubTypeDataName =   $this->checklist_subtype_dataName->store($checklist_sub_type_data->id);
                Session::flash('success', __('Your data has been created successfully'));
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', __('common.message_error'));
            }

            return redirect(admin_url('inspection/master/checklist-sub-type-data/list'));
        } catch (Exception $ex) {
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('inspection/master/checklist-sub-type-data/list'));
        }
    }

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $subcategory_name = $request->subcategory_name;
            $category_id = decryptId($request->category_id);
            $id = $request->id;
            if ($id == '') {
                $record = $this->checklist_subtype_data->uniqueCheck($subcategory_name, $category_id);
            } else {
                $id = decryptId($id);
                $record = $this->checklist_subtype_data->ExistuniqueCheck($subcategory_name, $category_id, $id);
            }
            if ($record->count()) {
                return Response::json(false);
            }
            return Response::json(true);
        }
    }

    public function View($id)
    {
        try {
            $id = decryptId($id);
            if (Auth::check()) {
                $checklist_type =   $this->checklist_subtype_data->selectOne($id);
                $checklistSubTypeDataNameList  = $this->checklist_subtype_dataName->where('checklist_sub_type_data_id', $id)->where('status', '1')->get();

                $data = array(
                    'checklist_type' => $checklist_type,
                    'checklistSubTypeDataNameList' => $checklistSubTypeDataNameList,

                );
            }
            return view('inspection.master.checklist_subtype_data.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('inspection/master/checklist-sub-type-data/list'));
        }
    }

    public function Edit($id)
    {
        try {
            $id = decryptId($id);
            $checklistTypeList  = $this->checklist_type->select('id', 'category_name')->where('status', '1')->get();
            $checklistSubTypeDataList =   $this->checklist_subtype_data->find($id);
            $checklistSubTypeDataNameList  = $this->checklist_subtype_dataName->where('checklist_sub_type_data_id', $id)->where('status', '1')->get();

            $data = array(
                'checklistTypeList' => $checklistTypeList,
                'checklistSubTypeDataList' => $checklistSubTypeDataList,
                'checklistSubTypeDataNameList' => $checklistSubTypeDataNameList,
            );
            return view('inspection.master.checklist_subtype_data.edit', $data);
        } catch (Exception $error) {
            dd($error->getMessage());
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rules = [
                'checklist_type_id' => 'required',
                'checklist_sub_type_id' => 'required',
            ];
            $messages = [
                'checklist_type_id.required' => "Checklist Type Name is Required",
                'checklist_sub_type_id.requred' => "Checklist Sub Type Name is Required",
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $checklist_sub_type_data =   $this->checklist_subtype_data->updates($id);
            $SubTypeDataName =   $this->checklist_subtype_dataName->updates($id);

            Session::flash('success', 'Checklist Category updated successfully!');
            return redirect(admin_url('inspection/master/checklist-sub-type-data/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('inspection/master/checklist-sub-type-data/list'));
        }
    }
    public function deleteChecklist($id)
    {
        try {

            $subtype_dataName = $this->checklist_subtype_dataName->findOrFail($id);
            $update_data = array(
                'status' => 0,
                'trash' => 'YES',
            );

            $subtype_dataName->update($update_data);

            return response()->json(['status' => 'success', 'msg' => 'Your data has been deleted successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->checklist_subtype_data->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'status changed'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function ExportExcel()
    {
        try {

            $allData =   $this->checklist_subtype_data->exportdata();
            $header = [
                __("common.sno"),
                __("Checklist Type Name"),
                __("Checklist Sub Type Name"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] = $data->category_name;
                $export[] =  $data->subcategory_name;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Checklist Sub Type Data.xlsx')
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

            $allData =   $this->checklist_subtype_data->exportdata();

            $header = [
                __("common.sno"),
                __("Checklist Type Name"),
                __("Checklist Sub Type Name"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Checklist Sub Type Data",
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

            $view = view('inspection.master.checklist_subtype_data.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Checklist Sub Type Data Details.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            dd($ex);
        }
    }

   
    public function DownloadSample()
    {

        $filedetails =  exportsamplefile('checklist_type');
        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;

        return redirect(url($filePath));
    }

}
