<?php

namespace App\Http\Controllers\Api\Inspection\Safety;

use Exception;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController;
use App\Mail\Inspection\Safety\SafetyInspection;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Safety\OHSPlantSummaryReport;

class OHSPlantSummary extends BaseController
{

    private $ohsreport;
    private $unit;
    private $document_reference;

    public function __construct()
    {
        $this->unit = new Unit();
        $this->ohsreport = new OHSPlantSummaryReport();
        $this->document_reference = new InspectionStaticDocno();
    }

    public function list()
    {
        if (Auth::user()) {
            $request = request();
            if ($request->has('search')) {
                if ($request->search != '' && $request->search != null) {
                    $search = $request->search;
                }
            }
            $query = OHSPlantSummaryReport::select(
                'inspection_safety_ohs_report.*',
            );
            $org_total_counts = $query->count();


            if (!empty($search)) {
                $searchDate = ($search);
                $query->where(function ($query) use ($searchDate) {
                    $query->orWhereRaw("DATE_FORMAT(inspection_safety_ohs_report.inspection_date, '%d-%m-%Y') LIKE ?", ["%{$searchDate}%"]);
                });
            }

            $query_array = $query->orderBy('inspection_safety_ohs_report.id', 'DESC')->paginate($request->input('per_page', 10));

            $inspection_list = $query_array->toArray();

            if (empty($inspection_list['data'])) {
                return $this->sendError('No records found.', [], 404);
            }


            $data_array = [];
            foreach ($inspection_list['data'] as $datas) {
                $data = [];
                $data['id'] = $datas['id'] ?? '';
                $data['date_of_inspection'] = Displaydateformat($datas['inspection_date']);
                $data['updated_frequency'] = ($datas['updated_frequency'] ?? '');
                $data['created_by'] = getUsername($datas['created_by'] ?? '');
                $data['created_at'] = Displaydateformat($datas['created_at'] ?? '');
                $data_array[] = $data;
            }

            $inspection_details = [
                'per_page' => $inspection_list['per_page'] ?? 0,
                'current_page' => $inspection_list['current_page'] ?? 0,
                'from' => $inspection_list['from'] ?? 0,
                'to' => $inspection_list['to'] ?? 0,
                'total' => $inspection_list['total'] ?? 0,
                'total_page' => $inspection_list['last_page'] ?? 0,
                'list' => $data_array,
            ];


            $success = [
                'inspection_details' => $inspection_details
            ];

            return $this->sendResponse($success, 'Inspection Details');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }


    public function view(Request $request)
    {
        try {
            if (Auth::user()) {
                $id = $request->id;
                $inspections = $this->ohsreport
                    ->where('inspection_safety_ohs_report.id', $id)
                    ->leftJoin(
                        'inspection_static_docno',
                        'inspection_safety_ohs_report.document_reference_id',
                        '=',
                        'inspection_static_docno.id'
                    )
                    ->select(
                        'inspection_safety_ohs_report.*',
                        'inspection_static_docno.*',
                        'inspection_safety_ohs_report.id as inspection_id',
                        'inspection_safety_ohs_report.created_by as inspection_created_by',
                        'inspection_safety_ohs_report.updated_at as inspection_updated_at',
                        'inspection_safety_ohs_report.updated_by as inspection_updated_by',
                    )
                    ->first();


                //Quantity Details
                $quantity_details = json_decode($inspections->quantity_details, true);
                $units = $this->unit->getunit();
                $quantity_details_array = [];
                foreach ($quantity_details as $detail) {
                    $quantity = [];
                    foreach ($units as $key => $unit) {
                        $unitKey = 'unit_' . ($key + 1);
                        $quantity[$unit->unit_name] = isset($detail[$unitKey]) ? $detail[$unitKey] : 0;
                    }
                    $quantity_details_array[] = [
                        'description' => $detail['description'] ?? '',
                        'quantity' => $quantity,
                        'total_quantity' => $detail['total_quantity'] ?? 0,
                    ];
                }


                //Fire Pump Details
                $fire_pump_details = json_decode($inspections->fire_water_pump_details, true);
                $fire_pump_details_array = [];
                foreach ($fire_pump_details as $detail) {
                    $Capacity = [];
                    foreach ($units as $key => $unit) {
                        $unitKey = 'fire_pump_details_unit_' . ($key + 1);
                        $Capacity[$unit->unit_name] = isset($detail[$unitKey]) ? $detail[$unitKey] : 0;
                    }
                    $fire_pump_details_array[] = [
                        'water_pump_water_storage_tank' => $detail['fire_pump_details'] ?? '',
                        'Capacity' => $Capacity,
                    ];
                }

                $inspection = [
                    'document_no' => $inspections->doc_no,
                    'issue_date' => Displaydateformat($inspections->issue_date),
                    'date_of_inspection' => Displaydateformat($inspections->inspection_date),
                    'rev_dt' => ($inspections->rev_dt),
                    'updated_frequency' => ($inspections->updated_frequency),
                ];

                $success = [
                    'id' => $inspections->inspection_id,
                    'inspection' => $inspection,
                    'quantity_details' => $quantity_details,
                    'fire_pump_details' => $fire_pump_details,
                ];
                return $this->sendResponse($success, 'Inspection Details');
            } else {
                return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
            }
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }


    public function store(Request $request)
    {

        try {

            $rules = [
                'doc_no' => 'required',
                'issue_date' => 'required',
                'inspection_date' => 'required',
                'updated_frequency' => 'required',
                'description.*' => 'required',
                'total_quantity.*' => 'required',
                'fire_pump_details.*' => 'required',
            ];

            $messages = [
                'doc_no.required' => 'Document number is required.',
                'issue_date.required' => 'Issue Date is required.',
                'inspection_date.required' => 'Inspection Date is required.',
                'updated_frequency.required' => 'Updated Frequency is required.',
                'description.*.required' => 'Description is required.',
                'total_quantity.*.required' => 'Total Quantity is required.',
                'fire_pump_details.*.required' => 'Fire Pump Details are required.',
            ];


            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors(), 422);
            }
            $ohs_summary_report = $this->ohsreport->store_api();
            $success = [
                "success" => $ohs_summary_report,
            ];
            return $this->sendResponse($success, 'Inspection Created');
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }
}
