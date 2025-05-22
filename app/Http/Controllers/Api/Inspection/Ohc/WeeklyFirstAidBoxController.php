<?php

namespace App\Http\Controllers\Api\Inspection\Ohc;

use App\Http\Controllers\Api\BaseController;
use Exception;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\FuncCall;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Inspection\Ohc\WeeklyFirstAidBox;

class WeeklyFirstAidBoxController extends BaseController
{


    private $weekly_first_aid;

    public function __construct()
    {
        $this->weekly_first_aid = new WeeklyFirstAidBox();
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
                dd($inspection);

                $inspection_data =  [
                    'inspection_id' =>$inspection->id,
                    'document_no'=>$inspection->doc_no,
                    'issue_date'=>$inspection->issue_date,
                    'revision_date'=>$inspection->rev_dt,
                    'date_of_inspection'=>$inspection->date_of_inspection,
                    'location'=>getLocationname($inspection->location),
                    'unit'=>getUnitname($inspection->unit),
                    'first_aid_box_no'=>$inspection->first_aid_box_no,


                ];
            } else {
                return $this->sendError('Unauthotized', ['error', 'Unauthorized'], 404);
            }
        } catch (Exception $ex) {
            dd($ex);
            return $this->sendError('Unauthotized', ['error', 'Unauthorized'], 404);
        }
    }
}
