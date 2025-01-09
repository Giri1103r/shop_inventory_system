<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Facades\File;

use Str;
use PDF;
use Mail;
use Illuminate\Support\Facades\Auth;
use Session;
use Exception;
use DataTables;
use Response;
use App\Jobs\ImportdepartmentJob;


use App\Models\Master\Company;
use App\Models\Master\Work;
use App\Models\Master\Location;
use App\Models\Master\Unit;
use App\Models\Master\Department;
use App\Models\User;
use App\Models\UploadLog;


class WorkController extends Controller
{

    private $company;
    private $user;
    private $department;
    private $location;
    private $unit;
    private $uploadlog;
    private $Work;

    public function __construct()
    {

        $this->company = new Company();
        $this->user = new User();
        $this->unit = new Unit();
        $this->location = new Location();
        $this->department = new Department();
        $this->uploadlog = new UploadLog();
        $this->Work = new Work();
    }


    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->Work->list();
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

                        ->addColumn('action', function ($row) {
                            $btn = '';
                            if (CheckUserPermission('view')) {
                                $btn = '<a href="' . admin_url('work/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            }
                            if (CheckUserPermission('edit')) {
                                $btn .= '<a href="' . admin_url('work/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
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
        $companyList  = $this->company->where('status', '1')->get();

        $data = array(
            'companyList' => $companyList,
        );
        return view('master.work.list', $data);
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $Work = $this->Work->selectOne($id);

                $data = array(
                    'work' => $Work,
                );
            }
            return view('master.work.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $work = $this->Work->find($id);

            // dd($work);
            $companyList  = $this->company->select('id', 'company_name')->where('status', '1')->get();
            $data = array(
                'companyList' => $companyList,
                'work' => $work,
            );

            return view('master.work.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rules = [
                'department' => 'required',
                'company' => 'required',
                'unit' => 'required',
                'emp_name' => 'required',
            ];
            $messages = [
                'department.required' => 'Please Select Department ',
                'company.required' => 'Please Select Company ',
                'unit.required' => 'Please Select Unit',
                'emp_name.required' => 'Please Enter Worker Name',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->Work->updates($id);

            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('work/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('work/list'));
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->Work->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Work status changed'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }


    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->Work->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Worker Id',
                'Worker Name',
                'Phone Number',
                'Company Name',
                'Location Name',
                'Unit Name',
                'Department Name',
                'Exit Date',
                'Worker Type',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];


            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->emp_id;
                $export[] =  $data->emp_name;
                $export[] =  $data->mobile_no;
                $export[] =  $data->company_name;
                $export[] =  $data->location_name;
                $export[] =  $data->unit_name;
                $export[] =  $data->department_name;
                $export[] =  Displaydateformat($data->exit_date);
                $export[] =  $data->wfemptype;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);
                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Worker Master.xlsx')
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

            $allData = $this->Work->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }
            
            $header = [
                __("common.sno"),
                'Worker Id',
                'Worker Name',
                'Phone Number',
                'Company Name',
                'Location Name',
                'Unit Name',
                'Department Name',
                'Exit Date',
                'Worker Type',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];
            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Worker Details",
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

            $view = view('master.work.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Worker Master.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
        }
    }

}
