<?php

namespace App\Http\Controllers\Api\Inspection\MSDS;

use App\Http\Controllers\Api\BaseController;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use App\Models\Master\Location;
use App\Models\Master\Department;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\Inspection\MSDS\MSDSEmail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\SimpleExcel\SimpleExcelWriter;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Models\Inspection\MSDS\MSDSDetails;
use App\Models\Inspection\MSDS\MSDSCheckList;
use App\Models\Inspection\MSDS\MSDSStatusLog;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Http\Controllers\Admin\AdminController;
use App\Models\Inspection\MSDS\Master\Chemical;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\MSDS\Master\NFARating;
use App\Models\Inspection\MSDS\MSDSSignatureUpload;
use App\Models\Inspection\MSDS\MsdsFileUpload;
use App\Models\Inspection\MSDS\Master\NFARatingValue;
use App\Models\Inspection\MSDS\MSDS;
use Exception;

class MSDSController extends BaseController
{
    private $msdsDetails;
    private $units;
    private $departments;
    private $locations;
    private $chemicals;
    private $document_reference;
    private $nfarating;
    private $nfaratingvalue;
    private $msdsFileUpload;
    private $msds;

    public function __construct()
    {
        $this->msdsDetails = new MSDSDetails();
        $this->units = new Unit();
        $this->departments = new Department();
        $this->locations = new Location();
        $this->chemicals = new Chemical();
        $this->document_reference = new InspectionStaticDocno();
        $this->nfarating = new NFARating();
        $this->nfaratingvalue = new NFARatingValue();
        $this->msds = new MSDS();
        $this->msdsFileUpload = new MsdsFileUpload();
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
            $query = MSDS::select(
                'inspection_msds.*',
                'masters_unit.unit_name',
                'masters_location.location_name',
                'masters_department.department_name'
            )
                ->leftjoin('masters_unit', 'inspection_msds.unit_id', '=', 'masters_unit.id')
                ->leftjoin('masters_department', 'inspection_msds.department_id', '=', 'masters_department.id')
                ->leftjoin('masters_location', 'inspection_msds.location_id', '=', 'masters_location.id');

            $org_total_counts = $query->count();

            if (!empty($search)) {
                $search = ($search);
                $query->where(function ($query) use ($search) {
                    $query->orWhere('masters_unit.unit_name', $search)
                        ->orWhere('masters_department.department_name', $search)
                        ->orWhere('masters_location.location_name', $search);
                });
            }

            $query_array = $query->orderBy('inspection_msds.id', 'DESC')->paginate($request->input('per_page', 10));

            $msds_list = $query_array->toArray();

            if (empty($msds_list['data'])) {
                return $this->sendError('No records found.', [], 404);
            }


            $data_array = [];
            foreach ($msds_list['data'] as $datas) {
                $data = [];
                $data['id'] = $datas['id'] ?? '';
                $data['location_id'] = getLocationname($datas['location_id']);
                $data['unit_id'] = getUnitname($datas['unit_id'] ?? '');
                $data['department_id'] = getDepartment($datas['department_id'] ?? '');
                $data['created_at'] = Displaydateformat($datas['created_at'] ?? '');
                $data_array[] = $data;
            }

            $msds_list_details = [
                'per_page' => $inspection_list['per_page'] ?? 0,
                'current_page' => $inspection_list['current_page'] ?? 0,
                'from' => $inspection_list['from'] ?? 0,
                'to' => $inspection_list['to'] ?? 0,
                'total' => $inspection_list['total'] ?? 0,
                'total_page' => $inspection_list['last_page'] ?? 0,
                'list' => $data_array,
            ];

            $success = [
                'msds_list' => $msds_list_details
            ];

            return $this->sendResponse($success, 'MSDS  Details');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

    public function view(Request $request)
    {
        try {
            $id = $request->id;


            if (!Auth::check()) {
                return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
            }


            $msds = $this->msds->find($id);
            if (!$msds) {
                return $this->sendError('MSDS not found.', ['error' => 'Record not found'], 404);
            }

            $inspection_details = $this->msdsDetails->getDetails($msds->id);


            $msds_inspection_details = [];

            foreach ($inspection_details as $detail) {

                $detail->msds_file = $this->msdsFileUpload->GetFile($msds->id, $detail->id);
                $file =  $detail->msds_file;
                $msds_file =[
                    'file_path' => admin_url($file->file_path),
                ];


                $nfa_rating = json_decode($detail->nfa_rating ?? '[]', true);


                $msds_inspection_details[] = [
                    'serial_no'                => $detail->serial_number,
                    'item_code'                => $detail->item_code,
                    'name_of_chemical'         => $detail->name_of_chemical,
                    'storage_capacity'         => $detail->storage_capacity,
                    'msds_availability_status' => getYesNoStatus($detail->msds_availability_status),
                    'remark'                   => $detail->remark,
                    'type_of_chemical'         => getChemicalName($detail->type_of_chemical),
                    'nfa_rating'               => $nfa_rating,
                    'msds_file'                => $msds_file ,
                ];
            }

            // ✅ Get document reference details
            $document_no = $this->document_reference->selectOne($msds->document_reference_id);


            $msds_summary = [
                'doc_no'         => $document_no->doc_no ?? '',
                'issue_date'     => Displaydateformat($document_no->issue_date ?? null),
                'rev_dt'         => $document_no->rev_dt ?? '',
                'location'       => getLocationname($msds->location_id),
                'unit'           => getUnitname($msds->unit_id),
                'department'     => getDepartment($msds->department_id),
                'excat_location' => $msds->excat_location,
                'created_by'     => getUsername($msds->created_by),
                'created_at'     => Displaydateformat($msds->created_at),
            ];


            $success = [
                'msds'                    => $msds_summary,
                'msds_inspection_details' => $msds_inspection_details,

            ];

            return $this->sendResponse($success, 'MSDS Details fetched successfully.');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('An unexpected error occurred.', ['error' => $ex->getMessage()], 500);
        }
    }
    
}
