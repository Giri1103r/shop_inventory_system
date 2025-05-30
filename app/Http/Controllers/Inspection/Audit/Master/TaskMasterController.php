<?php

namespace App\Http\Controllers\Inspection\Audit\Master;

use App\Http\Controllers\Controller;
use App\Jobs\ImportAuditTaskJob;
use App\Models\Inspection\audit\Master\Task;
use App\Models\UploadLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;



class TaskMasterController extends Controller
{
    private $audit_task;
    private $uploadlog;


    public function __construct()
    {
        $this->audit_task = new Task();
        $this->uploadlog = new UploadLog();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->audit_task->list();
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
                            $btn = '<a href="' . admin_url('audit/master/task/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';

                            $btn .= '<a href="' . admin_url('audit/master/task/edit/' . encryptId($row->id)) . '" class="edit-icon " title="' . __('common.edit') . '"><i class="fa-solid fa-pen-to-square"></i> ';
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

        $data = [];
        return view('inspection.inspection_audit.master.list', $data);
    }

    public function Add(Request $request)
    {
        try {

            return view('inspection.inspection_audit.master.add');
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {
        try {
            $rules = [
                'task_id' => 'required',
                'task_name' => 'required',

            ];
            $messages = [
                'task_id.required' => 'Task Id is Required',
                'task_name.required' => 'Task Name is Required',

            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {
                $this->audit_task->store();
                Session::flash('success', __('Your data has been created successfully'));
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }
            return redirect(admin_url('audit/master/task/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('audit/master/task/list'));
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $audit_task = $this->audit_task->find($id);


            $data = array(
                'audit_task' => $audit_task,
            );

            return view('inspection.inspection_audit.master.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function update(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $rules = [
                'task_id' => 'required',
                'task_name' => 'required',

            ];
            $messages = [
                'task_id.required' => 'Task Id is Required',
                'task_name.required' => 'Task Name is Required',

            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {


                $this->audit_task->updates($id);

                Session::flash('success', __('Your data has been updated successfully'));
            } catch (Exception $ex) {
                Session::flash('error', __('common.message_error'));
            }
            return redirect(admin_url('audit/master/task/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('audit/master/task/list'));
        }
    }



    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $audit_task = $this->audit_task->selectOne($id);

                $data = array(
                    'audit_task' => $audit_task,
                );
            }
            return view('inspection.inspection_audit.master.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }


    public function ExportExcel(Request $request)
    {

        try {
            $allData = $this->audit_task->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Task ID',
                'Task Name',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {
                $export = [];
                $export[] =  $i;
                $export[] =  $data->task_auto_id;
                $export[] =  $data->task_name;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);
                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Audit Task.xlsx')
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

            $allData = $this->audit_task->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Task ID',
                'Task Name',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Audit Task",
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

            $view = view('inspection.inspection_audit.master.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Audit Task.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Import(Request $request)
    {
        $data = array();

        return view('inspection.inspection_audit.master.import', $data);
    }

    public function DownloadSample(Request $request)
    {
        $filedetails =  exportsamplefile('audit_task');
        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;
        return Response::download($filePath, $customFileName);
    }

    public function ImportSubmit(Request $request)
    {
        // dd($request->all());
        try {
            $file = $request->file('audit_task_file');

            $rules = [
                'audit_task_file' => 'required',
            ];
            $messages = [
                'audit_task_file.required' => 'Please upload a file',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            if ($file != null) {
                $uploadpath = 'public/uploads/inspection/audit/master/audit_task';
                $folderPath = public_path('uploads/inspection/audit/master/audit_task');
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
                    'upload_type' => 14,
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

                dispatch(new ImportAuditTaskJob($details));
                // dispatch((new ImportAuditTaskJob($details))->onQueue('task'));
            }

            $insert_data['log_id'] = $insert_id;
            $insert_data['Uploded_by'] = Auth::user()->toArray();

            Session::flash('success', __('Task name Uploaded sucessfully'));
            return redirect(admin_url('audit/master/task/list'));
        } catch (Exception $ex) {
            Session::flash('error', __('Task to be taken upload failed'));
            return redirect(admin_url('audit/master/task/list'));
        }
    }


    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->audit_task->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => __('Audit Task Status is changed')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => 'Something went wrong, Please try after sometimes!'], 406);
        }
    }

    public function Delete(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $this->audit_task->deleterecord($id);

            return response()->json(['status' => 'success', 'msg' => __('Audit Task was deleted successfully')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => 'Something went wrong, Please try after sometimes!'], 406);
        }
    }

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $task_name = $request->task_name;
            $id = $request->id;
            if ($id == '') {
                $record = $this->audit_task->uniqueCheck($task_name);
            } else {
                $id = decryptId($id);
                $record = $this->audit_task->ExistuniqueCheck($task_name, $id);
            }
            if ($record->count()) {
                return Response::json(false);
            }
            return Response::json(true);
        }
    }
}
