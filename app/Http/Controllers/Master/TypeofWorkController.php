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

use App\Jobs\Ptw\ImportChecklistJob;

use App\Models\UploadLog;
use App\Models\Master\TypeofWork;
use App\Models\Master\ProtectiveEquip;
use App\Models\Master\EquipInvalve;
use App\Models\Master\SafeWork;
use App\Models\Master\Precaution;
use App\Models\Master\Checklist;
use App\Models\Master\TypeofWorkUpload;
use App\Models\Master\TypeofWorkChecklist;


class TypeofWorkController extends Controller
{
    private $typeofwork;
    private $protective;
    private $equipinvalve;
    private $safework;
    private $precaution;
    private $checklist;
    private $typeofworkupload;
    private $typeofworkchecklist;
    private $uploadlog;

    public function __construct()
    {
        $this->typeofwork = new TypeofWork();
        $this->protective = new ProtectiveEquip();
        $this->equipinvalve = new EquipInvalve();
        $this->safework = new SafeWork();
        $this->precaution = new Precaution();
        $this->checklist = new Checklist();
        $this->typeofworkupload = new TypeofWorkUpload();
        $this->typeofworkchecklist = new TypeofWorkChecklist();
        $this->uploadlog = new UploadLog();
    }

    public function index(Request $request)
    {

        if (Auth::check()) {
            if ($request->ajax()) {
                try {

                    $data =  $this->typeofwork->list();
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
                        ->addColumn('image', function ($row) {
                            if ($row->file_path) {
                                $url = asset($row->file_path);
                                return "
                                        <img src='" . $url . "' alt='Image' style='width:50px;height:50px;object-fit:cover;'>
                                ";
                            }
                            return "<span style='color:gray'>No Image</span>";
                        })
                        
                        
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            // /if (CheckUserPermission('view')) {
                            $btn = '<a href="' . admin_url('ptw/typeofworkmaster/view/' . encryptId($row->typeid)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            // if (CheckUserPermission('edit')) {
                            $btn .= '<a href="' . admin_url('ptw/typeofworkmaster/edit/' . encryptId($row->typeid)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            // }
                            // $btn .= '<a href="javascript:void(0);"  data-id="' . encryptId($row->id) . '" class="recordDelete" title="Delete"><i class="fa-solid fa-trash text-danger" ></i></i></a> ';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status','image'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);
                    return $datatables;
                } catch (Exception $ex) {

                    dd($ex);
                    return response()->json(['status' => 'error', 'msg' => __('Please try after sometime')], 406);
                }
            }
        }

        $data = array();

        return view('master.typeofwork.list', $data);
    }

    public function Add(Request $request)
    {
        try {
            $protectivequip_checklist = $this->protective->selectchecklist();
            $equipinvalve_checklist = $this->equipinvalve->selectchecklist();
            $safework_checklist = $this->safework->selectchecklist();
            $precaution_checklist = $this->precaution->selectchecklist();
            $equipchecklist_checklist = $this->checklist->selectchecklist();
            $data = array(
                'protectivequip_checklist' => $protectivequip_checklist,
                'equipinvalve_checklist' => $equipinvalve_checklist,
                'safework_checklist' => $safework_checklist,
                'precaution_checklist' => $precaution_checklist,
                'equipchecklist_checklist' => $equipchecklist_checklist,
            );
            return view('master.typeofwork.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {
        try {
            // $rules = [
            //     'checklist' => 'required',

            // ];
            // $messages = [
            //     'checklist.required' => __('Type of work is required'),

            // ];
            // $validator = Validator::make($request->all(), $rules, $messages);
            // if ($validator->fails()) {
            //     return redirect()->back()->withErrors($validator)->withInput();
            // }

            try {

                $typeofwork =    $this->typeofwork->store();
                $this->typeofworkupload->store($typeofwork->id);
                $this->typeofworkchecklist->store1($typeofwork->id);
                $this->typeofworkchecklist->store2($typeofwork->id);
                $this->typeofworkchecklist->store3($typeofwork->id);
                $this->typeofworkchecklist->store4($typeofwork->id);
                $this->typeofworkchecklist->store5($typeofwork->id);

                Session::flash('success', __('Type of work added successfully'));
            } catch (Exception $ex) {
                dd($ex);

                Session::flash('error', __('common.message_error'));
            }
            return redirect(admin_url('ptw/typeofworkmaster/list'));
        } catch (Exception $ex) {

            dd($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ptw/typeofworkmaster/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $typeofwork = $this->typeofwork->selectone($id);

                // dd($typeofwork);
                $protectivequip_checklist = $this->protective->selectchecklist();
                $equipinvalve_checklist = $this->equipinvalve->selectchecklist();
                $safework_checklist = $this->safework->selectchecklist();
                $precaution_checklist = $this->precaution->selectchecklist();
                $equipchecklist_checklist = $this->checklist->selectchecklist();
                $file = $this->typeofworkupload->where('typeofwork_id', $id)->first();


                $protective = $this->typeofworkchecklist->where('typeofwork_id', $id)->where('type', 'type1')->get()->KeyBy('check_points');
                $equipment = $this->typeofworkchecklist->where('typeofwork_id', $id)->where('type', 'type2')->get()->KeyBy('check_points');
                $manual = $this->typeofworkchecklist->where('typeofwork_id', $id)->where('type', 'type3')->get()->KeyBy('check_points');
                $check = $this->typeofworkchecklist->where('typeofwork_id', $id)->where('type', 'type4')->get()->KeyBy('check_points');
                $instruction = $this->typeofworkchecklist->where('typeofwork_id', $id)->where('type', 'type5')->get()->KeyBy('check_points');



                $getprotectivedetails = $this->typeofworkchecklist->getprotectivechecklistdetails($typeofwork->id, 'type1');
                $getequipmentdetails = $this->typeofworkchecklist->getequipmentchecklistdetails($typeofwork->id, 'type2');
                $getmanualdetails = $this->typeofworkchecklist->getmanualchecklistdetails($typeofwork->id, 'type3');
                $getcheckdetails = $this->typeofworkchecklist->getcheckchecklistdetails($typeofwork->id, 'type4');
                $getinstructiondetails = $this->typeofworkchecklist->getinstructionchecklistdetails($typeofwork->id, 'type5');

                $data = array(
                    'typeofwork' => $typeofwork,
                    'protectivequip_checklist' => $protectivequip_checklist,
                    'equipinvalve_checklist' => $equipinvalve_checklist,
                    'safework_checklist' => $safework_checklist,
                    'precaution_checklist' => $precaution_checklist,
                    'equipchecklist_checklist' => $equipchecklist_checklist,
                    'protective' => $protective,
                    'equipment' => $equipment,
                    'manual' => $manual,
                    'check' => $check,
                    'instruction' => $instruction,
                    'getprotectivedetails' => $getprotectivedetails,
                    'getequipmentdetails' => $getequipmentdetails,
                    'getmanualdetails' => $getmanualdetails,
                    'getcheckdetails' => $getcheckdetails,
                    'getinstructiondetails' => $getinstructiondetails,
                );
            }
            return view('master.typeofwork.view', $data);
        } catch (Exception $ex) {

            dd($ex);
            report($ex);
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $typeofwork = $this->typeofwork->selectone($id);
            $protectivequip_checklist = $this->protective->selectchecklist();
            $equipinvalve_checklist = $this->equipinvalve->selectchecklist();
            $safework_checklist = $this->safework->selectchecklist();
            $precaution_checklist = $this->precaution->selectchecklist();
            $equipchecklist_checklist = $this->checklist->selectchecklist();
            $file = $this->typeofworkupload->where('typeofwork_id', $id)->first();


            $protective = $this->typeofworkchecklist->where('typeofwork_id', $id)->where('type', 'type1')->get()->KeyBy('check_points');
            $equipment = $this->typeofworkchecklist->where('typeofwork_id', $id)->where('type', 'type2')->get()->KeyBy('check_points');
            $manual = $this->typeofworkchecklist->where('typeofwork_id', $id)->where('type', 'type3')->get()->KeyBy('check_points');
            $check = $this->typeofworkchecklist->where('typeofwork_id', $id)->where('type', 'type4')->get()->KeyBy('check_points');
            $instruction = $this->typeofworkchecklist->where('typeofwork_id', $id)->where('type', 'type5')->get()->KeyBy('check_points');


            $data = array(
                'protectivequip_checklist' => $protectivequip_checklist,
                'equipinvalve_checklist' => $equipinvalve_checklist,
                'safework_checklist' => $safework_checklist,
                'precaution_checklist' => $precaution_checklist,
                'equipchecklist_checklist' => $equipchecklist_checklist,
                'typeofwork' => $typeofwork,
                'file' => $file,
                'protective' => $protective,
                'equipment' => $equipment,
                'manual' => $manual,
                'check' => $check,
                'instruction' => $instruction,
            );
            return view('master.typeofwork.edit', $data);
        } catch (Exception $error) {

            dd($error);
            report($error->getMessage());
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            // dd($id)
            // $rules = [
            //     'checklist' => 'required',

            // ];
            // $messages = [
            //     'checklist.required' => __('Type of work is required'),

            // ];
            // $validator = Validator::make($request->all(), $rules, $messages);
            // if ($validator->fails()) {
            //     return redirect()->back()->withErrors($validator)->withInput();
            // }


            $typeofwork =  $this->typeofwork->updates($id);
            $updatedRecord = $this->typeofwork->find($id);
            $this->typeofworkupload->updates($updatedRecord->id);
            $this->typeofworkchecklist->update1($updatedRecord->id);
            $this->typeofworkchecklist->update2($updatedRecord->id);
            $this->typeofworkchecklist->update3($updatedRecord->id);
            $this->typeofworkchecklist->update4($updatedRecord->id);
            $this->typeofworkchecklist->update5($updatedRecord->id);

            Session::flash('success', __('Type of work updated successfully'));
            return redirect(admin_url('ptw/typeofworkmaster/list'));
        } catch (Exception $ex) {

            dd($ex);
            Session::flash('error', __('Something went wrong try again'));
            return redirect(admin_url('ptw/typeofworkmaster/list'));
        }
    }

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $work_name = $request->work_name;
            $id = $request->id;
            if ($id == '') {
                $record = $this->typeofwork->uniqueCheck($work_name);
            } else {
                $id = decryptId($id);
                $record = $this->typeofwork->ExistuniqueCheck($work_name, $id);
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

            $this->typeofwork->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => __('Type of work status changed')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('Please try after sometime')], 406);
        }
    }

    public function Delete(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $this->checklist->deleterecord($id);

            return response()->json(['status' => 'success', 'msg' => __('Type of work deleted successfully')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('Please try after sometime')], 406);
        }
    }


