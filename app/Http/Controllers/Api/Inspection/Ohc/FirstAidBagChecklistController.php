<?php

namespace App\Http\Controllers\Api\Inspection\Ohc;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Inspection\Ohc\FirstAidBagChecklist;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FirstAidBagChecklistController extends BaseController
{
    private $first_aid_bag_checklist;


    public function __construct()
    {
        $this->first_aid_bag_checklist = new FirstAidBagChecklist();
    }
    public function list()
    {
        if (Auth::user()) {
            $request = request();
            $search = '';
            if ($request->has('search')) {
                if ($request->search != '') {
                    $search = $request->search;
                }
            }
            
            $query = $this->first_aid_bag_checklist->select('inspection_ohc_first_aid_bag_inspection.*');

            $org_total_counts = $query->count();

            $user = Auth::user();
            $userRole = string_to_array($user->role);
            if (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole)  || in_array(ROLE_EHS_OFFICER, $userRole)  || in_array(ROLE_INSPECTION_CREATOR, $userRole) || in_array(ROLE_SAFETY_OFFICER, $userRole) || in_array(ROLE_MEDICAL_ASSISTANT, $userRole)) {
                $query->orderBy('inspection_ohc_first_aid_bag_inspection.id', 'DESC');
            } else {
                $query->where('inspection_ohc_first_aid_bag_inspection.created_by', Auth::id());
            }

            if (!empty($search)) {
                $audit_response = [
                    "Waiting For EHS Officer Verification" => 1,
                    "EHS Officer Rejected" => 2,
                    "Approved by Ehs officer" => 3,

                ];

                $query->where(function ($query) use ($search, $audit_response) {

                    if (isset($audit_response[$search])) {
                        $query->orWhere('inspection_ohc_first_aid_bag_inspection.inspection_status', $audit_response[$search]);
                    }
                    if (strtotime($search)) {
                        $query->orWhereDate('inspection_ohc_first_aid_bag_inspection.next_due', '=', $search);
                    }

                    if (strtotime($search)) {
                        $query->orWhereDate('inspection_ohc_first_aid_bag_inspection.inspection_date', '=', $search);
                    }
                });
            }


            $query_array = $query->orderBy('inspection_ohc_first_aid_bag_inspection.id', 'DESC')->paginate($request->input('per_page', 10));

            $inspection_list = $query_array->toArray();

            if (empty($inspection_list['data'])) {
                return $this->sendError('No records found.', [], 404);
            }

            $data_array = [];
            foreach ($inspection_list['data'] as $datas) {
                $data = [];
                $data['id'] = $datas['id'] ?? '';
                $data['date_of_inspection'] = Displaydateformat($datas['inspection_date']);
                $data['next_due_date'] = Displaydateformat($datas['next_due']);
                $data['location'] = getLocationname($datas['location']);
                $data['unit'] = getUnitname($datas['unit']);
                $data['frequency'] = getFrequencyname($datas['frequency']);
                $data['created_by'] = getUsername($datas['created_by'] ?? '');
                $data['created_at'] = Displaydateformat($datas['created_at'] ?? '');
                $data_array[] = $data;
            }

            $first_aid_bag_checklist = [
                'per_page' => $inspection_list['per_page'] ?? 0,
                'current_page' => $inspection_list['current_page'] ?? 0,
                'from' => $inspection_list['from'] ?? 0,
                'to' => $inspection_list['to'] ?? 0,
                'total' => $inspection_list['total'] ?? 0,
                'total_page' => $inspection_list['last_page'] ?? 0,
                'list' => $data_array,
            ];

            $success = [
                'first_aid_bag_checklist' => $first_aid_bag_checklist
            ];

            return $this->sendResponse($success, 'First Aid Bag Checklist Details');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

    public function view(Request $request)
    {
        try {
            $id = $request->id;

            $first_aid_bag_checklist = $this->first_aid_bag_checklist->find($id);

            if (!$first_aid_bag_checklist) {
                return $this->sendError('Not Found', ['error' => 'Record not found.']);
            }

            $first_aid_bag = [
                'date_of_inspection' => Displaydateformat($first_aid_bag_checklist->inspection_date ?? null),
                'next_due_date'      => Displaydateformat($first_aid_bag_checklist->next_due ?? null),
                'location'      => getLocationname($first_aid_bag_checklist->location ?? null),
                'unit'      => getUnitname($first_aid_bag_checklist->unit ?? null),
                'frequency'      => getFrequencyname($first_aid_bag_checklist->frequency ?? null),
                'shift'      => getShift($first_aid_bag_checklist->shift_id ?? null),
                'created_by'         => getUsername($first_aid_bag_checklist->created_by ?? null),
                'created_at'         => Displaydateformat($first_aid_bag_checklist->created_at ?? null),
            ];

            $checklist = json_decode($first_aid_bag_checklist->inspection_data, true) ?? [];

            $first_aid_bag_checklist = [];
            foreach ($checklist as $data) {
                $first_aid_bag_checklist[] = [
                    'medicine_name'      => isset($data['medicine_id']) ? getMedicinename($data['medicine_id']) : null,
                    'freeze_quantity' => $data['freeze_quantity'] ?? null,
                    'available_quantity' => $data['available_quantity'] ?? null,
                    'expired_date'       => isset($data['expired_date']) ? Displaydateformat($data['expired_date']) : null,
                    'employee_name'      => $data['emp_id'] ?? null,
                    'remarks'            => $data['remarks'] ?? null,
                ];
            }

            $success = [
                'first_aid_bag'                     => $first_aid_bag,
                'first_aid_bag_checklist' => $first_aid_bag_checklist,
            ];
            return $this->sendResponse($success, 'Data retrived Successfully!');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

  public function store(Request $request)
{
    try {
        $rules = [
            'date_of_inspection' => 'required|date_format:d-m-Y',
            'next_due_date'      => 'required|date_format:d-m-Y',
            'location'           => 'required|string',
            'unit'               => 'required|string',
            'shift'              => 'required|string',
            'frequency'          => 'required|string',
            'data'               => 'required|array|min:1',
            'data.*.medicine_name'      => 'required|string',
            'data.*.freeze_quantity'    => 'required|numeric',
            'data.*.available_quantity' => 'required|numeric',
            'data.*.expired_date'        => 'required|date_format:d-m-Y',
            'data.*.inpected_by'        => 'required|string',
            'data.*.remarks'            => 'nullable|string',
        ];

        $messages = [
            'date_of_inspection.required' => 'Date of inspection is required.',
            'shift.required'              => 'Shift selection is required.',
            'location.required'           => 'Location selection is required.',
            'next_due_date.required'      => 'Next due date is required.',
            'unit.required'               => 'Unit selection is required.',
            'frequency.required'          => 'Frequency is required.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }


        $inspection_data = [];
        foreach ($request->data as $row) {
            $inspection_data[] = [
                'medicine_id'      => $row['medicine_name'],
                'freeze_quantity'    => $row['freeze_quantity'],
                'available_quantity' => $row['available_quantity'],
                'expired_date'        =>DBdateformat($row['expired_date']),
                'emp_id'        => $row['inpected_by'],
                'remarks'            => $row['remarks'] ?? null,
            ];
        }

        // Store master record
        $data = FirstAidBagChecklist::create([
            'inspection_date' =>DBdateformat($request->date_of_inspection),
            'next_due'      => DBdateformat($request->next_due_date),
            'location'           => $request->location,
            'unit'               => $request->unit,
            'shift_id'              => $request->shift,
            'frequency'          => $request->frequency,
            'inspection_data'    => json_encode($inspection_data),
            'created_by'         => Auth::id(),
        ]);

        $success = [
            'first_aid_bag_checklist_id' => $data->id
        ];

        return $this->sendResponse($success, 'First Aid Bag Checklist created successfully.');
    } catch (\Exception $ex) {
        report($ex);
        return $this->sendError('Server Error', ['error' => $ex->getMessage()], 500);
    }
}

}
