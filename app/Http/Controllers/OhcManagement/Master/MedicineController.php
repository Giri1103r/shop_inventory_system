<?php

namespace App\Http\Controllers\OhcManagement\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;



use App\Models\Master\Unit;
use App\Models\Master\Location;
use App\Models\Master\Department;
use App\Models\User;
use App\Models\UploadLog;
use App\Jobs\ImportmedicineJob;
use App\Mail\Ohc\MedicineRequestEmail;
use App\Models\OhcManagement\Master\Medicine;
use App\Models\OhcManagement\OhcStatuslog;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\OhcManagement\Report\Inventory;

class MedicineController extends Controller
{

    private $medicine;
    private $unit;
    private $location;
    private $department;
    private $user;
    private $uploadlog;
    private $ohc_statuslog;
    private $inventory;


    public function __construct()
    {

        $this->medicine = new Medicine();
        $this->unit = new Unit();
        $this->location = new Location();
        $this->department = new Department();
        $this->user = new User();
        $this->uploadlog = new UploadLog();
        $this->ohc_statuslog = new OhcStatuslog();
        $this->inventory = new Inventory();
    }


    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {

                try {

                    $data =  $this->medicine->list();

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
                        ->addColumn('expiry_date', function ($row) {
                            return Displaydateformat($row->expiry_date);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->editColumn('unit_id', function ($row) {
                            return $row->unit_name;
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            // if (CheckUserPermission('view')) {
                            $btn = '<a href="' . admin_url('ohc/medicine/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            if (CheckUserPermission('edit')) {
                                $btn .= '<a href="' . admin_url('ohc/medicine/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            }

                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', 'unit_id', 'expiry_date'])
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
        $unit = $this->unit->getunit();
        $data = array(

            'unit' => $unit
        );

        return view('ohcmanagement.master.medicine.list', $data);
    }

    public function Add(Request $request)
    {

        try {
            $unit = $this->unit->getunit();
            $data = [
                'unit' => $unit
            ];
            return view('ohcmanagement.master.medicine.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {
        try {

            $rules = [
                'medicine' => 'required',
                'pack' => 'required',
                'hsn' => 'required',
                'unit_id' => 'required',
                'threshold_limit' => 'required',
                'expire_date' => 'required',

            ];
            $messages = [
                'medicine.required' => 'Please enter the medicine name.',
                'pack.required' => 'Please enter the pack details.',
                'hsn.required' => 'Please enter the HSN code.',
                'unit_id.required' => 'Please select a unit.',
                'threshold_limit.required' => 'Please enter the threshold limit.',
                'expire_date.required' => 'Please select the expiry date.',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

                $data =  $this->medicine->store();
                $id =  $data->id;
                $details = $this->medicine->selectOne($id);
                // $this->inventory->store($details);

                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/medicine/list'));
        } catch (Exception $ex) {

            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medicine/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $medicine = $this->medicine->selectOne($id);
                $unit = $this->unit->getunit();
                $data = array(
                    'medicine' => $medicine,
                );
            }
            return view('ohcmanagement.master.medicine.view', $data);
        } catch (Exception $ex) {
        }
    }
    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);


            $medicine = $this->medicine->find($id);
            $unit = $this->unit->getunit();
            $data = array(
                'medicine' => $medicine,
                'unit' => $unit
            );


            return view('ohcmanagement.master.medicine.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rules = [
                'medicine' => 'required',
                'pack' => 'required',
                'hsn' => 'required',
                'unit_id' => 'required',
                'threshold_limit' => 'required',
                'expire_date' => 'required',

            ];
            $messages = [
                'medicine.required' => 'Please enter the medicine name.',
                'pack.required' => 'Please enter the pack details.',
                'hsn.required' => 'Please enter the HSN code.',
                'unit_id.required' => 'Please select a unit.',
                'threshold_limit.required' => 'Please enter the threshold limit.',
                'expire_date.required' => 'Please select the expiry date.',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->medicine->updates($id);


            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('ohc/medicine/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medicine/list'));
        }
    }

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $medicine_name = $request->medicine_name;
            $unit_id = $request->unit_id;
            $hsn = $request->hsn;
            $id = $request->id;

            if (empty($id)) {
                $isUnique = $this->medicine->uniqueCheck($medicine_name, $unit_id);
            } else {
                $id = decryptId($id);
                // dd( $unit_id );
                $isUnique = $this->medicine->existUniqueCheck($medicine_name,  $id, $unit_id);
            }

            if ($isUnique->count()) {
                return Response::json(false);
            }
            return Response::json(true);
        }
    }

    public function hsnNumber(Request $request)
    {
        if ($request->ajax()) {

            $hsn = $request->hsn;

            $id = $request->id;
            if (empty($id)) {
                $isUnique = $this->medicine->HsnuniqueCheck($hsn);
            } else {
                $id = decryptId($id);

                $isUnique = $this->medicine->existHsnUniqueCheck($id, $hsn);
            }

            if ($isUnique->count()) {
                return Response::json(false);
            }
            return Response::json(true);
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->medicine->statuschange($id);
            // $medicine =  $this->medicine->selectOne($id);
            // $this->user->statuschange($medicine->login_id);

            return response()->json(['status' => 'success', 'msg' => 'Your status  has changed Successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function Delete(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $location = $this->location->where('medicine_id', $id)->exists();
            $unit = $this->unit->where('medicine_id', $id)->exists();
            $department = $this->department->where('medicine_id', $id)->exists();

            if ($location || $unit || $department) {
                return response()->json(['status' => 'error', 'msg' => 'module_exits'], 406);
            }
            $this->medicine->deleterecord($id);
            return response()->json(['status' => 'success', 'msg' => 'medicine deleted successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->medicine->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Medicine Name',
                'Pack',
                'HSN Number',
                'Unit',
                'Threshold Limt',
                'Expiry date',
                'Reamrks',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->medicine;
                $export[] =  $data->pack;
                $export[] =  $data->hsn;
                $export[] =  getUnitname($data->unit_id);
                $export[] =  $data->threshold_limit;
                $export[] =  Displaydateformat($data->expiry_date);
                $export[] =  $data->remarks;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('medicine .xlsx')
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

            $allData = $this->medicine->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Medicine Name',
                'Pack',
                'HSN Number',
                'Unit',
                'Threshold Limt',
                'Expiry date',
                'Reamrks',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Medicine Details",
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

            $view = view('ohcmanagement.master.medicine.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Medicine .pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
        }
    }

    public function DownloadSample(Request $request)
    {

        $filedetails =  exportsamplefile('medicine');

        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;

        //return Response::download($filePath, $customFileName);
        return redirect(url($filePath));
    }
}
