<?php

namespace App\Http\Controllers\Inspection\Fire;

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
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Safety\Master\Equipment;
use App\Models\Inspection\Safety\FireSafetyEquipment;
use App\Models\Inspection\Fire\MonthlyPhysicalInspection;
use App\Models\Inspection\Safety\FireSafetyEquipmentDetails;
use App\Models\Inspection\Safety\MonthlyPhysicalEquipmentList;
use App\Models\Inspection\Fire\MonthlyPhysicalInspectionFileUpload;

class MonthlyPhysicalInspectionController extends Controller
{
    private $safety_equipment;
    private $safety_equipment_details;
    private $equipment;
    private $document_reference;
    private $equipment_list;
    private $location;
    private $unit;
    private $monthly_inspection_file_upload;


    public function __construct()
    {
        $this->safety_equipment = new MonthlyPhysicalInspection();
        $this->equipment = new Equipment();
        $this->document_reference = new InspectionStaticDocno();
        $this->equipment_list = new MonthlyPhysicalEquipmentList();
        $this->location = new Location();
        $this->unit = new Unit();
        $this->monthly_inspection_file_upload = new MonthlyPhysicalInspectionFileUpload();
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
                            if ($row->equipment_status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->inspection_id) . "' data-type = '1'>Active</span>";
                            } else if ($row->equipment_status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->inspection_id) . "' data-type = '0'>In-Active</span>";
                            }
                            // }
                            return $text;
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('fire/equipment-monthly-physical-inspection/view/' . encryptId($row->inspection_id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('fire/equipment-monthly-physical-inspection/exportViewPdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                                <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                            </a>';
                            return $btn;
                        })
                        ->addColumn('created_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('date_of_inspection', function ($row) {
                            return Displaydateformat($row->date_of_inspection);
                        })
                        ->addColumn('standard_norms', function ($row) {
                            if ($row->status == STANDARD) {
                                $text = "<span  data-id='" . encryptId($row->inspection_id) . "' data-type = '1'>Standard</span>";
                            } else if ($row->status == NORMS) {
                                $text = "<span data-id='" . encryptId($row->inspection_id) . "' data-type = '0'>Norms</span>";
                            }
                            return $text;
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', 'inspection_status', 'date_of_inspection', 'standard_norms'])
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

        $equipment = $this->equipment->get();
        $location = $this->location->getLocationName();
        $unit = $this->unit->getUnit();

        $data = array(
            'equipment' => $equipment,
            'locations' => $location,
            'units' => $unit,
        );
        return view('inspection.fire.monthly_physical_inspection.list', $data);
    }

    public function Add(Request $request)
    {
        try {
            $equipment = $this->equipment->get();
            $document_no = $this->document_reference->selectUsingName('FireEquipmentMonthlyPhysicalInspection');
            $equipment_list = $this->equipment_list->getEquipmentList();
            $location = $this->location->getLocationName();
            $unit = $this->unit->getUnit();


            $data = array(
                'equipment' => $equipment,
                'document_no' => $document_no,
                'equipment_list' => $equipment_list,
                'locations' => $location,
                'units' => $unit,
            );
            return view('inspection.fire.monthly_physical_inspection.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/equipment-monthly-physical-inspection/list'));
        }
    }

    public function Store(Request $request)
    {
        try {


            $rules = [
                'inspection_date' => 'required',
                'location_id' => 'required',
                'unit_id' => 'required',
                'status.*' => 'required',
                'remarks.*' => 'required',
                'equipment.*' => 'required',
            ];

            $messages = [
                'inspection_date.required' => 'Inspection Date is required.',
                'location_id.required' => 'Location is required.',
                'unit_id.required' => 'Unit is required.',
                'status.*.required' => 'Status is required.',
                'remarks.*.required' => 'Remarks are required.',
                'equipment.*.required' => 'Equipment is required.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $fire_safety_equipment = $this->safety_equipment->store();
            $image_upload = $this->monthly_inspection_file_upload->store($fire_safety_equipment->id);


            Session::flash('success', 'Equipment Name is Added Successfully');
            return redirect(admin_url('fire/equipment-monthly-physical-inspection/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/equipment-monthly-physical-inspection/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $inspection_details = $this->safety_equipment->selectOne($id);
            $images = $this->monthly_inspection_file_upload->GetFile($inspection_details->id);
            $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);
            $inspection_data = json_decode($inspection_details->inspected_data, true);

            $data = array(
                'inspection_details' => $inspection_details,
                'document_no' => $document_no,
                'images' => $images,
                'inspection_data' => $inspection_data,
            );



            return view('inspection.fire.monthly_physical_inspection.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/equipment-monthly-physical-inspection/list'));
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

            $writer = SimpleExcelWriter::streamDownload('Fire Equipment Monthly Physical Inspection Details.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/equipment-monthly-physical-inspection/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->safety_equipment->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            } else if (count($allData) > 20) {
                return redirect()->back()->with('error', __('inspection.excess_error'));
            }

            $data = array(
                'content' => $allData,
                'pagetitle' => "Fire Equipment Monthly Physical Inspection Details",
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

            $view = view('inspection.fire.monthly_physical_inspection.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Fire Equipment Monthly Physical Inspection Details.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('fire/equipment-monthly-physical-inspection/list'));
        }
    }


    public function exportViewPdf(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $inspection_details = $this->safety_equipment->selectOne($id);
                $images = $this->monthly_inspection_file_upload->GetFile($inspection_details->id);
                $document_no = $this->document_reference->selectOne($inspection_details->document_reference_id);
                $inspection_data = json_decode($inspection_details->inspected_data, true);

                $data = [
                    'inspection_details' => $inspection_details,
                    'inspection_data' => $inspection_data,
                    'images' => $images,
                    'pagetitle' => "Fire Equipment Monthly Physical Inspection List",
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

            $html = view('inspection.fire.monthly_physical_inspection.viewPdf', $data);
            $view = $html->render();
            $mpdf->WriteHTML($view);

            $filename = "Fire Equipment Monthly Physical Inspection List.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            Session::flash('error', 'Something went wrong !');
            return redirect(admin_url('fire/equipment-monthly-physical-inspection/list'));
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->safety_equipment->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => __('Equipment Status is changed')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('administration.please_try_after_some_time')], 406);
        }
    }

    public function UniqueCheck(Request $request)
    {
        if ($request->ajax()) {
            $item_code  = $request->item_code;
            $equipment_name = decryptId($request->equipment_name);
            $isUnique = $this->safety_equipment->UniqueCheck($item_code, $equipment_name);
            return response()->json($isUnique);
        }
    }
    public function Equipmentunique(Request $request)
    {
        if ($request->ajax()) {
            $equipment_name = decryptId($request->equipment_name);
            $isUnique = $this->safety_equipment->EquipmentUniqueCheck($equipment_name);
            return response()->json($isUnique);
        }
    }
}