    public function DownloadSample(Request $request)
    {

        $filedetails =  exportsamplefile('checklist');



        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;

        return Response::download($filePath, $customFileName);
    }

    public function Import(Request $request)
    {
        $data = array();

        return view('master.typeofwork.import', $data);
    }

    public function ImportSubmit(Request $request)
    {
        try {
            $file = $request->file('checklist_upload');

            $rules = [
                'checklist_upload' => 'required',
            ];
            $messages = [
                'checklist_upload.required' => 'Please upload a file',
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

                dispatch(new ImportChecklistJob($details));
                //    dispatch((new ImportEmployeeJob($details))->onQueue('empimport'));
            }

            $insert_data['log_id'] = $insert_id;
            $insert_data['Uploded_by'] = Auth::user()->toArray();

            Session::flash('success', __('Type of work uploaded sucessfully'));
            return redirect(admin_url('ptw/typeofworkmaster/list'));
        } catch (Exception $ex) {
            Session::flash('error', __('Type of work upload failed'));
            return redirect(admin_url('ptw/typeofworkmaster/list'));
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->typeofwork->exportdata();

            $header = [
                __("common.sno"),
                __('Name'),
                __('Description'),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->work_name;
                $export[] =  $data->description;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Type of work .xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            ini_set("pcre.backtrack_limit", "5000000");

            $allData = $this->typeofwork->exportdata();

            $header = [
                __("common.sno"),
                __('Image'),
                __('Name'),
                __('Description'),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Type of work Details",
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

            $view = view('master.typeofwork.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Type of work Details.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
        }
    }
}
