<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Master\Unit;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use App\Models\Master\Employee;
use App\Models\Master\Department;
use Illuminate\Support\Facades\DB;
use App\Models\Permit\SafetyPermit;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use App\Models\Master\TrainingSchedule;
use Illuminate\Support\Facades\Session;
use App\Models\IMS\Incident\InitialIncident;
use App\Models\IMS\Incident\IncidentBodyParts;
use App\Models\Inspection\audit\AuditAnalysis;
use App\Models\Inspection\audit\AuditAssessment;
use App\Models\Inspection\audit\InterUnitAudit;
use App\Models\Inspection\audit\MonthlyAuditPlan;
use App\Models\Inspection\GembaWalk\GembaWalk;
use Illuminate\Support\Facades\DB as FacadesDB;
use App\Models\Inspection\GembaWalk\GembaWalkChecklist;
use App\Models\Master\Company;
use App\Models\Master\PpeExemption;
use App\Models\Master\PpeRequest;
use App\Models\OhcManagement\Opd\FirstAid;
use App\Models\OhcManagement\Opd\PrescribetoPatient;
use PHPUnit\TextUI\Configuration\IniSetting;

class AdminController extends Controller
{


    private $bodyparts;
    private $training_schedule;
    private $ims_incident;
    private $ptw;
    private $gembaWalk;
    private $unit;
    private $department;
    private $company;
    private $initial_incident;
    private $ppe_request;
    private $ppe_exemption;
    private $training;
    private $audit_assessment;
    private $audit_analysis;
    private $monthly_audit;
    private $inter_unit_audit;
    private $ims;
    private $opd;
    private $first_aid;
    private $incident_ims;
    private $gemba_Walk;

    public function __construct()
    {
        $this->bodyparts = new IncidentBodyParts();
        $this->bodyparts = new IncidentBodyParts();
        $this->incident_ims = new InitialIncident();
        $this->training_schedule = new TrainingSchedule();
        $this->ims_incident = new InitialIncident();
        $this->ptw = new SafetyPermit();
        $this->gembaWalk = new GembaWalkChecklist();
        $this->unit = new Unit();
        $this->department = new Department();
        $this->company = new Company();
        $this->ppe_request = new PpeRequest();
        $this->training = new TrainingSchedule();
        $this->ppe_exemption = new PpeExemption();
        $this->initial_incident = new InitialIncident();
        $this->audit_assessment = new AuditAssessment();
        $this->audit_analysis = new AuditAnalysis();
        $this->inter_unit_audit = new InterUnitAudit();
        $this->monthly_audit = new MonthlyAuditPlan();
        $this->ims = new InitialIncident();
        $this->opd = new PrescribetoPatient();
        $this->first_aid = new FirstAid();
        $this->gemba_Walk = new GembaWalk();
    }

