<?php

namespace App\Http\Controllers\IMS\Incident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Mail;
use App\Models\Master\Unit;
use App\Models\Master\Work;
use App\Models\Master\Location;
use App\Models\Master\Department;
use App\Models\Master\Employee;
use App\Models\User;
use App\Models\UploadLog;
use App\Jobs\ImportvendorJob;
use App\Mail\IMS\InvestigationEmail;
use App\Mail\IMS\RcpaEmail;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\IMS\Incident\InitialIncident;
use App\Models\IMS\Incident\IntialIncidentEvidencefile;
use App\Models\IMS\Master\IncidentType;
use App\Models\IMS\Master\Hira;
use App\Models\IMS\Incident\EHSReview;
use App\Models\IMS\Incident\HiraMoc;
use App\Models\IMS\Incident\IncidentInvestigation;
use App\Models\IMS\Incident\RiskAnalysis;
use App\Models\IMS\Incident\WhyWhyAnalysis;
use App\Models\IMS\Incident\FishboneAnalysis;
use App\Models\IMS\Incident\Statuslog;
use App\Models\IMS\Incident\Incidentstatus;
use App\Mail\IncidentEmail;
use App\Models\IMS\Incident\InjuryDetails;
use App\Models\IMS\Incident\IncidentBodyParts;
use App\Models\IMS\Incident\Rcpa;
use App\Models\Master\Company;

class InitialIncidentController extends Controller
{

    private $initialincident;
    private $initialincidentevidence;
    private $unit;
    private $location;
    private $inctype;
    private $employee;
    private $user;
    private $uploadlog;
    private $ehs_review;
    private $department;
    private $hira;
    private $hiramoc;
    private $incidentinvestigation;
    private $riskanalysis;
    private $whyanalysis;
    private $fishboneAnalysis;
    private $Statuslog;
    private $status;
    private $injury_details;
    private $incident_body_parts;
    private $rcpa;
    private $work;
    private $company;


    public function __construct()
    {

        $this->incident_body_parts = new IncidentBodyParts();
        $this->initialincident = new InitialIncident();
        $this->initialincidentevidence = new IntialIncidentEvidencefile();
        $this->inctype = new IncidentType();
        $this->unit = new Unit();
        $this->location = new Location();
        $this->employee = new Employee();
        $this->department = new Department();
        $this->user = new User();
        $this->uploadlog = new UploadLog();
        $this->ehs_review = new EHSReview();
        $this->hira = new Hira();
        $this->hiramoc = new HiraMoc();
        $this->incidentinvestigation = new IncidentInvestigation();
        $this->riskanalysis = new RiskAnalysis();
        $this->whyanalysis = new WhyWhyAnalysis();
        $this->fishboneAnalysis = new FishboneAnalysis();
        $this->Statuslog = new Statuslog();
        $this->status = new Incidentstatus();
        $this->injury_details = new InjuryDetails();
        $this->rcpa = new Rcpa();
        $this->work = new Work();
        $this->company = new Company();
    }


