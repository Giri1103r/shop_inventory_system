<?php

namespace App\Http\Controllers\Inspection\Ohc;

use App\Http\Controllers\Controller;
use App\Models\Inspection\Master\Frequency;
use App\Models\Inspection\Master\Shift;
use App\Models\Inspection\Ohc\EmergencyBuyerFirstAidChecklist;
use App\Models\Inspection\Ohc\Master\FirstAidEquipment;
use App\Models\Inspection\Ohc\OhcSignature;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Exception;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Spatie\SimpleExcel\SimpleExcelWriter;





class EmergencyBuyerFirstAidBagChecklistController extends Controller
{
    private $unit;
    private $shift;
    private $medicine;
    private $emergency_buyer_first_aid_bag;
    private $signature;
    private $frequency;



    public function __construct()
    {
        $this->unit = new Unit();
        $this->shift = new Shift();
        $this->medicine = new FirstAidEquipment();
        $this->emergency_buyer_first_aid_bag = new EmergencyBuyerFirstAidChecklist();
        $this->signature = new OhcSignature();
        $this->frequency = new Frequency();


    }
    public function Index(Request $request){
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->emergency_buyer_first_aid_bag->list();
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
                        ->addColumn('date_of_inspection', function ($row) {
                            return Displaydateformat($row->date_of_inspection);
                        })
                        ->addColumn('created_at', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })

                        ->addColumn('action', function ($row) {
                            $btn = '<a href="' . admin_url('ohc/emergency-buyer-first-aid-bag/checklist/view/' . encryptId($row->id)) . '" class="view-icon" title="' . __('common.view') . '">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>';
                        
                            $btn .= '<a href="' . admin_url('ohc/emergency-buyer-first-aid-bag/checklist/generalpdf/' . encryptId($row->id)) . '" style="margin-left: 5px;" title="PDF">
                                        <i class="fas fa-file-pdf" style="color: #e67265;" aria-hidden="true"></i>
                                    </a>';
                        
                            return $btn;
                        })
                        
