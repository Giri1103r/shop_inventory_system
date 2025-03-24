<?php

namespace App\Http\Controllers\OhcManagement\Opd;

use App\Http\Controllers\Controller;
use App\Models\OhcManagement\Opd\FirstAid;
use Exception;
use App\Models\Master\Employee;
use App\Models\Master\Unit;
use App\Models\Master\Work;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;

class FirstAidController extends Controller
{
    private $ohc_opd_first_aid;
    private $work;
    private $employee;
    public function __construct()
    {
        $this->ohc_opd_first_aid = new FirstAid();
        $this->employee = new Employee();
        $this->work = new Work();
    }
    public function index(Request $request)
    {

        if (Auth::check()) {

            if ($request->ajax()) {

                try {

                    $data =  $this->ohc_opd_first_aid->list();

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
                        ->editColumn('date_of_incident', function ($row) {
                            return displaydateformat($row->date_of_incident);
                        })
                        ->editColumn('unit_id', function ($row) {
                            return $row->unit_name;
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })

                        ->addColumn('action', function ($row) {
                            $btn = '';
                            // if (CheckUserPermission('view')) {
                            $btn = '<a href="' . admin_url('ohc/first-aid/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            // if (CheckUserPermission('edit')) {
                            $btn .= '<a href="' . admin_url('ohc/first-aid/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            // }

                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status','date_of_incident'])
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

        $data = [];

        return view('ohcmanagement.ohc-opd.first-aid.list', $data);
    }

    public function add()
    {
        try {

            return view('ohcmanagement.ohc-opd.first-aid.add');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/first-aid/list'));
        }
    }

    public function Store(Request $request)
    {
        try {

            $rules = [
                'emp_name' => 'required',
                'emp_id' => 'required',
                'date_of_incident' => 'required',
                'time_of_incident' => 'required',
            ];

            $messages = [
                'emp_name.required' => 'Employee name is required.',
                'emp_id.required' => 'Please select an employee Id.',
                'date_of_incident.required' => 'Date of Incident is required.',
                'time_of_incident.required' => 'Time of Incident is required.',

            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {


                $this->ohc_opd_first_aid->store();


                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                 report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/first-aid/list'));
        } catch (Exception $ex) {

             report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/first-aid/list'));
        }
    }

    public function edit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $opd_first_aid = $this->ohc_opd_first_aid->selectOne($id);
            }
            $data = array(
                'opd_first_aid' => $opd_first_aid,

            );
            return view('ohcmanagement.ohc-opd.first-aid.edit', $data);
        } catch (Exception $ex) {
             report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/first-aid/list'));
        }
    }

    public function update(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $rules = [
                'emp_name' => 'required',
                'emp_id' => 'required',
                'date_of_incident' => 'required',
                'time_of_incident' => 'required',
            ];

            $messages = [
                'emp_name.required' => 'Employee name is required.',
                'emp_id.required' => 'Please select an employee Id.',
                'date_of_incident.required' => 'Date of Incident is required.',
                'time_of_incident.required' => 'Time of Incident is required.',

            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            try {


                $this->ohc_opd_first_aid->updates($id);


                Session::flash('success', 'Your data has been created successfully!');
                return redirect(admin_url('ohc/first-aid/list'));
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/first-aid/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $opd_first_aid = $this->ohc_opd_first_aid->selectOne($id);
            }
            $data = array(
                'opd_first_aid' => $opd_first_aid,

            );
            return view('ohcmanagement.ohc-opd.first-aid.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/first-aid/list'));
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->ohc_opd_first_aid->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Employee Code',
                'Employee Name',
                'Date of Incident',
                'Time of Incident',
                'Treatment Provided',
                'Treatment Start Time',
                'Treatment End  Time',
                'First Aider Name',
                'Follow Up Required',
                'Refered To',
                'Remarks',
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
                $export[] =  $data->date_of_incident;
                $export[] =  $data->time_of_incident;
                $export[] =  $data->treatment_provided;
                $export[] =  $data->treatment_start_time;
                $export[] =  $data->treatment_end_time;
                $export[] =  $data->first_aider_name;
                $export[] =  $data->follow_up_required == 1 ? 'Yes' : 'No';
                $export[] =  $data->referred_to;
                $export[] =  $data->remarks;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload(' First Aid.xlsx')
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

            $allData = $this->ohc_opd_first_aid->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Employee Code',
                'Employee Name',
                'Date of Incident',
                'Time of Incident',
                'Treatment Provided',
                'Treatment Start Time',
                'Treatment End  Time',
                'First Aider Name',
                'Follow Up Required',
                'Refered To',
                'Remarks',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => " First Aid",
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

            $view = view('ohcmanagement.ohc-opd.first-aid.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "First Aid.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
        }
    }
    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->ohc_opd_first_aid->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'First Aid status changed'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }
    // employee name

    public function employeename(Request $request){
        $name = $request->input('search');

        $employee_code = $this->employee->where('emp_id', 'like', '%' . $name . '%')
            ->where('status', 1)
            ->limit(10)
            ->get();

        $work = $this->work->where('emp_id', 'like', '%' . $name . '%')
            ->where('status', 1)
            ->limit(10)
            ->get();


        $mergedResults = $employee_code->merge($work);

        return response()->json(
            $mergedResults->map(function ($employee) {
                return [
                    'id' => $employee->emp_id,
                    'text' => $employee->emp_id . ' - ' . $employee->emp_name,
                ];
            })
        );
    }

    // employee details

    public function employeedetails($emp_id)
    {
        $employee = Employee::select('emp_name')
            ->where('emp_id', $emp_id)
            ->first();

        if (!$employee) {
            $employee = Work::select('emp_name')
                ->where('emp_id', $emp_id)
                ->first();
        }

        if ($employee) {
            return response()->json([
                'employee' => $employee,

            ]);
        } else {
            return response()->json([
                'message' => 'Employee not found'
            ], 404);
        }
    }
}
