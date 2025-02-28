<?php

namespace App\Http\Controllers\IMS\Incident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Models\Master\Work;
use App\Models\Master\Employee;
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
use App\Models\IMS\Incident\AccidentBodyParts;
use App\Models\IMS\Incident\EHSReview;
use App\Models\IMS\Incident\AccidentReport;
use App\Models\IMS\Incident\AccidentInvestigationInjury;
use App\Models\IMS\Incident\AccidentInvestigation;
use App\Models\IMS\Master\Hira;
use App\Models\IMS\Incident\HiraMoc;
use App\Models\IMS\Incident\RiskAnalysis;
use App\Models\IMS\Incident\WhyWhyAnalysis;
use App\Models\IMS\Incident\FishboneAnalysis;

class AccidentReportController extends Controller
{

    private $accident_investigation;
    private $accident_investigation_injury;
    private $accident_body_parts;
    private $accident_report;
    private $unit;
    private $location;
    private $department;
    private $user;
    private $ehs_review;
    private $employee;
    private $uploadlog;
    private $hira;
    private $hiramoc;
    private $riskanalysis;
    private $whyanalysis;
    private $fishboneAnalysis;

    public function __construct()
    {


        $this->accident_investigation_injury = new AccidentInvestigationInjury();
        $this->accident_investigation = new AccidentInvestigation();
        $this->accident_body_parts = new AccidentBodyParts();
        $this->employee = new Employee();
        $this->ehs_review = new EHSReview();
        $this->accident_report = new AccidentReport();
        $this->unit = new Unit();
        $this->location = new Location();
        $this->department = new Department();
        $this->user = new User();
        $this->uploadlog = new UploadLog();
        $this->hira = new Hira();
        $this->hiramoc = new HiraMoc();
        $this->riskanalysis = new RiskAnalysis();
        $this->whyanalysis = new WhyWhyAnalysis();
        $this->fishboneAnalysis = new FishboneAnalysis();
    }


    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {

                try {

                    $data =  $this->accident_report->list();
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

                        ->addColumn('date_and_time', function ($row) {
                            return Displaydatetimeformat($row->date_and_time);
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
                            $btn = '<a href="' . admin_url('accidentReport/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            // if (CheckUserPermission('edit')) {
                            $btn .= '<a href="' . admin_url('accidentReport/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            // }

                            if ($row->accident_status == 1) {
                                $btn .= '<a href="' . admin_url('accidentReport/review/' . encryptId($row->id)) . '" class=" " title="Review"><i class="fa-solid fa-circle-check" style="color:rgb(0, 37, 132);"></i> ';
                            }
                            if ($row->accident_status == 2) {
                                $btn .= '<a href="' . admin_url('accidentReport/investigation/' . encryptId($row->id)) . '" class=" " title="Investigation"><i class="fa fa-search" style="color: #000000;"></i> ';
                            }

                            return $btn;
                        })
                        ->rawColumns(['action', 'date_and_time', 'created_date', 'created_by', 'status'])
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
        $departmentList  = $this->department->select('id', 'department_name')->where('status', '1')->get();
        // $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
        $employeeList  = $this->employee->select('id', 'emp_id')->whereRaw('FIND_IN_SET(' . ROLE_ADMIN . ', user_role)')->where('status', '1')->get();

        $data = array(
            'departmentList' => $departmentList,
            // 'unitList' => $unitList,
            'employeeList' => $employeeList,
        );

        return view('ims.incident.accidentReport.list', $data);
    }
    public function getEmployeeDetails($emp_id)
    {
        $id = decryptId($emp_id);
        $employee = $this->employee->fetchempDetails($id);
        if (!$employee) {
            return response()->json(['error' => 'Employee not found.'], 404);
        }
        return response()->json([
            'employee' => [
                'designation' => $employee->designation ?? '',
                'department_name' => $employee->department_name ?? '',
            ],
        ]);
    }
    public function fetchEmployeeDetails(Request $request, $emp_code)
    {
        $employee = $this->employee->getempDetails($emp_code);
        if (!$employee) {
            return response()->json(['error' => 'Employee not found.'], 404);
        }

        return response()->json([
            'employee' => [
                'designation' => $employee->designation_name ?? '',
                'unit_name' => $employee->unit_name ?? '',
                'department_name' => $employee->department_name ?? '',
            ],
        ]);
    }

    public function Add(Request $request)
    {

        try {
            $employeeList  = $this->employee->select('id', 'emp_id')->where('status', '1')->get();
            $locationList  = $this->location->select('id', 'location_name')->where('status', '1')->get();
            $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();

            $data = array(
                'locationList' => $locationList,
                'employeeList' => $employeeList,
                'unitList' => $unitList,
            );
            return view('ims.incident.accidentReport.add', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    public function Store(Request $request)
    {
        try {

            $rules = [
                'date_and_time' => 'required',
                'unit_id' => 'required',
                'shift' => 'required',
                'location_id' => 'required',
                'designation' => 'required',
                'department_id' => 'required',
                'emp_code' => 'required',
                'address_of_the_injuredperson' => 'required',
            ];
            $messages = [
                'date_and_time.required' => 'Please enter the date and time of the accident.',
                'unit_id.required' => 'Unit is required.',
                'shift.required' => 'Shift is required.',
                'location_id.required' => 'Location is required.',
                'designation.required' => 'Designation is required.',
                'department_id.required' => 'Department is required.',
                'emp_code.required' => 'Employee Code is required.',
                'address_of_the_injuredperson.required' => 'Address of the injured person is required.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {
                $accident_report =  $this->accident_report->store();
                if ($accident_report) {
                    $accidentReport = $this->accident_report->selectOne($accident_report->id);
                    $ehs_head_role = ROLE_EHS_HEAD;

                    $ehs_details = User::select('id', 'role', 'name', 'employee_id', 'email')
                        ->whereRaw('FIND_IN_SET(' . $ehs_head_role . ', role)')
                        ->get();

                    if (!empty($accidentReport)) {
                        $mailsubject = 'New Initial Accident Report';

                        /**
                         * Send Email Notifications
                         */
                        if ($ehs_details->isNotEmpty()) {
                            foreach ($ehs_details as $ehs_detail) {
                                if (!empty($ehs_detail->email)) {
                                    $accidentReportArray = [
                                        'name' => $ehs_detail->name,
                                        'accident_report_no' => $accidentReport->accident_report_no,
                                        'date_and_time' => Displaydatetimeformat($accidentReport->date_and_time),
                                        'emp_code' => $accidentReport->emp_code,
                                        'unit' => $accidentReport->unit_name,
                                        'department' => $accidentReport->department_name,
                                        'location' => $accidentReport->location_name,
                                        'mail_subject' => $mailsubject,
                                    ];

                                    // Queue email
                                    Mail::to($ehs_detail->email)->queue(new TrainingApprovalEmail($accidentReportArray));
                                }
                            }
                        }

                        /**
                         * Send Web Notifications
                         */
                        $ehsids = $ehs_details->pluck('id')->toArray();
                        if (!empty($ehsids)) {
                            $img = admin_url('public/assets/icons/training.png');
                            $notificationData = [
                                'notification_type' => 2,
                                'module_type' => 2,
                                'notification_message' => $mailsubject,
                                'mobile_notification' => json_encode([
                                    'title' => $mailsubject,
                                    'message' => 'A new training schedule has been created by ' . getUsername($accidentReport->created_by),
                                    'icon' => $img,
                                    'module' => 2,
                                ]),
                                'web_link' => 'taccidentReport/view/' . encryptId($accidentReport->id),
                                'assigned_user' => array_to_string($ehsids),
                                'created_by' => Auth::id(),
                            ];

                            // Save notification
                            notificationSave($notificationData);
                        }
                    }
                }
                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {

                dd($ex);
                report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('accidentReport/list'));
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('accidentReport/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $accident_report = $this->accident_report->selectOne($id);

                $data = array(
                    'accident_report' => $accident_report,
                );
            }
            return view('ims.incident.accidentReport.view', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $departmentList  = $this->department->select('id', 'department_name')->where('status', '1')->get();
            $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
            $employeeList  = $this->employee->select('id', 'emp_id')->where('status', '1')->get();
            $locationList  = $this->location->select('id', 'location_name')->where('status', '1')->get();

            $accident_report = $this->accident_report->find($id);
            $data = array(
                'departmentList' => $departmentList,
                'unitList' => $unitList,
                'employeeList' => $employeeList,
                'accident_report' => $accident_report,
                'locationList' => $locationList,

            );

            return view('ims.incident.accidentReport.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rules = [
                'date_and_time' => 'required',
                'unit_id' => 'required',
                'shift' => 'required',
                'location_id' => 'required',
                'designation' => 'required',
                'department_id' => 'required',
                'emp_code' => 'required',
                'address_of_the_injuredperson' => 'required',
            ];
            $messages = [
                'date_and_time.required' => 'Please enter the date and time of the accident.',
                'unit_id.required' => 'Unit is required.',
                'shift.required' => 'Shift is required.',
                'location_id.required' => 'Location is required.',
                'designation.required' => 'Designation is required.',
                'department_id.required' => 'Department is required.',
                'emp_code.required' => 'Employee Code is required.',
                'address_of_the_injuredperson.required' => 'Address of the injured person is required.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->accident_report->updates($id);


            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('accidentReport/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('accidentReport/list'));
        }
    }

    public function fetchEmployeeOrWorkerList($type)
    {
        if ($type == encryptId(1)) { // Employee
            $name = request()->input('search');
            $employees = Employee::select('id', 'emp_id', 'emp_name')
                ->where(function ($query) use ($name) {
                    $query->where('emp_name', 'like', '%' . $name . '%')
                        ->orWhere('emp_id', 'like', '%' . $name . '%');
                })
                ->where('status', 1)
                ->limit(10)
                ->get();

            return response()->json(
                $employees->map(function ($employee) {
                    return [
                        'id' => encryptId($employee->id),
                        'text' => $employee->emp_name . ' - ' . $employee->emp_id,
                    ];
                })
            );
        } elseif ($type == encryptId(2)) { // Worker
            $name = request()->input('search');
            $workers = Work::select('id', 'emp_id', 'emp_name')
                ->where(function ($query) use ($name) {
                    $query->where('emp_name', 'like', '%' . $name . '%')
                        ->orWhere('emp_id', 'like', '%' . $name . '%');
                })
                ->where('status', 1)
                ->limit(10)
                ->get();
            return response()->json(
                $workers->map(function ($worker) {
                    return [
                        'id' => encryptId($worker->id),
                        'text' => $worker->emp_name . ' - ' . $worker->emp_id,
                    ];
                })
            );
        }
    }
    public function fetchPersonDetails($id, $type)
    {
        if ($type == encryptId(1)) { // Employee
            $empId = decryptId($id);
            $employee = Employee::select(
                'masters_employee.id as employee_id',  // Alias to prevent ambiguity
                'masters_employee.emp_id',
                'masters_employee.emp_name',
                'masters_employee.designation',
                'masters_department.department_name'
            )
                ->leftJoin('masters_department', 'masters_employee.department', '=', 'masters_department.id')
                ->where('masters_employee.status', 1)
                ->where('masters_employee.id', $empId)
                ->where('masters_employee.trash', 'NO')  // Ensure this condition is correctly applied
                ->first();

            if (!$employee) {
                return response()->json(['error' => 'Employee not found.'], 404);
            }

            return response()->json([
                'employee' => [
                    'designation' => $employee->designation ?? '',
                    'department_name' => $employee->department_name ?? '',
                ],
            ]);
        } elseif ($type == encryptId(2)) { // Worker
            $workerId = decryptId($id);
            $worker = Work::select(
                'masters_work.id as worker_id',  // Alias to prevent ambiguity
                'masters_work.emp_id',
                'masters_work.emp_name',
                'masters_work.designation',
                'masters_department.department_name'
            )
                ->leftJoin('masters_department', 'masters_work.department', '=', 'masters_department.id')
                ->where('masters_work.status', 1)
                ->where('masters_work.id', $workerId)
                ->where('masters_work.trash', 'NO')  // Ensure this condition is correctly applied
                ->first();

            if (!$worker) {
                return response()->json(['error' => 'Worker not found.'], 404);
            }

            return response()->json([
                'worker' => [
                    'designation' => $worker->designation ?? '',
                    'department_name' => $worker->department_name ?? '',
                ],
            ]);
        }
    }


    public function employeename(Request $request)
    {
        $name = $request->input('search');
        $employees = Employee::select('id', 'emp_id', 'emp_name')
            ->where(function ($query) use ($name) {
                $query->where('emp_name', 'like', '%' . $name . '%')
                    ->orWhere('emp_id', 'like', '%' . $name . '%');
            })
            ->where('status', 1)
            ->limit(10)
            ->get();

        return response()->json(
            $employees->map(function ($employee) {
                return [
                    'id' => encryptId($employee->id),
                    'text' => $employee->emp_name . ' - ' . $employee->emp_id,
                ];
            })
        );
    }

    public function review(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $accident_report = $this->accident_report->selectOne($id);
                $employeeList  = $this->employee->select('id', 'emp_id', 'emp_name')->where('status', '1')->get();

                $data = array(
                    'employeeList' => $employeeList,
                    'accident_report' => $accident_report,
                );
            }
            return view('ims.incident.accidentReport.review', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }
    public function ehsHeadReviewSubmit(Request $request)
    {
        try {
            $rules = [
                'remark' => 'required',
            ];
            $messages = [
                'remark.required' => 'Please provide a remark.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $ehsReview = $this->ehs_review->store();
            $accident_status = 2;
            $accidentId = $ehsReview->accident_report_id;
            $accident = $this->accident_report->updateStatus($accidentId, $accident_status);

            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('accidentReport/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('accidentReport/list'));
        }
    }

    public function investigation(Request $request, $accident_id)
    {            
        try {
            $accidentId = decryptId($accident_id);
            $departmentList  = $this->department->select('id', 'department_name')->where('status', '1')->get();
            $id = decryptId($request->id);

            $accident_body_parts = $this->accident_body_parts->delete_temprow();

            $accident_investigation = $this->accident_investigation->find($id);
            $data = array(
                'accidentId' => $accidentId,
                'accident_body_parts' => $accident_body_parts,
                'departmentList' => $departmentList,

            );

            return view('ims.incident.accidentReport.investigation', $data);
        } catch (Exception $error) {
            dd($error->getMessage());
        }
    }

    public function investigationSubmit(Request $request)
    {
        try {

            // $rules = [
            //     'date_and_time' => 'required',
            //     'unit_id' => 'required',
            //     'shift' => 'required',
            //     'location_id' => 'required',
            //     'designation' => 'required',
            //     'department_id' => 'required',
            //     'emp_code' => 'required',
            //     'address_of_the_injuredperson' => 'required',
            // ];
            // $messages = [
            //     'date_and_time.required' => 'Please enter the date and time of the accident.',
            //     'unit_id.required' => 'Unit is required.',
            //     'shift.required' => 'Shift is required.',
            //     'location_id.required' => 'Location is required.',
            //     'designation.required' => 'Designation is required.',
            //     'department_id.required' => 'Department is required.',
            //     'emp_code.required' => 'Employee Code is required.',
            //     'address_of_the_injuredperson.required' => 'Address of the injured person is required.',
            // ];

            // $validator = Validator::make($request->all(), $rules, $messages);
            // if ($validator->fails()) {
            //     return redirect()->back()->withErrors($validator)->withInput();
            // }
            $incident_id = null;
            $fire_id = null;
            $accident_id = decryptId($request->accident_id);

                 if ($request->root_cause_analysis ==  3) {
                    $accident_status = STATUS_INCIDENT_CLOSED;
                } else {
                    $accident_status = STATUS_RISKANALYSIS_PENDING;
                }

                $accident_investigation =  $this->accident_investigation->store();
                $investigation_injury =  $this->accident_investigation_injury->store($accident_id, $accident_investigation->id);

                $accident = $this->accident_report->updateStatus($accident_id, $accident_status);

                if ($accident_investigation->root_cause_analysis ==  1) {
                    $whyanalysis = $this->whyanalysis->store($accident_id, $incident_id, $fire_id , $accident_investigation->id);
                }
                if ($accident_investigation->root_cause_analysis == 2) {
                    $this->fishboneAnalysis->storeFishbone($accident_id, $incident_id, $fire_id , $accident_investigation->id);
                }
    
                $this->hiramoc->updateAccidentInvestigation($accident_id, $accident_investigation->id);

                Session::flash('success', 'Your data has been created successfully!');
           
            return redirect(admin_url('accidentReport/list'));
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('accidentReport/list'));
        }
    }

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $vendor_name = $request->vendor_name;
            $license_no = $request->license_no;
            $id = $request->id;

            if (empty($id)) {
                $isUnique = !$this->accident_report->uniqueCheck($vendor_name, $license_no);
            } else {
                $id = decryptId($id);
                $isUnique = !$this->accident_report->existUniqueCheck($vendor_name, $license_no, $id);
            }

            return Response::json($isUnique);
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->accident_report->statuschange($id);
            return response()->json(['status' => 'success', 'msg' => 'Your status has changed successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }



    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->accident_report->exportdata();

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
                if ($data->hazard_type == 1) {
                    $export[] = 'P - Physical Hazard';
                } elseif ($data->hazard_type == 2) {
                    $export[] = 'C - Chemical Hazard';
                } elseif ($data->hazard_type == 3) {
                    $export[] = 'B - Behavioral Hazard';
                } elseif ($data->hazard_type == 4) {
                    $export[] = 'O - Other Hazard';
                }
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('AccidentReport.xlsx')
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

            $allData = $this->accident_report->exportdata();

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
                'pagetitle' => "Accident Report Details",
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

            $view = view('ims.incident.accidentReport.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Accident Report.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
        }
    }
    public function addInjury(Request $request)
    {
        try {
            $addInjury = $this->accident_body_parts->addInjury();
            return $addInjury;
        } catch (Exception $ex) {
            dd($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function getbodyEmpdetails(Request $request)
    {
        try {
            $getEmpdetails = $this->accident_body_parts->getEmpdetails();
            return $getEmpdetails;
        } catch (Exception $ex) {
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function existingHira($accident_id, Request $request)
    {
        try {
            $incident_id = '';
            $hiraList = $this->hira->select('id', 'services')->where('status', '1')->get();

            if ($request->ajax()) {
                return view('ims.initial.incident.existinghira', compact('hiraList', 'incident_id','accident_id'))->render();
            }

            return view('ims.initial.incident.existinghira', compact('hiraList', 'incident_id','accident_id'));
        } catch (Exception $error) {
            return response()->json(['error' => $error->getMessage()], 500);
        }
    }
    public function existingMOC($accident_id, Request $request)
    {
        try {
            $incident_id = '';

            $hiraList = $this->hira->select('id', 'services')->where('status', '1')->get();

            if ($request->ajax()) {
                return view('ims.initial.incident.existingMOC', compact('hiraList', 'incident_id','accident_id'))->render();
            }

            return view('ims.initial.incident.existingMOC', compact('hiraList', 'incident_id','accident_id'));
        } catch (Exception $error) {
            return response()->json(['error' => $error->getMessage()], 500);
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
