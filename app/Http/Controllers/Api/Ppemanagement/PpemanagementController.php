<?php

namespace App\Http\Controllers\Api\Ppemanagement;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Master\PpeStockinventory;
use App\Models\Master\PpeType;
use App\Models\Master\PpeTypeMaster;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PpemanagementController extends BaseController
{
    private $ppetype;
    public function __construct()
    {
        $this->ppetype = new PpeType();
    }

    public function ppetype()
    {
        try {
            if (Auth::check()) {

                $ppeList = PpeType::select('id', 'ppe_id', 'ppe_type')
                    ->where('status', 1)
                    ->get();
                $success = [
                    'Ppe_list' => $ppeList,
                ];
                return $this->sendResponse($success, 'PPE Type details');
            }

            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        } catch (Exception $ex) {

            Log::error('Employee Fetch Error: ' . $ex->getMessage());
            return $this->sendError('Something went wrong.', ['error' => $ex->getMessage()], 500);
        }
    }

    public function ppemaster(Request $request): JsonResponse
    {
        try {

            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorised.',
                    'errors' => ['error' => 'Unauthorised']
                ], 401);
            }


            $ppemasterList = PpeTypeMaster::select('id', 'item_code', 'ppe_name')
                ->where('status', 1)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'PPE Master details',
                'data' => $ppemasterList
            ], 200);
        } catch (Exception $ex) {
            Log::error('PPE Master Fetch Error: ' . $ex->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'errors' => ['error' => $ex->getMessage()]
            ], 500);
        }
    }

    public function ppestock(Request $request): JsonResponse
    {
        try {

            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorised.',
                    'errors' => ['error' => 'Unauthorised']
                ], 401);
            }


            $ppestockList = PpeStockinventory::select('id', 'item_code', 'ppe_name')
                ->where('status', 1)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'PPE Stock details',
                'data' => $ppestockList
            ], 200);
        } catch (Exception $ex) {
            Log::error('PPE Master Fetch Error: ' . $ex->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'errors' => ['error' => $ex->getMessage()]
            ], 500);
        }
    }
}
