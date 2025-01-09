<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Facades\File;
use App\Models\Master\Unit;

use Illuminate\Support\Str;
use PDF;
use Mail;
use Illuminate\Support\Facades\Auth;
use Exception;
use Response;
use Yajra\DataTables\DataTables;
use App\Models\Master\Department;
use App\Models\Master\Employee;
use App\Models\Master\Topic;
use App\Models\Master\TrainingMatrix;
use App\Models\Master\TrainingMatrixFile;
use App\Models\User;
use App\Models\UploadLog;
use App\Jobs\ImportTrainingMatrixJob;
use Illuminate\Support\Facades\Session;

class TrainingMatrixController extends Controller
{

    private $user;
    private $uploadlog;
    private $department;
    private $employee;
    private $topic;
    private $training_matrix;
    private $training_matrix_file;
    private $unit;


    public function __construct()
    {

        $this->training_matrix = new TrainingMatrix();
        $this->training_matrix_file = new TrainingMatrixFile();
        $this->topic = new Topic();
        $this->employee = new Employee();
        $this->department = new Department();
        $this->user = new User();
        $this->unit = new Unit();
        $this->uploadlog = new UploadLog();
    }


    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {

                try {

                    $data =  $this->training_matrix->list();

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
                                $btn = '<a href="' . admin_url('training_matrix/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            }
                            if (CheckUserPermission('edit')) {
                                $btn .= '<a href="' . admin_url('training_matrix/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            }
                            // if (CheckUserPermission('delete')) {
                            //     $btn .= '<a href="javascript:void(0);"  data-id="' . encryptId($row->id) . '"  data-login_id="' . encryptId($row->login_id) . '" class="recordDelete" title="Delete"><i class="fa-solid fa-trash text-danger" ></i></i></a> ';
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
        $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
        $topicList  = $this->topic->select('id', 'topic_name')->where('status', '1')->get();
        $employeeList  = $this->employee->select('id', 'emp_name')->where('user_role', ROLE_TRAINER)->where('status', '1')->get();

        $data = array(
            'unitList' => $unitList,
            'topicList' => $topicList,
            'employeeList' => $employeeList,
        );

        return view('master.training_matrix.list', $data);
    }

    public function Add(Request $request)
    {

        try {
            $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
            $topicList  = $this->topic->select('id', 'topic_name')->where('status', '1')->get();
            $employeeList  = $this->employee->select('id', 'emp_name')->where('user_role', ROLE_TRAINER)->where('status', '1')->get();

            $data = array(
                'unitList' => $unitList,
                'topicList' => $topicList,
                'employeeList' => $employeeList,
            );
            return view('master.training_matrix.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {
        try {

            $rules = [
                'topic_id' => 'required',
                'trainer_id' => 'required',
                'training_offered_for' => 'required',
                'unit_id' => 'required',
                'department_id' => 'required|array',
            ];

            $messages = [
                'topic_id.required' => 'Please select a training topic.',
                'trainer_id.required' => 'Please select a trainer.',
                'training_offered_for.required' => 'Please select who the training is offered for.',
                'unit_id.required' => 'Please select a unit name.',
                'department_id.required' => 'Please select a target department.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

                $training_matrix = $this->training_matrix->store();
                $this->training_matrix_file->store($training_matrix);
                if ($training_matrix->training_evaluation == 1) {
                    $this->training_matrix_file->store1($training_matrix);
                }
                Session::flash('success', 'Your data has been created successfully');
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('training_matrix/list'));
        } catch (Exception $ex) {
            report($ex);

            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('training_matrix/list'));
        }
    }

    public function Uniquecheck(Request $request, $topicId, $trainerId)
    {
        $topicId = decryptId($topicId);
        $trainerId = decryptId($trainerId);
        $unitId = decryptId($request->input('unit_id'));
        $departmentIds = decryptId($request->input('department_id'));
        if (!is_array($departmentIds)) {
            $departmentIds = [$departmentIds];
        }
        $departmentIds = array_map('decryptId', $departmentIds);

        $data = $this->training_matrix->getuique($trainerId, $topicId, $unitId, $departmentIds);
        return response()->json([
            'exists' => $data
        ]);
    }

    public function uniquecheckTrainingMatrix(Request $request)
    {
        $ids = $request->input('id') ? decryptId($request->input('id')) : null;
        $topicId = decryptId($request->input('topicId'));
        $trainerId = decryptId($request->input('trainerId'));

        if (empty($ids)) {
            $conflicts = $this->training_matrix->getUniqueSchedule($topicId, $trainerId);
        } else {
            $conflicts = $this->training_matrix->getExistUniqueSchedule($topicId, $trainerId, $ids);
        }

        return response()->json(['conflicts' => $conflicts]);
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $training_matrix = $this->training_matrix->selectOne($id);
                $departmentList  = $this->department->select('id', 'department_name')->where('status', '1')->get();
                $training_matrixFiles_target_content  = $this->training_matrix_file->where('training_matrix_id', $id)->where('file_type', '1')->where('status', '1')->first();
                $training_matrixFiles_questionnaire  = $this->training_matrix_file->where('training_matrix_id', $id)->where('file_type', '2')->where('status', '1')->first();
                $data = array(
                    'training_matrix' => $training_matrix,
                    'departmentList' => $departmentList,
                    'training_matrixFiles_target_content' => $training_matrixFiles_target_content,
                    'training_matrixFiles_questionnaire' => $training_matrixFiles_questionnaire,
                );
            }
            return view('master.training_matrix.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
            $topicList  = $this->topic->select('id', 'topic_name')->where('status', '1')->get();
            $employeeList  = $this->employee->select('id', 'emp_name')->where('user_role', ROLE_TRAINER)->where('status', '1')->get();
            $training_matrix = $this->training_matrix->find($id);
            $training_matrixFiles_target_content  = $this->training_matrix_file->where('training_matrix_id', $id)->where('file_type', '1')->where('status', '1')->first();
            $training_matrixFiles_questionnaire  = $this->training_matrix_file->where('training_matrix_id', $id)->where('file_type', '2')->where('status', '1')->first();
            $data = array(
                'unitList' => $unitList,
                'topicList' => $topicList,
                'employeeList' => $employeeList,
                'training_matrix' => $training_matrix,
                'training_matrixFiles_target_content' => $training_matrixFiles_target_content,
                'training_matrixFiles_questionnaire' => $training_matrixFiles_questionnaire,
            );

            return view('master.training_matrix.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rules = [
                'topic_id' => 'required',
                'trainer_id' => 'required',
                'training_offered_for' => 'required',
                'unit_id' => 'required',
                'department_id' => 'required',
            ];

            $messages = [
                'topic_id.required' => 'Please select a training topic.',
                'trainer_id.required' => 'Please select a trainer.',
                'training_offered_for.required' => 'Please select who the training is offered for.',
                'unit_id.required' => 'Please select a unit name.',
                'department_id.required' => 'Please select a target department.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $training_matrix = $this->training_matrix->updates($id);
            $this->training_matrix_file->updates($id);
            $training_evaluation = decryptId($request->training_evaluation);
            if ($training_evaluation == 1) {
                $this->training_matrix_file->updates1($id);
            }

            Session::flash('success', 'Your data has been updated successfully');
            return redirect(admin_url('training_matrix/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('training_matrix/list'));
        }
    }


    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->training_matrix->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Training Matrix status changed'], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function Delete(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $this->training_matrix->deleterecord($id);
            $this->training_matrix_file->deleterecord($id);

            return response()->json(['status' => 'success', 'msg' => 'Training Matrix deleted successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }
    public function Import(Request $request)
    {
        $data = array();
        return view('master.training_matrix.import', $data);
    }
    public function ImportSubmit(Request $request)
    {
        try {
            $file = $request->file('training_matrix_upload');

            $rules = [
                'training_matrix_upload' => 'required',
            ];
            $messages = [
                'training_matrix_upload.required' => 'Please upload a file',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }


            if ($file != null) {

                $uploadpath = 'public/uploads/training_matrix';

                $folderPath = public_path('uploads/training_matrix');

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
                    'upload_type' => 10,
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

                // dispatch(new ImportTrainingMatrixJob($details));
                dispatch((new ImportTrainingMatrixJob($details))->onQueue('training_matrix'));
            }

            $insert_data['log_id'] = $insert_id;
            $insert_data['Uploded_by'] = Auth::user()->toArray();

            Session::flash('success', __('Training Matrix uploaded sucessfully'));
            return redirect(admin_url('training_matrix/list'));
        } catch (Exception $ex) {

            Session::flash('error', __('Training Matrix upload failed'));
            return redirect(admin_url('training_matrix/list'));
        }
    }
    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->training_matrix->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $departmentList = $this->department->select('id', 'department_name')->where('status', 1)->get();

            $departmentMap = $departmentList->pluck('department_name', 'id')->toArray();

            foreach ($allData as &$row) {
                $departmentIds = explode(',', $row->department_id ?? '');
                $row->department_name = implode(', ', array_map(function ($id) use ($departmentMap) {
                    return $departmentMap[$id] ?? 'Unknown';
                }, $departmentIds));
            }
            $header = [
                __("common.sno"),
                'Training Topic',
                'Trainer',
                'Training Offered for',
                'Unit Name',
                'Target Department',
                'Mode of Training',
                'Training Evaluation',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->topic_name;
                $export[] =  $data->emp_name;
                $export[] =  $data->training_offered_for == 1 ? 'Worker' : 'Executive';
                $export[] =  $data->unit_name;
                $export[] =  $data->department_name;
                $export[] =  $data->mode_of_training == 1 ? 'Online' : 'Offline';
                $export[] =  $data->training_evaluation == 1 ? 'Yes' : 'No';
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Training Matrix Details.xlsx')
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

            $allData = $this->training_matrix->exportdata();
            
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $departmentList = $this->department->select('id', 'department_name')->where('status', 1)->get();

            $departmentMap = $departmentList->pluck('department_name', 'id')->toArray();

            foreach ($allData as &$row) {
                $departmentIds = explode(',', $row->department_id ?? '');
                $row->department_name = implode(', ', array_map(function ($id) use ($departmentMap) {
                    return $departmentMap[$id] ?? 'Unknown';
                }, $departmentIds));
            }
            $header = [
                __("common.sno"),
                'Training Topic',
                'Trainer',
                'Training Offered for',
                'Unit Name',
                'Target Department',
                'Mode of Training',
                'Training Evaluation',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Training Matrix Details",
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

            $view = view('master.training_matrix.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Training Matrix.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
        }
    }

    public function DownloadSample(Request $request)
    {

        $filedetails =  exportsamplefile('training_matrix');

        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;

        //return Response::download($filePath, $customFileName);
        return redirect(url($filePath));
    }
}
