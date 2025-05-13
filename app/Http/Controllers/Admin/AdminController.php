<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Master\Employee;
use DB;
use Exception;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use App\Models\User;
use App\Models\IMS\Incident\InitialIncident;
use App\Models\Permit\SafetyPermit;
use App\Models\Master\TrainingSchedule;
use App\Models\IMS\Incident\IncidentBodyParts;
use Illuminate\Support\Facades\DB as FacadesDB;

class AdminController extends Controller
{


    private $bodyparts;
    private $training_schedule;
    private $ims_incident;
    private $ptw;

    private $incident_ims;

    public function __construct()
    {
        $this->bodyparts = new IncidentBodyParts();
        $this->incident_ims = new InitialIncident();
        $this->training_schedule = new TrainingSchedule();
        $this->ims_incident = new InitialIncident();
        $this->ptw = new SafetyPermit();
        $this->training_schedule = new TrainingSchedule();
    }

    public function index()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $data = [];
            if ((in_array(ROLE_SUPERADMIN, getUserRoleId(Auth::id())) || in_array(ROLE_ADMIN, getUserRoleId(Auth::id())))) {
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

                $data = [
                    'masterLink' => $masterLink,
                ];
            }
            if (Auth::user()->role == ROLE_SUPERADMIN || Auth::user()->role == ROLE_ADMIN) {
                return view('admin.dashboard', $data);
            } else {
                return view('admin.userdashboard', $data);
            }
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


    public function getTotalIncident(Request $request)
    {
        try {
            $chartData = $this->incident_ims->getTotalIncidentCountData($request);

            if ($chartData->isEmpty()) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }

            $formattedData = [];
            foreach ($chartData as $row) {
                $unit = $row->unit_name ?? 'Unknown Unit';
                $type = $row->incident_type_name ?? 'Unknown Type';

                if (!isset($formattedData[$type])) {
                    $formattedData[$type] = [];
                }

                if (!isset($formattedData[$type][$unit])) {
                    $formattedData[$type][$unit] = 0;
                }

                $formattedData[$type][$unit]++;
            }
            return view('admin.dashboard.totalIncidentsCount', [
                'formattedData' => $formattedData,
                'getdashdata' => $request,
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

            // Format data for the chart
            $formattedData = [];
            foreach ($chartData as $row) {
                $type = $row->incident_type_name ?? 'Unknown Type';
                $formattedData[$type] = (int) $row->incident_count;
            }

            return view('admin.dashboard.incidentTypeconut', [ // Make sure your blade file name matches
                'formattedData' => $formattedData,
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
                'to_date' =>  $request->input('ToDate')
            ];
            $active_close_count = $this->ptw->ActiveVsClose();

            if ($active_close_count == null) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 150px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }

            $data = [
                'active_close_count' => $active_close_count,
                'dates' => $dates,
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
                'to_date' =>  $request->input('ToDate')
            ];

            $work_wise_count = $this->ptw->GetTypeWiseCount();

            $data = [
                'work_wise_count' => $work_wise_count,
                'dates' => $dates,
            ];

            return view('admin.dashboard.ptw_type_wise_count', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }

    public function TrainingHoursSafetyDepartmentWise(Request $request)
    {
        try {
            $dates = [
                'from_date' => $request->input('FromDate'),
                'to_date' =>  $request->input('ToDate')
            ];

            $training_data = $this->training_schedule->GetTrainingHoursDepartmentData($request);

            $chartData = [
                'labels' => [],
                'series' => [],
                'departments' => []
            ];

            foreach ($training_data as $item) {
                $chartData['labels'][] = $item->topic_name;
                $chartData['series'][] = (float) $item->total_hours;
                $chartData['departments'][] = $item->department_name;
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

    public function ptwholdviolation(Request $request)
    {
        try {

            $dates = [
                'from_date' => $request->input('FromDate'),
                'to_date' =>  $request->input('ToDate')
            ];

            $hold_count = $this->ptw->getHoldStatus($request);

            $data = [
                'hold_count' => $hold_count,
                'dates' => $dates,
            ];

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
            $chartData = getPPERequestChartData($form_date, $to_date);

            $data = [
                'getdashdata' => $request,
            ];

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
            $chartData = getPTWAvgTimeChartData($form_date, $to_date);

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
            $chartData = getPPEAvailabilityChartData($form_date, $to_date);

            return view('admin.dashboard.chartPPEAvailability', [
                'getdashdata' => $request,
                'chartData' => $chartData,
            ]);
        } catch (\Exception $ex) {
            report($ex);
        }
    }

    public function getTrainingCompletionCount(Request $request)
    {
        try {
            $chartData = $this->training_schedule->getTrainigCompletionCountData($request);

            if (empty($chartData) || $chartData->training_total_count == 0) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }

            $formattedData = [
                'total' => $chartData->training_total_count,
                'closed' => $chartData->closed_count,
                'open' => $chartData->open_count,
                'closed_percentage' => $chartData->closed_percentage,
                'open_percentage' => $chartData->open_percentage,
            ];

            return view('admin.dashboard.training_completion', [
                'formattedData' => $formattedData,
                'getdashdata' => $request,
            ]);
        } catch (\Exception $ex) {
            report($ex);
            return back()->with('error', 'Failed to load training completion data.');
        }
    }

    public function getTypeofIIRCount(Request $request)
    {
        try {
            $chartData = $this->ims_incident->getTypeofIIRCountData($request);

            // Format data for chart: separate arrays for names and counts
            $formattedData = [
                'labels' => $chartData->pluck('incident_type_name'),
                'counts' => $chartData->pluck('total'),
            ];

            return view('admin.dashboard.type_of_irr', [
                'formattedData' => $formattedData,
                'getdashdata' => $request,
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

            $formattedData = [
                'labels' => $chartData->pluck('unit_name'),
                'major' => $chartData->pluck('major'),
                'minor' => $chartData->pluck('minor'),
                'fatal' => $chartData->pluck('fatal'),
            ];

            return view('admin.dashboard.accident_report_unit_wise', [
                'formattedData' => $formattedData,
                'getdashdata' => $request,
            ]);
        } catch (\Exception $ex) {
            report($ex);
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
            $data = [
                'chartData' => $chartData,
            ];
 
            return view('admin.dashboard.iir_wise_rcpa', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }
    public function IIRTypeWiseUAUC(Request $request)
    {
        try {
            $chartData = $this->ims_incident->getTypeofIIRUAUCCountData($request);
            $data = [
                'chartData' => $chartData,
            ];

            return view('admin.dashboard.iir_wise_uauc', $data);
        } catch (\Exception $ex) {
            report($ex);
        }
    }

    public function nearMissFrequency(Request $request)
    {
        try {
            $chartData = $this->ims_incident->getNearMissCountData($request);
            // dd($chartData);

            $data = [
                'chartData' => $chartData,
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

            $data = [
                'auditAssessmentCount' => $auditAssessmentCount,
                'auditAnalysisCount' => $auditAnalysisCount,
                'interUnitCount' => $interUnitCount,
                'auditMonthlyCount' => $auditMonthlyCount,
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
}
