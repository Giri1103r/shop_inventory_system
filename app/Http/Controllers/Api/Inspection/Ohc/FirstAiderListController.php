<?php

namespace App\Http\Controllers\Api\Inspection\Ohc;

use App\Http\Controllers\Controller;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Ohc\FirstAiderList;
use App\Models\Inspection\Ohc\FirstAiderListDetails;
use Exception;
use Illuminate\Http\Request;

class FirstAiderListController extends Controller
{
    private $first_aider;
    private $first_aider_details;
    private $document_no;
    public function __construct()
    {
        $this->first_aider = new FirstAiderList();
        $this->first_aider_details = new FirstAiderListDetails();
        $this->document_no = new InspectionStaticDocno();
    }

    public function list()
    {
        try {

            $data = $this->first_aider->listApi();
            if (count($data) > 0) {
                return response()->json([
                    'status' => true,
                    'data' => $data,
                    'message' => 'Data Received Successfully'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'data' => $data,
                    'message' => 'No Date Found'
                ]);
            }
        } catch (Exception $ex) {
            report($ex);
            return response()->json([
                'status' => false,
                'data' => [],
                'message' => 'Something Went Wrong'
            ]);
        }
    }

    public function view(Request $request)
    {
        try {
            $id = $request->id;
            $data = $this->first_aider->find($id);
            $doc = $this->document_no->find($data->document_reference_id);

            if ($data) {
                $details = $this->first_aider_details->where('reference_id', $id)->get()->toArray();
                $details_array = [];
                foreach ($details as $index => $detail) {
                    $detail_array = [];
                    $detail_array['id'] = $detail['id'];
                    $detail_array['emp_id'] = getEmployeeName(($detail['emp_id']));
                    $detail_array['designation'] = ($detail['designation_id']);
                    $detail_array['department'] = getDepartment(($detail['department_id']));
                    $detail_array['unit'] = getUnitName(($detail['unit_id']));
                    $detail_array['mobile_no'] = $detail['mobile_no'];
                    $detail_array['status'] = ($detail['status'] == 1) ? 'Active' : 'Inactive';
                    $details_array[$index] = $detail_array;
                }
                $response_data = [
                    'id' => $data->id,
                    'doc_no' => $doc->doc_no,
                    'issue_date' => Displaydateformat($doc->issue_date),
                    'rev_dt' => ($doc->rev_dt),
                    'last_updated_date' => Displaydateformat($data->last_updated_date),
                    'next_review_date' => Displaydateformat($data->next_review_date),
                    'details' => $details_array
                ];

                return response()->json([
                    'status' => true,
                    'data' => $response_data,
                    'message' => 'Data Received Successfully'
                ]);;
            } else {
                return response()->json([
                    'status' => false,
                    'data' => [],
                    'message' => 'No Date Found'
                ]);;
            }
        } catch (Exception $ex) {
            dd($ex);
            return response()->json([
                'status' => false,
                'data' => [],
                'message' => 'Something Went Wrong'
            ]);
        }
    }
}
