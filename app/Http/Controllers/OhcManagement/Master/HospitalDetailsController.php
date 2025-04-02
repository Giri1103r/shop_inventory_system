<?php

namespace App\Http\Controllers\OhcManagement\Master;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;



use App\Models\Master\Unit;
use App\Models\Master\Location;
use App\Models\Master\Department;
use App\Models\User;
use App\Models\UploadLog;
use App\Jobs\ImportmedicineJob;
use App\Mail\Ohc\MedicineApprovalEmail;
use App\Mail\Ohc\MedicineRequestEmail;
use App\Mail\Ohc\MedicineStockRequestEmail;
use App\Models\OhcManagement\Master\HospitalDetails;
use App\Models\OhcManagement\Master\Medicine;
use App\Models\OhcManagement\OhcStatuslog;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\OhcManagement\Report\Inventory;

class HospitalDetailsController extends Controller
{

    private $hospital_details;
    private $unit;
    private $location;
    private $department;
    private $user;
    private $uploadlog;
    private $ohc_status;
    private $inventory;


    public function __construct()
    {

        $this->hospital_details = new HospitalDetails();
        $this->unit = new Unit();
        $this->location = new Location();
        $this->department = new Department();
        $this->user = new User();
        $this->uploadlog = new UploadLog();
        $this->ohc_status = new OhcStatuslog();
        $this->inventory = new Inventory();
    }


    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {

                try {

                    $data =  $this->hospital_details->list();

                    $datatables = Datatables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active<span>";
                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class= 'statusChange' data-id='" . encryptId($row->id) . "' data-type = '1' >Active<span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class= 'statusChange' data-id='" . encryptId($row->id) . "' data-type = '0' >In-Active<span>";
                            }
                            return $text;
                        })
                        ->addColumn('created_at', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('expiry_date', function ($row) {
                            return Displaydateformat($row->expiry_date);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })

                        ->addColumn('action', function ($row) {
                            $btn = '';
                            // if (CheckUserPermission('view')) {
                                $btn = '<a href="' . admin_url('ohc/hospital-details/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            // if (CheckUserPermission('edit') ) {
                                $btn .= '<a href="' . admin_url('ohc/hospital-details/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            // }

                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', ])
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
        $unit = $this->unit->getunit();
        $data = array(

            'unit' => $unit
        );

        return view('ohcmanagement.master.hospital_details.list', $data);
    }
    // add

    public function Add(Request $request)
    {

        try {
            $unit = $this->unit->getunit();
            $data = [
                'unit' => $unit
            ];
            return view('ohcmanagement.master.hospital_details.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {
        try {

            $data =  $this->hospital_details->store();
            Session::flash('success', 'Your data has been created successfully!');
            return redirect(admin_url('ohc/hospital-details/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/hospital-details/list'));
        }
    }
    // view
    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $hospitalDetails = $this->hospital_details->selectOne($id);

                $data = array(
                    'hospitalDetails' => $hospitalDetails,
                );
            }
            return view('ohcmanagement.master.hospital_details.view', $data);
        } catch (Exception $ex) {
        }
    }


    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);


            $hospitalDetails = $this->hospital_details->find($id);

            $data = array(
                'hospitalDetails' => $hospitalDetails,

            );


            return view('ohcmanagement.master.hospital_details.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);



            $this->hospital_details->updates($id);


            Session::flash('success', 'Your data has been updated successfully!');
            return redirect(admin_url('ohc/hospital-details/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/hospital-details/list'));
        }
    }

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $hospital_name = $request->hospital_name;
            $mobile_no = $request->mobile_no;


            $id = $request->id;

            if (empty($id)) {
                $isUnique = $this->hospital_details->uniqueCheck($hospital_name,$mobile_no);
            } else {
                $id = decryptId($id);

                $isUnique = $this->hospital_details->existUniqueCheck($hospital_name,$mobile_no, $id);
            }

            if ($isUnique->count()) {
                return Response::json(false);
            }
            return Response::json(true);
        }
    }



    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->hospital_details->statuschange($id);


            return response()->json(['status' => 'success', 'msg' => 'Your status  has changed Successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }


    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->hospital_details->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Hospital Name',
                'Mobile Number',
                'Telephone Number',
                'Address',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->hospital_name;
                $export[] =  $data->mobile_no;
                $export[] =  $data->tel_no;
                $export[] =  $data->address;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Hospital Details .xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/hospital-details/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->hospital_details->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Hospital Name',
                'Mobile Number',
                'Telephone Number',
                'Address',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Hospital Details",
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

            $view = view('ohcmanagement.master.hospital_details.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Hospital .pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/hospital-details/list'));
        }
    }


}
