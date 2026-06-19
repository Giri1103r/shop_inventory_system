<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class uomController extends Controller
{

    private $manufacture;
    public function __construct()
    {

        $this->manufacture = new Manufacturer();
    }
    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->manufacture->list();

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
                            return Displaydatetimeformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })

                        ->addColumn('action', function ($row) {
                            $btn = '';
                            // if (CheckUserPermission('view')) {
                            $btn = '<a href="' . admin_url('master/manufacture/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            // if (CheckUserPermission('edit')) {
                            $btn .= '<a href="' . admin_url('master/manufacture/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            // }


                            return $btn;
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
        $manufactureList = $this->manufacture->where('trash', 'No')->get();
        $data = array(
            'manufactureList' => $manufactureList,
        );
        return view('admin.master.manufacture.list', $data);
    }

    public function add()
    {
        try {
            $data = [];
            return view('admin.master.manufacture.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',);
            return redirect(admin_url('master/manufacture/list'));
        }
    }
    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $manufacture = $this->manufacture->find($id);
            $data = [
                'manufacture' => $manufacture,
            ];
            return view('admin.master.manufacture.edit', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',);
            return redirect(admin_url('master/manufacture/list'));
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'manufacturer_id' => 'required',
                'manufacture_name' => 'required',
                'license_number' => 'required',
                'contact_person' => 'required',
                'email' => 'required',
                'mobile_no' => 'required',
            ];
            $messages = [

                'manufacturer_id.required' => 'Please Enter Manufacture Id',
                'manufacture_name.required' => 'Please Enter Manufacture Name',
                'license_number.required' => 'Please Enter License Number',
                'contact_person.required' => 'Please Enter Contact Person',
                'email.required' => 'Please Enter email',
                'mobile_no.required' => 'Please Enter Mobile Number',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $manufacture = $this->manufacture->store();
            Session::flash('Success', 'Your Data has been Created Successfully');
            return redirect(admin_url('master/manufacture/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Some thing went wrong Please try again after some time');
            return redirect(admin_url('master/manufacture/list'));
        }
    }
    public function update(Request $request)
    {
        try {
            $rules = [
                'manufacturer_id' => 'required',
                'manufacture_name' => 'required',
                'license_number' => 'required',
                'contact_person' => 'required',
                'email' => 'required',
                'mobile_no' => 'required',
            ];
            $messages = [

                'manufacturer_id.required' => 'Please Enter Manufacture Id',
                'manufacture_name.required' => 'Please Enter Manufacture Name',
                'license_number.required' => 'Please Enter License Number',
                'contact_person.required' => 'Please Enter Contact Person',
                'email.required' => 'Please Enter email',
                'mobile_no.required' => 'Please Enter Mobile Number',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $id = decryptId($request->id);
            $category = $this->manufacture->updates($id);
            Session::flash('Success', 'Your Data has been Created Successfully');
            return redirect(admin_url('master/manufacture/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Some thing went wrong Please try again after some time');
            return redirect(admin_url('master/manufacture/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $manufacture =  $this->manufacture->selectOne($id);
            $data = [
                'manufacture' => $manufacture,
            ];
            return view('admin.master.manufacture.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went Wrong Please Try again After Some time');
            return redirect(admin_url('master/manufacture/list'));
        }
    }

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {


            $mName = $request->manufacture_name;
            $lNo = $request->license_number;
            $email = $request->email;

            $id = $request->id ? decryptId($request->id) : null;

            if (empty($id)) {

                $exists = $this->manufacture
                    ->uniqueCheck($mName, $lNo, $email)
                    ->exists();
            } else {

                $exists = $this->manufacture
                    ->ExistuniqueCheck($mName, $lNo, $email, $id)
                    ->exists();
            }

            return Response::json(!$exists);
        }
    }


    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->manufacture->exportdata();


            $header = [
                __("common.sno"),
                'Manufacture Id',
                'Manufacturer Name',
                'License Number',
                'Email',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->manufacture_id;
                $export[] =  $data->manufacturer_name;
                $export[] =  $data->license_number;
                $export[] =  $data->email;

                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Manufacturer Details.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went Wrong Please Try again After Some time');
            return redirect(admin_url('master/manufacture/list'));
        }
    }

    public function ExportPdf(Request $request)
    {
        try {

            $allData = $this->manufacture->exportdata();


            $header = [
                __("common.sno"),
                'Manufacture Id',
                'Manufacturer Name',
                'License Number',
                'Email',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Manufacturer Details",
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

            $view = view('admin.master.manufacture.pdf', $data);
            $html = $view->render();


            $mpdf->WriteHTML($html);

            $filename = "Manufacture.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went Wrong Please Try again After Some time');
            return redirect(admin_url('master/manufacture/list'));
        }
    }
}
