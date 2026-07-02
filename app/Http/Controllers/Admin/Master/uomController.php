<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\UOM;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Exceptions\Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;

class uomController extends Controller
{

    private $uom;
    public function __construct()
    {

        $this->uom = new UOM();
    }
    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->uom->list();

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
                            $btn = '<a href="' . admin_url('master/uom/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            // if (CheckUserPermission('edit')) {
                            $btn .= '<a href="' . admin_url('master/uom/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
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
        $uomList = $this->uom->where('trash', 'No')->get();
        $data = array(
            'uomList' => $uomList,
        );
        return view('admin.master.uom.list', $data);
    }

    public function add()
    {
        try {
            $data = [];
            return view('admin.master.uom.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',);
            return redirect(admin_url('master/uom/list'));
        }
    }
    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $uom = $this->uom->find($id);
            $data = [
                'uom' => $uom,
            ];
            return view('admin.master.uom.edit', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',);
            return redirect(admin_url('master/uom/list'));
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'uom_name' => 'required',
            ];
            $messages = [
                'uom_name.required' => 'Please Enter UOM Name',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $uom = $this->uom->store();
            Session::flash('Success', 'Your Data has been Created Successfully');
            return redirect(admin_url('master/uom/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Some thing went wrong Please try again after some time');
            return redirect(admin_url('master/uom/list'));
        }
    }
    public function update(Request $request)
    {
        try {
            $rules = [
                'uom_name' => 'required',
            ];
            $messages = [
                'uom_name.required' => 'Please Enter UOM Name',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $id = decryptId($request->id);
            $category = $this->uom->updates($id);
            Session::flash('Success', 'Your Data has been Created Successfully');
            return redirect(admin_url('master/uom/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Some thing went wrong Please try again after some time');
            return redirect(admin_url('master/uom/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $uom =  $this->uom->selectOne($id);
            $data = [
                'uom' => $uom,
            ];
            return view('admin.master.uom.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went Wrong Please Try again After Some time');
            return redirect(admin_url('master/uom/list'));
        }
    }

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {


            $uom = $request->uom_name;

            $id = $request->id ? decryptId($request->id) : null;

            if (empty($id)) {

                $exists = $this->uom
                    ->uniqueCheck($uom)
                    ->exists();
            } else {

                $exists = $this->uom
                    ->ExistuniqueCheck($uom, $id)
                    ->exists();
            }

            return Response::json(!$exists);
        }
    }


    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->uom->exportdata();


            $header = [
                __("common.sno"),
                'UOM Id',
                'UOM  Name',
                'Description',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->uom_id;
                $export[] =  $data->uom_name;
                $export[] =  $data->description;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('UOM Details.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went Wrong Please Try again After Some time');
            return redirect(admin_url('master/uom/list'));
        }
    }

    public function ExportPdf(Request $request)
    {
        try {

            $allData = $this->uom->exportdata();


            $header = [
                __("common.sno"),
                'UOM Id',
                'UOM Name',
                'Description',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "UOM Details",
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

            $view = view('admin.master.uom.pdf', $data);
            $html = $view->render();


            $mpdf->WriteHTML($html);

            $filename = "uom.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went Wrong Please Try again After Some time');
            return redirect(admin_url('master/uom/list'));
        }
    }
}
