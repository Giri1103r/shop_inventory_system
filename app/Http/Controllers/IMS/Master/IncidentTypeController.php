<?php

namespace App\Http\Controllers\IMS\Master;

use App\Http\Controllers\Controller;
use App\Jobs\ImportIncidentType;
use Illuminate\Http\Request;


use App\Models\Master\Unit;
use App\Models\Master\Location;
use App\Models\Master\Department;
use App\Models\User;
use App\Models\UploadLog;
use App\Jobs\ImportvendorJob;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\IMS\Master\IncidentType;

class IncidentTypeController extends Controller
{

    private $incidenttype;
    private $unit;
    private $location;
    private $department;
    private $user;
    private $uploadlog;


    public function __construct()
    {

        $this->incidenttype = new IncidentType();
        $this->unit = new Unit();
        $this->location = new Location();
        $this->department = new Department();
        $this->user = new User();
        $this->uploadlog = new UploadLog();
    }


    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {

                try {

                    $data =  $this->incidenttype->list();

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
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            // if (CheckUserPermission('view')) {
                            $btn = '<a href="' . admin_url('ohc/medicine/list/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            // if (CheckUserPermission('edit')) {
                            $btn .= '<a href="' . admin_url('ohc/medicine/list/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
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

        return view('ims.master.incident_type.list', $data);
    }

    public function Add(Request $request)
    {

        try {

            $data = array();
            return view('ims.master.incident_type.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {
        try {

            $rules = [

                'incident_type_name' => 'required',
                'short_name' => 'required',
            ];
            $messages = [

                'incident_type_name.required' => 'Please enter incident type Name',
                'short_name.required' => 'Please enter short name number',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {


                $this->incidenttype->store();


                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/medicine/list/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medicine/list/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $incident_type = $this->incidenttype->selectOne($id);

                $data = array(
                    'incident_type' => $incident_type,
                );
            }
            return view('ims.master.incident_type.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);


            $incident_type = $this->incidenttype->find($id);
            $data = array(
                'incident_type' => $incident_type,
            );


            return view('ims.master.incident_type.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $rules = [

                'incident_type_name' => 'required',
                'short_name' => 'required',
            ];
            $messages = [

                'incident_type_name.required' => 'Please enter incident type Name',
                'short_name.required' => 'Please enter short name number',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $this->incidenttype->updates($id);
            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('ohc/medicine/list/list'));
        } catch (Exception $ex) {
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medicine/list/list'));
        }
    }



    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $incident_type_name = $request->incident_type_name;
            $id = $request->id;

            if (empty($id)) {
                $isUnique = !$this->incidenttype->uniqueCheck($incident_type_name);
            } else {
                $id = decryptId($id);
                $isUnique = !$this->incidenttype->existUniqueCheck($incident_type_name, $id);
            }

            return Response::json($isUnique);
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->incidenttype->statuschange($id);
            return response()->json(['status' => 'success', 'msg' => 'Your status has changed successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }



    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->incidenttype->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Incident Type ID',
                'Incident Type Name',
                'Incident TypeShort Name',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->incident_type_id;
                $export[] =  $data->incident_type_name;
                $export[] =  $data->short_name;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Incident Type.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            report($ex);
        }
    }

    public function Import(Request $request)
    {
        $data = array();
        return view('ims.master.incident_type.import', $data);
    }
    public function ImportSubmit(Request $request)
    {
        try {
            $file = $request->file('file_upload');

            $rules = [
                'file_upload' => 'required',
            ];
            $messages = [
                'file_upload.required' => 'Please upload a file',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }


            if ($file != null) {

                $uploadpath = 'public/uploads/ims/incident_type';

                $folderPath = public_path('uploads/ims/incident_type');

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
                    'upload_type' => 1,
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

                dispatch(new ImportIncidentType($details));
                // dispatch((new ImportIncidentType($details))->onQueue('medicine'));
            }

            $insert_data['log_id'] = $insert_id;
            $insert_data['Uploded_by'] = Auth::user()->toArray();

            Session::flash('success', __('Medicine uploaded sucessfully'));
            return redirect(admin_url('ohc/medicine/list/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('Medicine upload failed'));
            return redirect(admin_url('ohc/medicine/list/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->incidenttype->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Incident Type ID',
                'Incident Type Name',
                'Incident TypeShort Name',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Incident Type Details",
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

            $view = view('ims.master.incident_type.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Incident Type Master.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
        }
    }

    public function DownloadSample(Request $request)
    {

        $filedetails =  exportsamplefile('incident_type');

        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;

        //return Response::download($filePath, $customFileName);
        return redirect(url($filePath));
    }
}
