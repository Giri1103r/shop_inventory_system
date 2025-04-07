<?php

namespace App\Http\Controllers\Inspection\Ohc;

use App\Http\Controllers\Controller;
use App\Models\Inspection\Ohc\HealthInstrumentCalibration;
use App\Models\Inspection\Ohc\HealthInstrumentCalibrationDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Master\Unit;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Exception;

class HealthInstrumentCalibrationController extends Controller
{

    private $health_instrument_calibration;
    private $health_instrument_calibration_details;
    private $unit;

    public function __construct()
    {
        $this->health_instrument_calibration = new HealthInstrumentCalibration();
        $this->health_instrument_calibration_details = new HealthInstrumentCalibrationDetails();
        $this->unit = new Unit();

    }

    
    public function Index(Request $request){
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->health_instrument_calibration->list();
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
                        ->addColumn('created_at', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('issue_date', function ($row) {
                            return Displaydateformat($row->issue_date);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })

                        ->addColumn('action', function ($row) {
                            $btn = '<a href="' . admin_url('ohc/health-instrument/calibration-track-sheet/view/' . encryptId($row->id)) . '" class="view-icon" title="' . __('common.view') . '">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>';
                        
                            $btn .= '<a href="' . admin_url('ohc/health-instrument/calibration-track-sheet/generalpdf/' . encryptId($row->id)) . '" style="margin-left: 5px;" title="PDF">
                                        <i class="fas fa-file-pdf" style="color: #e67265;" aria-hidden="true"></i>
                                    </a>';
                        
                            return $btn;
                        })
                        
                        ->rawColumns(['action', 'issue_date', 'created_by', 'status', 'created_at'])
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

        return view('inspection.inspection_ohc.health_instrument.list');
    }


    public function Add(Request $request)
    {
        try {
            $unitList = $this->unit->getUnitList();

            $data = array(
                'unitList' => $unitList,
            );
            
            return view('inspection.inspection_ohc.health_instrument.add',$data);

        } catch (Exception $ex) {
            report($ex);
        }
    }
    
    public function Store(Request $request)
    {
        try {
            $rules = [
                'document_no' => 'required',
                'issue_date' => 'required',
                'review_date' => 'required',
                'health_instrument.*.sr_no' => 'required',
                'health_instrument.*.instrument_name' => 'required',
                'health_instrument.*.resource_code' => 'required',
                'health_instrument.*.exact_location' => 'required',
                'health_instrument.*.unit_id' => 'required',
                'health_instrument.*.instrument_serial_no' => 'required',
                'health_instrument.*.make' => 'required',
                'health_instrument.*.model' => 'required',
                'health_instrument.*.instrument_range' => 'required',
                'health_instrument.*.calibration_frequency' => 'required',
                'health_instrument.*.date_of_calibration' => 'required',
                'health_instrument.*.due_date_of_calibration' => 'required',
                'health_instrument.*.instrument_remarks' => 'required',
            ];
            
            $messages = [
                'document_no.required' => 'Document number is required.',
                'issue_date.required' => 'Issue date is required.',
                'review_date.required' => 'Review date is required.',
                'health_instrument.*.sr_no.required' => 'Serial number is required.',
                'health_instrument.*.instrument_name.required' => 'Please enter the instrument name.',
                'health_instrument.*.resource_code.required' => 'Please enter the resource code.',
                'health_instrument.*.exact_location.required' => 'Please specify the exact location.',
                'health_instrument.*.unit_id.required' => 'Please select a unit.',
                'health_instrument.*.instrument_serial_no.required' => 'Instrument serial number is required.',
                'health_instrument.*.make.required' => 'Please enter the make of the instrument.',
                'health_instrument.*.model.required' => 'Please enter the model of the instrument.',
                'health_instrument.*.instrument_range.required' => 'Instrument range is required.',
                'health_instrument.*.calibration_frequency.required' => 'Calibration frequency is required.',
                'health_instrument.*.date_of_calibration.required' => 'Please select the date of calibration.',
                'health_instrument.*.due_date_of_calibration.required' => 'Please select the due date of calibration.',
                'health_instrument.*.instrument_remarks.required' => 'Remarks are required.',
            ];
            
            $validator = Validator::make($request->all(), $rules, $messages);
            
            
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }  
            try {

                $health_instrument = $this->health_instrument_calibration->store();
                $id = $health_instrument->id;

                $health_instrument_details = $this->health_instrument_calibration_details->store($id);

                Session::flash('success', 'Your data has been created successfully!');
                return redirect(admin_url('ohc/health-instrument/calibration-track-sheet/list'));

            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function view(Request $request,$id)
    {
        try {
            if (Auth::check()) {

                $id = decryptId($id);
                $health_instrument_calibration_details = $this->health_instrument_calibration->selectOne($id);

                $data = array(
                    'health_instrument_calibration_details' => $health_instrument_calibration_details,
                    
                );

            }
            return view('inspection.inspection_ohc.health_instrument.view',$data);

        } catch (Exception $ex) {
            dd($ex);
            report($ex);
        }
    }


    public function ExportPdf(Request $request)
    {
        try {
            $allData = $this->health_instrument_calibration->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Document Number',
                'Issue Date',
                'Revision Date',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Health Instrument Calibration Details",
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
            $view = view('inspection.inspection_ohc.health_instrument.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Heakth Instrument Calibration.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/health-instrument/calibration-track-sheet/list'));
        }
    }

    public function ExportExcel(Request $request)
    {
        try {
            $allData = $this->health_instrument_calibration->exportdata();
            
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Document Number',
                'Issue Date',
                'Revision Date',
                'Status',
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->doc_no;
                $export[] =  $data->issue_date;
                $export[] = $data->revision_date;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);
                $exportData[] = $export;
                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Health Instrument Calibration.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('inspection/gemba-walk/list'));
        }
    }

    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);
            
            if (Auth::check()) {
                $health_instrument_calibration_details = $this->health_instrument_calibration->selectOne($id);

                $data = [
                   'health_instrument_calibration_details' => $health_instrument_calibration_details,
                ];
            }

            $property = [
                'tempDir' => 'public/pdf/temp/',
                'mode' => 'c',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,

            ];

            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';

            $html = view('inspection.inspection_ohc.health_instrument.generalpdf',$data)->render();
            $mpdf->WriteHTML($html);

            $filename = "Health instrument Calibration Details.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            return redirect()->back()->withErrors(['error' => 'An error occurred while generating the PDF.']);
        }
    }

    public function StatusChange(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $this->health_instrument_calibration->statuschange($id);


            return response()->json(['status' => 'success', 'msg' => 'Your status  has changed Successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }
        
}

