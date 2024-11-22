<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

use Str;
use Response;
use Session;
use Exception;
use DataTables;

use App\Jobs\Ptw\ImportequipinvalveJob;

use App\Models\UploadLog;
use App\Models\Master\EquipInvalve;


class EquipInvalveController extends Controller
{
    private $equipinvalve;
    private $uploadlog;

    public function __construct()
    {
        $this->equipinvalve = new EquipInvalve();
        $this->uploadlog = new UploadLog();
    }

    public function index(Request $request)
    {

        if (Auth::check()) {
            if ($request->ajax()) {
                try {

                    $data =  $this->equipinvalve->list();
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
                        ->addColumn('created_date', function ($row) {
                            return Displaydatetimeformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            // /if (CheckUserPermission('view')) {
                            $btn = '<a href="' . admin_url('ptw/equipinvolvemaster/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            // if (CheckUserPermission('edit')) {
                            $btn .= '<a href="' . admin_url('ptw/equipinvolvemaster/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            // }
                            $btn .= '<a href="javascript:void(0);"  data-id="' . encryptId($row->id) . '" class="recordDelete" title="Delete"><i class="fa-solid fa-trash text-danger" ></i></i></a> ';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);
                    return $datatables;
                } catch (Exception $ex) {
                    return response()->json(['status' => 'error', 'msg' => __('administration.please_try_after_some_time')], 406);
                }
            }
        }

        $data = array();

        return view('master.equipinvalve.list', $data);
    }

    public function Add(Request $request)
    {
        try {

            $data = array();
            return view('master.equipinvalve.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {
        try {
            $rules = [
                'equip_involve' => 'required',

            ];
            $messages = [
                'equip_involve.required' => __('Equipments invalve job is required'),

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

                $this->equipinvalve->store();

                Session::flash('success', __('Equipments invalve job added successfully'));
            } catch (Exception $ex) {
                Session::flash('error', __('common.message_error'));
            }
            return redirect(admin_url('ptw/equipinvolvemaster/list'));
        } catch (Exception $ex) {
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ptw/equipinvolvemaster/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $equipinvalve = $this->equipinvalve->selectOne($id);

                $data = array(
                    'equipinvalve' => $equipinvalve,
                );
            }
            return view('master.equipinvalve.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $equipinvalve = $this->equipinvalve->find($id);

            $data = array(
                'equipinvalve' => $equipinvalve,
            );
            return view('master.equipinvalve.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $rules = [
                'equip_involve' => 'required',
            ];

            $messages = [
                'equip_involve.required' => __('Equipments invalve job is required'),
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->equipinvalve->updates($id);

            Session::flash('success', __('Equipments invalve job updated successfully'));
            return redirect(admin_url('ptw/equipinvolvemaster/list'));
        } catch (Exception $ex) {

            Session::flash('error', __('Something went wrong try again'));
            return redirect(admin_url('ptw/equipinvolvemaster/list'));
        }
    }

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $equip_involve = $request->equip_involve;
            $id = $request->id;
            if ($id == '') {
                $record = $this->equipinvalve->uniqueCheck($equip_involve);
            } else {
                $id = decryptId($id);
                $record = $this->equipinvalve->ExistuniqueCheck($equip_involve, $id);
            }
            if ($record->count()) {
                return Response::json(false);
            }
            return Response::json(true);
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->equipinvalve->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => __('Equipments invalve job status changed')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('administration.please_try_after_some_time')], 406);
        }
    }

    public function Delete(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $this->equipinvalve->deleterecord($id);

            return response()->json(['status' => 'success', 'msg' => __('Equipments invalve job deleted successfully')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('administration.please_try_after_some_time')], 406);
        }
    }


    public function DownloadSample(Request $request)
    {

        $filedetails =  exportsamplefile('equip_involved');

        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;
        
        return Response::download($filePath, $customFileName);
    }

    public function Import(Request $request)
    {
        $data = array();

        return view('master.equipinvalve.import', $data);
    }

    public function ImportSubmit(Request $request)
    {
        try {
            $file = $request->file('equip_involve_upload');

            $rules = [
                'equip_involve_upload' => 'required',
            ];
            $messages = [
                'equip_involve_upload.required' => 'Please upload a file',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }


            if ($file != null) {

                $uploadpath = 'public/uploads/ptw/';

                $folderPath = public_path('uploads/ptw');

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

                dispatch(new ImportequipinvalveJob($details));
                //    dispatch((new ImportEmployeeJob($details))->onQueue('empimport'));
            }

            $insert_data['log_id'] = $insert_id;
            $insert_data['Uploded_by'] = Auth::user()->toArray();

            Session::flash('success', __('Equipment Involved in Job uploaded sucessfully'));
            return redirect(admin_url('ptw/equipinvolvemaster/list'));
        } catch (Exception $ex) {

            Session::flash('error', __('Equipment Involved in Job upload failed'));
            return redirect(admin_url('ptw/equipinvolvemaster/list'));
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->equipinvalve->exportdata();

            $header = [
                __("common.sno"),
                __('Name'),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->equip_involve;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Protective equipments to be worn .xlsx')
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

            $allData = $this->equipinvalve->exportdata();

            $header = [
                __("common.sno"),
                __('Name'),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Location Details",
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

            $view = view('master.equipinvalve.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Equipments Involved Job Details.pdf";
            $mpdf->Output($filename, 'I');
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
        }
    }

}
