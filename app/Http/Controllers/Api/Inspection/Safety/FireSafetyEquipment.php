<?php

namespace App\Http\Controllers\Api\Inspection\Safety;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Safety\Master\Equipment;
use App\Models\Inspection\Safety\OHSPlantSummaryReport;
use App\Models\Inspection\Safety\FireSafetyEquipmentDetails;
use App\Models\Inspection\Safety\FireSafetyEquipment as SafetyFireSafetyEquipment;

class FireSafetyEquipment extends BaseController
{
    private $safety_equipment;
    private $safety_equipment_details;
    private $equipment;
    private $document_reference;


    public function __construct()
    {
        $this->safety_equipment = new SafetyFireSafetyEquipment();
        $this->equipment = new Equipment();
        $this->safety_equipment_details = new FireSafetyEquipmentDetails();
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
            $query = SafetyFireSafetyEquipment::select(
                'inspection_safety_equipment.*',
            );


            $query = SafetyFireSafetyEquipment::select(
                'inspection_safety_equipment.*',
                'inspection_safety_master_equipment.*',
                'inspection_safety_equipment.id as inspection_id',
                'inspection_safety_equipment.created_at as inspection_created_at',
                'inspection_safety_equipment.status as equipment_status'
            )
                ->leftJoin('inspection_safety_master_equipment', 'inspection_safety_equipment.equipment_id', '=', 'inspection_safety_master_equipment.id')
                ->leftJoin('inspection_static_docno', 'inspection_safety_equipment.document_reference_id', '=', 'inspection_static_docno.id');

            $org_total_counts = $query->count();

            if (!empty($search)) {
                $search = ($search);
                $query->where(function ($query) use ($search) {
                    $query->orWhere('inspection_safety_master_equipment.item_code', $search)
                        ->orWhere('equipment_name', $search);
                });
            }

            $query_array = $query->orderBy('inspection_safety_equipment.id', 'DESC')->paginate($request->input('per_page', 10));

            $equipment_list = $query_array->toArray();

            if (empty($equipment_list['data'])) {
                return $this->sendError('No records found.', [], 404);
            }


            $data_array = [];
            foreach ($equipment_list['data'] as $datas) {
                $data = [];
                $data['id'] = $datas['id'] ?? '';
                $data['equipment_name'] = ($datas['equipment_name']);
                $data['item_code'] = ($datas['item_code'] ?? '');
                $data['equipment_status'] = (isset($datas['equipment_status']) && $datas['equipment_status'] == 1) ? 'Active' : 'Inactive';
                $data['standard_norms'] = (isset($datas['standard_norms']) && $datas['standard_norms'] == 1) ? 'Standard' : 'Norms';
                $data['created_by'] = getUsername($datas['created_by'] ?? '');
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
                'equipment_list' => $equipment_list
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
                $equipments = $this->safety_equipment
                    ->where('inspection_safety_equipment.id', $id)
                    ->leftJoin(
                        'inspection_static_docno',
                        'inspection_safety_equipment.document_reference_id',
                        '=',
                        'inspection_static_docno.id'
                    )
                    ->select(
                        'inspection_safety_equipment.*',
                        'inspection_static_docno.*',
                        'inspection_safety_equipment.id as inspection_id',
                        'inspection_safety_equipment.created_by as inspection_created_by',
                        'inspection_safety_equipment.updated_at as inspection_updated_at',
                        'inspection_safety_equipment.updated_by as inspection_updated_by',
                    )
                    ->first();

                $equipment_view = [
                    'document_no' => $equipments->doc_no,
                    'issue_date' => Displaydateformat($equipments->issue_date),
                    'rev_dt' => ($equipments->rev_dt),
                    'equipment_name' => getEquipmentName($equipments->equipment_id),
                    'item_code' => ($equipments->item_code),
                    'economic_order_quantity' => ($equipments->economic_order_quantity),
                    'minimum_order_level' => ($equipments->minimum_order_level),
                    'measurement_unit' => ($equipments->measurement_unit),
                    'equipment_category' => ($equipments->equipment_category),
                    'economic_order_quantity' => ($equipments->economic_order_quantity),
                    'remark' => ($equipments->remark),
                    'observation_status' => (isset($equipments->observation_status) && $equipments->observation_status == 1) ? 'Active' : 'Inactive',
                    'standard_norms' => (isset($equipments->standard_norms) && $equipments->standard_norms == 1) ? 'Standard' : 'Norms',
                ];

                $success = [
                    'id' => $equipments->inspection_id,
                    'inspection' => $equipment_view,
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
                return $this->sendError('Validation Error', $validator->errors(), 422);
            }
            $safety_equipment = $this->safety_equipment->store_api();
            $success = [
                "success" => $safety_equipment,
            ];
            return $this->sendResponse($success, 'Equipment Added');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

    public function equipment()
    {
        try {
            $equipments = $this->equipment->GetEquipmentName()->toArray();

            $equipment = collect($equipments)->map(function ($item) {
                return [
                    'id' => $item['id'],
                    'name' => $item['equipment_name'],
                    'created_by' => getUsername($item['created_by']),
                    'status' => ($item['status'] == 1 ? 'Active' : 'InActive'),
                ];
            })->toArray();

            $success = array(
                'equipments' => $equipment,
            );
            return $this->sendResponse($success, 'Equipment Name');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }
}
