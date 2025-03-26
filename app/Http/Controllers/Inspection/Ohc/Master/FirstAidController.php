<?php

namespace App\Http\Controllers\Inspection\Ohc\Master;

use App\Http\Controllers\Controller;
use App\Jobs\ImportFirstAidEquipmentJob;
use App\Models\Inspection\Ohc\Master\FirstAidEquipment;
use App\Models\OhcManagement\Master\Medicine;
use App\Models\UploadLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;






class FirstAidController extends Controller
{
    private $first_aid_equipment;
    private $medicine;
    private $uploadlog;



    public function __construct()
    {
        $this->first_aid_equipment = new FirstAidEquipment();
        $this->medicine = new Medicine();
        $this->uploadlog = new UploadLog();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->first_aid_equipment->list();
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
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('ohc/master/first-aid-stock/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('ohc/master/first-aid-stock/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
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
        $medicine = $this->medicine->getMedicineData();



        $data = array(
            'medicine' => $medicine,
        );

        return view('inspection.inspection_ohc.master.first_aid_equipment.list', $data);
    }

    public function Add(Request $request)
    {
        try {
            $medicine = $this->medicine->getMedicineData();

            $data = array(
                'medicine' => $medicine,
            );
            return view('inspection.inspection_ohc.master.first_aid_equipment.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }



    public function Store(Request $request)
    {
        try {
            $rules = [
                'medicine_id' => 'required',
                'freeze_quantity' => 'required',

            ];
            $messages = [
                'medicine_id.required' => 'Medicine Name is Required',
                'freeze_quantity.required' => 'Freeze Qantity is Required',


            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {
                $this->first_aid_equipment->store();
                Session::flash('success', __('Your data has been created successfully'));
            } catch (Exception $ex) {
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }
            return redirect(admin_url('ohc/master/first-aid-stock/list'));
        } catch (Exception $ex) {
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/master/first-aid-stock/list'));
        }
    }

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $medicine_id = decryptId($request->medicine_id);
            $id = decryptId($request->id);
            if ($id == '') {

                $record = $this->first_aid_equipment->uniqueCheck($medicine_id);
            } else {

                $record = $this->first_aid_equipment->ExistuniqueCheck($medicine_id, $id);
            }
            if ($record->count()) {
                return Response::json(false);
            }
            return Response::json(true);
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $first_aid = $this->first_aid_equipment->find($id);

            $medicine = $this->medicine->getMedicineData();

            $data = array(
                'medicine' => $medicine,
                'first_aid' => $first_aid,
            );

            return view('inspection.inspection_ohc.master.first_aid_equipment.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rules = [
                'medicine_id' => 'required',
                'freeze_quantity' => 'required',

            ];
            $messages = [
                'medicine_id.required' => 'Medicine Name is Required',
                'freeze_quantity.required' => 'Freeze Qantity is Required',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                dd($validator);
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->first_aid_equipment->updates($id);

            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('ohc/master/first-aid-stock/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/master/first-aid-stock/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $first_aid_equipment = $this->first_aid_equipment->selectOne($id);

                $data = array(
                    'first_aid_equipment' => $first_aid_equipment,
                );
            }
            return view('inspection.inspection_ohc.master.first_aid_equipment.view', $data);
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->first_aid_equipment->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => __('First Aid  Status is changed')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => 'Something went wrong, Please try after sometimes!'], 406);
        }
    }

    public function Delete(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $this->first_aid_equipment->deleterecord($id);

            return response()->json(['status' => 'success', 'msg' => __('First Aid was deleted successfully')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => 'Something went wrong, Please try after sometimes!'], 406);
        }
    }

    public function ExportExcel(Request $request)
    {

        try {
            $allData = $this->first_aid_equipment->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Medicine Name',
                'Freeze Qantity',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {
                $export = [];
                $export[] =  $i;
                $export[] =  $data->medicine;
                $export[] =  $data->freeze_quantity;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);
                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('First Aid.xlsx')
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

            $allData = $this->first_aid_equipment->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Medicine Name',
                'Freeze Qantity',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "First Aid Name",
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

            $view = view('inspection.inspection_ohc.master.first_aid_equipment.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "First Aid.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
        }
    }

    public function DownloadSample(Request $request)
    {
        $filedetails =  exportsamplefile('first_aid');
        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;
        return Response::download($filePath, $customFileName);
    }

    public function Import(Request $request)
    {
        $data = array();

        return view('inspection.inspection_ohc.master.first_aid_equipment.import', $data);
    }

    public function ImportSubmit(Request $request)
    {
        try {
            $file = $request->file('first_aid_file');

            $rules = [
                'first_aid_file' => 'required',
            ];
            $messages = [
                'first_aid_file.required' => 'Please upload a file',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            if ($file != null) {
                $uploadpath = 'public/uploads/inspection/master/first_aid';
                $folderPath = public_path('uploads/inspection/master/first_aid');
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

                dispatch(new ImportFirstAidEquipmentJob($details));
                // dispatch((new ImportFirstAidEquipmentJob($details))->onQueue('equipmentimport'));
            }

            $insert_data['log_id'] = $insert_id;
            $insert_data['Uploded_by'] = Auth::user()->toArray();

            Session::flash('success', __('Medicine name Uploaded sucessfully'));
            return redirect(admin_url('ohc/master/first-aid-stock/list'));
        } catch (Exception $ex) {
            Session::flash('error', __('Medicine to be taken upload failed'));
            return redirect(admin_url('ohc/master/first-aid-stock/list'));
        }
    }
}
