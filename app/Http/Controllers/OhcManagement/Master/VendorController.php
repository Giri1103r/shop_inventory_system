<?php

namespace App\Http\Controllers\OhcManagement\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Models\Master\Unit;
use App\Models\Master\Location;
use App\Models\Master\Department;
use App\Models\User;
use App\Models\UploadLog;
use App\Jobs\ImportvendorJob;
use App\Jobs\Ohc\ImportVendorjob as OhcImportVendorjob;
use App\Models\OhcManagement\Master\Vendor;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class VendorController extends Controller
{

    private $vendor;
    private $unit;
    private $location;
    private $department;
    private $user;
    private $uploadlog;


    public function __construct()
    {

        $this->vendor = new Vendor();
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

                    $data =  $this->vendor->list();

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
                            if (CheckUserPermission('view')) {
                                $btn = '<a href="' . admin_url('ohc/vendor/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            }
                            if (CheckUserPermission('edit')) {
                                $btn .= '<a href="' . admin_url('ohc/vendor/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
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

        return view('ohcmanagement.master.vendor.list', $data);
    }

    public function Add(Request $request)
    {

        try {

            $data = array();
            return view('ohcmanagement.master.vendor.add', $data);
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/vendor/list'));
        }
    }

    public function Store(Request $request)
    {
        try {

            $rules = [

                'vendor_name' => 'required|min:3|max:30',
                'license_no' => 'required|min:3|max:30',
                'address' => 'required|min:3|max:600',
            ];
            $messages = [

                'vendor_name.required' => 'Please enter vendor Name',
                'vendor_name.min' => 'The Vendor must be at least 3.',
                'vendor_name.max' => 'The Vendor must not exceed 30.',
                'license_no.required' => 'Please enter license number',
                'license_no.min' => 'The license number must be at least 3.',
                'license_no.max' => 'The license number must not exceed 30.',
                'address.required' => 'Please enter vendor Address',
                'address.min' => 'The vendor Address must be at least 3.',
                'address.max' => 'The vendor Address must not exceed 600.',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {


                $this->vendor->store();


                Session::flash('success',  __('common.created_msg'));
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error',  __('common.message_error'));
            }

            return redirect(admin_url('ohc/vendor/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/vendor/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $vendor = $this->vendor->selectOne($id);

                $data = array(
                    'vendor' => $vendor,
                );
            }
            return view('ohcmanagement.master.vendor.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/vendor/list'));
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);


            $vendor = $this->vendor->find($id);
            $data = array(
                'vendor' => $vendor,
            );


            return view('ohcmanagement.master.vendor.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/vendor/list'));
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rules = [

                'vendor_name' => 'required|min:3|max:30',
                'license_no' => 'required|min:3|max:30',
                'address' => 'required|min:3|max:600',
            ];
            $messages = [

                'vendor_name.required' => 'Please enter vendor Name',
                'vendor_name.min' => 'The Vendor must be at least 3.',
                'vendor_name.max' => 'The Vendor must not exceed 30.',
                'license_no.required' => 'Please enter license number',
                'license_no.min' => 'The license number must be at least 3.',
                'license_no.max' => 'The license number must not exceed 30.',
                'address.required' => 'Please enter vendor Address',
                'address.min' => 'The vendor Address must be at least 3.',
                'address.max' => 'The vendor Address must not exceed 600.',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {

                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->vendor->updates($id);



            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('ohc/vendor/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/vendor/list'));
        }
    }



    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $vendor_name = $request->vendor_name;
            $license_no = $request->license_no;
            $id = $request->id;

            if (empty($id)) {
                $isUnique = !$this->vendor->uniqueCheck($vendor_name, $license_no);
            } else {
                $id = decryptId($id);
                $isUnique = !$this->vendor->existUniqueCheck($vendor_name, $license_no, $id);
            }

            return Response::json($isUnique);
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->vendor->statuschange($id);
            return response()->json(['status' => 'success', 'msg' => 'Your status has changed successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }



    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->vendor->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __("ohc_management.vendor_name"),
                __("ohc_management.license_number"),
                __("ohc_management.address"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->vendor_name;
                $export[] =  $data->license_no;
                $export[] =  $data->address;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Vendor.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/vendor/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->vendor->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __("ohc_management.vendor_name"),
                __("ohc_management.license_number"),
                __("ohc_management.address"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Vendor Details",
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

            $view = view('ohcmanagement.master.vendor.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "vendor Master.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ohc/vendor/list'));
        }
    }


    public function Import(Request $request)
    {
        $data = array();
        return view('ohcmanagement.master.vendor.import', $data);
    }
    public function ImportSubmit(Request $request)
    {
        try {
            $file = $request->file('vendor_upload');

            $rules = [
                'vendor_upload' => 'required',
            ];
            $messages = [
                'vendor_upload.required' => 'Please upload a file',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }


            if ($file != null) {

                $uploadpath = 'public/uploads/vendor';

                $folderPath = public_path('uploads/vendor');

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
                    'upload_type' => 29,
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

                dispatch(new OhcImportVendorjob($details));
                // dispatch((new OhcImportVendorjob($details))->onQueue('vendor'));
            }

            $insert_data['log_id'] = $insert_id;
            $insert_data['Uploded_by'] = Auth::user()->toArray();

            Session::flash('success',  __('common.file_upload_success_msg'));
            return redirect(admin_url('ohc/vendor/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.file_upload_fails_msg'));
            return redirect(admin_url('ohc/vendor/list'));
        }
    }

    public function DownloadSample(Request $request)
    {

        $filedetails =  exportsamplefile('vendor');

        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;

        //return Response::download($filePath, $customFileName);
        return redirect(url($filePath));
    }
}
