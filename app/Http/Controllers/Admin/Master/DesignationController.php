<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

use Str;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use App\Jobs\ImportdesignationJob;

use App\Models\Master\Designation;
use App\Models\User;
use App\Models\Administration\UploadLog;


class DesignationController extends Controller
{


    private $user;
    private $uploadlog;

    private $designation;


    public function __construct()
    {


        $this->user = new User();
        $this->designation = new Designation();

        $this->uploadlog = new UploadLog();
    }


    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {

                try {

                    $data =  $this->designation->list();

                    $datatables = Datatables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active<span>";
                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class= 'statusChange' data-id='" . encryptId($row->id) . "' data-type = '1' >Active<span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class= 'statusChange' data-id='" . encryptId($row->id) . "' data-type = '0' >In-Active<span>";
                            }
                            return $text;
                        })
                        ->addColumn('created_at', function ($row) {
                            return Displaydatetimeformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            if (CheckUserPermission('view')) {
                                $btn = '<a href="' . admin_url('master/designation/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            }
                            if (CheckUserPermission('edit')) {
                                $btn .= '<a href="' . admin_url('master/designation/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            }
                            // if (CheckUserPermission('delete')) {
                            //     $btn .= '<a href="javascript:void(0);"  data-id="' . encryptId($row->id) . '" class="recordDelete" title="Delete"><i class="fa-solid fa-trash text-danger" ></i></i></a> ';
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
        $data = array();
        return view('admin.master.designation.list', $data);
    }

    public function Add(Request $request)
    {

        try {

            $data = array();
            return view('admin.master.designation.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('master/designation/list'));
        }
    }

    public function Store(Request $request)
    {
        try {
            DB::beginTransaction();

            $rules = [
                'designation_name' => 'required|unique:masters_designation,designation_name',
            ];
            $messages = [
                'designation_name.required' => 'Please Enter Designation Name',
                'designation_name.unique' => 'Designation Name should be unique',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }


            $this->designation->store();
            DB::commit();

            Session::flash('success', 'Your data has been created successfully!');
            return redirect(admin_url('master/designation/list'));
        } catch (Exception $ex) {

            report($ex);
            DB::rollBack();
            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('master/designation/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $designation = $this->designation->selectOne($id);

            if (!$designation) {
                Session::flash('error', __('common.error_msg'));
                return redirect(admin_url('master/designation/list'));
            }

            $data = array(
                'designation' => $designation,
            );

            return view('admin.master.designation.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('master/designation/list'));
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $designation = $this->designation->find($id);

            if (!$designation) {
                Session::flash('error', __('common.error_msg'));
                return redirect(admin_url('master/designation/list'));
            }

            $data = array(
                'designation' => $designation,
            );

            return view('admin.master.designation.edit', $data);
        } catch (Exception $error) {
            report($error);

            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('master/designation/list'));
        }
    }

    public function Update(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = decryptId($request->id);
            $rules = [
                'designation_name' => 'required|unique:masters_designation,designation_name,' . $id . ',id',
            ];
            $messages = [
                'designation_name.required' => 'Please Enter Designation Name',
                'designation_name.unique' => 'Designation Name should be unique',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->designation->updates($id);
            DB::commit();

            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('master/designation/list'));
        } catch (Exception $ex) {
            report($ex);
            DB::rollBack();
            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('master/designation/list'));
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->designation->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Designation status changed'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function Delete(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $this->designation->deleterecord($id);

            return response()->json(['status' => 'success', 'msg' => 'Designation deleted successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }


    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->designation->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __("common.designation_id"),
                __("common.designation"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $exportData = [];
            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->des_code;
                $export[] =  $data->designation_name;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Designation Master.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('master/designation/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->designation->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __("common.designation_id"),
                __("common.designation"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Designation Details",
            );

            $property = [
                'tempDir' => public_path('pdf/temp'),
                'mode' => 'c',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,

            ];

            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';

            $view = view('admin.master.designation.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Designation Master.pdf";

            /* Hand the PDF back as a response instead of echoing it: mPDF sends
               its own Content-Length and then returns, so letting the action fall
               through made the framework append a second body to the download. */
            return response($mpdf->Output($filename, \Mpdf\Output\Destination::STRING_RETURN), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('master/designation/list'));
        }
    }

    public function Import(Request $request)
    {
        $data = array();
        return view('admin.master.designation.import', $data);
    }

    public function ImportSubmit(Request $request)
    {
        try {
            $rules = [
                'designation_upload' => 'required',
            ];
            $messages = [
                'designation_upload.required' => 'Please upload a file',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $file = $request->file('designation_upload');
            $uploadtype = exportsamplefile('designation');

            if (!$uploadtype) {
                Session::flash('error', 'Designation upload type is not configured.');
                return redirect(admin_url('master/designation/list'));
            }

            if ($file != null) {

                $uploadpath = 'public/uploads/designation';

                $folderPath = public_path('uploads/designation');

                if (!File::exists($folderPath)) {

                    File::makeDirectory($folderPath, 0755, true);
                }

                $filenewname = time() . Str::random('10') . '.' . $file->getClientOriginalExtension();

                $fileName = $file->getClientOriginalName();
                $fileSize = $file->getSize();

                $fileExt = $file->getClientOriginalExtension();

                $file->move($uploadpath, $filenewname);

                $path = $uploadpath . "/" . $filenewname;
                $user_id = Auth::id();

                $insert_data = array(
                    'upload_type' => $uploadtype->id,
                    'upload_status' => 0,
                    'file_name' => $filenewname,
                    'file_orgname' => $fileName,
                    'file_path' => $path,
                    'file_size' => $fileSize,
                    'file_extension' => $fileExt,
                    'created_by' => $user_id,
                );

                $insert_id =  $this->uploadlog->create($insert_data)->id;


                $details = [
                    "user_id" => $user_id,
                    "log_id" => $insert_id,
                    "path" => $path,
                ];

                dispatch(new ImportdesignationJob($details));
            }

            Session::flash('success', __('common.file_upload_success_msg'));

            return redirect(admin_url('master/designation/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('master/designation/list'));
        }
    }

    public function DownloadSample(Request $request)
    {

        $filedetails =  exportsamplefile('designation');

        if (!$filedetails) {
            Session::flash('error', 'Sample file is not configured.');
            return redirect(admin_url('master/designation/import'));
        }

        $filePath = $filedetails->sample_file;

        return redirect(url($filePath));
    }

    public function multipleList(Request $request)
    {
        $preselectedIds = array_map('decryptId', $request->input('preselected_ids', []));

        $designations = $this->designation->multipleAjaxList($preselectedIds);

        return response()->json($designations);
    }

    public function list(Request $request)
    {
        $id = $request->id != '' ? decryptId($request->id) : '';
        $designations = $this->designation->ajaxList($id);

        return response()->json($designations);
    }

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $designation_name = $request->designation_name;
            $id = $request->id;
            if ($id == '') {

                $record = $this->designation->uniqueCheck($designation_name);
            } else {

                $id = decryptId($id);
                $record = $this->designation->ExistuniqueCheck($designation_name, $id);
            }
            if ($record->count()) {
                return Response::json(false);
            }
            return Response::json(true);
        }
    }
}