    public function index(Request $request)
    {
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $data = [];

                if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_ADMIN) || CheckUserRole(ROLE_EHS_HEAD) ||  CheckUserRole(ROLE_DASHBOARD_VIEWER)) {
                    $masterLink = [
                        [
                            'link' => 'company/list',
                            'name' => 'Company',
                            'count' => gettotalCount('company'),
                            'icon' => 'bx bx-message-square-detail',
                            'icon_color' => 'text-primary',
                        ],
                        [
                            'link' => 'location/list',
                            'name' => 'Location',
                            'count' => gettotalCount('location'),
                            'icon' => 'bx bx-message-square-detail',
                            'icon_color' => 'text-primary',
                        ],
                        [
                            'link' => 'unit/list',
                            'name' => 'Unit',
                            'count' => gettotalCount('unit'),
                            'icon' => 'bx bx-message-square-detail',
                            'icon_color' => 'text-primary',
                        ],

                        [
                            'link' => 'department/list',
                            'name' => 'Department',
                            'count' => gettotalCount('department'),
                            'icon' => 'bx bx-message-square-detail',
                            'icon_color' => 'text-primary',
                        ],


                        [
                            'link' => 'employee/list',
                            'name' => 'Employees',
                            'count' => gettotalCount('employee'),
                            'icon' => 'bx bx-message-square-detail',
                            'icon_color' => 'text-primary',
                        ],

                        [
                            'link' => 'work/list',
                            'name' => 'Workers',
                            'count' => gettotalCount('work'),
                            'icon' => 'bx bx-message-square-detail',
                            'icon_color' => 'text-primary',
                        ],

                    ];

                    $companyList  = $this->company->where('status', '1')->get();
                    $data = [
                        'masterLink' => $masterLink,
                        'companyList' => $companyList,
                    ];
                }


                if (
                    CheckUserRole(ROLE_SUPERADMIN) ||
                    CheckUserRole(ROLE_ADMIN) ||
                    CheckUserRole(ROLE_EHS_HEAD) ||
                    CheckUserRole(ROLE_DASHBOARD_VIEWER)
                ) {
                    return view('admin.dashboard', $data);
                } else {
                    return view('admin.userdashboard', $data);
                }
            }
        } catch (\Exception $ex) {
            report($ex);
            return back()->with('error', 'Failed to load heatmap incident data.');
        }
    }


    public function profileView()
    {
        $id = Auth::user()->id;
        $page_data['user_detail'] = Auth::user();
        $page_data['country_detail'] = Auth::user();
        return view('admin.user_profile', $page_data);
    }

    public function profileUpdate(Request $request)
    {
        try {
            $id = Auth::id();

            $file = $request->file('profile_image');
            if ($file != null) {
                $uploadpath = 'public/uploads/profile';
                $filenewname = time() . Str::random('10') . '.' . $file->getClientOriginalExtension();
                $fileName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                $fileMimetype = $file->getMimeType();
                $fileExt = $file->getClientOriginalExtension();
                $file->move($uploadpath, $filenewname);
                $update_data['profile_image'] = $filenewname;

                User::where('id', $id)->update($update_data);
            }



            Session::flash('success', 'User profile is updated successfully!');
            return redirect(admin_url('profile'));
        } catch (Exception $ex) {
            report($ex);
            return "Error";
        }
    }
    public function signatureUpload(Request $request)
    {
        try {
            $id = Auth::id();

            if ($request->hasFile('signature_image')) {
                $file = $request->file('signature_image');


                $destinationPath = 'uploads/signatureupload';

                if (!File::exists(public_path($destinationPath))) {
                    File::makeDirectory(public_path($destinationPath), 0777, true, true);
                }

                $signature_image_name = time() . '_' . $file->getClientOriginalName();

                $file->move(public_path($destinationPath), $signature_image_name);
                $signature_image_path = 'public/' . $destinationPath . '/' . $signature_image_name;

                $update_data['signature_upload'] = $signature_image_path;

                User::where('id', $id)->update($update_data);
                Employee::where('login_id', $id)->update($update_data);

                Session::flash('success', 'User Signature is updated successfully!');
            } else {
                Session::flash('error', 'No file was uploaded.');
            }

            return redirect(admin_url('profile'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong. Please try again later.');
            return redirect(admin_url('profile'));
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = Auth::id();
            $request->validate([
                'name' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
            ]);
            $update_data = array(
                'name' => $request->name,
                'first_name' => $request->name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'mobile' => $request->phone,
                'updated_by' => Auth::id()
            );

            User::where('id', $id)->update($update_data);
            Session::flash('success', 'User profile is updated successfully!');
            return redirect(admin_url('profile'));
        } catch (Exception $ex) {
            report($ex);
            return "Error";
        }
    }
    public function changeProfilePassword(Request $request)
    {

        try {

            $user = Auth::user();

            if (Auth::attempt(array('username' => Auth::user()->username, 'password' => $request->old_password))) {

                if ($request->password != null && $request->confirm_password != null) {
                    if ($request->password == $request->confirm_password) {
                        $password = $request->password;
                        $user->password = Hash::make($password);

                        $user->save();

                        Session::flash('success', 'Password updated successfully!');
                    } else {
                        Session::flash('error', 'Password missmatch!');
                    }
                }
            } else {
                Session::flash('error', 'Invalid old password');
            }

            return redirect(admin_url('profile'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Please try after sometimes!');
            return redirect()->back();
        }
    }


    // card

    // ppe totals

    public function getpperequest(Request $request)
    {
        try {
            $total = $this->ppe_request->getTotalRecords();

            return response()->json([
                'status' => 'success',
                'total' => $total,
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage(),
            ], 500);
        }
    }

    // total of the ppe exemption

    public function getExemption(Request $request)
    {
        try {
            $total = $this->ppe_exemption->getTotalRecords();

            return response()->json([
                'status' => 'success',
                'total' => $total,
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage(),
            ], 500);
        }
    }

    // Safety Permit

    public function getSafetytotal(Request $request)
    {
        try {
            $total = $this->ptw->getTotalRecords();

            return response()->json([
                'status' => 'success',
                'total' => $total,
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage(),
            ], 500);
        }
    }
    // training
    public function getTrainingTotal(Request $request)
    {
        try {
            $total = $this->training->getTotalRecords();

            return response()->json([
                'status' => 'success',
                'total' => $total,
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage(),
            ], 500);
        }
    }

    // audit assessment

    public function getAuditAssessmentTotal(Request $request)
    {
        try {
            $total = $this->audit_assessment->getTotalRecords();

            return response()->json([
                'status' => 'success',
                'total' => $total,
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage(),
            ], 500);
        }
    }

    // audit assessment

    public function getAuditAnanlysisTotal(Request $request)
    {
        try {
            $total = $this->audit_analysis->getTotalRecords();

            return response()->json([
                'status' => 'success',
                'total' => $total,
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage(),
            ], 500);
        }
    }

    // monthly audit

    public function getMonthlyTotal(Request $request)
    {
        try {
            $total = $this->monthly_audit->getTotalRecords();

            return response()->json([
                'status' => 'success',
                'total' => $total,
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage(),
            ], 500);
        }
    }

    // inter unit audit

    public function getInterTotal(Request $request)
    {
        try {
            $total = $this->inter_unit_audit->getTotalRecords();

            return response()->json([
                'status' => 'success',
                'total' => $total,
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage(),
            ], 500);
        }
    }

    // opd --> ohc managemnt
    public function getopdTotal(Request $request)
    {
        try {
            $total = $this->opd->getTotalRecords();

            return response()->json([
                'status' => 'success',
                'total' => $total,
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage(),
            ], 500);
        }
    }

    // get first aid total

    public function getFirstaidTotal(Request $request)
    {
        try {
            $total = $this->first_aid->getTotalRecords();

            return response()->json([
                'status' => 'success',
                'total' => $total,
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage(),
            ], 500);
        }
    }
    // minor accident
    public function getminorAccident(Request $request)
    {
        try {
            $total = $this->ims->getminorTotalRecords();

            return response()->json([
                'status' => 'success',
                'total' => $total,
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage(),
            ], 500);
        }
    }
    // major accident
    public function getmajorAccident(Request $request)
    {
        try {
            $total = $this->ims->getmajorTotalRecords();

            return response()->json([
                'status' => 'success',
                'total' => $total,
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage(),
            ], 500);
        }
    }

    // near miss
    public function getNearmiss(Request $request)
    {
        try {
            $total = $this->ims->getNearTotalRecords();

            return response()->json([
                'status' => 'success',
                'total' => $total,
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage(),
            ], 500);
        }
    }
    // unsafe act
    public function getUnsafeTotal(Request $request)
    {
        try {
            $total = $this->ims->getUnsafeTotalRecords();

            return response()->json([
                'status' => 'success',
                'total' => $total,
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage(),
            ], 500);
        }
    }
    // unsafe condition
    public function getUnsafeConditionTotal(Request $request)
    {
        try {
            $total = $this->ims->getUnsafeConditionTotalRecords();

            return response()->json([
                'status' => 'success',
                'total' => $total,
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage(),
            ], 500);
        }
    }
    // gemba walk
    public function getGembaWalk(Request $request)
    {
        try {
            $total = $this->gemba_Walk->getGembaWalkTotalRecords();

            return response()->json([
                'status' => 'success',
                'total' => $total,
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage(),
            ], 500);
        }
    }

    //  fire incidence total
    public function getFireincidence(Request $request)
    {
        try {
            $total = $this->ims->getFireIncidenceTotal();

            return response()->json([
                'status' => 'success',
                'total' => $total,
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage(),
            ], 500);
        }
    }


    //  fire inspection total
    public function getFireinspection(Request $request)
    {
        try {
            $totalData = GetInspectionCount('Fire');
            $total = isset($totalData['Fire']) ? $totalData['Fire'] : 0;

            return response()->json([
                'status' => 'success',
                'total' => $total,
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage(),
            ], 500);
        }
    }



    //  ohc inspection total
    public function getOhcinspection(Request $request)
    {
        try {
            $totalData = GetInspectionCount('Ohc');
            $total = isset($totalData['Ohc']) ? $totalData['Ohc'] : 0;

            return response()->json([
                'status' => 'success',
                'total' => $total,
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage(),
            ], 500);
        }
    }

    //  safety inspection total
    public function getSafetyinspection(Request $request)
    {
        try {

            $totalData = GetInspectionCount('Safety');
            $total = isset($totalData['Safety']) ? $totalData['Safety'] : 0;

            return response()->json([
                'status' => 'success',
                'total' => $total,
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage(),
            ], 500);
        }
    }

    public function getTotalIncident(Request $request)
    {
        try {
            $chartData = $this->incident_ims->getTotalIncidentCountData($request);
            if ($chartData->isEmpty()) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }
            $formattedData = [];
            $incidentTypeIds = [];
            $unitIds = [];
            $lookup = [];

            foreach ($chartData as $row) {
                // dd($row);
                $unitName = $row->unit_name;
                $incidentTypeName = $row->incident_type_name;


                $formattedData[$incidentTypeName][$unitName] = $row->incident_count;

                $incidentTypeIds[$incidentTypeName] = $row->incident_type_id;
                $unitIds[$unitName] = $row->unit_id;

                $lookup[$incidentTypeName][$unitName] = [
                    'incident_type_id' => $row->incident_type_id,
                    'unit_id' => $row->unit_id,
                ];
            }

            return view('admin.dashboard.totalIncidentsCount', [
                'formattedData' => $formattedData,
                'incidentTypeIds' => $incidentTypeIds,
                'unitIds' => $unitIds,
                'lookup' => $lookup,
                'getdashdata' => $request,
                'chartData' => $chartData,
            ]);
        } catch (\Exception $ex) {
            report($ex);
            return back()->with('error', 'Failed to load unit-wise incident data.');
        }
    }
    public function getIncidentTypeChart(Request $request)
    {
        try {
            $chartData = $this->incident_ims->getIncidentTypeCountData($request);

            if ($chartData->isEmpty()) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }
            $formattedData = [];
            $typeIdMap = [];

            foreach ($chartData as $row) {
                $type = $row->incident_type_name ?? 'Unknown Type';
                $formattedData[$type] = (int) $row->incident_count;
                $typeIdMap[$type] = $row->incident_type_id;
            }

            return view('admin.dashboard.incidentTypeconut', [
                'formattedData' => $formattedData,
                'typeIdMap' => $typeIdMap,
                'getdashdata' => $request,
            ]);
        } catch (\Exception $ex) {
            report($ex);
            return back()->with('error', 'Failed to load incident type chart data.');
        }
    }



    public function getHeatmapImsData(Request $request)
    {
        try {
            $chartData = $this->incident_ims->getHeatmapImsCountData($request);

            if ($chartData->isEmpty()) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }

            // Map injury codes to readable labels
            $injuryMap = [
                1 => 'Major',
                2 => 'Minor',
                3 => 'Fatal',
            ];

            // Initialize all month slots for each injury type
            $monthlyCounts = [];
            foreach (range(1, 12) as $month) {
                foreach ($injuryMap as $label) {
                    $monthlyCounts[$label][$month] = 0;
                }
            }

            // Populate the monthly counts from DB results
            foreach ($chartData as $row) {
                if (!isset($row->nature_of_injury) || !isset($injuryMap[$row->nature_of_injury])) {
                    continue; // Skip null or unknown injury types
                }

                $month = (int) $row->month;
                $type = $injuryMap[$row->nature_of_injury];
                $monthlyCounts[$type][$month] = (int) $row->incident_count;
            }

            // Format for ApexCharts
            $formattedData = [];
            foreach ($monthlyCounts as $type => $months) {
                $data = [];
                foreach ($months as $month => $count) {
                    $data[] = [
                        'x' => date('M', mktime(0, 0, 0, $month, 10)), // e.g. Jan, Feb
                        'y' => $count,
                    ];
                }

                $formattedData[] = [
                    'name' => $type,
                    'data' => $data,
                ];
            }

            return view('admin.dashboard.heatmapImsData', [
                'formattedData' => $formattedData,
                'getdashdata' => $request,
            ]);
        } catch (\Exception $ex) {
            report($ex);
            return back()->with('error', 'Failed to load heatmap incident data.');
        }
    }

    public function InspectionWiseCount(Request $request)
    {
        try {
            $from_date = $request->input('Fromdate');
            $to_date = $request->input('Todate');

            $inspection_wise_count = InspectionCount($from_date, $to_date);

            $return_flag = true;
            foreach ($inspection_wise_count as $index => $values) {
                if ($values == 0) {
                    $return_flag = false;
                } else {
                    $return_flag = true;
                }
            }

            if ($return_flag == false) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }

            $data = [
                'inspection_wise_count' => $inspection_wise_count,
                'from_date' => $from_date,
                'to_date' => $to_date,
            ];

            return view('admin.dashboard.inspection_wise_count', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }

    public function PTWActiveVsClose(Request $request)
    {
        try {

            $dates = [
                'from_date' => $request->input('FromDate'),
                'to_date' =>  $request->input('ToDate'),
                'company_id' =>  $request->input('CompanyId')
            ];
            $active_close_count = $this->ptw->ActiveVsClose();

            $return_flag = true;
            foreach ($active_close_count as $index => $values) {
                if ($values == 0) {
                    $return_flag = false;
                } else {
                    $return_flag = true;
                }
            }

            if ($return_flag == false) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }



            $openEncrypted = encryptId(1);
            $closeEncrypted = encryptId(2);
            $data = [
                'active_close_count' => $active_close_count,
                'dates' => $dates,
                'openStatusEncrypted' => $openEncrypted,
                'closeStatusEncrypted' => $closeEncrypted,
            ];

            return view('admin.dashboard.ptw_open_close', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }


    public function PTWTypeWiseCount(Request $request)
    {
        try {

            $dates = [
                'from_date' => $request->input('FromDate'),
                'to_date' =>  $request->input('ToDate'),
                'company_id' =>  $request->input('CompanyId')
            ];

            $work_wise_count = $this->ptw->GetTypeWiseCount();
            if (count($work_wise_count) < 0) {
                return response()->json([
                    'html' => '<div class="border-0 pb-3" style="margin-top: 150px;"><h4 style="text-align: center;">No data Found.</h4></div>',
                    'status' => 'empty'
                ]);
            }

            $totalCount = array_sum(array_column($work_wise_count, 'count'));
            $data = [
                'work_wise_count' => $work_wise_count,
                'dates' => $dates,
                'totalCount' => $totalCount,
            ];

            return view('admin.dashboard.ptw_type_wise_count', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }




    public function ptwholdviolation(Request $request)
    {
        try {

            $dates = [
                'from_date' => $request->input('FromDate'),
                'to_date' =>  $request->input('ToDate'),
                'company_id' =>  $request->input('CompanyId')
            ];

            $hold_count = $this->ptw->getHoldStatus($request);

            $permitStatus = encryptId(3);
            $data = [
                'hold_count' => $hold_count,
                'dates' => $dates,
                'permitStatus' => $permitStatus,
            ];
            if (empty($hold_count)) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }
            return view('admin.dashboard.ptw_hold_wise_count', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }

    public function getPPEIssuanceGroupWise(Request $request)
    {
        try {
            $form_date = $request->input('Fromdate');
            $to_date = $request->input('Todate');
            $company_id = $request->input('CompanyId');
            $chartData = getPPERequestChartData($form_date, $to_date, $company_id);

            return view('admin.dashboard.chartPPEIssuanceGroupWise', [
                'getdashdata' => $request,
                'chartData' => $chartData,
            ]);
        } catch (\Exception $ex) {
            report($ex);
        }
    }

    public function getPTWAvgTimeChart(Request $request)
    {
        try {

            $form_date = $request->input('Fromdate');
            $to_date = $request->input('Todate');
            $company_id = $request->input('CompanyId');
            $chartData = getPTWAvgTimeChartData($form_date, $to_date, $company_id);

            return view('admin.dashboard.chartPTWAvgTimeChart', [
                'getdashdata' => $request,
                'chartData' => $chartData,
            ]);
        } catch (\Exception $ex) {
            report($ex);
        }
    }

    public function getPPEAvailabilityChart(Request $request)
    {
        try {

            $form_date = $request->input('Fromdate');
            $to_date = $request->input('Todate');
            $company_id = $request->input('CompanyId');
            $chartData = getPPEAvailabilityChartData($form_date, $to_date, $company_id);
            $return_flag = true;
            foreach ($chartData as $index => $values) {
                if ($values == 0) {
                    $return_flag = false;
                } else {
                    $return_flag = true;
                }
            }

            if ($return_flag == false) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }

            return view('admin.dashboard.chartPPEAvailability', [
                'getdashdata' => $request,
                'chartData' => $chartData,
            ]);
        } catch (\Exception $ex) {
            report($ex);
        }
    }



    public function getTypeofIIRCount(Request $request)
    {
        try {
            $chartData = $this->ims_incident->getTypeofIIRCountData($request);

            if ($chartData->isEmpty()) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }

            $formattedData = [
                'labels' => $chartData->pluck('incident_type_name'),
                'counts' => $chartData->pluck('total'),
                'idMap' => $chartData->mapWithKeys(function ($item) {
                    return [$item->incident_type_name => $item->incident_type_id];
                }),
            ];
            return view('admin.dashboard.type_of_irr', [
                'formattedData' => $formattedData,

            ]);
        } catch (\Exception $ex) {
            report($ex);
            return back()->with('error', 'Failed to load Type of IIR data.');
        }
    }


    public function getAccidentReportUnitWiseCount(Request $request)
    {
        try {
            $chartData = $this->ims_incident->getAccidentReportUnitWiseCountData($request);


            if ($chartData->isEmpty()) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }

            // Build lookup structure for JS click event
            $lookup = [];
            foreach ($chartData as $item) {
                $lookup['Major'][$item->unit_name] = [
                    'unit_id' => $item->unit_id,
                    'injury_type' =>IIR_TYPE_MAJOR
                ];
                $lookup['Minor'][$item->unit_name] = [
                    'unit_id' => $item->unit_id,
                    'injury_type' => IIR_TYPE_MINOR
                ];
                $lookup['Fatal'][$item->unit_name] = [
                    'unit_id' => $item->unit_id,
                    'injury_type' =>IIR_TYPE_FATAL
                ];
            }

            $formattedData = [
                'labels' => $chartData->pluck('unit_name'),
                'major' => $chartData->pluck('major'),
                'minor' => $chartData->pluck('minor'),
                'fatal' => $chartData->pluck('fatal'),
                'lookup' => $lookup
            ];
            return view('admin.dashboard.accident_report_unit_wise', [
                'formattedData' => $formattedData,
                'getdashdata' => $request
            ]);
        } catch (\Exception $ex) {
            return back()->with('error', 'Failed to load unit-wise accident data.');
        }
    }
    public function getInjurypart(Request $request)
    {
        try {
            $countBodyPart = $this->bodyparts->countBodyPart();
            $data = array(
                'countBodyPart' => $countBodyPart
            );
            return view('admin.dashboard.injurypartchart', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }

    public function injurybodycount(Request $request)
    {
        $request = request();
        $parts = $request->input('part');

        $listResp = FacadesDB::table('ims_initial_incident as inc')
            ->select([
                'body.id as bodyids',
                'inc.id as inveeid',
            ])
            ->leftJoin('ims_injury_details as inj', 'inj.incident_id', '=', 'inc.id')
            ->leftJoin('ims_incident_body_parts as body', 'body.injury_id', '=', 'inj.id')
            ->where('inc.trash', 'NO')
            ->where('body.body_parts_label', 'like', '%' . $parts . '%') // Correct LIKE usage
            ->get();

        $inveeid = [];
        $response = [];

        if (!$listResp->isEmpty()) {
            foreach ($listResp as $lists) {
                $inveeid[] = $lists->inveeid;
            }

            $response['count'] = count($listResp);
            $response['inc_id'] = $inveeid;
        } else {
            $response['count'] = 0;
            $response['inc_id'] = [];
        }

        return response()->json($response);
    }

    public function IIRTypeWiseRCPA(Request $request)
    {
        try {
            $chartData = $this->ims_incident->getTypeofIIRRCPACountData($request);

            if ($chartData->isEmpty()) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }

            $data = [
                'chartData' => $chartData->map(function ($item) {
                    return [
                        'incident_type_name' => $item->incident_type_name,
                        'iir_type' => $item->iir_type,
                        'total_incident' => $item->total_incident,
                        'total_rcpa' => $item->total_rcpa
                    ];
                })->toArray(),
            ];


            return view('admin.dashboard.iir_wise_rcpa', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }



    public function gembaWalkObservation(Request $request)
    {
        try {
            $chartData = $this->gembaWalk->gembaWalkPotentialCount();

            if (count($chartData) <= 0) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }



            $chartDataSeries = [
                'Unsafe Act' => [],
                'Unsafe Condition' => [],
            ];

            $categories = [];

            foreach ($chartData as $data) {
                $categories[] = $data['unit_name'];
                foreach ($chartDataSeries as $key => $value) {
                    $chartDataSeries[$key][] = isset($data[$key]) ? $data[$key] : 0;
                }
            }

            $chartData = [
                'categories' => $categories,
                'series' => [
                    ['name' => 'Unsafe Act', 'data' => $chartDataSeries['Unsafe Act']],
                    ['name' => 'Unsafe Condition', 'data' => $chartDataSeries['Unsafe Condition']],
                ],
            ];

            return view('admin.dashboard.gembaWalkObservation', [
                'chartData' => $chartData
            ]);
        } catch (\Exception $ex) {
            report($ex);

            return back()->with('error', 'Failed to load unit-wise incident data.');
        }
    }

    public function DailyObservationMonthCount()
    {
        try {
            $chartData = $this->gembaWalk->DailyObservationMonthCount();

            if (count($chartData) <= 0) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }

            $categories = [];
            $chartDataSeries = [
                'open' => [],
                'closed' => [],
                'total' => [],
            ];

            foreach ($chartData as $data) {
                $categories[] = $data['unit_name'];

                $chartDataSeries['open'][] = $data['open'];
                $chartDataSeries['closed'][] = $data['closed'];
                $chartDataSeries['total'][] = $data['total'];
            }

            $chartData = [
                'categories' => $categories,
                'series' => [
                    ['name' => 'Open', 'data' => $chartDataSeries['open']],
                    ['name' => 'Closed', 'data' => $chartDataSeries['closed']],
                    ['name' => 'Total', 'data' => $chartDataSeries['total']],
                ],
            ];


            return view('admin.dashboard.observationCountMonthly', [
                'chartData' => $chartData
            ]);
        } catch (\Exception $ex) {
            report($ex);
            return back()->with('error', 'Failed to load unit-wise incident data.');
        }
    }


    public function IIRTypeWiseUAUC(Request $request)
    {
        try {
            $chartData = $this->ims_incident->getTypeofIIRUAUCCountData($request);

            if ($chartData->isEmpty()) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }
            $data = [
                'chartData' => $chartData,
            ];

            return view('admin.dashboard.iir_wise_uauc', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }


    public function uaucStaticReport(Request $request)
    {
        try {
            $chartData = $this->ims_incident->getTypeofUAUCStatusCountData($request);

            if ($chartData->isEmpty()) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }

            // Format for ApexCharts
            $units = $chartData->pluck('unit_name')->unique()->values()->all();
            $totals = [];
            $opens = [];
            $closeds = [];

            foreach ($chartData as $row) {
                $totals[] = ($row->ua_total + $row->uc_total);
                $opens[] = ($row->ua_open + $row->uc_open);
                $closeds[] = ($row->ua_closed + $row->uc_closed);
            }

            $series = [
                ['name' => 'Total', 'data' => $totals],
                ['name' => 'Open', 'data' => $opens],
                ['name' => 'Closed', 'data' => $closeds],
            ];

            return view('admin.dashboard.unit_wise_uauc', compact('series', 'units', 'request'));
        } catch (\Exception $ex) {
            report($ex);
        }
    }

    public function nearMissFrequency(Request $request)
    {
        try {
            $chartData = $this->ims_incident->getNearMissCountData($request);

            if ($chartData->isEmpty()) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }

            $data = [
                'chartData' => $chartData->toArray(),
            ];

            return view('admin.dashboard.near_miss_frequency', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }
    public function auditFindings(Request $request)
    {
        try {
            $from = $request->Fromdate ? DBdateformat($request->Fromdate) : null;
            $to   = $request->Todate ? DBdateformat($request->Todate) : null;

            $auditAssessmentCount = DB::table('inspection_audit_assessment')
                ->when($from && $to, fn($q) => $q->whereBetween('created_at', [$from, $to]))
                ->when($from && !$to, fn($q) => $q->where('created_at', '>=', $from))
                ->when(!$from && $to, fn($q) => $q->where('created_at', '<=', $to))
                ->count('id');

            $auditAnalysisCount = DB::table('inspection_audit_analysis')
                ->when($from && $to, fn($q) => $q->whereBetween('created_at', [$from, $to]))
                ->when($from && !$to, fn($q) => $q->where('created_at', '>=', $from))
                ->when(!$from && $to, fn($q) => $q->where('created_at', '<=', $to))
                ->count('id');

            $interUnitCount = DB::table('inspection_audit_inter_unit')
                ->when($from && $to, fn($q) => $q->whereBetween('created_at', [$from, $to]))
                ->when($from && !$to, fn($q) => $q->where('created_at', '>=', $from))
                ->when(!$from && $to, fn($q) => $q->where('created_at', '<=', $to))
                ->count('id');

            $auditMonthlyCount = DB::table('inspection_audit_monthly_audit_plan')
                ->when($from && $to, fn($q) => $q->whereBetween('created_at', [$from, $to]))
                ->when($from && !$to, fn($q) => $q->where('created_at', '>=', $from))
                ->when(!$from && $to, fn($q) => $q->where('created_at', '<=', $to))
                ->count('id');
            $total = $auditAssessmentCount + $auditAnalysisCount + $interUnitCount + $auditMonthlyCount;

            if ($total === 0) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data available.</h4></div>');
            }

            $data = [
                'auditAssessmentCount' => $auditAssessmentCount,
                'auditAnalysisCount' => $auditAnalysisCount,
                'interUnitCount' => $interUnitCount,
                'auditMonthlyCount' => $auditMonthlyCount,
                'hasData' => $total,
                'getdashdata' => (object)[
                    'Fromdate' => $request->Fromdate,
                    'Todate' => $request->Todate,
                ]
            ];

            return view('admin.dashboard.auditFindings', $data);
        } catch (\Exception $ex) {
            report($ex);
            return back()->withErrors('An error occurred while processing the audit findings.');
        }
    }


    public function unitwiseptw(Request $request)
    {
        try {
            $user = Auth::user();
            $id = Auth::id();


            $unit = $this->unit
                ->select('id', 'unit_name')
                ->where('status', 1)
                ->where('trash', 'NO')
                ->get();


            $permitCountsQuery = $this->ptw
                ->selectRaw('unit_id, COUNT(*) as permit_count')
                ->where('status', 1)
                ->where('trash', 'NO')
                ->groupBy('unit_id');



            $company_id = $request->input('CompanyId');
            if ($company_id) {
                $companyId = decryptId($company_id);
                $permitCountsQuery->where('company_id', $companyId);
            }

            if ($request->Fromdate && $request->Todate) {
                $permitCountsQuery->whereBetween('created_at', [
                    DBdateformat($request->Fromdate),
                    DBdateformat($request->Todate) . ' 23:59:59'
                ]);
            } elseif ($request->Fromdate) {
                $permitCountsQuery->where('created_at', '>=', DBdateformat($request->Fromdate));
            } elseif ($request->Todate) {
                $permitCountsQuery->where('created_at', '<=', DBdateformat($request->Todate) . ' 23:59:59');
            }

            $permitCounts = $permitCountsQuery->get()->pluck('permit_count', 'unit_id');

            $result = $unit->map(function ($unit) use ($permitCounts) {
                return [
                    'unit_id' => $unit->id,
                    'unit_name' => $unit->unit_name,
                    'permit_count' => $permitCounts[$unit->id] ?? 0,
                ];
            });


            return view('admin.dashboard.unitwisecount', [
                'unitData' => $result,
            ]);
        } catch (\Exception $ex) {
            report($ex);
            return back()->withErrors('An error occurred');
        }
    }


    public function monthwiseptw(Request $request)
    {
        try {
            $permitCountsQuery = $this->ptw
                ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as permit_count')
                ->where('status', 1)
                ->where('trash', 'NO');

            $company_id = $request->input('CompanyId');
            if ($company_id) {
                $companyId = decryptId($company_id);
                $permitCountsQuery->where('company_id', $companyId);
            }

            if ($request->Fromdate && $request->Todate) {
                $permitCountsQuery->whereBetween('created_at', [
                    DBdateformat($request->Fromdate),
                    DBdateformat($request->Todate) . ' 23:59:59'
                ]);
            } elseif ($request->Fromdate) {
                $permitCountsQuery->where('created_at', '>=', DBdateformat($request->Fromdate));
            } elseif ($request->Todate) {
                $permitCountsQuery->where('created_at', '<=', DBdateformat($request->Todate) . ' 23:59:59');
            }
            $permitCounts = $permitCountsQuery
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();

            $result = [];
            $currentYear = date('Y');
            for ($month = 1; $month <= 12; $month++) {
                $result[$month] = 0;
            }

            foreach ($permitCounts as $count) {
                if ($count->year == $currentYear) {
                    $result[$count->month] = $count->permit_count;
                }
            }

            if (count($permitCounts) <  0) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }
            return view('admin.dashboard.monthwisecount', [
                'monthlyCounts' => $result,
            ]);
        } catch (\Exception $ex) {
            report($ex);
            return back()->withErrors('An error occurred');
        }
    }

    // Training Management

    // Training Open Close

    public function getTrainingOpenClose(Request $request)
    {
        try {
            $chartData = $this->training_schedule->getTrainingOpenClose($request);


            $row = $chartData[0] ?? null;

            if (!$row || $row->training_total_count == 0) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }

            $formattedData = [
                'closed' => $row->closed_count,
                'open' => $row->open_count,
                'closed_percentage' => $row->closed_percentage,
                'open_percentage' => $row->open_percentage,
            ];

            return view('admin.dashboard.training_open_close', [
                'formattedData' => $formattedData,
                'getdashdata' => $request,
            ]);
        } catch (\Exception $ex) {
            report($ex);
            return back()->with('error', 'Failed to load training completion data.');
        }
    }


    // Training topic wise data
    public function trainingTopicWise(Request $request)
    {
        try {
            $dates = [
                'from_date' => $request->input('FromDate'),
                'to_date' => $request->input('ToDate')
            ];

            $training_data = $this->training_schedule->getTopicWiseTraining();


            if ($training_data->isEmpty()) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }

            $chartData = [
                'labels' => [],
                'series' => [],
                'topic_name' => [],
                'training_topic_id' => []
            ];

            foreach ($training_data as $item) {
                $chartData['labels'][] = $item->topic_name;
                $chartData['series'][] = (float) $item->total_hours;
                $chartData['topic_name'][] = $item->department_name;
                $chartData['training_topic_id'][] = $item->training_topic_id;
            }

            return view('admin.dashboard.training_hour_department_wise', [
                'training_data' => $training_data,
                'chartData' => $chartData,
                'getdashdata' => (object) $dates
            ]);
        } catch (\Exception $ex) {
            report($ex);
        }
    }
    // month wise data
    public function getmonthwiseTraining(Request $request)
    {
        try {
            $chartData = $this->training_schedule->monthwiseTrainingCountData();
            if ($chartData->isEmpty()) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }

            $pendingCounts = array_fill(1, 12, 0);
            $rejectedCounts = array_fill(1, 12, 0);
            $inProgressCounts = array_fill(1, 12, 0);
            $completedCounts = array_fill(1, 12, 0);

            foreach ($chartData as $data) {
                $pendingCounts[$data->month] = $data->pending_count;
                $rejectedCounts[$data->month] = $data->rejected_count;
                $inProgressCounts[$data->month] = $data->inprogress_count;
                $completedCounts[$data->month] = $data->completed_count;
            }


            $months = [
                'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'December'
            ];



            return view('admin.dashboard.monthwisetraining', [


                'chartData' => [
                    'categories' => $months,
                    'series' => [
                        [
                            'name' => 'Pending',
                            'data' => array_values($pendingCounts),
                            'id' => 'pending'
                        ],
                        [
                            'name' => 'Rejected',
                            'data' => array_values($rejectedCounts),
                            'id' => 'rejected'
                        ],
                        [
                            'name' => 'In Progress',
                            'data' => array_values($inProgressCounts),
                            'id' => 'inprogress'
                        ],
                        [
                            'name' => 'Completed',
                            'data' => array_values($completedCounts),
                            'id' => 'completed'
                        ],
                    ]
                ]

            ]);
        } catch (\Exception $ex) {
            report($ex);
            return back()->with('error', 'Failed to load month-wise Training data.');
        }
    }

    // department wise schedule count
    public function getDepartment(Request $request)
    {
        try {
            $chartData = $this->training_schedule->getDepartmentData();

            if ($chartData->isEmpty()) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }


            $departmentDetails = $this->department->select('id', 'department_name')->get()->keyBy('id');

            $chartDataArray = [];
            foreach ($chartData as $data) {
                $deptId = $data->department_id;
                if (isset($departmentDetails[$deptId])) {
                    $chartDataArray[$deptId] = $data->count;
                }
            }


            $chartDataArray = array_filter($chartDataArray, fn($count) => $count > 0);

            $data = [
                'getdashdata' => $request,
                'departmentDetails' => $departmentDetails,
                'chartDataArray' => $chartDataArray
            ];

            return view('admin.dashboard.departmentData', $data);
        } catch (\Exception $ex) {
            report($ex);
            return response()->json('An error occurred.');
        }
    }

    // not addede in the main dashboard training status wise count
    public function gettrainingStatusCount(Request $request)
    {
        try {
            $chartData = $this->training_schedule->getTrainingCount();

            if ($chartData) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }

            // Prepare data for the pie chart
            $chartDataArray = [
                'Pending' => $chartData->pending_count ?? 0,
                'Rejected' => $chartData->rejected_count ?? 0,
                'In Progress' => $chartData->inprogress_count ?? 0,
                'Completed' => $chartData->completed_count ?? 0,
            ];
            $data = [
                'getdashdata' => $request,
                'chartDataArray' => $chartDataArray
            ];
            return view('admin.dashboard.trainingstatusCount', $data);
        } catch (\Exception $ex) {
            report($ex); // Debug any errors during execution
        }
    }
}
