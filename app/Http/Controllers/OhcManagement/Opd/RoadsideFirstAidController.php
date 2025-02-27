<?php

namespace App\Http\Controllers\OhcManagement\Opd;

use App\Http\Controllers\Controller;
use App\Models\OhcManagement\Opd\InjuredCondition;
use Exception;
use App\Models\OhcManagement\Opd\RoadsideFirstAid;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;

class RoadsideFirstAidController extends Controller
{
    private $ohc_opd_roadside_first_aid;
    private $injured_condition;

    public function __construct()
    {
        $this->ohc_opd_roadside_first_aid = new RoadsideFirstAid();
        $this->injured_condition = new InjuredCondition();
    }
    public function index(Request $request)
    {

        if (Auth::check()) {

            if ($request->ajax()) {

                try {

                    $data =  $this->ohc_opd_roadside_first_aid->list();

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
                        ->editColumn('person_condtion', function ($row) {
                            return ($row->injured_condtion);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })

                        ->addColumn('action', function ($row) {
                            $btn = '';
                            // if (CheckUserPermission('view')) {
                            $btn = '<a href="' . admin_url('ohc/roadside-first-aid/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            // if (CheckUserPermission('edit')) {
                            $btn .= '<a href="' . admin_url('ohc/roadside-first-aid/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            // }
                            // $btn .= '<a href="' . admin_url('ohc/roadside-first-aid/generalpdf/' . encryptId($row->id)) . '" class="" title="PDF"> <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i></a> ';

                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', 'date_of_incident', 'person_condtion'])
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

        return view('ohcmanagement.ohc-opd.roadside-firstaid.list', $data);
    }

    public function add()
    {
        try {

            $injuredCondition = $this->injured_condition->getinjurred();
            $data = [
                'injuredCondition' => $injuredCondition
            ];

            return view('ohcmanagement.ohc-opd.roadside-firstaid.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/roadside-first-aid/list'));
        }
    }

    public function Store(Request $request)
    {
        try {

            $rules = [
                'emp_name' => 'required',
                'date_of_incident' => 'required',
                'time_of_incident' => 'required',
            ];

            $messages = [
                'emp_name.required' => 'Employee name is required.',
                'date_of_incident.required' => 'Date of Incident is required.',
                'time_of_incident.required' => 'Time of Incident is required.',

            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {


                $this->ohc_opd_roadside_first_aid->store();


                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/roadside-first-aid/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/roadside-first-aid/list'));
        }
    }

    public function edit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $opd_roadside_first_aid = $this->ohc_opd_roadside_first_aid->selectOne($id);
            }
            $injuredCondition = $this->injured_condition->getinjurred();
            $data = array(
                'opd_roadside_first_aid' => $opd_roadside_first_aid,
                'injuredCondition' => $injuredCondition
            );
            return view('ohcmanagement.ohc-opd.roadside-firstaid.edit', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/roadside-first-aid/list'));
        }
    }

    public function update(Request $request)
    {
        $id = decryptId($request->id);

        try {

            $rules = [
                'emp_name' => 'required',
                'date_of_incident' => 'required',
                'time_of_incident' => 'required',
            ];

            $messages = [
                'emp_name.required' => 'Employee name is required.',
                'date_of_incident.required' => 'Date of Incident is required.',
                'time_of_incident.required' => 'Time of Incident is required.',

            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {


                $this->ohc_opd_roadside_first_aid->updates($id);


                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/roadside-first-aid/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/roadside-first-aid/list'));
        }
    }

    public function view(Request $request)
    {

        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $opd_roadside_first_aid = $this->ohc_opd_roadside_first_aid->selectOne($id);
            }

            $data = array(
                'opd_roadside_first_aid' => $opd_roadside_first_aid,

            );
            return view('ohcmanagement.ohc-opd.roadside-firstaid.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/roadside-first-aid/list'));
        }
    }
    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->ohc_opd_roadside_first_aid->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'RoadSide FirstAid status changed'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $emp_name = $request->emp_name;
            $id = $request->id;
            if ($id == '') {
                $record = $this->ohc_opd_roadside_first_aid->uniqueCheck($emp_name);
            } else {
                $id = decryptId($id);
                $record = $this->ohc_opd_roadside_first_aid->ExistuniqueCheck($emp_name, $id);
            }
            if ($record->count()) {
                return Response::json(false);
            }
            return Response::json(true);
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->ohc_opd_roadside_first_aid->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Injured Person Name',
                'Date of Incident',
                'Time of Incident',
                'Location of Incident',
                'Personal Condition',
                'First Aid Provided',
                'Transport Medical Facility',
                'First Aider Name',
                'Transport Method',
                'Incident Report Filled',
                'Remarks',
                __("common.status"),

                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->name;
                $export[] =  displaydateformat($data->date_of_incident);
                $export[] =  $data->time_of_incident;
                $export[] =  $data->location_of_incident;
                $export[] =  getPersonalCondition($data->person_condtion);
                $export[] =  $data->first_aid_provided;
                $export[] =  $data->transport_to_medical_facility == 1 ? 'Yes' : 'No';
                $export[] =  $data->first_aider_name;
                $export[] =  $data->transport_method;
                $export[] =  $data->incident_report_filled == 1 ? 'Yes' : 'No';
                $export[] =  $data->remarks;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Roadside First Aid.xlsx')
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

            $allData = $this->ohc_opd_roadside_first_aid->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Injured Person Name',
                'Date of Incident',
                'Time of Incident',
                'Location of Incident',
                'Personal Condition',
                'First Aid Provided',
                'Transport Medical Facility',
                'First Aider Name',
                'Transport Method',
                'Incident Report Filled',
                'Remarks',
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Roadside First Aid",
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

            $view = view('ohcmanagement.ohc-opd.roadside-firstaid.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Roadside First Aid.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
        }
    }

    // incident report

    public function incidentreport(Request $request)
    {
        $id = decryptId($request->id);

        if (Auth::check()) {

        }
        $data = [

            'pagetitle' => "Incident Report",
        ];

        $property = [
            'tempDir' => 'public/pdf/temp/',
            'mode' => 'c',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,

        ];

        $mpdf = new \Mpdf\Mpdf($property);
        $mpdf->setAutoTopMargin = 'stretch';

        $html = view('ohcmanagement.ohc-opd.roadside-firstaid.incidentreport', $data)->render();
        $mpdf->WriteHTML($html);

        $filename = "Incident Report.pdf";
        return $mpdf->Output($filename, 'I');
    }
}
