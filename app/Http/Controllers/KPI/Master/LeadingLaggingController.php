<?php

namespace App\Http\Controllers\KPI\Master;

use Exception;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\kpi\LeadinglaggingJob;
use App\Models\KPI\LeadingLagging;
use App\Models\UploadLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class LeadingLaggingController extends Controller
{

    private $leading_lagging;
    private $uploadlog;

    public function __construct()
    {
        $this->leading_lagging = new LeadingLagging();
        $this->uploadlog = new UploadLog();
    }


    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {

                    $data =  $this->leading_lagging->list();

                    $datatables = DataTables::of($data['data'])
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
                        ->addColumn('type', function ($row) {

                            if ($row->type == 1) {
                                $text = "<span  data-id='" . encryptId($row->id) . "' data-type = '1' >Leading<span>";
                            } else if ($row->type == 2) {
                                $text = "<span data-id='" . encryptId($row->id) . "' data-type = '0' >Lagging<span>";
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
                            $btn = '<a href="' . admin_url('kpi/master/leading-lagging/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('kpi/master/leading-lagging/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', 'type'])
                        ->setFilteredRecords($data['total_records'])
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
        return view('kpi.master.leading_lagging.list');
    }

    public function Add(Request $request)
    {
        try {
            return view('kpi.master.leading_lagging.add');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('kpi/master/leading-lagging/list'));
        }
    }

    public function Store(Request $request)
    {
        try {
            $rules = [
                'type' => 'required',
                'value' => 'required',
            ];
            $messages = [
                'type.required' => 'Please Select Type',
                'value.required' => 'Please Enter Value',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            try {
                $this->leading_lagging->store();
                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('kpi/master/leading-lagging/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('kpi/master/leading-lagging/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $leading_lagging = $this->leading_lagging->selectOne($id);

                $data = array(
                    'leading_lagging' => $leading_lagging,
                );
            }
            return view('kpi.master.leading_lagging.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('kpi/master/leading-lagging/list'));
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $leading_lagging = $this->leading_lagging->find($id);

            $data = array(
                'leading_lagging' => $leading_lagging,
            );

            return view('kpi.master.leading_lagging.edit', $data);
        } catch (Exception $error) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('kpi/master/leading-lagging/list'));
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rules = [

                'type' => 'required',
                'value' => 'required',
            ];
            $messages = [
                'type.required' => 'Please Select Type',
                'value.required' => 'Please Enter Value',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->leading_lagging->updates($id);

            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('kpi/master/leading-lagging/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('kpi/master/leading-lagging/list'));
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->leading_lagging->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Leading and Lagging status changed'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->leading_lagging->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __("common.type"),
                __("common.value"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] = $data->type == LEADING ? __('common.leading') : __('common.lagging');
                $export[] =  $data->value;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Leading and Lagging.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('kpi/master/leading-lagging/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->leading_lagging->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __("common.type"),
                __("common.value"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Leading and Lagging Details",
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

            $view = view('kpi.master.leading_lagging.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Leading and Lagging Master.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('kpi/master/leading-lagging/list'));
        }
    }
    public function Import(Request $request)
    {
        $data = array();
        return view('kpi.master.leading_lagging.import', $data);
    }
    public function ImportSubmit(Request $request)
    {
        try {
            $file = $request->file('leading_upload');

            $rules = [
                'leading_upload' => 'required',
            ];
            $messages = [
                'leading_upload.required' => 'Please upload a file',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }


            if ($file != null) {

                $uploadpath = 'public/uploads/leadinglagging';

                $folderPath = public_path('uploads/leadinglagging');

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

                dispatch(new LeadinglaggingJob($details));
                // dispatch((new LeadinglaggingJob($details))->onQueue('company'));
            }

            $insert_data['log_id'] = $insert_id;
            $insert_data['Uploded_by'] = Auth::user()->toArray();


            Session::flash('success', 'File Uploaded Successfully!');
            return redirect(admin_url('kpi/master/leading-lagging/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'File uploads fails!');
            return redirect(admin_url('kpi/master/leading-lagging/list'));
        }
    }
    public function list(Request $request, $companyId)
    {

        $companyId = decryptId($companyId);
        $id = decryptId($request->id);
        $leading_laggings = $this->leading_lagging->ajaxList($id, $companyId);

        return response()->json($leading_laggings);
    }


    public function alllist(Request $request)
    {
        $companyId = decryptId($request->type);
        $leading_laggings = $this->leading_lagging->ajaxallList($companyId);
        return response()->json($leading_laggings);
    }

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $value = $request->value;
            $type = decryptId($request->type);
            $id = $request->id;
            if ($id == '') {
                $record = $this->leading_lagging->uniqueCheck($value, $type);
            } else {
                $id = decryptId($id);
                $record = $this->leading_lagging->ExistuniqueCheck($value, $type, $id);
            }
            if ($record->count()) {
                return Response::json(false);
            }
            return Response::json(true);
        }
    }

    public function DownloadSample(Request $request)
    {

        // dd(1);
        $filedetails =  exportsamplefile('leading_lagging');
        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;

        //return Response::download($filePath, $customFileName);
        return redirect(url($filePath));
    }
}
