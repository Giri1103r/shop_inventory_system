<?php

namespace App\Http\Controllers\Api\Inspection\Ohc;

use Exception;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\FuncCall;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Inspection\Ohc\OhcSignature;
use App\Http\Controllers\Api\BaseController;
use App\Models\Inspection\Ohc\WeeklyFirstAidBox;
use App\Models\OhcManagement\Master\CertifiedFirstAider;

class WeeklyFirstAidBoxController extends BaseController
{


    private $weekly_first_aid;
    private $signature;
    private $certified_First_aid;



    public function __construct()
    {
        $this->weekly_first_aid = new WeeklyFirstAidBox();
        $this->signature = new OhcSignature();
        $this->certified_First_aid = new CertifiedFirstAider();
    }

    public function list()
    {
        if (Auth::check()) {
            try {
                $data = $this->weekly_first_aid->listApi();
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
                return $this->sendError('Unauthotized', ['error', 'Unauthorized'], 404);
            }
        } else {
            return $this->sendError('Unauthotized', ['error', 'Unauthorized'], 404);
        }
    }

    public function view(Request $request)
    {
        try {
            if (Auth::check()) {
                $id = $request->id;

                $inspection = $this->weekly_first_aid->getInspectionData($id);
                $inspection_type = OHC_TYPE_WEEEKLY_FIRST_AID_MEDICINE_STORE;
                $inspection_data = json_decode($inspection->inspection_data, true);
                $inspection_file = GetOHCSignature($inspection->created_by, $inspection->id, $inspection_type);

                $inspection_data =  [
                    'inspection_id' => $inspection->id,
                    'document_no' => $inspection->doc_no,
                    'issue_date' => $inspection->issue_date,
                    'revision_date' => $inspection->rev_dt,
                    'date_of_inspection' => $inspection->date_of_inspection,
                    'location' => getLocationname($inspection->location),
                    'unit' => getUnitname($inspection->unit),
                    'first_aid_box_no' => $inspection->first_aid_box_no,
                    'first_aider_name' => getFirstAider($inspection->first_aider),
                    'shift' => getShift($inspection->shift),
                    'inspection_data' => $inspection_data,
                    'signature' => admin_url($inspection_file),
                    'remark_by' => $inspection->remark_by


                ];

                $success = [
                    'id' => $inspection->id,
                    'inspection_data' => $inspection_data,
                ];

                return $this->sendResponse($success, 'Date Received Successfully');
            } else {
                return $this->sendError('Unauthotized', ['error', 'Unauthorized'], 404);
            }
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthotized', ['error', 'Unauthorized'], 404);
        }
    }

    public function Store(Request $request)
    {
        try {
            $weekly_first_aid = $this->weekly_first_aid->storeApi();
            $weekly_first_aid_id = $weekly_first_aid->id;
            $inspection_type = OHC_TYPE_WEEEKLY_FIRST_AID_MEDICINE_STORE;
            $files = $this->signature->requestorsignatureUpload_api($inspection_type, $weekly_first_aid_id);

            return response()->json([
                'success' => true,
                'message' => 'Inspection data stored successfully',
            ], 201);
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    public function getFirstAiderName(Request $request)
    {
        try {

            $first_aid = $this->certified_First_aid->getFirstAider();

            $first_aider_name = [];
            $first_aider_list = [];

            foreach ($first_aid as $index => $data) {
                $first_aider_name['id'] = $data['id'];
                $first_aider_name['certifier_name'] = $data['certifier_name'];
                $first_aider_list[$index] = $first_aider_name;
            }

            return response()->json([
                'success' => true,
                'message' => 'First Aider Name',
                'data' => $first_aider_list
            ], 201);
        } catch (Exception $ex) {
            dd($ex);
        }
    }
}
