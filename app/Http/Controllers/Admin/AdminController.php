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
use App\Models\Inspection\GembaWalk\GembaWalk;
use Illuminate\Support\Facades\DB as FacadesDB;
use App\Models\Inspection\GembaWalk\GembaWalkChecklist;
use App\Models\Master\Company;


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

    private $incident_ims;

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
    }

    public function index(Request $request)
    {
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $data = [];
                if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_ADMIN) || CheckUserRole(ROLE_EHS_HEAD)) {
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
                if (CheckUserRole(ROLE_SUPERADMIN) || CheckUserRole(ROLE_ADMIN) || CheckUserRole(ROLE_EHS_HEAD)) {
                    return view('admin.dashboard', $data);
                } else {
                    return view('admin.userdashboard', $data);
                }
            }
        } catch (\Exception $ex) {
            dd($ex);
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
            dd($ex);
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
            dd($ex);
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
            dd($ex);
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
            dd($ex);
            Session::flash('error', 'Please try after sometimes!');
            return redirect()->back();
        }
    }


    public function getTotalIncident(Request $request)
    {
        try {
            $chartData = $this->incident_ims->getTotalIncidentCountData($request);
            // if ($chartData->isEmpty()) {
            //     return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            // }
            $formattedData = [];
            $incidentTypeIds = [];
            $unitIds = [];
            $lookup = [];

            foreach ($chartData as $row) {
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
            // dd($chartData);
            return view('admin.dashboard.totalIncidentsCount', [
                'formattedData' => $formattedData,
                'incidentTypeIds' => $incidentTypeIds,
                'unitIds' => $unitIds,
                'lookup' => $lookup,
                'getdashdata' => $request,
                'chartData' => $chartData,
            ]);
        } catch (\Exception $ex) {
            dd($ex);
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
            dd($ex);
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
            dd($ex);
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
            dd($ex);
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
            dd($ex);
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

            // if (empty($work_wise_count) || array_sum($work_wise_count) === 0) {
            //     return response()->json([
            //         'html' => '<div class="border-0 pb-3" style="margin-top: 150px;"><h4 style="text-align: center;">No data Found.</h4></div>',
            //         'status' => 'empty'
            //     ]);
            // }
            $data = [
                'work_wise_count' => $work_wise_count,
                'dates' => $dates,
            ];

            return view('admin.dashboard.ptw_type_wise_count', $data);
        } catch (\Exception $ex) {
            dd($ex);
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

            if ($training_data->isEmpty()) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }

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
            if ($training_data->isEmpty()) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }
            return view('admin.dashboard.training_hour_department_wise', [
                'training_data' => $training_data,
                'chartData' => $chartData,
                'getdashdata' => (object) $dates
            ]);
        } catch (\Exception $ex) {
            dd($ex);
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
            $data = [
                'hold_count' => $hold_count,
                'dates' => $dates,
            ];
            if (empty($hold_count)) {
                return response()->json('<div class="border-0 pb-3" style="margin-top: 166px;"><h4 style="text-align: center;">No data Found.</h4></div>');
            }
            return view('admin.dashboard.ptw_hold_wise_count', $data);
        } catch (\Exception $ex) {
            dd($ex);
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
            dd($ex);
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
            dd($ex);
        }
    }

    public function getPPEAvailabilityChart(Request $request)
    {
        try {

            $form_date = $request->input('Fromdate');
            $to_date = $request->input('Todate');
            $company_id = $request->input('CompanyId');
            $chartData = getPPEAvailabilityChartData($form_date, $to_date, $company_id);

            return view('admin.dashboard.chartPPEAvailability', [
                'getdashdata' => $request,
                'chartData' => $chartData,
            ]);
        } catch (\Exception $ex) {
            dd($ex);
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
            dd($ex);
            return back()->with('error', 'Failed to load training completion data.');
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
            ];

            return view('admin.dashboard.type_of_irr', [
                'formattedData' => $formattedData,
                'getdashdata' => $request,
            ]);
        } catch (\Exception $ex) {
            dd($ex);
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
            dd($ex);
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
            dd($ex);
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
            dd($ex);
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
                    $chartDataSeries[$key][] = $data[$key];
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
            dd($ex);
            dd($ex);
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
            dd($ex);
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
            dd($ex);
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
            dd($ex);
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
                'hasData' => ($auditAssessmentCount + $auditAnalysisCount + $interUnitCount + $auditMonthlyCount) > 0,
                'getdashdata' => (object)[
                    'Fromdate' => $request->Fromdate,
                    'Todate' => $request->Todate,
                ]
            ];

            return view('admin.dashboard.auditFindings', $data);
        } catch (\Exception $ex) {
            dd($ex);
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

            // dd($result);

            return view('admin.dashboard.unitwisecount', [
                'unit' => $unit,
                'unitData' => $result,
            ]);
        } catch (\Exception $ex) {
            dd($ex);
            return back()->withErrors('An error occurred');
        }
    }


    public function monthwiseptw(Request $request)
    {
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
        // dd($result);

        return view('admin.dashboard.monthwisecount', [
            'monthlyCounts' => $result,
        ]);
    }
    public function gettrainingStatusCount(Request $request)
    {
        try {
            $chartData = $this->training_schedule->getTrainingCount();

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
            dd($ex); // Debug any errors during execution
        }
    }


    public function getDepartment(Request $request)
    {
        try {
            $chartData = $this->training_schedule->getDepartmentData();
            $departmentDetails = $this->department->select('department_name', 'id')->get();

            // Initialize chartDataArray with all departments having count 0
            $chartDataArray = $departmentDetails->pluck('id', 'department_name')->mapWithKeys(function ($value, $key) {
                return [$key => 0];
            });

            // Fill chartDataArray with actual counts from the query
            foreach ($chartData as $data) {
                if (isset($chartDataArray[$data->department_name])) {
                    $chartDataArray[$data->department_name] = $data->count;
                }
            }

            $chartDataArray = $chartDataArray->filter(function ($count) {
                return $count > 0;
            });

            $data = [
                'getdashdata' => $request,
                'departmentDetails' => $departmentDetails,
                'chartDataArray' => $chartDataArray
            ];

            return view('admin.dashboard.departmentData', $data);
        } catch (\Exception $ex) {
            dd($ex); // Debug any errors during execution
        }
    }

    public function getmonthwiseTraining(Request $request)
    {
        try {
            // Fetch chart data
            $chartData = $this->training_schedule->monthwiseTrainingCountData();

            // Initialize count arrays for each month
            $overallCounts = array_fill(1, 12, 0);
            $pendingCounts = array_fill(1, 12, 0);
            $rejectedCounts = array_fill(1, 12, 0);
            $inProgressCounts = array_fill(1, 12, 0);
            $completedCounts = array_fill(1, 12, 0);

            // Populate counts based on fetched data
            foreach ($chartData as $data) {
                $overallCounts[$data->month] = $data->total_count;
                $pendingCounts[$data->month] = $data->pending_count;
                $rejectedCounts[$data->month] = $data->rejected_count;
                $inProgressCounts[$data->month] = $data->inprogress_count;
                $completedCounts[$data->month] = $data->completed_count;
            }

            // Prepare chart data array
            $chartDataArray = [];
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

            foreach ($months as $monthIndex => $monthName) {
                $chartDataArray[$monthName] = [
                    'pending' => $pendingCounts[$monthIndex + 1],
                    'rejected' => $rejectedCounts[$monthIndex + 1],
                    'in_progress' => $inProgressCounts[$monthIndex + 1],
                    'completed' => $completedCounts[$monthIndex + 1]
                ];
            }
            return view('admin.dashboard.monthwisetraining', [
                'getdashdata' => $request,
                'chartDataArray' => $chartDataArray
            ]);
        } catch (\Exception $ex) {
            dd($ex);
            return back()->with('error', 'Failed to load month-wise Training data.');
        }
    }
}
