<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Facades\File;

use Str;
use PDF;
use Mail;
use Illuminate\Support\Facades\Auth;
use Session;
use Exception;
use DataTables;
use Response;

use App\Models\Master\Designation;
use App\Models\Master\Company;
use App\Models\Master\Department;
use App\Models\Master\UserRole;

use App\Models\User;
use App\Models\UploadLog;


class UserController extends Controller
{

    private $designation;
    private $company;
    private $department;
    private $Role;
    private $user;
    private $uploadlog;

    public function __construct()
    {

        // $this->designation = new designataion();
        $this->company = new Company();
        $this->department = new department();
        $this->Role = new UserRole();
        $this->user = new User();
        $this->uploadlog = new UploadLog();
    }


    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {

                    $data =  $this->user->list();
                    $datatables = Datatables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active<span>";
                            if ($row->status == 1) {
                                $text = "<span  data-id='" . encryptId($row->id) . "' data-type = '1' >Active<span>";
                            } else if ($row->status == 0) {
                                $text = "<span  data-id='" . encryptId($row->id) . "' data-type = '0' >In-Active<span>";
                            }
                            return $text;
                        })
                        ->addColumn('user_id', function ($row) {
                            return $row->employee_id;
                        })
                        ->addColumn('user_name', function ($row) {
                            return $row->name;
                        })
                        ->addColumn('user_role', function ($row) {
                            return $row->role_name;
                        })

                        ->addColumn('created_at', function ($row) {
                            return Displaydatetimeformat($row->created_at);
                        })

                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            // $btn = '<a href="' . admin_url('administration/users/view/' . encryptId($row->id)) . '"   class="view-icon" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // $btn .= '<a href="' . admin_url('administration/users/edit/' . encryptId($row->id)) . '" class="edit-icon" title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            // if (CheckUserRole(ROLE_ADMIN)) {
                            //     $btn .= '<a href="' . admin_url('administration/users/passwordchange/' . encryptId($row->id)) . '" class="key-icon" title="passwordchange"><i class="fas fa-key"></i> ';
                            // }
                            // $btn .= '<a href="javascript:void(0);"  data-id="' . encryptId($row->id) . '"  data-login_id="' . encryptId($row->login_id) . '" class="recordDelete" title="Delete"><i class="fa-solid fa-trash text-danger" ></i></i></a> ';
                            // return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);
                    return $datatables;
                } catch (Exception $ex) {


                    report($ex);
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }

        $data = array();

        return view('master.user.list', $data);
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->user->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }


            $header = [
                __("common.sno"),
                'User  ID',
                'User  Name',
                'Role',
                'Email ID',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];
            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->employee_id;
                $export[] =  $data->name;
                $export[] =  $data->role_name;
                $export[] =  $data->email;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Users Details.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            report($ex);
        }
    }


    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->user->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'User  ID',
                'User  Name',
                'Role',
                'Email ID',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "User Details",
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

            $view = view('master.user.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "master.user.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function getUser(Request $request)
    {

        $userrole = $request->userrole;
        $department = decryptId($request->department);

        $where = $whereIn = array();

        switch ($userrole) {
            case "job_owner":
                $whereIn = array(
                    ROLE_JOBOWNER
                );
                $where = array(
                    'emp_department_id' => $department,
                );
                break;
            default:
                $whereIn = array();
                $where = array();
                break;
        }

        $employee = $this->user->ajaxList($where, $whereIn);
        return response()->json($employee);
    }
}
