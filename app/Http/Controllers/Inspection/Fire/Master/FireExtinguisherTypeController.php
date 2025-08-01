<?php

namespace App\Http\Controllers\Inspection\Fire\Master;

use Exception;
use App\Http\Controllers\Controller;
use App\Models\Inspection\Fire\FireExtinguisherType;
use App\Models\UploadLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\SimpleExcel\SimpleExcelWriter;

class FireExtinguisherTypeController extends Controller
{

    private $fireExtinguisherType;

    private $uploadlog;


    public function __construct()
    {

        $this->fireExtinguisherType = new FireExtinguisherType();

        $this->uploadlog = new UploadLog();
    }


    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {

                try {

                    $data =  $this->fireExtinguisherType->list();

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
                                $btn = '<a href="' . admin_url('company/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            }
                            if (CheckUserPermission('edit')) {
                                $btn .= '<a href="' . admin_url('company/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
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
        $data = array();

        return view('inspection.fire.master.fire_extinguisher_type.list', $data);
    }

    public function Add(Request $request)
    {

        try {

            $data = array();
            return view('inspection.fire.master.fire_extinguisher_type.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {
        try {

            $rules = [
                'fire_extinguisher_id' => 'required',
                'fire_extinguisher_name' => 'required',

            ];
            $messages = [
                'fire_extinguisher_id.required' => 'Please enter Fire Extinguisher Type ID',
                'fire_extinguisher_name.required' => 'Please enter Fire Extinguisher Type Name',


            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {


                $company = $this->fireExtinguisherType->store();


                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('fire/master/fire_extinguisher-type/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/master/fire_extinguisher-type/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $fireExtinguisherType = $this->fireExtinguisherType->selectOne($id);

                $data = array(
                    'fireExtinguisherType' => $fireExtinguisherType,
                );
            }
            return view('inspection.fire.master.fire_extinguisher_type.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);


            $fireExtinguisherType = $this->fireExtinguisherType->find($id);
            $data = array(
                'fireExtinguisherType' => $fireExtinguisherType,
            );


            return view('inspection.fire.master.fire_extinguisher_type.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rules = [
                'fire_extinguisher_id' => 'required',
                'fire_extinguisher_name' => 'required',

            ];
             $messages = [
                'fire_extinguisher_id.required' => 'Please enter Fire Extinguisher Type ID',
                'fire_extinguisher_name.required' => 'Please enter Fire Extinguisher Type Name',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->fireExtinguisherType->updates($id);

            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('fire/master/fire_extinguisher-type/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/master/fire_extinguisher-type/list'));
        }
    }


    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $fire_extinguisher_name = $request->fire_extinguisher_name;
            $id = $request->id;
            if ($id == '') {
                $record = $this->fireExtinguisherType->uniqueCheck($fire_extinguisher_name);
            } else {
                $id = decryptId($id);
                $record = $this->fireExtinguisherType->ExistuniqueCheck($fire_extinguisher_name, $id);
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

            $this->fireExtinguisherType->statuschange($id);


            return response()->json(['status' => 'success', 'msg' => 'Fire Extinguisher type status changed'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }


    public function Import(Request $request)
    {
        $data = array();
        return view('inspection.fire.master.fire_extinguisher_type.import', $data);
    }
    public function ImportSubmit(Request $request)
    {
        try {
            $file = $request->file('company_upload');

            $rules = [
                'company_upload' => 'required',
            ];
            $messages = [
                'company_upload.required' => 'Please upload a file',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }


            if ($file != null) {

                $uploadpath = 'public/uploads/company';

                $folderPath = public_path('uploads/company');

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

                // dispatch(new ImportCompanyJob($details));
                // dispatch((new ImportCompanyJob($details))->onQueue('company'));
            }

            $insert_data['log_id'] = $insert_id;
            $insert_data['Uploded_by'] = Auth::user()->toArray();

            Session::flash('success', __('Company uploaded sucessfully'));
            return redirect(admin_url('fire/master/fire_extinguisher-type/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('Company upload failed'));
            return redirect(admin_url('fire/master/fire_extinguisher-type/list'));
        }
    }
    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->fireExtinguisherType->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Company ID',
                'Company Name',
                'Short Name',
                'Address',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->company_id;
                $export[] =  $data->company_name;
                $export[] =  $data->short_name;
                $export[] =  $data->address;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Company Master.xlsx')
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

            $allData = $this->fireExtinguisherType->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Company ID',
                'Company Name',
                'Short Name',
                'Address',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Company Details",
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

            $view = view('inspection.fire.master.fire_extinguisher_type.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Company Master.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
        }
    }

    public function DownloadSample(Request $request)
    {

        $filedetails =  exportsamplefile('company');

        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;

        //return Response::download($filePath, $customFileName);
        return redirect(url($filePath));
    }
}
