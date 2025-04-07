<?php

namespace App\Http\Controllers\Inspection\Ohc;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Department;
use App\Models\Master\Location;
use App\Models\Master\Unit;
use App\Models\Inspection\Master\Shift;
use App\Models\Inspection\Ohc\FirstAiderList;
use App\Models\Inspection\Ohc\FirstAiderListDetails;
use App\Models\Master\Employee;
use App\Models\Master\Work;
use App\Models\OhcManagement\Report\Inventory;
use App\Models\UploadLog;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Inspection\InspectionStaticDocno;
class FirstAiderlistController extends Controller
{

    private $upload_log;
    private $unit;
    private $shift;
    private $department;
    private $user;
    private $first_aider;
    private $first_aider_details;
    private $employee;
    private $document_reference;
    private $inventory;


    private $location;
    public function __construct()
    {

        $this->upload_log = new UploadLog();
        $this->unit = new Unit();
        $this->department = new Department();
        $this->shift = new Shift();
        $this->location = new Location();
        $this->first_aider = new FirstAiderList();
        $this->inventory = new Inventory();
        $this->user = new User();
        $this->employee = new Employee();
        $this->document_reference = new InspectionStaticDocno();
        $this->first_aider_details = new FirstAiderListDetails();
    }
    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->first_aider->list();

                    $datatables = Datatables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active</span>";
                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->id) . "' data-type='1'>Active</span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->id) . "' data-type='0'>In-Active</span>";
                            }
                            return $text;
                        })
                        ->addColumn('created_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('issue_date', function ($row) {
                            return Displaydateformat($row->issue_date);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })

                        ->addColumn('action', function ($row) {
                            return '<a href="' . admin_url('ohc/first-aider/view/' . encryptId($row->id)) . '" class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a>';
                        })
                        ->rawColumns(['action', 'issue_date', 'created_by', 'status', 'issue_date'])
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

        return view('inspection.inspection_ohc.first_aider_list.list');
    }

    public function Add(Request $request)
    {
        try {
            $unit = $this->unit->getunit();
            $employee = $this->employee->getEmployeefulldata();
            $document_no = $this->document_reference->selectUsingName('FirstAiderList');

            $data = array(
                'unit' => $unit,
                'employee' => $employee,
                'document_no' => $document_no,



            );
            return view('inspection.inspection_ohc.first_aider_list.add', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'unit_id' => 'required',
                'department_id' => 'required',
                'review_date' => 'required',

            ];
            $messages = [
                'department_id.required' => 'Please select a Deparment.',
                'unit_id.required' => 'Please select a unit.',
                'review_date.required' => 'Please select the expiry date.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {
                // dd($request->all());
                // Store user medicine requisition
                $first_aider = $this->first_aider->store();
                $first_aider_details = $this->first_aider_details->store($first_aider);


                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/first-aider/list'));
        } catch (Exception $ex) {

            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/first-aider/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $first_aider = $this->first_aider->Selectone($id);
                $first_aider_details = $this->first_aider_details->Selectone($id);

                $data = array(
                    'first_aider' => $first_aider,
                    'first_aider_details' => $first_aider_details,
                );
            }
            return view('inspection.inspection_ohc.first_aider_list.view', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->first_aider->statuschange($id);
            $this->first_aider_details->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Your Status Changed Successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->first_aider->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Document Number',
                'Review date',
                'Issued Date',
                'Last Updated Date',
                'Next Reiview Date',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->doc_no;
                $export[] =  $data->revision_date;
                $export[] =  Displaydateformat($data->issue_date);
                $export[] = Displaydateformat( $data->last_updated_date);
                $export[] = Displaydateformat( $data->next_review_date);
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('First Aider List.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/first-aider/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->first_aider->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Document Number',
                'Review date',
                'Issued Date',
                'Last Updated Date',
                'Next Reiview Date',

                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "First Aider List",
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

            $view = view('inspection.inspection_ohc.first_aider_list.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "First Aider List.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            ($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/first-aider/list'));
        }
    }

    public function employeename(Request $request)
    {
        $unit_id = decryptId($request->input('unit_id'));
        $department_id = decryptId($request->input('department'));
        $employees = Employee::where('unit', $unit_id)
            ->where('department', $department_id)
            ->get();

        if ($employees->isNotEmpty()) {
            return response()->json([
                'employee' => $employees->map(function ($employee) {
                    return [
                        'id' => encryptId($employee->id),
                        'emp_name' => $employee->emp_name,
                    ];
                }),
            ]);
        } else {
            return response()->json([
                'message' => 'Employee not found'
            ], 404);
        }
    }


    public function employeedetails(Request $request)
    {
        $id = $request->input('emp_name');

        $employee = Employee::find($id);

        if ($employee) {
            return response()->json([
                'employee' => [
                    'designation' => $employee->designation,
                    'mobile_no' => $employee->mobile_no
                ]
            ]);
        } else {
            return response()->json(['message' => 'Employee not found'], 404);
        }
    }
}
