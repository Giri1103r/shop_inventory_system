<?php

namespace App\Http\Controllers\Inspection\Safety\Master;

use Exception;
use App\Models\UploadLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\ImportEquipmentJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Models\Inspection\Safety\Master\Equipment;

class EquipmentController extends Controller
{
    private $equipment;
    private $uploadlog;

    public function __construct()
    {
        $this->equipment = new Equipment();
        $this->uploadlog = new UploadLog();
    }

    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->equipment->list();
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
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('safety/master/equipment/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('safety/master/equipment/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
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
                    return response()->json(['status' => 'error', 'msg' => __('Inspection.please_try_after_some_time')], 406);
                }
            }
        }

        $data = array();

        return view('inspection.safety.master.equipment.list', $data);
    }

    public function Add(Request $request)
    {
        try {

            $data = array();
            return view('inspection.safety.master.equipment.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {
        try {
            $rules = [
                'equipment_name' => 'required',
            ];
            $messages = [
                'equipment_name.required' => __('equipment to be taken is required'),
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {
                $this->equipment->store();
                Session::flash('success', __('Your data has been created successfully'));
            } catch (Exception $ex) {
                Session::flash('error', __('common.message_error'));
            }
            return redirect(admin_url('safety/master/equipment/list'));
        } catch (Exception $ex) {
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('safety/master/equipment/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $equipment = $this->equipment->selectOne($id);

                $data = array(
                    'equipment' => $equipment,
                );
            }
            return view('inspection.safety.master.equipment.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('safety/master/equipment/list'));
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $equipment = $this->equipment->find($id);

            $data = array(
                'equipment' => $equipment,
            );
            return view('inspection.safety.master.equipment.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('safety/master/equipment/list'));
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $rules = [
                'equipment_name' => 'required',

            ];
            $messages = [
                'equipment_name.required' => __('equipment to be taken is required'),

            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }


            $this->equipment->updates($id);

            Session::flash('success', __('Your data has been updated successfully'));
            return redirect(admin_url('safety/master/equipment/list'));
        } catch (Exception $ex) {

            Session::flash('error', __('Something went wrong try again'));
            return redirect(admin_url('safety/master/equipment/list'));
        }
    }

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $equipment = $request->equipment_name;
            $id = $request->id;
            if ($id == '') {
                $record = $this->equipment->uniqueCheck($equipment);
            } else {
                $id = decryptId($id);
                $record = $this->equipment->ExistuniqueCheck($equipment, $id);
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

            $this->equipment->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => __('Equipment Status is changed')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('administration.please_try_after_some_time')], 406);
        }
    }

    public function Delete(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $this->equipment->deleterecord($id);

            return response()->json(['status' => 'success', 'msg' => __('Equipment was deleted successfully')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('administration.please_try_after_some_time')], 406);
        }
    }


    public function DownloadSample(Request $request)
    {
        $filedetails =  exportsamplefile('equipment');
        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;
        return Response::download($filePath, $customFileName);
    }

    public function Import(Request $request)
    {
        $data = array();

        return view('inspection.safety.master.equipment.import', $data);
    }

    public function ImportSubmit(Request $request)
    {
        try {
            $file = $request->file('equipment_file');

            $rules = [
                'equipment_file' => 'required',
            ];
            $messages = [
                'equipment_file.required' => 'Please upload a file',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            if ($file != null) {
                $uploadpath = 'public/uploads/inspection/master/equipment';
                $folderPath = public_path('uploads/inspection/master/equipment');
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
                    'upload_type' => 19,
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

                // dispatch(new ImportequipmentJob($details));
                dispatch((new ImportEquipmentJob($details))->onQueue('equipmentimport'));
            }

            $insert_data['log_id'] = $insert_id;
            $insert_data['Uploded_by'] = Auth::user()->toArray();

            Session::flash('success', __('Equipment name Uploaded sucessfully'));
            return redirect(admin_url('safety/master/equipment/list'));
        } catch (Exception $ex) {
            Session::flash('error', __('equipment to be taken upload failed'));
            return redirect(admin_url('safety/master/equipment/list'));
        }
    }

    public function ExportExcel(Request $request)
    {

        try {
            $allData = $this->equipment->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __('inspection.equipment_name'),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {
                $export = [];
                $export[] =  $i;
                $export[] =  $data->equipment_name;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);
                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Equipment Name.xlsx')
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

            $allData = $this->equipment->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __('inspection.equipment_name'),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Equipment Name",
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

            $view = view('inspection.safety.master.equipment.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Equipment Name.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
        }
    }
}