    public function index(Request $request)
    {

        if (Auth::check()) {
            if ($request->ajax()) {

                try {

                    $data =  $this->initialincident->list();


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


                        ->editColumn('status_batch', function ($row) {
                            return "<span class='" . $row->bg_color . "' >" . $row->status_name . "</span>";
                        })
                        ->addColumn('unit_name', function ($row) {
                            return getUnitname($row->unit_id);
                        })
                        //  ->addColumn('company_id', function ($row) {
                        //     return getCompanyname($row->company_id);
                        // })
                        //  ->addColumn('location_id', function ($row) {
                        //     return getLocationname($row->location_id);
                        // })
                        ->addColumn('created_at', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('iir_type', function ($row) {
                            return getIIRTypename($row->iir_type);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            // if (CheckUserPermission('view')) {
                            $btn = '<a href="' . admin_url('incident/initial-incident/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            // if (CheckUserPermission('edit')) {

                            if ($row->incident_status == 1 && $row->created_by == Auth::id()) {
                                $btn .= '<a href="' . admin_url('incident/initial-incident/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            }
                            // }

                            if ((CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_EHS_HEAD)) && ($row->incident_status == 1)) {
                                $btn .= '<a href="' . admin_url('incident/initial-incident/review/' . encryptId($row->id)) . '" class=" " title="Review"><i class="fa-solid fa-circle-check" style="color:rgb(0, 37, 132);"></i> ';
                            }


                            $btn .= '<a href="' . admin_url('incident/initial-incident/generalpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                             </a>';

                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', 'status_batch', 'iir_type'])
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
        $companyList = $this->company->getcompany();
        $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
        $status = Incidentstatus::select('id', 'status_name')->where('status', '1')->get();
        $incTypeList  = $this->inctype->getIncidentType();
        $type = ($request->type);
        $condition = ($request->condition);
        $major = $request->major_accident;
        $minor = $request->minor_accident;
        $near_miss = $request->near_miss;
        $companyId = $request->company_id;
        $fromdate = $request->fromDate;
        $toDate = $request->toDate;
        $near_miss = $request->near_miss;
        $unsafe_act = $request->unsafe_act;
        $unsafe_condition = $request->unsafe_condition;
        $fire_incidence = $request->fire_incidence;
        $data = array(
            'unitList' => $unitList,
            'companyList' => $companyList,
            'status' => $status,
            'dashboard_search' => $request,
            'type' => $type,
            'condition' => $condition,
            'fromdate' => $fromdate,
            'fromdate' => $fromdate,
            'minor' => $minor,
            'major' => $major,
            'near_miss' => $near_miss,
            'toDate' => $toDate,
            'unsafe_act' => $unsafe_act,
            'unsafe_condition' => $unsafe_condition,
            'fire_incidence' => $fire_incidence,
            'companyId' => $companyId,
            'incTypeList' => $incTypeList,
        );

        return view('ims.initial.incident.list', $data);
    }

    public function redirectindex(Request $request)
    {

        if (Auth::check()) {
            if ($request->ajax()) {

                try {

                    $data =  $this->initialincident->list();


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


                        ->editColumn('status_batch', function ($row) {
                            return "<span class='" . $row->bg_color . "' >" . $row->status_name . "</span>";
                        })
                        ->addColumn('unit_name', function ($row) {
                            return getUnitname($row->unit_id);
                        })
                        //  ->addColumn('company_id', function ($row) {
                        //     return getCompanyname($row->company_id);
                        // })
                        //  ->addColumn('location_id', function ($row) {
                        //     return getLocationname($row->location_id);
                        // })
                        ->addColumn('created_at', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })

                        ->addColumn('action', function ($row) {
                            $btn = '';
                            // if (CheckUserPermission('view')) {
                            $btn = '<a href="' . admin_url('incident/initial-incident/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            // if (CheckUserPermission('edit')) {

                            if ($row->incident_status == 1 && $row->created_by == Auth::id()) {
                                $btn .= '<a href="' . admin_url('incident/initial-incident/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            }
                            // }

                            if ((CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_EHS_HEAD)) && ($row->incident_status == 1)) {
                                $btn .= '<a href="' . admin_url('incident/initial-incident/review/' . encryptId($row->id)) . '" class=" " title="Review"><i class="fa-solid fa-circle-check" style="color:rgb(0, 37, 132);"></i> ';
                            }


                            $btn .= '<a href="' . admin_url('incident/initial-incident/generalpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                             </a>';

                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', 'status_batch'])
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
        $companyList = $this->company->getcompany();
        $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
        $status = Incidentstatus::select('id', 'status_name')->where('status', '1')->get();
        $incTypeList  = $this->inctype->getIncidentType();
        $type = ($request->type);
        $condition = ($request->condition);
        $major = $request->major_accident;
        $minor = $request->minor_accident;
        $near_miss = $request->near_miss;
        $companyId = $request->company_id;
        $fromdate = $request->fromDate;
        $toDate = $request->toDate;
        $near_miss = $request->near_miss;
        $unsafe_act = $request->unsafe_act;
        $unsafe_condition = $request->unsafe_condition;
        $fire_incidence = $request->fire_incidence;
        $data = array(
            'unitList' => $unitList,
            'companyList' => $companyList,
            'status' => $status,
            'dashboard_search' => $request,
            'type' => $type,
            'condition' => $condition,
            'fromdate' => $fromdate,
            'fromdate' => $fromdate,
            'minor' => $minor,
            'major' => $major,
            'near_miss' => $near_miss,
            'toDate' => $toDate,
            'unsafe_act' => $unsafe_act,
            'unsafe_condition' => $unsafe_condition,
            'fire_incidence' => $fire_incidence,
            'companyId' => $companyId,
            'incTypeList' => $incTypeList,
        );

        return view('ims.initial.incident.list', $data);
    }


    public function investigationList(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {

                try {

                    $data =  $this->initialincident->investigationlist();


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


                        ->editColumn('status_batch', function ($row) {
                            return "<span class='" . $row->bg_color . "' >" . $row->status_name . "</span>";
                        })
                        ->addColumn('unit_name', function ($row) {
                            return getUnitname($row->unit_id);
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
                            $btn = '<a href="' . admin_url('incident/initial-incident/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            if (!empty($row->investigation_reported_by) && $row->incident_status == 2) {
                                $assignedUsers =  $row->investigation_reported_by;
                                $loggedInUserId = Auth::id();
                                $assignedLoginIds = Employee::where('id', $assignedUsers)->pluck('login_id')->toArray();
                                if (($row->investigation_reported_by == Auth::id()) || (CheckUserRole(ROLE_SUPERADMIN))) {
                                    $btn .= '<a href="' . admin_url('incident/initial-incident/investigation/' . encryptId($row->id)) . '" class=" " title="Investigation">
                                                <i class="fa fa-search" style="color: #000000;"></i>
                                             </a>';
                                }
                            }

                            $btn .= '<a href="' . admin_url('incident/initial-incident/generalpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                             </a>';

                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', 'status_batch'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);
                    return $datatables;
                } catch (Exception $ex) {

                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }
        $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
        $status = Incidentstatus::select('id', 'status_name')->where('status', '1')->get();
        $data = array(
            'unitList' => $unitList,
            'status' => $status,
        );

        return view('ims.initial.incident.investigationlist', $data);
    }

    public function calist(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {

                try {

                    $data =  $this->rcpa->list();


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


                        ->editColumn('status_batch', function ($row) {
                            return "<span class='" . $row->bg_color . "' >" . $row->status_name . "</span>";
                        })
                        ->addColumn('unit_name', function ($row) {
                            return getUnitname($row->unit_id);
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
                            $btn = '<a href="' . admin_url('incident/initial-incident/caview/' . encryptId($row->id) . '/' . encryptId($row->incident_id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }

                            if ((CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_EHS_HEAD)) && ($row->incident_status == 7)) {
                                $btn .= '<a href="' . admin_url('incident/initial-incident/ehsApproval/' . encryptId($row->id) . '/' . encryptId($row->incident_id)) . '" class=" " title="Review"><i class="fa-solid fa-circle-check" style="color:rgb(0, 37, 132);"></i> ';
                            }
                            if ($row->incident_status == 6 && (CheckUserRole(ROLE_SUPERADMIN) || $row->responsibility == Auth::id())) {
                                $btn .= '<a href="' . admin_url('incident/initial-incident/caSubmission/' . encryptId($row->id) . '/' . encryptId($row->incident_id)) . '"
                                            class="edit-icon"
                                            title="' . __('Corrective Action') . '">';
                                $btn .= '<img src="' . public_image('common/ca.png') . '"
                                            alt="' . __('common.edit') . '"
                                            style="width: 20px;">';
                                $btn .= '</a>';
                            }

                            $btn .= '<a href="' . admin_url('incident/initial-incident/cageneralpdf/' . encryptId($row->id) . '/' . encryptId($row->incident_id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                             </a>';

                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', 'status_batch'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);
                    return $datatables;
                } catch (Exception $ex) {
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }
        $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
        $status = Incidentstatus::select('id', 'status_name')->where('status', '1')->get();
        $data = array(
            'unitList' => $unitList,
            'status' => $status,
            'dashboard_search' => $request,
        );

        return view('ims.initial.incident.calist', $data);
    }

    public function Add(Request $request)
    {
        try {
            $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
            $locationList  = $this->location->select('id', 'location_name')->where('status', '1')->get();
            $incTypeList  = $this->inctype->select('id', 'incident_type_name')->where('status', '1')->get();

            $body_parts = $this->incident_body_parts->delete_webtemprow();
            $companyList = $this->company->getcompany();
            $randomID = getsequence('IncidentRandomID');
            $data = array(
                'unitList' => $unitList,
                'companyList' => $companyList,
                'locationList' => $locationList,
                'incTypeList' => $incTypeList,
                'randomID' => $randomID,
                'body_parts' => $body_parts,
            );
            return view('ims.initial.incident.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/initial-incident/list/all/type'));
        }
    }


    public function employeename(Request $request)
    {
        $name = $request->input('search');

        $employees = Employee::where(function ($query) use ($name) {
            $query->where('emp_name', 'like', '%' . $name . '%')
                ->orWhere('emp_id', 'like', '%' . $name . '%');
        })
            ->where('status', 1)
            ->limit(10)
            ->get()
            ->map(function ($employee) {
                return [
                    'id' =>  $employee->emp_id,
                    'text' => $employee->emp_name . ' - ' . $employee->emp_id,
                ];
            });

        $workers = Work::where(function ($query) use ($name) {
            $query->where('emp_name', 'like', '%' . $name . '%')
                ->orWhere('emp_id', 'like', '%' . $name . '%');
        })
            ->where('status', 1)
            ->limit(10)
            ->get()
            ->map(function ($worker) {
                return [
                    'id' =>  $worker->emp_id,
                    'text' => $worker->emp_name . ' - ' . $worker->emp_id,
                ];
            });

        return response()->json(
            collect($employees)->merge(collect($workers))
        );
    }


    public function employeeid(Request $request)
    {
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
                    'text' => $employee->emp_id,
                ];
            })
        );
    }

    public function getEmployee(Request $request)
    {
        $name = $request->input('search');

        $employees  = Employee::select('id', 'emp_id', 'emp_name')->where('status', '1')->get();
        return response()->json(
            $employees->map(function ($employee) {
                return [
                    'id' => encryptId($employee->id),
                    'text' => $employee->emp_name . ' - ' . $employee->emp_id,
                ];
            })
        );
    }


    public function getWorkers(Request $request)
    {

        $workers = Work::select('id', 'emp_id', 'emp_name')->where('status', '1')->get();

        return response()->json(
            $workers->map(function ($employee) {
                return [
                    'id' => encryptId($employee->id),
                    'text' => $employee->emp_name . ' - ' . $employee->emp_id,
                ];
            })
        );
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

    public function fetchEmployeeDetails($emp_id)
    {

        $employee = Employee::select('emp_name', 'emp_id', 'email', 'department', 'designation')
            ->where('emp_id', $emp_id)
            ->first();

        if (!$employee) {
            $employee = Work::select('emp_name', 'emp_id', 'department', 'designation')
                ->where('emp_id', $emp_id)
                ->first();
        }
        if ($employee) {

            return response()->json([
                'departments' => $this->department->select('id', 'department_name')->where('status', '1')->get(),
                'employee' => $employee,
            ]);
        } else {
            return response()->json([
                'message' => 'Employee not found'
            ], 404);
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

    public function getbodyEmpdetails(Request $request)
    {
        try {
            $getEmpdetails = $this->incident_body_parts->getEmpdetails();
            return $getEmpdetails;
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function apigetbodyEmpdetails(Request $request)
    {
        try {
            $getEmpdetails = $this->incident_body_parts->apigetEmpdetails();
            return $getEmpdetails;
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function addInjury(Request $request)
    {
        try {
            $addInjury = $this->incident_body_parts->addInjury();
            return $addInjury;
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }
    public function addInjury_api(Request $request)
    {
        try {
            $addInjury = $this->incident_body_parts->addInjuryApi();
            Session::flash('success', 'Your data has been created successfully!');
            
            return $addInjury;
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }


    public function Store(Request $request)
    {
        try {

            // $rules = [

            //     'incident_date_time' => 'required',
            //     'unit_id' => 'required',
            //     'shift' => 'required',
            //     // 'locationi_d' => 'required',
            //     'exact_location' => 'required',
            //     'iir_type' => 'required',
            //     'reported_name' => 'required',
            //     'designation' => 'required',
            //     'department' => 'required',
            //     'employee_code' => 'required',
            //     'time_of_reporting' => 'required',
            //     'reporting_media' => 'required',
            //     'brief_description' => 'required',
            //     'immediate_action_taken' => 'required',
            // ];
            // $messages = [

            //     'incident_date_time.required' => 'Please enter Date and Time',
            //     'unit_id.required' => 'Please enter Unit',
            //     'shift.required' => 'Please enter Shift',
            //     // 'location_id.required' => 'Please enter Location',
            //     'exact_location.required' => 'Please enter Exact Location',
            //     'iir_type.required' => 'Please enter IIR Type',
            //     'reported_name.required' => 'Please enter Name',
            //     'designation.required' => 'Please enter Designation',
            //     'department.required' => 'Please enter Department',
            //     'employee_code.required' => 'Please enter Employee Code',
            //     'time_of_reporting.required' => 'Please enter Time of reporting',
            //     'reporting_media.required' => 'Please enter Reporting Media',
            //     'brief_description.required' => 'Please enter Brief Description',
            //     'immediate_action_taken.required' => 'Please enter Immediate Action Taken',

            // ];
            // $validator = Validator::make($request->all(), $rules, $messages);
            // if ($validator->fails()) {
            //     dd($validator);
            //     return redirect()->back()->withErrors($validator)->withInput();
            // }

            try {


                $initialincident =   $this->initialincident->store();
                $this->initialincidentevidence->store($initialincident, 1);
                if ($initialincident->anyone_injured == 1) {
                    $this->injury_details->store($initialincident->id, $initialincident->random_id);
                }
                // $investigation_injury =  $this->incident_body_parts->store($initialincident->random_id, $initialincident->id);
                $incident_status = STATUS_INCIDENT_REPORT;
                $user_role = ROLE_EHS_HEAD;
                $mailsubject = 'Incident has been submitted';
                $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->pluck('id')->toArray();
                $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();



                if (count($users) > 0) {
                    foreach ($users as $user) {

                        $email_id = $user->email;

                        if ($email_id != '' || $email_id != null) {
                            $incidentDetails =  $this->initialincident->selectOne($initialincident->id);
                            $incidentarray  = $incidentDetails->toArray();

                            $incidentarray['name'] = $user->name;
                            $incidentarray['email_id'] =  $email_id;
                            $incidentarray['mail_subject'] = $mailsubject;

                            Mail::to($incidentarray['email_id'])->queue(new IncidentEmail($incidentarray));
                        }
                    }
                }

                $notificationData = array(
                    'notification_type' => 5,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Incident' . $initialincident->sr_no . ' submitted by ' . getUsername($initialincident->created_by),
                        'icon' =>  admin_url('public/assets/icons/incident.png'),
                        'id' => $initialincident->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('incident/initial-incident/review/' . encryptId($initialincident->id)),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);
                $insert_array = array(
                    'ims_type' => 1,
                    'ims_id' => $initialincident->id,
                    'from_status' => 0,
                    'to_status' => $incident_status,
                    'is_reject' => null,
                    'remarks' => null,
                    'approved_by' => Auth::id(),
                );

                $this->Statuslog->create($insert_array);

                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {

                report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('incident/initial-incident/list/all/type'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/initial-incident/list/all/type'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $incident_report = $this->initialincident->selectOne($id);
                $rcpa = $this->rcpa->getRCPA($id);
                $getEHSVerify = $this->initialincident->getEHSVerifyincident($id);
                $getEHSReview = $this->initialincident->getEHSReviewincident($id);
                $getInvestigation = $this->initialincident->getInvestigation($id);
                $getwhywhy = $this->initialincident->getwhywhy($id);
                $getfishbone = $this->initialincident->getfishbone($id);
                $fishboneData = json_decode($getfishbone->first()->fishbone, true);
                $getrisklevel = $this->initialincident->getrisklevel($id);
                $getEHSApprovalincident = $this->initialincident->getEHSApprovalincident($id);
                $initialincidentevidence = $this->initialincidentevidence->selectOne($id);
                $injury_details = $this->injury_details->getBodypartsInjuryPerson($id);
                $mediaOptions = [
                    1 => 'Phone',
                    2 => 'Walkie Talkie',
                    3 => 'Extension',
                    4 => 'Others',
                ];

                $selectedMedia = isset($incident_report->reporting_media)
                    ? explode(',', $incident_report->reporting_media)
                    : [];

                $displayMedia = array_map(function ($media) use ($mediaOptions) {
                    return $mediaOptions[$media] ?? $media;
                }, $selectedMedia);

                $status_log = $this->Statuslog->selectOne($id, 1);
                $data = array(
                    'incident_report' => $incident_report,
                    'displayMedia' => $displayMedia,
                    'initialincidentevidence' => $initialincidentevidence,
                    'getEHSVerify' => $getEHSVerify,
                    'getInvestigation' => $getInvestigation,
                    'getEHSReview' => $getEHSReview,
                    'getwhywhy' => $getwhywhy,
                    'fishboneData' => $fishboneData,
                    'getrisklevel' => $getrisklevel,
                    'getEHSApprovalincident' => $getEHSApprovalincident,
                    'status_log' => $status_log,
                    'rcpa' => $rcpa,
                    'injury_details' => $injury_details,
                );
            }
            return view('ims.initial.incident.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/initial-incident/list/all/type'));
        }
    }

    public function caview(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $incident_id = decryptId($request->incident_id);
            if (Auth::check()) {
                $incident_report = $this->initialincident->selectOne($incident_id);
                $rcpa = $this->rcpa->selectOne($id);
                $getEHSVerify = $this->initialincident->getEHSVerifyincident($incident_id);
                $getEHSReview = $this->initialincident->getEHSReviewincident($incident_id);
                $getInvestigation = $this->initialincident->getInvestigation($incident_id);
                $getwhywhy = $this->initialincident->getwhywhy($incident_id);
                $getfishbone = $this->initialincident->getfishbone($incident_id);
                $fishboneData = json_decode($getfishbone->first()->fishbone, true);
                $getrisklevel = $this->initialincident->getrisklevel($incident_id);
                $initialincidentevidence = $this->initialincidentevidence->selectOne($incident_id);
                $getEHSApprovalincident = $this->initialincident->getEHSApprovalincident($incident_id);

                $mediaOptions = [
                    1 => 'Phone',
                    2 => 'Walkie Talkie',
                    3 => 'Extension',
                    4 => 'Others',
                ];

                $selectedMedia = isset($incident_report->reporting_media)
                    ? explode(',', $incident_report->reporting_media)
                    : [];

                $displayMedia = array_map(function ($media) use ($mediaOptions) {
                    return $mediaOptions[$media] ?? $media;
                }, $selectedMedia);
                $injury_details = $this->injury_details->getBodypartsInjuryPerson($id);
                $status_log = $this->Statuslog->selectCAPA($incident_id, $id, 1);
                $capaEvidence = $this->initialincidentevidence->SelectcapaEvidence($id, $incident_id);
                $data = array(
                    'incident_report' => $incident_report,
                    'displayMedia' => $displayMedia,
                    'initialincidentevidence' => $initialincidentevidence,
                    'getEHSVerify' => $getEHSVerify,
                    'getInvestigation' => $getInvestigation,
                    'getEHSReview' => $getEHSReview,
                    'getwhywhy' => $getwhywhy,
                    'fishboneData' => $fishboneData,
                    'getrisklevel' => $getrisklevel,
                    'rcpa' => $rcpa,
                    'injury_details' => $injury_details,
                    'getEHSApprovalincident' => $getEHSApprovalincident,
                    'status_log' => $status_log,
                    'capaEvidence' => $capaEvidence,
                );
            }
            return view('ims.initial.incident.caView', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/initial-incident/list/all/type'));
        }
    }


    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);


            $initialincident = $this->initialincident->selectOne($id);
            $initialincidentevidence = $this->initialincidentevidence->selectOne($id);
            $injury_details = $this->injury_details->find_foreignkey($id);
            $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();
            $locationList  = $this->location->select('id', 'location_name')->where('status', '1')->get();
            $incTypeList  = $this->inctype->select('id', 'incident_type_name')->where('status', '1')->get();
            $companyList = $this->company->getcompany();
            $body_parts = $this->incident_body_parts->delete_webtemprow();
            $randomID = getsequence('IncidentRandomID');

            $data = array(
                'unitList' => $unitList,
                'locationList' => $locationList,
                'incTypeList' => $incTypeList,
                'companyList' => $companyList,
                'initialincident' => $initialincident,
                'initialincidentevidence' => $initialincidentevidence,
                'injury_details' => $injury_details,
                'randomID' => $randomID,
                'body_parts' => $body_parts,
            );


            return view('ims.initial.incident.edit', $data);
        } catch (Exception $error) {
            report($error);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/initial-incident/list/all/type'));
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rules = [

                'incident_date_time' => 'required',
                'unit_id' => 'required',
                'shift' => 'required',
                'location_id' => 'required',
                'exact_location' => 'required',
                'iir_type' => 'required',
                'reported_name' => 'required',
                'designation' => 'required',
                'department' => 'required',
                'employee_code' => 'required',
                'time_of_reporting' => 'required',
                'reporting_media' => 'required',
                'brief_description' => 'required',
            ];
            $messages = [

                'incident_date_time.required' => 'Please enter Date and Time',
                'unit_id.required' => 'Please enter Unit',
                'shift.required' => 'Please enter Shift',
                'location_id.required' => 'Please enter Location',
                'exact_location.required' => 'Please enter Exact Location',
                'iir_type.required' => 'Please enter IIR Type',
                'reported_name.required' => 'Please enter Name',
                'designation.required' => 'Please enter Designation',
                'department.required' => 'Please enter Department',
                'employee_code.required' => 'Please enter Employee Code',
                'time_of_reporting.required' => 'Please enter Time of reporting',
                'reporting_media.required' => 'Please enter Reporting Media',
                'brief_description.required' => 'Please enter Brief Description',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $initialincident =   $this->initialincident->updates($id);
            $this->initialincidentevidence->updates($id);
            $initialincident = $this->initialincident->find($id);
            $this->injury_details->store($id, $initialincident->random_id);
            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('incident/initial-incident/list/all/type'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/initial-incident/list/all/type'));
        }
    }

    public function deleteEvidence($evidenceid)
    {
        $id = $evidenceid;
        $this->initialincidentevidence->deleterecord($evidenceid);
        return response()->json(['success' => true, 'message' => 'Evidence deleted successfully.']);
    }

    public function injuryDelete($incidentId, $injuryId)
    {
        $incidentId = $incidentId;
        $injuryId = $injuryId;
        $this->injury_details->deleterecord($incidentId, $injuryId);
        return response()->json(['success' => true, 'message' => 'Injury deleted successfully.']);
    }


    public function review(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $incident_report = $this->initialincident->selectOne($id);
                $rcpa = $this->rcpa->getRCPA($id);
                $getEHSVerify = $this->initialincident->getEHSVerifyincident($id);
                $getEHSReview = $this->initialincident->getEHSReviewincident($id);
                $getInvestigation = $this->initialincident->getInvestigation($id);
                $getwhywhy = $this->initialincident->getwhywhy($id);
                $getfishbone = $this->initialincident->getfishbone($id);
                $fishboneData = json_decode($getfishbone->first()->fishbone, true);
                $getrisklevel = $this->initialincident->getrisklevel($id);
                $initialincidentevidence = $this->initialincidentevidence->selectOne($id);
                $injury_details = $this->injury_details->getBodypartsInjuryPerson($id);


                $mediaOptions = [
                    1 => 'Phone',
                    2 => 'Walkie Talkie',
                    3 => 'Extension',
                    4 => 'Others',
                ];

                $selectedMedia = isset($incident_report->reporting_media)
                    ? explode(',', $incident_report->reporting_media)
                    : [];

                $displayMedia = array_map(function ($media) use ($mediaOptions) {
                    return $mediaOptions[$media] ?? $media;
                }, $selectedMedia);

                $data = array(
                    'incident_report' => $incident_report,
                    'displayMedia' => $displayMedia,
                    'initialincidentevidence' => $initialincidentevidence,
                    'getEHSVerify' => $getEHSVerify,
                    'getInvestigation' => $getInvestigation,
                    'getEHSReview' => $getEHSReview,
                    'getwhywhy' => $getwhywhy,
                    'fishboneData' => $fishboneData,
                    'getrisklevel' => $getrisklevel,
                    'rcpa' => $rcpa,
                    'injury_details' => $injury_details,
                );
            }
            return view('ims.initial.incident.review', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/initial-incident/list/all/type'));
        }
    }

    public function ehsApproval(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $incident_id = decryptId($request->incident_id);
            if (Auth::check()) {
                $incident_report = $this->initialincident->selectOne($incident_id);
                $rcpa = $this->rcpa->selectOne($id, $incident_id);
                $getEHSVerify = $this->initialincident->getEHSVerifyincident($incident_id);
                $getEHSReview = $this->initialincident->getEHSReviewincident($incident_id);
                $getInvestigation = $this->initialincident->getInvestigation($incident_id);
                $getwhywhy = $this->initialincident->getwhywhy($incident_id);
                $getfishbone = $this->initialincident->getfishbone($incident_id);
                $fishboneData = json_decode($getfishbone->first()->fishbone, true);
                $getrisklevel = $this->initialincident->getrisklevel($incident_id);
                $initialincidentevidence = $this->initialincidentevidence->selectOne($incident_id);
                $injury_details = $this->injury_details->getBodypartsInjuryPerson($incident_id);

                $mediaOptions = [
                    1 => 'Phone',
                    2 => 'Walkie Talkie',
                    3 => 'Extension',
                    4 => 'Others',
                ];

                $selectedMedia = isset($incident_report->reporting_media)
                    ? explode(',', $incident_report->reporting_media)
                    : [];

                $displayMedia = array_map(function ($media) use ($mediaOptions) {
                    return $mediaOptions[$media] ?? $media;
                }, $selectedMedia);
                $capaEvidence = $this->initialincidentevidence->SelectcapaEvidence($id, $incident_id);
                $data = array(
                    'incident_report' => $incident_report,
                    'displayMedia' => $displayMedia,
                    'initialincidentevidence' => $initialincidentevidence,
                    'getEHSVerify' => $getEHSVerify,
                    'getInvestigation' => $getInvestigation,
                    'getEHSReview' => $getEHSReview,
                    'getwhywhy' => $getwhywhy,
                    'fishboneData' => $fishboneData,
                    'getrisklevel' => $getrisklevel,
                    'rcpa' => $rcpa,
                    'injury_details' => $injury_details,
                    'capaEvidence' => $capaEvidence,
                );
            }
            return view('ims.initial.incident.ehsApproval', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/initial-incident/list/all/type'));
        }
    }


    public function caSubmission(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $incident_id = decryptId($request->incident_id);

            if (Auth::check()) {
                $incident_report = $this->initialincident->selectOne($incident_id);
                $rcpa = $this->rcpa->selectOne($id, $incident_id);
                $getEHSVerify = $this->initialincident->getEHSVerifyincident($incident_id);
                $getEHSReview = $this->initialincident->getEHSReviewincident($incident_id);
                $getInvestigation = $this->initialincident->getInvestigation($incident_id);
                $getwhywhy = $this->initialincident->getwhywhy($incident_id);
                $getfishbone = $this->initialincident->getfishbone($incident_id);
                $fishboneData = json_decode($getfishbone->first()->fishbone, true);
                $getrisklevel = $this->initialincident->getrisklevel($incident_id);
                $initialincidentevidence = $this->initialincidentevidence->selectOne($incident_id);
                $injury_details = $this->injury_details->getBodypartsInjuryPerson($incident_id);
                $mediaOptions = [
                    1 => 'Phone',
                    2 => 'Walkie Talkie',
                    3 => 'Extension',
                    4 => 'Others',
                ];

                $selectedMedia = isset($incident_report->reporting_media)
                    ? explode(',', $incident_report->reporting_media)
                    : [];

                $displayMedia = array_map(function ($media) use ($mediaOptions) {
                    return $mediaOptions[$media] ?? $media;
                }, $selectedMedia);

                $data = array(
                    'incident_report' => $incident_report,
                    'displayMedia' => $displayMedia,
                    'initialincidentevidence' => $initialincidentevidence,
                    'getEHSVerify' => $getEHSVerify,
                    'getInvestigation' => $getInvestigation,
                    'getEHSReview' => $getEHSReview,
                    'getwhywhy' => $getwhywhy,
                    'fishboneData' => $fishboneData,
                    'getrisklevel' => $getrisklevel,
                    'rcpa' => $rcpa,
                    'injury_details' => $injury_details,
                );
            }
            return view('ims.initial.incident.ehsApproval', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/initial-incident/list/all/type'));
        }
    }

    public function teamMembers(Request $request)
    {
        $name = $request->input('search');

        $name = $request->input('search');

        $employees = Employee::select('id', 'emp_id', 'emp_name', 'login_id')
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
                    'id' => encryptId($employee->login_id),
                    'text' => $employee->emp_name . ' - ' . $employee->emp_id,
                ];
            })
        );
    }
    public function reportedBy(Request $request)
    {
        $name = $request->input('search');

        $employees = Employee::select('id', 'emp_id', 'emp_name', 'login_id')
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
                    'id' => encryptId($employee->login_id),
                    'text' => $employee->emp_name . ' - ' . $employee->emp_id,
                ];
            })
        );
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
            $approve_type = EHS_REVIEW;
            $ehsReview = $this->ehs_review->store($approve_type);
            $incident_status = STATUS_INVESTIGATION_PENDING;
            $incident_id = $ehsReview->inicdent_report_id;
            $initialincident = $this->initialincident->find($incident_id);
            $investigationassigned = $this->initialincident->investigationassigned($incident_id);
            $incident = $this->initialincident->updateStatus($incident_id, $incident_status);

            $decryptedTeamMemberIds = is_array($request->team_member)
                ? array_map('decryptId', $request->team_member)
                : [];
            $commaSeparatedTeamMembers = !empty($decryptedTeamMemberIds) ? implode(',', $decryptedTeamMemberIds) : null;

            // Combine all recipient IDs
            $allUserIds = $decryptedTeamMemberIds;

            if (!empty($request->reported_by)) {
                $reportedById = decryptId($request->reported_by);
                $allUserIds[] = $reportedById;
            }

            // Remove duplicates
            $allUserIds = array_unique($allUserIds);

            if (!empty($allUserIds)) {
                $users = User::whereIn('id', $allUserIds)->get();
                $userids = $users->pluck('id')->toArray(); // for notification

                $mailsubject = 'Investigation Assigned';

                $incidentDetails = $this->initialincident->selectOne($incident_id);
                $incidentarray = $incidentDetails->toArray();

                foreach ($users as $user) {
                    $email_id = $user->email;
                    if (!empty($email_id)) {
                        $incidentarray['name'] = $user->name;
                        $incidentarray['email_id'] = $email_id;
                        $incidentarray['mail_subject'] = $mailsubject;

                        Mail::to($email_id)->queue(new IncidentEmail($incidentarray));
                    }
                }

                // Send notification
                $notificationData = array(
                    'notification_type' => 5,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Incident ' . $incidentDetails->sr_no . ' submitted by ' . getUsername($ehsReview->created_by),
                        'icon' => admin_url('public/assets/icons/incident.png'),
                        'id' => $incidentDetails->id,
                        'module' => 1,
                    )),
                    'web_link' => admin_url('incident/initial-incident/investigation/' . encryptId($incidentDetails->id)),
                    'assigned_user' => array_to_string($userids),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                // Insert status log
                $insert_array = array(
                    'ims_type' => 1,
                    'ims_id' => $incidentDetails->id,
                    'from_status' => STATUS_INCIDENT_REPORT,
                    'to_status' => $incident_status,
                    'is_reject' => null,
                    'remarks' => $ehsReview->remark,
                    'approved_by' => Auth::id(),
                );
                $this->Statuslog->create($insert_array);
            }



            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('incident/initial-incident/list/all/type'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/initial-incident/list/all/type'));
        }
    }


    public function investigation(Request $request, $incident_id)
    {
        try {
            $accidentId = null;
            $fire_id = null;

            $incidentId = decryptId($incident_id);
            $departmentList  = $this->department->select('id', 'department_name')->where('status', '1')->get();
            $hiraList  = $this->hira->select('id', 'services')->where('status', '1')->get();
            $incident_report = $this->initialincident->selectOne($incidentId);
            $initialincidentevidence = $this->initialincidentevidence->selectOne($incidentId);
            $getEHSVerify = $this->initialincident->getEHSVerifyincident($incidentId);
            $getEHSReview = $this->initialincident->getEHSReviewincident($incidentId);
            $mediaOptions = [
                1 => 'Phone',
                2 => 'Walkie Talkie',
                3 => 'Extension',
                4 => 'Others',
            ];

            $selectedMedia = isset($incident_report->reporting_media)
                ? explode(',', $incident_report->reporting_media)
                : [];

            $displayMedia = array_map(function ($media) use ($mediaOptions) {
                return $mediaOptions[$media] ?? $media;
            }, $selectedMedia);
            $injury_details = $this->injury_details->getBodypartsInjuryPerson($incidentId);
            $data = array(
                'incidentId' => $incidentId,
                'departmentList' => $departmentList,
                'hiraList' => $hiraList,
                'incident_report' => $incident_report,
                'displayMedia' => $displayMedia,
                'initialincidentevidence' => $initialincidentevidence,
                'getEHSVerify' => $getEHSVerify,
                'getEHSReview' => $getEHSReview,
                'injury_details' => $injury_details,

            );

            return view('ims.initial.incident.investigation', $data);
        } catch (Exception $error) {
            report($error);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/initial-incident/list/all/type'));
        }
    }


    public function existingHira($incident_id, Request $request)
    {
        try {
            $hiraList = $this->hira->select('id', 'services', 'likelihood', 'risk_levels')->where('status', '1')->get();
            $inc_id = decryptId($incident_id);
            $newHiraList = $this->hira->select('id', 'incident_id', 'accident_id', 'fire_id', 'hiramoc_id', 'services', 'likelihood', 'risk_levels')->where('incident_id', $inc_id)->where('hiramoc_id', '1')->first();
            $selectedhira = $this->hiramoc
                ->select('id', 'hira_id', 'incident_id', 'hiramoc_status')
                ->where('hiramoc_status', 'T')
                ->where('incident_id', $inc_id)
                ->where('hira_id', '!=', 0)
                ->first();
            if ($request->ajax()) {
                return view('ims.initial.incident.existinghira', compact('hiraList', 'selectedhira', 'incident_id', 'newHiraList'))->render();
            }

            return view('ims.initial.incident.existinghira', compact('hiraList', 'selectedhira', 'incident_id', 'newHiraList'));
        } catch (Exception $error) {
            return response()->json(['error' => $error->getMessage()], 500);
        }
    }
    public function existingMOC($incident_id, Request $request)
    {
        try {
            $hiraList = $this->hira->select('id', 'services', 'likelihood', 'risk_levels')->where('status', '1')->get();
            $inc_id = decryptId($incident_id);
            $newHiraList = $this->hira->select('id', 'incident_id', 'accident_id', 'fire_id', 'hiramoc_id', 'services', 'likelihood', 'risk_levels')->where('incident_id', $inc_id)->where('hiramoc_id', 2)->first();



            $selectedhira = $this->hiramoc
                ->select('id', 'moc_id', 'incident_id', 'hiramoc_status')
                ->where('hiramoc_status', 'T')
                ->where('incident_id', $inc_id)
                ->where('moc_id', '!=', 0)
                ->first();
            if ($request->ajax()) {
                return view('ims.initial.incident.existingMOC', compact('hiraList', 'selectedhira', 'incident_id', 'newHiraList'))->render();
            }

            return view('ims.initial.incident.existingMOC', compact('hiraList', 'selectedhira', 'incident_id', 'newHiraList'));
        } catch (Exception $error) {
            return response()->json(['error' => $error->getMessage()], 500);
        }
    }
    public function investigationSubmit(Request $request)
    {
        try {
            // dd($request->all());
            $incident_id = decryptId($request->incident_id);
            if ($request->root_cause ==  3) {
                $incident_status = STATUS_INCIDENT_CLOSED;
                $capaStatus = STATUS_INCIDENT_CLOSED;
            } else {
                $incident_status = STATUS_INVESTIGATION_CLOSED;
                $capaStatus = STATUS_ACTION_PENDING;
            }

            $incidentinvestigation = $this->incidentinvestigation->store($incident_id, $incident_status);
            $rcpa =  $this->rcpa->storeRCPA($incident_id, $capaStatus);
            $this->initialincident->uaucsubmit($incident_id);
            $this->riskanalysis->store($incident_id, $incident_status);
            $incident = $this->initialincident->updateStatus($incident_id, $incident_status);
            if ($incidentinvestigation->root_cause_analysis ==  1) {
                $whyanalysis = $this->whyanalysis->store($incident_id,  $incidentinvestigation->id);
            }
            if ($incidentinvestigation->root_cause_analysis == 2) {
                $this->fishboneAnalysis->storeFishbone($incident_id, $incidentinvestigation->id);
            }
            $incidentDetails = $this->initialincident->selectOne($incident_id);
            $investigationDetails = $this->incidentinvestigation->SelectOne($incident_id);
            $rcpaDetails = $this->rcpa->getRCPA($incident_id);
            foreach ($rcpa as $item) {
                $responsibilityIds = is_array($item['responsibility']) ? $item['responsibility'] : [$item['responsibility']];

                $responsibleUsers = User::whereIn('id', $responsibilityIds)->get();
                $ehsHeadUsers = User::where('role', ROLE_EHS_HEAD)->get();
                $superadminUsers = User::where('role', ROLE_SUPERADMIN)->get();
                $users = $responsibleUsers->merge($ehsHeadUsers)->merge($superadminUsers)->unique('id');

                if ($users->isEmpty()) {
                    continue;
                }

                $mailsubject = 'Investigation Submitted CAPA Pending';
                $incidentarray = $incidentDetails->toArray();
                $investigationarray = $investigationDetails->toArray();
                $rcpaarray = $rcpaDetails->toArray();

                foreach ($users as $user) {
                    $email_id = $user->email;

                    if ($email_id) {
                        $incidentarray['name'] = $user->name;
                        $incidentarray['email_id'] = $email_id;
                        $incidentarray['mail_subject'] = $mailsubject;

                        Mail::to($email_id)->queue(new InvestigationEmail($incidentarray,  $investigationarray, $rcpaarray));
                    }
                }

                $notificationData = [
                    'notification_type' => 5,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode([
                        'title' => $mailsubject,
                        'message' => 'Incident ' . $incidentDetails->sr_no . ' submitted by ' . getUsername($incidentinvestigation->created_by),
                        'icon' => admin_url('public/assets/icons/incident.png'),
                        'id' => $incidentDetails->id,
                        'module' => 1,
                    ]),
                    'web_link' => admin_url('incident/initial-incident/caSubmission/' . encryptId($item['rcpa_id']) . '/' . encryptId($incident_id)),
                    'assigned_user' => implode(',', $users->pluck('id')->toArray()),
                    'created_by' => Auth::id(),
                ];

                notificationSave($notificationData);
            }
            $insert_array = array(
                'ims_type' => 1,
                'ims_id' => $incidentDetails->id,
                'from_status' => STATUS_INVESTIGATION_PENDING,
                'to_status' => $incident_status,
                'is_reject' => null,
                'remarks' => null,
                'approved_by' => Auth::id(),
            );

            $this->Statuslog->create($insert_array);
            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('incident/initial-incident/investigationList'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/initial-incident/investigationList'));
        }
    }

    public function approvereject(Request $request, $incident_id)
    {
        try {


            $incidentId = decryptId($incident_id);
            $departmentList  = $this->department->select('id', 'department_name')->where('status', '1')->get();
            $hiraList  = $this->hira->select('id', 'services')->where('status', '1')->get();
            $incident_report = $this->initialincident->selectOne($incidentId);
            $initialincidentevidence = $this->initialincidentevidence->selectOne($incidentId);
            $getInvestigation = $this->initialincident->getInvestigation($incidentId);
            $getEHSVerify = $this->initialincident->getEHSVerifyincident($incidentId);
            $getwhywhy = $this->initialincident->getwhywhy($incidentId);
            $getfishbone = $this->initialincident->getfishbone($incidentId);
            $fishboneData = json_decode($getfishbone->first()->fishbone, true);
            $getrisklevel = $this->initialincident->getrisklevel($incidentId);
            $getEHSReview = $this->initialincident->getEHSReviewincident($incidentId);

            $mediaOptions = [
                1 => 'Phone',
                2 => 'Walkie Talkie',
                3 => 'Extension',
                4 => 'Others',
            ];

            $selectedMedia = isset($incident_report->reporting_media)
                ? explode(',', $incident_report->reporting_media)
                : [];

            $displayMedia = array_map(function ($media) use ($mediaOptions) {
                return $mediaOptions[$media] ?? $media;
            }, $selectedMedia);

            $data = array(
                'incidentId' => $incidentId,
                'departmentList' => $departmentList,
                'hiraList' => $hiraList,
                'incident_report' => $incident_report,
                'displayMedia' => $displayMedia,
                'initialincidentevidence' => $initialincidentevidence,
                'getInvestigation' => $getInvestigation,
                'getEHSVerify' => $getEHSVerify,
                'fishboneData' => $fishboneData,
                'getwhywhy' => $getwhywhy,
                'getrisklevel' => $getrisklevel,
                'getEHSReview' => $getEHSReview,

            );

            return view('ims.initial.incident.riskanalysis', $data);
        } catch (Exception $error) {
            report($error);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/initial-incident/list/all/type'));
        }
    }


    public function ehsHeadVerifySubmit(Request $request)
    {
        try {

            $approve_type = EHS_VERIFY;
            $ehsReview = $this->ehs_review->store($approve_type);
            $incident_status = STATUS_ACTION_PENDING;
            $incident_id = $ehsReview->inicdent_report_id;
            $this->initialincident->chooseAssigneeUpdate($incident_id, $ehsReview->team_member);
            $incident = $this->initialincident->updateStatus($incident_id, $incident_status);
            if ($ehsReview->team_member) {
                $teamMemberIds = explode(',', $ehsReview->team_member);

                $employees = Employee::whereIn('id', $teamMemberIds)->get(['emp_name', 'email', 'login_id']);

                // Extract login IDs into an array for notification
                $loginIds = $employees->pluck('login_id')->toArray();
                $mailsubject = 'Action Submission Pending';
                $incidentDetails = $this->initialincident->selectOne($incident_id);
                $incidentarray = $incidentDetails->toArray();

                foreach ($employees as $employee) {
                    $username = $employee->emp_name;
                    $email_id = $employee->email;

                    if (!empty($email_id)) {
                        $incidentarray['name'] = $username;
                        $incidentarray['email_id'] = $email_id;
                        $incidentarray['mail_subject'] = $mailsubject;

                        Mail::to($email_id)->queue(new IncidentEmail($incidentarray));
                    }
                }

                $notificationData = array(
                    'notification_type' => 5,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Incident ' . $incidentDetails->sr_no . ' submitted by ' . getUsername($ehsReview->created_by),
                        'icon' => admin_url('public/assets/icons/incident.png'),
                        'id' => $incidentDetails->id,
                        'module' => 1,
                    )),
                    'web_link' => admin_url('incident/initial-incident/review/' . encryptId($incidentDetails->id)),
                    'assigned_user' => implode(',', $loginIds),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                $insert_array = array(
                    'ims_type' => 1,
                    'ims_id' => $incidentDetails->id,
                    'from_status' => $incidentDetails->incident_status,
                    'to_status' => $incident_status,
                    'is_reject' => null,
                    'remarks' => $ehsReview->remark,
                    'approved_by' => Auth::id(),
                );
                $this->Statuslog->create($insert_array);
            }
            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('incident/initial-incident/investigationList'));
        } catch (Exception $ex) {

            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/initial-incident/investigationList'));
        }
    }
    public function actiontakenSubmit(Request $request)
    {

        try {

            $incident_id = decryptId($request->incident_id);
            $rcpa_id = decryptId($request->rcpa_id);
            $initialincident = $this->initialincident->selectOne($incident_id);
            $incident_status = STATUS_EHSAPPROVAL_PENDING;
            $this->initialincidentevidence->capaEvidence($initialincident, $rcpa_id);
            $this->rcpa->actiontakensubmit($rcpa_id);
            $user_role = ROLE_EHS_HEAD;
            $mailsubject = 'Action Submitted';
            $userids = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->pluck('id')->toArray();
            $users = User::whereRaw('FIND_IN_SET(' . $user_role . ', role)')->get();
            $investigationDetails = $this->incidentinvestigation->SelectOne($incident_id);
            $rcpaDetails = $this->rcpa->getRCPA($incident_id);
            $rcpaActionTaken = $this->rcpa->selectOne($rcpa_id);
            if (count($users) > 0) {
                $incidentarray = $initialincident->toArray();
                $investigationarray = $investigationDetails->toArray();
                $rcpaarray = $rcpaDetails->toArray();
                $rcpaActionTakenarray = $rcpaActionTaken->toArray();

                foreach ($users as $user) {
                    $email_id = $user->email;

                    if ($email_id) {
                        $incidentarray['name'] = $user->name;
                        $incidentarray['email_id'] = $email_id;
                        $incidentarray['mail_subject'] = $mailsubject;

                        Mail::to($email_id)->queue(new RcpaEmail($incidentarray,  $investigationarray, $rcpaarray, $rcpaActionTakenarray));
                    }
                }
            }

            $notificationData = array(
                'notification_type' => 5,
                'module_type' => 1,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => 'Incident' . $initialincident->sr_no . ' submitted by ' . getUsername($initialincident->created_by),
                    'icon' =>  admin_url('public/assets/icons/incident.png'),
                    'id' => $initialincident->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('incident/initial-incident/review/' . encryptId($initialincident->id)),
                'assigned_user' => array_to_string($userids),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);
            $insert_array = array(
                'ims_type' => 1,
                'ims_id' => $initialincident->id,
                'capa_id' => $rcpa_id,
                'from_status' => STATUS_INVESTIGATION_PENDING,
                'to_status' => $incident_status,
                'is_reject' => null,
                'remarks' => null,
                'approved_by' => Auth::id(),
            );

            $this->Statuslog->create($insert_array);
            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('incident/initial-incident/calist'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/initial-incident/calist'));
        }
    }

    public function ehsApprovalSubmit(Request $request)
    {
        try {

            $incident_id = decryptId($request->incident_id);
            $rcpa_id = decryptId($request->rcpa_id);
            $initialincident = $this->initialincident->selectOne($incident_id);
            $approve_type = EHS_APPROVAL;
            $ehsApproval = $this->ehs_review->store($approve_type);
            if ($request->has('approve')) {
                $incident_status = STATUS_INCIDENT_CLOSED;
            } else {
                $incident_status = STATUS_EHSAPPROVAL_REJECTED;
            }
            $incident_id = $ehsApproval->inicdent_report_id;
            $incident = $this->rcpa->updateStatus($rcpa_id, $incident_status);
            $investigationDetails = $this->incidentinvestigation->SelectOne($incident_id);
            $rcpaDetails = $this->rcpa->getRCPA($incident_id);
            $rcpaActionTaken = $this->rcpa->selectOne($rcpa_id);
            $rcpaResponsibility = $rcpaActionTaken->responsibility;
            $mailsubject = 'Incident Closed';

            if ($request->has('approve')) {
                $incidentarray = $initialincident->toArray();
                $investigationarray = $investigationDetails->toArray();
                $rcpaarray = $rcpaDetails->toArray();
                $rcpaActionTakenarray = $rcpaActionTaken->toArray();

                $mailsubject = 'Incident Closed';

                // Collect user IDs
                $userIds = [
                    $initialincident->created_by,
                    $initialincident->investigation_reported_by,
                    $rcpaActionTaken->responsibility,
                ];


                $ehsHeads = User::whereRaw("FIND_IN_SET(?, role)", [ROLE_EHS_HEAD])
                    ->select('id', 'name', 'email')
                    ->get();
                $ehsHeadIds = $ehsHeads->pluck('id')->toArray();


                $userIds = array_unique(array_filter(array_merge($userIds, $ehsHeadIds)));


                $users = User::whereIn('id', $userIds)->select('name', 'email', 'id')->get();

                foreach ($users as $user) {
                    $email_id = $user->email;

                    if (!empty($email_id)) {
                        $mailData = $incidentarray;
                        $mailData['name'] = $user->name;
                        $mailData['email_id'] = $email_id;
                        $mailData['mail_subject'] = $mailsubject;

                        Mail::to($email_id)->queue(new RcpaEmail($mailData, $investigationarray, $rcpaarray, $rcpaActionTakenarray));
                    }
                }


                $notificationData = array(
                    'notification_type' => 5,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Incident ' . $initialincident->sr_no . ' submitted by ' . getUsername($initialincident->created_by),
                        'icon' => admin_url('public/assets/icons/incident.png'),
                        'id' => $initialincident->id,
                        'module' => 1,
                    )),
                    'web_link' => admin_url('incident/initial-incident/view/' . encryptId($initialincident->id)),
                    'assigned_user' => array_to_string($userIds),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);

                // Log status update
                $insert_array = array(
                    'ims_type' => 1,
                    'ims_id' => $initialincident->id,
                    'capa_id' => $rcpa_id,
                    'from_status' => STATUS_ACTION_PENDING,
                    'to_status' => $incident_status,
                    'is_reject' => null,
                    'remarks' => $ehsApproval->remark,
                    'approved_by' => Auth::id(),
                );
                $this->Statuslog->create($insert_array);
            } else {

                $user_role = ROLE_EHS_HEAD;
                $mailsubject = 'EHS Rejected';
                $incidentarray = $initialincident->toArray();
                $investigationarray = $investigationDetails->toArray();
                $rcpaarray = $rcpaDetails->toArray();
                $rcpaActionTakenarray = $rcpaActionTaken->toArray();

                // Collect user IDs
                $userIds = [
                    $initialincident->created_by,
                    $initialincident->investigation_reported_by,
                    $rcpaActionTaken->responsibility,
                ];


                $ehsHeads = User::whereRaw("FIND_IN_SET(?, role)", [ROLE_EHS_HEAD])
                    ->select('id', 'name', 'email')
                    ->get();
                $ehsHeadIds = $ehsHeads->pluck('id')->toArray();


                $userIds = array_unique(array_filter(array_merge($userIds, $ehsHeadIds)));


                $users = User::whereIn('id', $userIds)->select('name', 'email', 'id')->get();

                foreach ($users as $user) {
                    $email_id = $user->email;

                    if (!empty($email_id)) {
                        $mailData = $incidentarray;
                        $mailData['name'] = $user->name;
                        $mailData['email_id'] = $email_id;
                        $mailData['mail_subject'] = $mailsubject;

                        Mail::to($email_id)->queue(new RcpaEmail($mailData, $investigationarray, $rcpaarray, $rcpaActionTakenarray));
                    }
                }


                $notificationData = array(
                    'notification_type' => 5,
                    'module_type' => 1,
                    'notification_message' => $mailsubject,
                    'mobile_notification' => json_encode(array(
                        'title' => $mailsubject,
                        'message' => 'Incident' . $initialincident->sr_no . ' submitted by ' . getUsername($initialincident->created_by),
                        'icon' =>  admin_url('public/assets/icons/incident.png'),
                        'id' => $initialincident->id,
                        'module' => 1,
                    )),
                    'web_link' =>  admin_url('incident/initial-incident/review/' . encryptId($initialincident->id)),
                    'assigned_user' => array_to_string($userIds),
                    'created_by' => Auth::id(),
                );
                notificationSave($notificationData);
                $insert_array = array(
                    'ims_type' => 1,
                    'ims_id' => $initialincident->id,
                    'capa_id' => $rcpa_id,
                    'from_status' => STATUS_ACTION_PENDING,
                    'to_status' => $incident_status,
                    'is_reject' => null,
                    'remarks' => $ehsApproval->remark,
                    'approved_by' => Auth::id(),
                );

                $this->Statuslog->create($insert_array);
            }
            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('incident/initial-incident/calist'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');

            return redirect(admin_url('incident/initial-incident/calist'));
        }
    }

    public function gethiradetails($hira_id)
    {
        $hira_id = decryptId($hira_id);

        $hira = Hira::select('services', 'likelihood', 'risk_levels')
            ->where('id', $hira_id)
            ->first();
        $risk_levels = '';
        if ($hira->risk_levels == 1) {
            $risk_levels = '1 to 9';
        } elseif ($hira->risk_levels == 2) {
            $risk_levels = '10 to 16';
        } elseif ($hira->risk_levels == 3) {
            $risk_levels = '17 to 25';
        } elseif ($hira->risk_levels == 4) {
            $risk_levels = 'Legal';
        }

        return response()->json([
            'hira' => $hira,
            'risk_levels' => $risk_levels,
        ]);
    }

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $vendor_name = $request->vendor_name;
            $license_no = $request->license_no;
            $id = $request->id;

            if (empty($id)) {
                $isUnique = !$this->hira->uniqueCheck($vendor_name, $license_no);
            } else {
                $id = decryptId($id);
                $isUnique = !$this->hira->existUniqueCheck($vendor_name, $license_no, $id);
            }

            return Response::json($isUnique);
        }
    }
    public function empBodyPartUrl($randomId = '', $rowId = '', $injury_person_type = ' ', $injured_person_id = '')
    {
        try {
            $body_parts = $this->incident_body_parts->delete_temprow($randomId, $injury_person_type, $injured_person_id);

            $data = [
                'randomID' => $randomId,
                'rowId' => $rowId,
                'injury_person_type' => $injury_person_type,
                'injured_person_id' => $injured_person_id,
                'body_parts' => $body_parts,
            ];
            return view('ims.initial.incident.api.bodyPart_img', $data);
        } catch (Exception $ex) {
            Session::flash('error', 'Something went wrong, Please try again!');
            return redirect(admin_url('incident/initial-incident/list'));
        }
    }
    public function editempBodyPartUrl($randomId = '', $rowId = '')
    {

        try {
            // $body_parts = $this->incident_body_parts->delete_temprow();
            $getbodyParts = IncidentBodyParts::where('random_id', $randomId)
                ->where('row_id', $rowId)
                ->first();
            //    dd($getbodyParts );


            $bobypart_id = $getbodyParts->id;
            $injury_id = $getbodyParts->injury_id;
            $injuryPersonType = $getbodyParts->injured_person_type;
            if ($getbodyParts->injured_person_type == 3) {
                $injuredPerson = $getbodyParts->injury_person_name;
            } else {
                $injuredPerson = $getbodyParts->injury_person_id;
            }

            $data = [
                'randomID' => $randomId,
                'rowId' => $rowId,
                'bobypart_id' => $bobypart_id,
                'injury_id' => $injury_id,
                'injuryPersonType' => $injuryPersonType,
                'getbodyParts' => $getbodyParts,
                'injuredPerson' => $injuredPerson,
            ];

            return view('ims.initial.incident.api.editbodyPart_img', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try again!');
            return redirect(admin_url('incident/initial-incident/list'));
        }
    }



    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->initialincident->statuschange($id);
            $this->rcpa->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Your status has changed successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $incident_report = $this->initialincident->selectOne($id);
                $getEHSVerify = $this->initialincident->getEHSVerifyincident($id);
                $getEHSReview = $this->initialincident->getEHSReviewincident($id);
                $getInvestigation = $this->initialincident->getInvestigation($id);
                $getwhywhy = $this->initialincident->getwhywhy($id);
                $getfishbone = $this->initialincident->getfishbone($id);
                $fishboneData = json_decode($getfishbone->first()->fishbone, true);
                $getrisklevel = $this->initialincident->getrisklevel($id);
                $getEHSApprovalincident = $this->initialincident->getEHSApprovalincident($id);
                $initialincidentevidence = $this->initialincidentevidence->selectOne($id);
                $mediaOptions = [
                    1 => 'Phone',
                    2 => 'Walkie Talkie',
                    3 => 'Extension',
                    4 => 'Others',
                ];

                $selectedMedia = isset($incident_report->reporting_media)
                    ? explode(',', $incident_report->reporting_media)
                    : [];

                $displayMedia = array_map(function ($media) use ($mediaOptions) {
                    return $mediaOptions[$media] ?? $media;
                }, $selectedMedia);
            }
            $injury_details = $this->injury_details->getBodypartsInjuryPerson($id);
            $rcpa = $this->rcpa->getRCPA($id);

            $data = array(
                'incident_report' => $incident_report,
                'displayMedia' => $displayMedia,
                'initialincidentevidence' => $initialincidentevidence,
                'getEHSVerify' => $getEHSVerify,
                'getInvestigation' => $getInvestigation,
                'getEHSReview' => $getEHSReview,
                'getwhywhy' => $getwhywhy,
                'fishboneData' => $fishboneData,
                'getfishbone' => $getfishbone,
                'getrisklevel' => $getrisklevel,
                'getEHSApprovalincident' => $getEHSApprovalincident,
                'injury_details' => $injury_details,
                'rcpa' => $rcpa,
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
            $html = view('ims.initial.incident.exportpdf', $data)->render();
            $mpdf->WriteHTML($html);
            $filename = "Incident.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/initial-incident/list/all/type'));
        }
    }


    public function cageneralpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $incident_id = decryptId($request->incident_id);
            if (Auth::check()) {
                $incident_report = $this->initialincident->selectOne($incident_id);
                $getEHSVerify = $this->initialincident->getEHSVerifyincident($incident_id);
                $getEHSReview = $this->initialincident->getEHSReviewincident($incident_id);
                $getInvestigation = $this->initialincident->getInvestigation($incident_id);
                $getwhywhy = $this->initialincident->getwhywhy($incident_id);
                $getfishbone = $this->initialincident->getfishbone($incident_id);
                $fishboneData = json_decode($getfishbone->first()->fishbone, true);
                $getrisklevel = $this->initialincident->getrisklevel($incident_id);
                $getEHSApprovalincident = $this->initialincident->getEHSApprovalincident($incident_id);
                $initialincidentevidence = $this->initialincidentevidence->selectOne($incident_id);
                $capaEvidence = $this->initialincidentevidence->SelectcapaEvidence($id, $incident_id);
                $mediaOptions = [
                    1 => 'Phone',
                    2 => 'Walkie Talkie',
                    3 => 'Extension',
                    4 => 'Others',
                ];

                $selectedMedia = isset($incident_report->reporting_media)
                    ? explode(',', $incident_report->reporting_media)
                    : [];

                $displayMedia = array_map(function ($media) use ($mediaOptions) {
                    return $mediaOptions[$media] ?? $media;
                }, $selectedMedia);
            }
            $injury_details = $this->injury_details->getBodypartsInjuryPerson($incident_id);

            $rcpa = $this->rcpa->selectOne($id, $incident_id);
            $status_log = $this->Statuslog->selectCAPA($incident_id, $id, 1);
            $data = array(
                'incident_report' => $incident_report,
                'displayMedia' => $displayMedia,
                'initialincidentevidence' => $initialincidentevidence,
                'getEHSVerify' => $getEHSVerify,
                'getInvestigation' => $getInvestigation,
                'getEHSReview' => $getEHSReview,
                'getwhywhy' => $getwhywhy,
                'fishboneData' => $fishboneData,
                'getfishbone' => $getfishbone,
                'getrisklevel' => $getrisklevel,
                'getEHSApprovalincident' => $getEHSApprovalincident,
                'injury_details' => $injury_details,
                'rcpa' => $rcpa,
                'status_log' => $status_log,
                'capaEvidence' => $capaEvidence,
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
            $html = view('ims.initial.incident.capdf', $data)->render();
            $mpdf->WriteHTML($html);
            $filename = "Incident.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/initial-incident/list/all/type'));
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->initialincident->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Sr. No',
                __("common.company"),
                __("common.location"),
                __("common.unit"),
                'Shift',
                'IIR Type',
                // 'From Status',
                'Approve Status',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->sr_no;
                $export[] =  getcompanyname($data->company_id);
                $export[] =  getlocationname($data->location_id);
                $export[] =  getUnitname($data->unit_id);
                $export[] =  $data->shift;
                $export[] = getIIRTypename($data->iir_type);
                // $export[] =  $data->to_status;
                $export[] =  $data->status_name;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Initial Incident.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/initial-incident/list/all/type'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->initialincident->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Sr. No',
                __("common.company"),
                __("common.location"),
                __("common.unit"),
                'Shift',
                'IIR Type',
                // 'From Status',
                'Approve Status',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Initial Incident",
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

            $view = view('ims.initial.incident.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Initial Incident.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/initial-incident/list/all/type'));
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
