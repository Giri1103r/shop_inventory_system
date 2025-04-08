<?php

namespace App\Http\Controllers\Inspection\Ohc;

use Exception;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use App\Models\Master\Location;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Models\Inspection\Master\Frequency;
use App\Models\Inspection\Ohc\OhcSignature;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\ohc\FirstAidBagChecklist;
use App\Models\Inspection\Ohc\Master\FirstAidEquipment;

class FirstAidBagChecklistController extends Controller
{
    private $medicine_checklist;
    private $medicine;
    private $signature;
    private $document_reference;
    private $location;
    private $unit;
    private $frequency;


    public function __construct()
    {
        $this->medicine_checklist = new FirstAidBagChecklist();
        $this->medicine = new FirstAidEquipment();
        $this->signature = new OhcSignature();
        $this->document_reference = new InspectionStaticDocno();
        $this->location  = new Location();
        $this->unit = new Unit();
        $this->frequency = new Frequency();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->medicine_checklist->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active</span>";
                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->inspection_id) . "' data-type='1'>Active</span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->inspection_id) . "' data-type='0'>In-Active</span>";
                            }
                            return $text;
                        })
                        ->addColumn('created_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('inspection_date', function ($row) {
                            return Displaydateformat($row->inspection_date);
                        })
                        ->addColumn('next_due', function ($row) {
                            return Displaydateformat($row->next_due);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })

                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('ohc/emergency-floor-first-aid-bag/checklist/view/' . encryptId($row->inspection_id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';

                            if ($row->inspection_status == OBSERVATION_PENDING &&  isAdmin()) {
                                $btn .= '<a href="' . admin_url('ohc/emergency-floor-first-aid-bag/checklist/approval/' . encryptId($row->inspection_id)) . '" class="" title="Action"><i class="fa-solid fa-check-to-slot text-success"></i></a> ';
                            }

                            $btn .= '<a href="' . admin_url('ohc/emergency-floor-first-aid-bag/checklist/exportViewpdf/' . encryptId($row->inspection_id)) . '" style="margin-right: 5px;" title="PDF">
                            <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                        </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', 'inspection_date', 'next_due'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);
                    return $datatables;
                } catch (Exception $ex) {
                    dd($ex);
                    report($ex);
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }

        $location = $this->location->getLocationName();
        $unit = $this->unit->getUnit();
        $frequency = $this->frequency->getFrequency();

        $data = array(
            'locations' => $location,
            'units' => $unit,
            'frequency' => $frequency,
        );

        return view('inspection.inspection_ohc.first_aid_bag_inspection.list', $data);
    }

    public function Add(Request $request)
    {
        try {

            $medicines = $this->medicine->getFirstAidData();
            $document_no = $this->document_reference->selectUsingName('EmergencyFloorFirstAidBagChecklist');
            $location = $this->location->getLocationName();
            $unit = $this->unit->getUnit();
            $frequency = $this->frequency->getFrequency();

            $data = array(
                'medicines' => $medicines,
                'document_no' => $document_no,
                'locations' => $location,
                'units' => $unit,
                'frequency' => $frequency,

            );
            return view('inspection.inspection_ohc.first_aid_bag_inspection.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/emergency-floor-first-aid-bag/checklist/list'));
        }
    }

    public function Store(Request $request)
    {
        try {

            $rules = [
                'inspection_date' => 'required',
                'frequency_id' => 'required',
                'location_id' => 'required',
                'unit_id' => 'required',
                'next_due' => 'required',
                'available_quantity.*' => 'required',
                'expired_date.*' => 'required',
                'emp_id.*' => 'required',
                'remarks.*' => 'required'
            ];

            $messages = [
                'inspection_date.required' => 'Inspection Date is required.',
                'frequency_id.required' => 'Frequency is required.',
                'location_id.required' => 'Location is required.',
                'unit_id.required' => 'Unit is required.',
                'next_due.required' => 'Next Due Date is required.',
                'available_quantity.*.required' => 'Available Quantity is required',
                'expired_date.*.required' => 'Expired Date is required',
                'remarks.*' => 'Remarks is required',
                'emp_id.*' => 'Employee is required',
                'signature_upload' => 'Signature is required.',
            ];


            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $store = $this->medicine_checklist->store();
            $inspection_type = FIRST_AID_BAG_INSPECTION_CHECKLIST;
            $inspection_details = $this->medicine_checklist->selectOne($store->id);
            $files = $this->signature->requestorsignatureUpload($inspection_type, $inspection_details->id);

            Session::flash('success', 'Your data has been added successfully');
            return redirect(admin_url('ohc/emergency-floor-first-aid-bag/checklist/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/emergency-floor-first-aid-bag/checklist/list'));
        }
    }

    public function View(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_details = $this->medicine_checklist->selectOne($id);
            $inspection_type = FIRST_AID_BAG_INSPECTION_CHECKLIST;
            $signature = GetOHCSignature($inspection_details->created_by, $inspection_details->id, $inspection_type);
            $inspection_data = json_decode($inspection_details->inspection_data, true);

            $data = array(
                'inspection_details' => $inspection_details,
                'signature' => $signature,
                'inspection_data' => $inspection_data,
            );


            return view('inspection.inspection_ohc.first_aid_bag_inspection.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/emergency-floor-first-aid-bag/checklist/list'));
        }
    }

    public function ExportExcel()
    {
        try {
            $allData = $this->medicine_checklist->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Date of Inspection',
                'Next Due',
                'Status',
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  Displaydateformat($data->inspection_date);
                $export[] =  Displaydateformat($data->next_due);
                $export[] =  ($data->status == '1' ? 'Active' : 'Inactive');
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);
                $exportData[] = $export;
                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('FIRST AID BAG INSPECTION CHECKLIST.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong!');
            return redirect(admin_url('ohc/emergency-floor-first-aid-bag/checklist/list'));
        }
    }

    public function ExportPDF()
    {
        try {
            $allData = $this->medicine_checklist->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Date of Inspection',
                'Next Due',
                'Status',
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "FIRST AID BAG INSPECTION CHECKLIST",
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

            $view = view('inspection.inspection_ohc.first_aid_bag_inspection.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "FIRST AID BAG INSPECTION CHECKLIST.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/emergency-floor-first-aid-bag/checklist/list'));
        }
    }

    public function ExportViewPDF(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_detail = $this->medicine_checklist->selectOne($id);
            $inspection_type = FIRST_AID_BAG_INSPECTION_CHECKLIST;
            $inspection_file = $this->signature->getFiles($id, $inspection_type);
            $inspection_data = json_decode($inspection_detail->inspection_data, true);
            $inspection_created_by = GetOHCSignature($inspection_detail->created_by, $inspection_detail->id, $inspection_type);


            $property = [
                'tempDir' => 'public/pdf/temp/',
                'mode' => 'c',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,

            ];

            $data = array(
                'inspection_detail' => $inspection_detail,
                'inspection_file' => $inspection_file,
                'pagetitle' => "FIRST AID BAG INSPECTION CHECKLIST",
                'inspection_data' => $inspection_data,
                'inspection_created_by' => $inspection_created_by,
            );


            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';

            $html = view('inspection.inspection_ohc.first_aid_bag_inspection.viewpdf', $data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "FIRST AID BAG INSPECTION CHECKLIST.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/emergency-floor-first-aid-bag/checklist/list'));
        }
    }

    public function approval(Request $request)
    {
        try {

            $id = decryptId($request->id);
            $inspection_details = $this->medicine_checklist->selectOne($id);
            $inspection_type = FIRST_AID_BAG_INSPECTION_CHECKLIST;
            $inspection_file = GetOHCSignature($inspection_details->created_by, $inspection_details->id, $inspection_type);
            $inspection_data = json_decode($inspection_details->inspection_data, true);
            $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);

            $data = array(
                'inspection_details' => $inspection_details,
                'inspection_file' => $inspection_file,
                'inspection_data' => $inspection_data,
                'document_no' => $document_no,

            );

            return view('inspection.inspection_ohc.first_aid_bag_inspection.approval', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('ohc/emergency-floor-first-aid-bag/checklist/list'));
        }
    }

    public function approvalSubmit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $status = $request->has('approved') ? 1 : 0;
            $remarks = $request->capa_remarks;
            $eye_wash_inspection = $this->medicine_checklist->approvalSubmit($id, $status, $remarks);
            $inspection_details = $this->medicine_checklist->selectOne($id);
            $signature_update = $this->signature->signatureUpload(FIRST_AID_BAG_INSPECTION_CHECKLIST);
            // $ehsOfficer = GetEHSOfficer();
            // $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            // if ($status == 1) {
            //     $message = 'FORKLIFT INSPECTION - OBSERVATION APPROVED';
            //     $to_status = OBSERVATION_APPROVED;
            // } else {
            //     $message = 'FORKLIFT INSPECTION - OBSERVATION APPROVED';
            //     $to_status = OBSERVATION_REJECTED;
            // }
            // $web_link =   admin_url('ohc/emergency-floor-first-aid-bag/checklist/view/' . encryptId($inspection_details->id));
            // $mailsubject = 'SAFETY INSPECTION';
            // $notificationData = array(
            //     'notification_type' => SAFETY_INSPECTION,
            //     'module_type' => 1,
            //     'notification_message' => $mailsubject,
            //     'mobile_notification' => json_encode(array(
            //         'title' => $mailsubject,
            //         'message' => $message,
            //         'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
            //         'id' => $inspection_details->id,
            //         'module' => 1,
            //     )),
            //     'web_link' =>  $web_link,
            //     'assigned_user' => array_to_string($ehsOfficers),
            //     'created_by' => Auth::id(),
            // );
            // notificationSave($notificationData);
            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('ohc/emergency-floor-first-aid-bag/checklist/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something Went wrong!');
            return redirect(admin_url('ohc/emergency-floor-first-aid-bag/checklist/list'));
        }
    }
}
