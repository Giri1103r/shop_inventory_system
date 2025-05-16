<?php

namespace App\Http\Controllers\KPI;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\KPI\HSCInputs;
use App\Models\KPI\HSCInputsLagging;
use App\Models\KPI\HSCInputsLeading;
use App\Models\KPI\LeadingLagging;
use App\Models\Master\Company;
use App\Models\Master\Department;
use App\Models\Master\Location;
use App\Models\Master\Unit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;

class HSCInputsController extends Controller
{
    private $hsc_inputs;
    private $company;
    private $location;
    private $unit;
    private $department;
    private $leading_lagging;
    private $leading;
    private $lagging;

    public function __construct()
    {
        $this->hsc_inputs = new HSCInputs();
        $this->company = new Company();
        $this->location = new Location();
        $this->unit = new Unit();
        $this->department = new Department();
        $this->leading_lagging = new LeadingLagging();
        $this->leading = new HSCInputsLeading();
        $this->lagging = new HSCInputsLagging();
    }


    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->hsc_inputs->list();
                    $datatables = DataTables::of($data['data'])
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
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('kpi/master/hsc-inputs/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('kpi/master/hsc-inputs/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', 'type'])
                        ->setFilteredRecords($data['total_records'])
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
        $companies = $this->company->getCompany();
        $locations = $this->location->getLocationName();
        $units = $this->unit->getUnit();
        $department = $this->department->getdepartment();

        $data = [
            'companies' => $companies,
            'locations' => $locations,
            'units' => $units,
            'department' => $department,
        ];
        return view('kpi.master.hsc_inputs.list', $data);
    }

    public function Add(Request $request)
    {
        $companies = $this->company->getCompany();
        $locations = $this->location->getLocationName();
        $units = $this->unit->getUnit();
        $department = $this->department->getdepartment();
        $leading = $this->leading_lagging->getLeading();
        $lagging = $this->leading_lagging->getLagging();

        $data = [
            'companies' => $companies,
            'locations' => $locations,
            'units' => $units,
            'department' => $department,
            'leadings' => $leading,
            'laggings' => $lagging,
        ];

        try {
            return view('kpi.master.hsc_inputs.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {
        try {
            $rules = [
                'company_id' => 'required',
                'location_id' => 'required',
                'unit_id' => 'required',
                'department_id' => 'required',
                'year' => 'required',
                'month' => 'required',
                'financial_year' => 'required',
            ];
            $messages = [
                'company_id.required' => 'Please Select Type',
                'location_id.required' => 'Please Select Type',
                'unit_id.required' => 'Please Select Type',
                'department_id.required' => 'Please Select Type',
                'year.required' => 'Please Enter Value',
                'month.required' => 'Please Enter Value',
                'financial_year.required' => 'Please Enter Value',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            try {
                $hsc_inputs =  $this->hsc_inputs->store();
                $leading =  $this->leading->store($hsc_inputs->id);
                $lagging =  $this->lagging->store($hsc_inputs->id);
                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                dd($ex);
                report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('kpi/master/hsc-inputs/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('kpi/master/hsc-inputs/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $hsc_inputs = $this->hsc_inputs->selectOne($id);
                $leadings = $this->leading->selectUsingLeading($hsc_inputs->id);
                $laggings = $this->lagging->selectUsingLagging($hsc_inputs->id);

                $data = array(
                    'hsc_inputs' => $hsc_inputs,
                    'leadings' => $leadings,
                    'laggings' => $laggings,

                );
            }
            return view('kpi.master.hsc_inputs.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $hsc_inputs = $this->hsc_inputs->selectOne($id);
            $leadings = $this->leading->selectUsingLeading($hsc_inputs->id);
            $laggings = $this->lagging->selectUsingLagging($hsc_inputs->id);
            $companies = $this->company->getCompany();

            $data = array(
                'hsc_inputs' => $hsc_inputs,
                'leadings' => $leadings,
                'laggings' => $laggings,
                'companies' => $companies,

            );

            return view('kpi.master.hsc_inputs.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rules = [
                'company_id' => 'required',
                'location_id' => 'required',
                'unit_id' => 'required',
                'department_id' => 'required',
                'year' => 'required',
                'month' => 'required',
                'financial_year' => 'required',
            ];
            $messages = [
                'company_id.required' => 'Please Select Type',
                'location_id.required' => 'Please Select Type',
                'unit_id.required' => 'Please Select Type',
                'department_id.required' => 'Please Select Type',
                'year.required' => 'Please Enter Value',
                'month.required' => 'Please Enter Value',
                'financial_year.required' => 'Please Enter Value',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $hsc_inputs = $this->hsc_inputs->updates($id);
            $leading =  $this->leading->updates($id);
            $lagging =  $this->lagging->updates($id);

            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('kpi/master/hsc-inputs/list'));
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('kpi/master/hsc-inputs/list'));
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->hsc_inputs->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Leading and Lagging status changed'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->hsc_inputs->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __("common.type"),
                __("common.value"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] = $data->type == LEADING ? __('common.leading') : __('common.lagging');
                $export[] =  $data->value;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Leading and Lagging.xlsx')
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

            $allData = $this->hsc_inputs->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __("common.type"),
                __("common.value"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Leading and Lagging Details",
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

            $view = view('kpi.master.hsc_inputs.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Leading and Lagging Master.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function list(Request $request, $companyId)
    {

        $companyId = decryptId($companyId);
        $id = decryptId($request->id);
        $hsc_inputss = $this->hsc_inputs->ajaxList($id, $companyId);

        return response()->json($hsc_inputss);
    }


    public function alllist(Request $request)
    {
        $companyId = decryptId($request->type);
        $hsc_inputss = $this->hsc_inputs->ajaxallList($companyId);
        return response()->json($hsc_inputss);
    }

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $company_id = decryptId($request->company_id);
            $location_id = decryptId($request->location_id);
            $unit_id = decryptId($request->unit_id);
            $department_id = decryptId($request->department_id);
            $year = ($request->year);
            $month = ($request->month);
            $id = $request->id;
            if ($id == '') {
                $record = $this->hsc_inputs->uniqueCheck($company_id, $location_id, $unit_id, $department_id, $year, $month);
            } else {
                $id = decryptId($id);
                $record = $this->hsc_inputs->ExistuniqueCheck($company_id, $location_id, $unit_id, $department_id, $year, $month, $id);
            }
            if ($record->count()) {
                return Response::json(false);
            }
            return Response::json(true);
        }
    }
}
