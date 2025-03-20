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
use App\Models\IMS\Master\HiraApproval;
use App\Models\IMS\Incident\HiraMoc;

class HiraController extends Controller
{

    private $hira;
    private $hira_approval;
    private $unit;
    private $location;
    private $department;
    private $user;
    private $uploadlog;
    private $hiramoc;


    public function __construct()
    {

        $this->hira = new Hira();
        $this->hira_approval = new HiraApproval();
        $this->unit = new Unit();
        $this->location = new Location();
        $this->department = new Department();
        $this->user = new User();
        $this->uploadlog = new UploadLog();
        $this->hiramoc = new HiraMoc();
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
                        ->editColumn('status_batch', function ($row) {

                            return "<span class='" . $row->bg_color . "' >" . $row->status_name . "</span>";
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
                            if ($row->hira_status == 1) {
                                $btn .= '<a href="' . admin_url('incident/hira-master/ehsapproval/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-circle-check" style="color:rgb(0, 37, 132);"></i> ';
                            }
                            // }

                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', 'status_batch'])
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
        $data = array();

        return view('ims.master.hira.list', $data);
    }

    public function Add(Request $request)
    {

        try {

            $accident_id = null;
            $incidentId = null;
            $fire_id = null;
            $hiramoc = null;
            $data = array(
                'incident_id' => $incidentId,
                'accident_id' => $accident_id,
                'fire_id' => $fire_id,
                'hiramoc_id' => $hiramoc,
            );
            return view('ims.master.hira.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }
    public function addNewHiraIncident(Request $request, $incidentId, $hiramoc)
    {
        try {
            $accident_id = null;
            $fire_id = null;
            $data = array(
                'incident_id' => $incidentId,
                'accident_id' => $accident_id,
                'fire_id' => $fire_id,
                'hiramoc_id' => $hiramoc,
            );
            return view('ims.master.hira.add', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }
    public function addNewHiraAccident(Request $request, $accidentId, $hiramoc)
    {
        try {
            $incident_id = null;
            $fire_id = null;
            $data = array(
                'incident_id' => $incident_id,
                'accident_id' => $accidentId,
                'fire_id' => $fire_id,
                'hiramoc_id' => $hiramoc,
            );
            return view('ims.master.hira.add', $data);
        } catch (Exception $ex) {
            dd($ex);
        }
    }
    public function addNewHiraFire(Request $request, $fireId, $hiramoc)
    {
        try {
            $incident_id = null;
            $accident_id = null;
            $data = array(
                'incident_id' => $incident_id,
                'accident_id' => $accident_id,
                'fire_id' => $fireId,
                'hiramoc_id' => $hiramoc,
            );
            return view('ims.master.hira.add', $data);
        } catch (Exception $ex) {
            dd($ex);
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


                $hiraStore = $this->hira->store();

                if ($request->incident_id || $request->accident_id || $request->fire_id) {

                        $hiraStore = $this->hiramoc->hiramocstore($hiraStore->id);
                }


                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {

                dd($ex);
                report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }
            if ($request->incident_id) {
                return redirect(admin_url('incident/initial-incident/investigation/' . ($request->incident_id)));
            } elseif ($request->accident_id) {
                return redirect(admin_url('accidentReport/investigation/' . ($request->accident_id)));
            } elseif ($request->fire_id) {
                return redirect(admin_url('incident/fire-incident/investigation/' . ($request->fire_id)));
            } else {
                return redirect(admin_url('incident/hira-master/list'));
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
    public function ehsapproval(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $hira = $this->hira->selectOne($id);

                $data = array(
                    'hira' => $hira,
                );
            }
            return view('ims.master.hira.ehsapproval', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }


    public function ehsApprovalSubmit(Request $request)
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
            if ($request->has('approve')) {
                $hira_status = 2;
            } else {
                $hira_status = 3;
            }
            $id =  $request->hira_id;
            $ehsReview = $this->hira_approval->ehsapproval($id);
            $this->hira->updateStatus($id, $hira_status);


            // if ($ehsReview->team_member) {
            //     $teamMemberIds = explode(',', $ehsReview->team_member);

            //     $employees = Employee::whereIn('id', $teamMemberIds)->get(['emp_name', 'email']);
            //     $mailsubject = 'Investigation Assigned';

            //     // Fetch incident details once, not inside the loop
            //     $incidentDetails = $this->initialincident->selectOne($incident_id);
            //     $incidentarray = $incidentDetails->toArray();

            //     foreach ($employees as $employee) {
            //         $username = $employee->emp_name;
            //         $email_id = $employee->email;

            //         if (!empty($email_id)) { // Corrected email validation
            //             $incidentarray['name'] = $username;
            //             $incidentarray['email_id'] = $email_id;
            //             $incidentarray['mail_subject'] = $mailsubject;

            //             Mail::to($email_id)->queue(new IncidentEmail($incidentarray));
            //         }
            //     }

            //     // Use incidentDetails for notification data
            //     $notificationData = array(
            //         'notification_type' => 3,
            //         'module_type' => 1,
            //         'notification_message' => $mailsubject,
            //         'mobile_notification' => json_encode(array(
            //             'title' => $mailsubject,
            //             'message' => 'Incident ' . $incidentDetails->sr_no . ' submitted by ' . getUsername($ehsReview->created_by),
            //             'icon' => admin_url('public/assets/icons/incident.png'),
            //             'id' => $incidentDetails->id,
            //             'module' => 1,
            //         )),
            //         'web_link' => admin_url('incident/initial-incident/review/' . encryptId($incidentDetails->id)),
            //         'assigned_user' => array_to_string($teamMemberIds),
            //         'created_by' => Auth::id(),
            //     );
            //     notificationSave($notificationData);

            //     // Insert status log
            //     $insert_array = array(
            //         'incident_id' => $incidentDetails->id,
            //         'from_status' => $incidentDetails->incident_status,
            //         'to_status' => $incident_status,
            //         'is_reject' => null,
            //         'remarks' => null,
            //         'approved_by' => Auth::id(),
            //     );
            //     $this->Statuslog->create($insert_array);
            // }


            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('incident/hira-master/list'));
        } catch (Exception $ex) {
            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('incident/hira-master/list'));
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
                $isUnique = !$this->hira->uniqueCheck($vendor_name, $license_no);
            } else {
                $id = decryptId($id);
                $isUnique = !$this->hira->existUniqueCheck($vendor_name, $license_no, $id);
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
                'Hira Status',
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
                    $export[] = "P - Physical Hazard";
                } elseif ($data->hazard_type == 2) {
                    $export[] = "C - Chemical Hazard";
                } elseif ($data->hazard_type == 3) {
                    $export[] = "B - Behavioral Hazard";
                } elseif ($data->hazard_type == 4) {
                    $export[] = "O - Other Hazard";
                }
                if ($data->hira_status == 1) {
                    $export[] = "EHS Head Approval Pending";
                } elseif ($data->hira_status == 2) {
                    $export[] = "Approved";
                } elseif ($data->hira_status == 3) {
                    $export[] = "EHS Head Approval Rejected";
                } else {
                    $export[] = "";
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
                'Hira Status',
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
