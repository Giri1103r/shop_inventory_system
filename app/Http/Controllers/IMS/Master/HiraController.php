<?php

namespace App\Http\Controllers\IMS\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Models\Master\Unit;
use App\Models\Master\Location;
use App\Models\Master\Department;
use App\Models\User;
use App\Models\UploadLog;
use App\Jobs\ImportvendorJob;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\IMS\Master\Hira;

class HiraController extends Controller
{

    private $hira;
    private $unit;
    private $location;
    private $department;
    private $user;
    private $uploadlog;


    public function __construct()
    {

        $this->hira = new Hira();
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

                    $data =  $this->hira->list();

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

                        ->addColumn('hazard_type', function ($row) {
                            $hazardTypes = [
                                1 => 'P - Physical Hazard',
                                2 => 'C - Chemical Hazard',
                                3 => 'B - Behavioral Hazard',
                                4 => 'O - Other Hazard',
                            ];
                        
                            return $hazardTypes[$row->hazard_type];
                        })
                        ->addColumn('created_at', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            // if (CheckUserPermission('view')) {
                                $btn = '<a href="' . admin_url('incident/hira-master/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            // if (CheckUserPermission('edit')) {
                                $btn .= '<a href="' . admin_url('incident/hira-master/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            // }

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

        return view('ims.master.hira.list', $data);
    }

    public function Add(Request $request)
    {

        try {

            $data = array();
            return view('ims.master.hira.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {
        try {

            $rules = [

                'services' => 'required',
                'narration' => 'required',
                'hazard_description' => 'required',
                'hazard_type' => 'required',
                'severity' => 'required',
                'risk_consequence' => 'required',
                'likelihood' => 'required',
                'risk_levels' => 'required',
                'current_controls' => 'required',
                'type_controls' => 'required',
                'legal_req' => 'required',
                'risk_rating' => 'required',
            ];
            $messages = [

                'services.required' => 'Please enter Source, Situation, Act,Activity, Product,Services',
                'narration.required' => 'Please enter Narration',
                'hazard_description.required' => 'Please enter Hazard Description',
                'hazard_type.required' => 'Please enter Type of Hazard',
                'severity.required' => 'Please enter Severity',
                'risk_consequence.required' => 'Please enter Risk/Consequence',
                'likelihood.required' => 'Please enter Likelihood',
                'risk_levels.required' => 'Please enter Risk Levels',
                'current_controls.required' => 'Please enter Current Controls',
                'type_controls.required' => 'Please enter Type of Controls',
                'legal_req.required' => 'Please enter Legal Requirements',
                'risk_rating.required' => 'Please enter Risk Ratings',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {


                 $this->hira->store();


                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {

                dd($ex);
                report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('incident/hira-master/list'));
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/hira-master/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $hira = $this->hira->selectOne($id);

                $data = array(
                    'hira' => $hira,
                );
            }
            return view('ims.master.hira.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);


            $hira = $this->hira->find($id);
            $data = array(
                'hira' => $hira,
            );


            return view('ims.master.hira.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rules = [

                'services' => 'required',
                'narration' => 'required',
                'hazard_description' => 'required',
                'hazard_type' => 'required',
                'severity' => 'required',
                'risk_consequence' => 'required',
                'likelihood' => 'required',
                'risk_levels' => 'required',
                'current_controls' => 'required',
                'type_controls' => 'required',
                'legal_req' => 'required',
                'risk_rating' => 'required',
            ];
            $messages = [

                'services.required' => 'Please enter Source, Situation, Act,Activity, Product,Services',
                'narration.required' => 'Please enter Narration',
                'hazard_description.required' => 'Please enter Hazard Description',
                'hazard_type.required' => 'Please enter Type of Hazard',
                'severity.required' => 'Please enter Severity',
                'risk_consequence.required' => 'Please enter Risk/Consequence',
                'likelihood.required' => 'Please enter Likelihood',
                'risk_levels.required' => 'Please enter Risk Levels',
                'current_controls.required' => 'Please enter Current Controls',
                'type_controls.required' => 'Please enter Type of Controls',
                'legal_req.required' => 'Please enter Legal Requirements',
                'risk_rating.required' => 'Please enter Risk Ratings',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                dd($validator->errors());
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->hira->updates($id);

            // $vendor = $this->vendor->find($id);
            // $this->user->vendorUpdate($vendor->login_id);

            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('incident/hira-master/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/hira-master/list'));
        }
    }



    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $vendor_name = $request->vendor_name;
            $license_no = $request->license_no;
            $id = $request->id;

            if (empty($id)) {
                $isUnique = !$this->hira->uniqueCheck($vendor_name,$license_no);
            } else {
                $id = decryptId($id);
                $isUnique = !$this->hira->existUniqueCheck($vendor_name,$license_no, $id);
            }

            return Response::json($isUnique);
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->hira->statuschange($id);
            return response()->json(['status' => 'success', 'msg' => 'Your status has changed successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }



    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->hira->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Sr. No',
                'Source, Situation, Act,Activity, Product,Services',
                'Type of Hazard',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->sr_no;
                $export[] =  $data->services;
                if($data->hazard_type == 1){
                    $export[] = 'P - Physical Hazard';
                }elseif($data->hazard_type == 2){
                    $export[] = 'C - Chemical Hazard';
                }elseif($data->hazard_type == 3){
                    $export[] = 'B - Behavioral Hazard';
                }elseif($data->hazard_type == 4){
                    $export[] = 'O - Other Hazard';
                }
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('HIRA.xlsx')
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

            $allData = $this->hira->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Sr. No',
                'Source, Situation, Act,Activity, Product,Services',
                'Type of Hazard',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "HIRA Details",
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

            $view = view('ims.master.hira.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "HIRA.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
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