                        ->rawColumns(['action', 'created_by', 'date_of_inspection', 'created_at'])
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
        $unit = $this->unit->getunit();
        $shift = $this->shift->getShiftname();
        $data = array(
            'unit'=>$unit,
            'shift'=>$shift
        );
        return view('inspection.inspection_ohc.emergency_buyer_bag_inspection.list',$data   );
    }

    public function Add(Request $request)
    {
        try {
            $unit = $this->unit->getunit();
            $shift = $this->shift->getShiftname();
            $medicines = $this->medicine->getFirstAidData();
            $frequency = $this->frequency->getFrequency();
            $data = array(
                'unit' => $unit,
                'shift' => $shift,
                'medicines' => $medicines,
                'frequency' => $frequency,

            );
            
            return view('inspection.inspection_ohc.emergency_buyer_bag_inspection.add',$data);


        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {
        try {
            $rules = [
                'date_of_inspection' => 'required',
                'location_first_aid_bag' => 'required',
                'shift' => 'required',
                'next_due_date' => 'required',
                'unit_id' => 'required',
                'frequency_id' => 'required',
                'medicine_id.*' => 'required',
                'freeze_quantity.*' => 'required',
                'available_quantity.*' => 'required',
                'expired_date.*' => 'required',
                'remarks.*' => 'required',
                'remark_by' => 'required',
            ];
            
            $messages = [
                'date_of_inspection.required' => 'Date of inspection is required.',
                'location_first_aid_bag.required' => 'Location of the first aid bag is required.',
                'shift.required' => 'Shift selection is required.',
                'next_due_date.required' => 'Next due date is required.',
                'unit_id.required' => 'Unit selection is required.',
                'frequency_id.required' => 'Frequency is required.',
                'medicine_id.*.required' => 'Medicine ID is required.',
                'freeze_quantity.*.required' => 'Freeze quantity is required.',
                'available_quantity.*.required' => 'Available quantity is required.',
                'expired_date.*.required' => 'Expired date is required.',
                'remarks.*.required' => 'Remarks are required.',
                'remark_by.required' => 'Remark by is required.',
            ];
            
            $validator = Validator::make($request->all(), $rules, $messages);
            
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            
            try {

                $emergency_buyer_first_aid_bag = $this->emergency_buyer_first_aid_bag->store();
                $emergency_buyer_first_aid_bag_id = $emergency_buyer_first_aid_bag->id;
                $inspection_type = OHC_TYPE_EMERGENCY_BUYER_FIRST_AID_BAG_CHECKLIST;
                $inspection_details = $this->emergency_buyer_first_aid_bag->selectOne($emergency_buyer_first_aid_bag_id);
                $files = $this->signature->requestorsignatureUpload($inspection_type, $inspection_details->id);

                Session::flash('success', 'Your data has been created successfully!');
                return redirect(admin_url('ohc/emergency-buyer-first-aid-bag/checklist/list'));
                
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }
            
        } catch (Exception $ex) {
            report($ex);
        }
    }


    public function View(Request $request)
    {
        try {
            
            $id = decryptId($request->id);
            $inspection_details = $this->emergency_buyer_first_aid_bag->selectOne($id);
            $inspection_type = OHC_TYPE_EMERGENCY_BUYER_FIRST_AID_BAG_CHECKLIST;
            $inspection_data = json_decode($inspection_details->inspection_data, true);
            $inspection_file = GetOHCSignature($inspection_details->created_by, $inspection_details->id, $inspection_type);

            $data = array(
                'inspection_details' => $inspection_details,
                'inspection_file' => $inspection_file,
                'inspection_data' => $inspection_data,
            );

            return view('inspection.inspection_ohc.emergency_buyer_bag_inspection.view',$data);

        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/monthly-medicine-store/inspection/list'));
        }
    }

    public function generalpdf(Request $request)
    {
        try {

            if (Auth::check()) {
                $id = decryptId($request->id);
                $inspection_details = $this->emergency_buyer_first_aid_bag->selectOne($id);
                $inspection_type = OHC_TYPE_EMERGENCY_BUYER_FIRST_AID_BAG_CHECKLIST;
                $inspection_data = json_decode($inspection_details->inspection_data, true);
                $inspection_created_by = GetOHCSignature($inspection_details->created_by, $inspection_details->id, $inspection_type);

                $data = array(
                    'inspection_details' => $inspection_details,
                    'inspection_created_by' => $inspection_created_by,
                    'inspection_data' => $inspection_data,
                );
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

            $html = view('inspection.inspection_ohc.emergency_buyer_bag_inspection.generalpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "Emergency Buyer Bag Inspection Details.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            return redirect()->back()->withErrors(['error' => 'An error occurred while generating the PDF.']);
        }
    }

    public function ExportExcel(Request $request)
    {

        try {
            $allData = $this->emergency_buyer_first_aid_bag->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Date Of Inspection',
                'Location First Aid Bag',
                'Shift',
                'Unit',
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {
                $export = [];
                $export[] =  $i;
                $export[] =  Displaydateformat($data->date_of_inspection);
                $export[] =  $data->location_first_aid_bag;
                $export[] =  getShift($data->shift_id);
                $export[] =  getUnitname($data->unit_id);
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);
                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Emergency Buyer Bag Inspection.xlsx')
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

            ini_set("pcre.backtrack_limit", "5000000");

            $allData = $this->emergency_buyer_first_aid_bag->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }elseif(count($allData) > 20){
                return redirect()->back()->with('error',   __('inspection.excess_error'));
            }


            $data = array(
                'content' => $allData,
                'pagetitle' => "Emergency Buyer Bag Inspection",
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

            $view = view('inspection.inspection_ohc.emergency_buyer_bag_inspection.pdf', $data);
            $html = $view->render();


            $mpdf->WriteHTML($html);

            $filename = "Emergency Buyer Bag Inspection.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
        }
    }
}
