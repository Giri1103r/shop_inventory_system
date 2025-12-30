<?php

namespace App\Http\Controllers\Api\Inspection\Ohc;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Inspection\Ohc\FloorStretcher;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OHCFloorStretcherChecklistController extends BaseController
{
    private $floor_stretcher;

    public function __construct()
    {
        $this->floor_stretcher = new FloorStretcher();
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
            $query = FloorStretcher::select(
                'inspection_ohc_floorstretcher_checklist.*',
                'masters_unit.*',
                'inspection_ohc_floorstretcher_checklist.shift as shift_name',
                'inspection_frequency_option.*',
                'inspection_shift_option.*',
                'inspection_ohc_floorstretcher_checklist.id as checklist_id',
                'inspection_ohc_floorstretcher_checklist.created_at as inspection_created_at'
            )
                ->leftJoin('masters_unit', 'inspection_ohc_floorstretcher_checklist.unit', '=', 'masters_unit.id')
                ->leftJoin('inspection_frequency_option', 'inspection_ohc_floorstretcher_checklist.frequency', '=', 'inspection_frequency_option.id')
                ->leftJoin('inspection_shift_option', 'inspection_ohc_floorstretcher_checklist.shift', '=', 'inspection_shift_option.id');


            $search = $request->input('search', '');

            if (!empty($search)) {


                $query->where(function ($query) use ($search) {
                    $query->orWhere('masters_unit.unit_name', 'LIKE', "%{$search}%")
                        ->orWhere('masters_location.location_name', 'LIKE', "%{$search}%")
                        ->orWhere('inspection_frequency_option.frequency_name', 'LIKE', "%{$search}%");

                    if (strtotime($search)) {
                        $query->orWhereDate('inspection_ohc_floorstretcher_checklist.issue_date', '=', $search);
                    }
                });
            }


            $query_array = $query->orderBy('inspection_ohc_floorstretcher_checklist.id', 'DESC')->paginate($request->input('per_page', 10));

            $inspection_list = $query_array->toArray();

            if (empty($inspection_list['data'])) {
                return $this->sendError('No records found.', [], 404);
            }

            $data_array = [];
            foreach ($inspection_list['data'] as $datas) {
                $data = [];
                $data['id'] = $datas['inspection_id'] ?? '';
                $data['date_of_inspection'] = Displaydateformat($datas['issue_date']);
                $data['frequency'] = getFrequencyname($datas['frequency'] ?? '');
                $data['unit'] = getUnitname($datas['unit'] ?? '');
                $data['shift'] = getShift($datas['shift_name'] ?? '');
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
                'ohc_floor_stretcher' => $inspection_details
            ];
            return $this->sendResponse($success, 'OHC Floor Stretcher Checklist Inspection');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

    public function view(Request $request)
    {
        try {
            $id = $request->id;

            $ohc_floor_stretcher = $this->floor_stretcher->find($id);

            if (!$ohc_floor_stretcher) {
                return $this->sendError('Not Found', ['error' => 'Record not found.']);
            }

            $ohc_floor = [
                'id' => $id,
                'issue_date' => Displaydateformat($ohc_floor_stretcher->issue_date),
                'frequency' => getFrequencyname($ohc_floor_stretcher->frequency),
                'unit' => getUnitname($ohc_floor_stretcher->unit),
                'shift' => getShift($ohc_floor_stretcher->shift),
                'created_by' => getUsername($ohc_floor_stretcher->created_by),
                'created_at' => Displaydateformat($ohc_floor_stretcher->created_at),
            ];


            $decoded_response = json_decode($ohc_floor_stretcher->responses, true);

            // Extract each section safely
            $resource_code = $decoded_response['resource_code'] ?? [];
            $response = $decoded_response['response'] ?? [];
            $remarks = $decoded_response['remarks'] ?? [];

            // Combine them into a readable array
            $checklist_data = [];

            foreach ($response as $sectionId => $items) {
                foreach ($items as $itemId => $answers) {
                    $checklist_data[] = [
                        'section_id' => $sectionId,
                        'item_id' => $itemId,
                        'resource_code' => $resource_code[$sectionId][$itemId] ?? null,
                        'answers' => $answers,
                        'remarks' => $remarks[$sectionId][$itemId] ?? null,
                    ];
                }
            }

            $success = [
                'ohc_floor_stretcher_checklist' => $ohc_floor,
                'checklist_details' => $checklist_data
            ];


            return $this->sendResponse($success, 'OHC Floor Stretcher Checklist fetched successfully.');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorized', ['error' => 'Something went wrong, please try again later.']);
        }
    }
}
