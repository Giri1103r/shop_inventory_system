<?php

namespace App\Http\Controllers\Inspection\Ohc;

use App\Http\Controllers\Controller;
use App\Jobs\ImportCurrentNewExtCodeDailingJob;
use App\Models\Inspection\Ohc\CurrentNewExtCodeDialing;
use App\Models\Master\Department;
use App\Models\Master\Employee;
use App\Models\Master\Unit;
use App\Models\UploadLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CurrentNewExtCodeDialingController extends Controller
{
    private $current_new_ext_code;
    private $unit;
    private $department;
    private $employee;
    private $uploadlog;



    public function __construct()
    {
        $this->current_new_ext_code = new CurrentNewExtCodeDialing();
        $this->unit = new Unit();
        $this->department = new Department();
        $this->employee = new Employee();
        $this->uploadlog = new UploadLog();

    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->current_new_ext_code->list();
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
                        ->addColumn('created_date', function ($row) {
                            return Displaydatetimeformat($row->created_at);
                        })
                        ->addColumn('created_date', function ($row) {
                            return Displaydatetimeformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('ohc/current-new-ext-code-dialing/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('ohc/current-new-ext-code-dialing/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
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
                    dd($ex);
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }
        $unitList = $this->unit->getUnitList();

        $data = array(
            'unitList' => $unitList,
        );
        return view('inspection.inspection_ohc.current_new_ext_code_dialing.list', $data);
    }

    public function Add(Request $request)
    {
        try {
            $unitList = $this->unit->getUnitList();

            $data = array(
                'unitList' => $unitList,
            );
            return view('inspection.inspection_ohc.current_new_ext_code_dialing.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {
        dd($request->all());
        try {
            // $rules = [
            //     'unit_id' => 'required',
            //     'department_id' => 'required',
            //     'review_date' => 'required',

            // ];
            // $messages = [
            //     'department_id.required' => 'Please select a Deparment.',
            //     'unit_id.required' => 'Please select a unit.',
            //     'review_date.required' => 'Please select the expiry date.',
            // ];
            // $validator = Validator::make($request->all(), $rules, $messages);
            // if ($validator->fails()) {
            //     return redirect()->back()->withErrors($validator)->withInput();
            // }

            try {

                $current_new_ext_code = $this->current_new_ext_code->store();


                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/current-new-ext-code-dialing/list'));
        } catch (Exception $ex) {

            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/current-new-ext-code-dialing/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $current_new_ext_code = $this->current_new_ext_code->selectOne($id);

                $data = array(
                    'current_new_ext_code' => $current_new_ext_code,
                );
            }
            return view('inspection.inspection_ohc.current_new_ext_code_dialing.view', $data);
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $current_new_ext_code = $this->current_new_ext_code->find($id);

            $unitList = $this->unit->getUnitList();

            $data = array(
                'unitList' => $unitList,
                'current_new_ext_code' => $current_new_ext_code,

            );
            // dd($data);

            return view('inspection.inspection_ohc.current_new_ext_code_dialing.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            // $rules = [
            //     'medicine_id' => 'required',
            //     'freeze_quantity' => 'required',

            // ];
            // $messages = [
            //     'medicine_id.required' => 'Medicine Name is Required',
            //     'freeze_quantity.required' => 'Freeze Qantity is Required',

            // ];
            // $validator = Validator::make($request->all(), $rules, $messages);
            // if ($validator->fails()) {
            //     dd($validator);
            //     return redirect()->back()->withErrors($validator)->withInput();
            // }

            $this->current_new_ext_code->updates($id);

            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('ohc/current-new-ext-code-dialing/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/current-new-ext-code-dialing/list'));
        }
    }

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $unit_id = decryptId($request->unit_id);
            $department_id = decryptId($request->department_id);
            $emp_name_id = decryptId($request->emp_name_id);
            $number = $request->number;
            $id = decryptId($request->id);
            if ($id == '') {
                $record = $this->current_new_ext_code->uniqueCheck($unit_id, $department_id, $emp_name_id, $number);
            } else {
                $record = $this->current_new_ext_code->ExistuniqueCheck($unit_id, $department_id, $emp_name_id, $number, $id);
            }
            if (count($record) > 0) {
                return Response::json(false);
            }
            return Response::json(true);
        }
    }


    public function ExportExcel(Request $request)
    {

        try {
            $allData = $this->current_new_ext_code->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Unit',
                'Department',
                'Employee Name',
                'Dailing Number',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {
                $export = [];
                $export[] =  $i;
                $export[] =  $data->unit_name;
                $export[] =  $data->department_name;
                $export[] =  $data->emp_name;
                $export[] =  $data->number;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);
                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Code Dailing .xlsx')
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

            $allData = $this->current_new_ext_code->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Unit',
                'Department',
                'Employee Name',
                'Dailing Number',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Current New Code Dailing",
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

            $view = view('inspection.inspection_ohc.current_new_ext_code_dialing.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Code Dailing.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
        }
    }

    public function Import(Request $request)
    {
        $data = array();

        return view('inspection.inspection_ohc.current_new_ext_code_dialing.import', $data);
    }

    public function DownloadSample(Request $request)
    {
        $filedetails =  exportsamplefile('code_dailing');
        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;
        return Response::download($filePath, $customFileName);
    }


    public function ImportSubmit(Request $request)
    {
        // dd($request->all());
        try {
            $file = $request->file('code_dailing_file');

            $rules = [
                'code_dailing_file' => 'required',
            ];
            $messages = [
                'code_dailing_file.required' => 'Please upload a file',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            if ($file != null) {
                $uploadpath = 'public/uploads/inspection/ohc/code_dailing';
                $folderPath = public_path('uploads/inspection/ohc/code_dailing');
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

                dispatch(new ImportCurrentNewExtCodeDailingJob($details));
                // dispatch((new ImportFirstAidEquipmentJob($details))->onQueue('equipmentimport'));
            }

            $insert_data['log_id'] = $insert_id;
            $insert_data['Uploded_by'] = Auth::user()->toArray();

            Session::flash('success', __('Current New Ext Code Dailing Uploaded sucessfully'));
            return redirect(admin_url('ohc/current-new-ext-code-dialing/list'));
        } catch (Exception $ex) {
            Session::flash('error', __('Current New Ext Code Dailing to be taken upload failed'));
            return redirect(admin_url('ohc/current-new-ext-code-dialing/list'));
        }
    }


    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->current_new_ext_code->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => __('Current New Ext Code Dialing Detail Status is changed')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => 'Something went wrong, Please try after sometimes!'], 406);
        }
    }

    public function Delete(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $this->current_new_ext_code->deleterecord($id);

            return response()->json(['status' => 'success', 'msg' => __('Current New Ext Code Dialing Detailwas deleted successfully')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => 'Something went wrong, Please try after sometimes!'], 406);
        }
    }
}
