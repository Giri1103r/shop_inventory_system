<?php

namespace App\Http\Controllers\Inspection\Safety;

use Exception;
use App\Models\UploadLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Safety\Master\Equipment;
use App\Models\Inspection\Safety\FireSafetyEquipment;
use App\Models\Inspection\Safety\FireSafetyEquipmentDetails;

class FireSafetyEquipmentController extends Controller
{
    private $safety_equipment;
    private $safety_equipment_details;
    private $equipment;
    private $document_reference;


    public function __construct()
    {
        $this->safety_equipment = new FireSafetyEquipment();
        $this->equipment = new Equipment();
        $this->safety_equipment_details = new FireSafetyEquipmentDetails();
        $this->document_reference = new InspectionStaticDocno();

    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->safety_equipment->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active</span>";
                            // if (CheckUserRole(ROLE_SUPERADMIN)) {
                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->id) . "' data-type = '1'>Active</span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->id) . "' data-type = '0'>In-Active</span>";
                            }
                            // }
                            return $text;
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('safety/fire-safety-equipment/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('safety/fire-safety-equipment/exportViewPdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                        <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                    </a>';
                            return $btn;
                        })
                        ->addColumn('created_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('issue_date', function ($row) {
                            return Displaydateformat($row->issue_date);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', 'inspection_status', 'issue_date'])
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

        $data = array();
        return view('inspection.Safety.safety_equipment.list', $data);
    }

    public function Add(Request $request)
    {
        try {
            $equipment = $this->equipment->get();
            $document_no = $this->document_reference->selectUsingName('ListofFireSafetyEquipment');

            $data = array(
                'equipment' => $equipment,
                'document_no' => $document_no,
            );
            return view('inspection.Safety.safety_equipment.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('safety/fire-safety-equipment/list'));
        }
    }

    public function Store(Request $request)
    {
        try {

            $rules = [
                'issue_date' => 'required',

                'equipment_name.*' => 'required',
                'item_code.*' => 'required',
                'standard_norms.*' => 'required',
                'equipment_category.*' => 'required',
                'unit_of_measurement.*' => 'required',
                'minimum_order_value.*' => 'required',
                'economic_order_quantity.*' => 'required',
                'observation_status.*' => 'required',
                'remarks.*' => 'required',
                'signature_upload' => [
                    function ($attribute, $value, $fail) {
                        $user = Auth::user();
                        if (is_null($user->signature_upload)) {
                            $fail('Signature is required.');
                        }
                    }
                ],
            ];

            $messages = [
                'doc_no.required' => 'Document number is required.',
                'issue_date.required' => 'Issue Date is required.',
                'equipment_name.*.required' => 'Equipment Name is required.',
                'item_code.*.required' => 'Item code  is required.',
                'standard_norms.*.required' => 'Standard Norms is required.',
                'equipment_category.*.required' => 'Equipment Category is required.',
                'unit_of_measurement.*.required' => 'Unit of measurement is required.',
                'minimum_order_value.*.required' => 'Minimum order value is required.',
                'economic_order_quantity.*.required' => 'Economic Order Quantity is required.',
                'observation_status.*.required' => 'Observation Status is required.',
                'remarks.*.required' => 'Remarks is required.',
                'signature_upload' => 'Signature is required.',
            ];


            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $fire_safety_equipment_store = $this->safety_equipment->store();
            $fire_safety_id = $fire_safety_equipment_store->id;
            $inspection_details = $this->safety_equipment->selectOne($fire_safety_id);
            $fire_safety_equipment_details = $this->safety_equipment_details->store($fire_safety_id);
            Session::flash('success', 'Equipment Name is Added Successfully');
            return redirect(admin_url('safety/fire-safety-equipment/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('safety/fire-safety-equipment/list'));
        }
    }

    public function GetEquipment(Request $request)
    {
        try {
            $locations = $this->equipment->GetEquipment();
            return response()->json($locations);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['error' => 'Please try again after sometimes'], 406);
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $inspection_details = $this->safety_equipment->selectOne($id);
            $inspection = $this->safety_equipment_details->GetDetails($inspection_details->id);
            $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);

            $data = array(
                'inspection' => $inspection,
                'inspection_details' => $inspection_details,
                'document_no' => $document_no,
            );

            return view('inspection.Safety.safety_equipment.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('safety/fire-safety-equipment/list'));
        }
    }

    public function ExportExcel(Request $request)
    {

        try {
            $allData = $this->safety_equipment->exportdata();
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
                $export[] = $data->revision_data;
                $export[] =  ($data->status == 1) ? 'Active' : 'InActive';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);
                $exportData[] = $export;
                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Safety Equipment Details.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('safety/fire-safety-equipment/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->safety_equipment->exportdata();
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

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Safety Equipment Details",
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

            $view = view('inspection.safety.safety_equipment.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Safety Equipment Details.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('safety/fire-safety-equipment/list'));
        }
    }


    public function exportViewPdf(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $inspection_details = $this->safety_equipment->selectOne($id);
                $inspection = $this->safety_equipment_details->GetDetails($inspection_details->id);
                $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);

                $data = [
                    'inspection_details' => $inspection_details,
                    'inspection' => $inspection,
                    'pagetitle' => "Safety Equipment List",
                    'document_no' => $document_no,
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

            $html = view('inspection.Safety.safety_equipment.viewPdf', $data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "Safety Equipment List.pdf";
            return $mpdf->Output($filename, 'i');
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('safety/fire-safety-equipment/list'));
        }
    }
}
