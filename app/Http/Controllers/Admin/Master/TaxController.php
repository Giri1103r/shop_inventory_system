<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Tax;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Exceptions\Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;

class TaxController extends Controller
{

    private $tax;
    public function __construct()
    {

        $this->tax = new Tax();
    }
    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->tax->list();

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
                            $btn = '<a href="' . admin_url('master/tax/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            // if (CheckUserPermission('edit')) {
                            $btn .= '<a href="' . admin_url('master/tax/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
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
        $taxList = $this->tax->where('trash', 'No')->get();
        $data = array(
            'taxList' => $taxList,
        );
        return view('admin.master.tax.list', $data);
    }

    public function add()
    {
        try {
            $data = [];
            return view('admin.master.tax.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',);
            return redirect(admin_url('master/tax/list'));
        }
    }
    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $tax = $this->tax->find($id);
            $data = [
                'tax' => $tax,
            ];
            return view('admin.master.tax.edit', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',);
            return redirect(admin_url('master/tax/list'));
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'tax_name' => 'required',

            ];
            $messages = [
                'tax_name.required' => 'Please Enter Tax Name',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $tax = $this->tax->store();
            Session::flash('Success', 'Your Data has been Created Successfully');
            return redirect(admin_url('master/tax/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Some thing went wrong Please try again after some time');
            return redirect(admin_url('master/tax/list'));
        }
    }
    public function update(Request $request)
    {
        try {
            $rules = [
                'tax_name' => 'required',

            ];
            $messages = [
                'tax_name.required' => 'Please Enter Tax Name',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $id = decryptId($request->id);
            $category = $this->tax->updates($id);
            Session::flash('Success', 'Your Data has been Created Successfully');
            return redirect(admin_url('master/tax/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Some thing went wrong Please try again after some time');
            return redirect(admin_url('master/tax/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $tax =  $this->tax->selectOne($id);
            $data = [
                'tax' => $tax,
            ];
            return view('admin.master.tax.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went Wrong Please Try again After Some time');
            return redirect(admin_url('master/tax/list'));
        }
    }

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {


            $tax = $request->tax_name;

            $id = $request->id ? decryptId($request->id) : null;

            if (empty($id)) {

                $exists = $this->tax
                    ->uniqueCheck($tax)
                    ->exists();
            } else {

                $exists = $this->tax
                    ->ExistuniqueCheck($tax, $id)
                    ->exists();
            }

            return Response::json(!$exists);
        }
    }


    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->tax->exportdata();


            $header = [
                __("common.sno"),
                'Tax Id',
                'Tax Name',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->tax_id;
                $export[] =  $data->tax_name;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Tax Details.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went Wrong Please Try again After Some time');
            return redirect(admin_url('master/tax/list'));
        }
    }

    public function ExportPdf(Request $request)
    {
        try {

            $allData = $this->tax->exportdata();


            $header = [
                __("common.sno"),
                'Tax Id',
                'Tax Name',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];
            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Tax Details",
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

            $view = view('admin.master.tax.pdf', $data);
            $html = $view->render();


            $mpdf->WriteHTML($html);

            $filename = "tax.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went Wrong Please Try again After Some time');
            return redirect(admin_url('master/tax/list'));
        }
    }
}
