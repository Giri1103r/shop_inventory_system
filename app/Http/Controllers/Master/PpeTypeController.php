<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Jobs\ImportPpeTypeJob;
use App\Models\Master\PpeType;
use App\Models\UploadLog;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;

class PpeTypeController extends Controller
{
    private $ppetype;
    private $uploadlog;

    public function __construct()
    {
        $this->ppetype = new PpeType();
        $this->uploadlog = new UploadLog();
    }
    public function index(Request $request)
    {

        if (Auth::check()) {
            if ($request->ajax()) {
                try {

                    $data =  $this->ppetype->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active<span>";
                            if ($row->status == 1) {
                                $text = "<span style='color:green;'>Active<span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;'>In-Active<span>";
                            }


                            if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_HOD) || CheckUserRole(ROLE_EHS_OFFICER) || CheckUserRole(ROLE_EHS_HEAD)) {
                                if ($row->status == 1) {
                                    $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->id) . "' data-type='1'>Active<span>";
                                } else if ($row->status == 0) {
                                    $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->id) . "' data-type='0'>In-Active<span>";
                                }
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

                            $btn = '<a href="' . admin_url('ppe_type/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('ppe_type/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            // $btn .= '<a href="javascript:void(0);"  data-id="' . encryptId($row->id) . '" class="recordDelete" title="Delete"><i class="fa-solid fa-trash text-danger" ></i></i></a> ';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);
                    return $datatables;
                } catch (Exception $ex) {
                    return response()->json(['status' => 'error', 'msg' => __('ppe.please_try_after_some_time')], 406);
                }
            }
        }

        $data = array();

        return view('master.ppetype.list', $data);
    }

    public function add()
    {
        return view('master.ppetype.add');
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'ppe_type' => 'required',

            ];
            $messages = [

                'ppe_type.required' => __('PPE Type  is required'),

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

                $this->ppetype->store();

                Session::flash('success', __('Your data has been added successfully'));
            } catch (Exception $ex) {
                 report($ex)
;
                Session::flash('error', __('common.message_error'));
            }
            return redirect(admin_url('ppe_type/list'));
        } catch (Exception $ex) {

            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ppe_type/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $ppetype = $this->ppetype->selectOne($id);
            }
            $data = [
                'ppetype' =>  $ppetype
            ];
            return view('master.ppetype.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function edit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $ppetype = $this->ppetype->find($id);

            $data = array(
                'ppetype' => $ppetype,
                'encryptid' => $request->id,
            );
            return view('master.ppetype.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rules = [
                'ppe_type' => 'required',

            ];
            $messages = [


                'ppe_type.required' => __('PPE Type  is required'),


            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

                $this->ppetype->updates($id);

                Session::flash('success', __('Your data has been  updated successfully'));
            } catch (Exception $ex) {
                Session::flash('error', __('common.message_error'));
            }
            return redirect(admin_url('ppe_type/list'));
        } catch (Exception $ex) {

            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ppe_type/list'));
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->ppetype->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => __('PPE Type status changed Successfully')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('administration.please_try_after_some_time')], 406);
        }
    }

    public function Delete(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $this->ppetype->deleterecord($id);

            return response()->json(['status' => 'success', 'msg' => __('Your data has deleted successfully')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('administration.please_try_after_some_time')], 406);
        }
    }

    public function uniqueCheck(Request $request)
    {
        if ($request->ajax()) {
            $ppe_type = $request->ppe_type;
            $id = $request->id;

            if (empty($id)) {
                $record = $this->ppetype->uniqueCheck(['param' => 'ppe_type', 'value' => $ppe_type]);
            } else {
                $record = $this->ppetype->existUniqueCheck(['param' => 'ppe_type', 'value' => $ppe_type, 'id' => $id]);
            }

            return response()->json($record->isEmpty());
        }
    }



    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->ppetype->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __("PPE ID"),
                __("PPE Type"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->ppe_id;
                $export[] =  $data->ppe_type;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('PPE Type  to be taken .xlsx')
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

            $allData = $this->ppetype->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __("PPE ID"),
                __("PPE Type"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "PPE Type ",
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

            $view = view('master.ppetype.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Precation to be takens Details.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Import(Request $request)
    {
        $data = array();
        return view('master.ppetype.import', $data);
    }





    public function ImportSubmit(Request $request)
    {
        try {
            $file = $request->file('ppetype_upload');
            $rules = [
                'ppetype_upload' => 'required|file',
            ];
            $messages = [
                'ppetype_upload.required' => 'Please upload a file',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            if ($file) {

                $uploadpath = 'public/uploads/ppe_type_file';

                $folderPath = public_path('uploads/ppe_type_file');


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

                $insert_data = [
                    'upload_type' => 1,
                    'upload_status' => 0,
                    'file_name' => $filenewname,
                    'file_orgname' => $fileName,
                    'file_path' => $path,
                    'file_size' => $fileSize,
                    'file_extension' => $fileExt,
                    'created_by' => $user_id,
                ];

                $upload = $this->uploadlog->create($insert_data);
                $insert_id = $upload->id;

                $details = [
                    "user_id" => $user_id,
                    "log_id" => $insert_id,
                    "path" => $path,
                ];

                try {
                    // dispatch(new ImportPpeTypeJob($details));
                    dispatch(new ImportPpeTypeJob($details))->onQueue('ppetype');
                    $insert_data['log_id'] = $insert_id;
                    $insert_data['Uploded_by'] = Auth::user()->toArray();

                    Session::flash('success', 'PPE Type Uploaded Successfully');
                    return redirect(admin_url('ppe_type/list'));
                } catch (Exception $ex) {
                    Session::flash('error', 'PPE Type failed');
                    return redirect(admin_url('ppe_type/list'));
                }
            }
        } catch (Exception $ex) {
            Session::flash('error', ' PPE Typefailed: ' . $ex->getMessage());
            return redirect(admin_url('ppe_type/list'));
        }
    }



    public function DownloadSample(Request $request)
    {

        $filedetails =  exportsamplefile('ppetype');

        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;

        //return Response::download($filePath, $customFileName);
        return redirect(url($filePath));
    }
}
