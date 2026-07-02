<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Tax;
use App\Models\Master\Medicine;
use App\Models\Master\Category;
use App\Models\Master\Uom;
use App\Models\Master\Manufacturer;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Exceptions\Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;

class MedicineController extends Controller
{

    private $medicine;
    private $category;
    private $tax;
    private $uom;
    private $manufacturer;
    public function __construct()
    {

        $this->medicine = new Medicine();
        $this->category = new Category();
        $this->tax = new Tax();
        $this->uom = new Uom();
        $this->manufacturer = new Manufacturer();
    }
    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->medicine->list();

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
                            $btn = '<a href="' . admin_url('master/medicine/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            // if (CheckUserPermission('edit')) {
                            $btn .= '<a href="' . admin_url('master/medicine/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
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
        $medicineList = $this->medicine->where('trash', 'No')->get();
        $data = array(
            'medicineList' => $medicineList,
        );
        return view('admin.master.medicine.list', $data);
    }

    public function add()
    {
        try {
            $categoryList = $this->category->getalldata();
            $taxList = $this->tax->getalldata();
            $uomList = $this->uom->getalldata();
            $manufacturerList = $this->manufacturer->getalldata();
            $data = [
                'categoryList' => $categoryList,
                'taxList' => $taxList,
                'uomList' => $uomList,
                'manufacturerList' => $manufacturerList,
            ];
            return view('admin.master.medicine.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',);
            return redirect(admin_url('master/medicine/list'));
        }
    }
    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $categoryList = $this->category->getalldata();
            $taxList = $this->tax->getalldata();
            $uomList = $this->uom->getalldata();
            $manufacturerList = $this->manufacturer->getalldata();
            $data = [
                'categoryList' => $categoryList,
                'taxList' => $taxList,
                'uomList' => $uomList,
                'manufacturerList' => $manufacturerList,
            ];
            return view('admin.master.medicine.edit', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Some thing went wrong Please try again after some time');
            return redirect(admin_url('master/medicine/list'));
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'medicine_name' => 'required',
                'category_id' => 'required',
            ];
            $messages = [
                'medicine_name.required' => 'Please Enter Medicine Name',
                'category_id.required' => 'Please Select Category',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $tax = $this->medicine->store();
            Session::flash('Success', 'Your Data has been Created Successfully');
            return redirect(admin_url('master/medicine/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Some thing went wrong Please try again after some time');
            return redirect(admin_url('master/medicine/list'));
        }
    }
    public function update(Request $request)
    {
        try {
            $rules = [
                'medicine_name' => 'required',
            ];
            $messages = [
                'medicine_name.required' => 'Please Enter Medicine Name',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $id = decryptId($request->id);
            $category = $this->medicine->updates($id);
            Session::flash('Success', 'Your Data has been Created Successfully');
            return redirect(admin_url('master/medicine/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Some thing went wrong Please try again after some time');
            return redirect(admin_url('master/medicine/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $medicine =  $this->medicine->selectOne($id);
            $data = [
                'medicine' => $medicine,
            ];
            return view('admin.master.medicine.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went Wrong Please Try again After Some time');
            return redirect(admin_url('master/medicine/list'));
        }
    }

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {


            $tax = $request->medicine_name;

            $id = $request->id ? decryptId($request->id) : null;

            if (empty($id)) {

                $exists = $this->medicine
                    ->uniqueCheck($tax)
                    ->exists();
            } else {

                $exists = $this->medicine
                    ->ExistuniqueCheck($tax, $id)
                    ->exists();
            }

            return Response::json(!$exists);
        }
    }


    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->medicine->exportdata();


            $header = [
                __("common.sno"),
                'Medicine Id',
                'Medicine Name',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->medicine_id;
                $export[] =  $data->medicine_name;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Medicine Details.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went Wrong Please Try again After Some time');
            return redirect(admin_url('master/medicine/list'));
        }
    }

    public function ExportPdf(Request $request)
    {
        try {

            $allData = $this->medicine->exportdata();


            $header = [
                __("common.sno"),
                'Medicine Id',
                'Medicine Name',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];
            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Medicine Details",
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

            $view = view('admin.master.medicine.pdf', $data);
            $html = $view->render();


            $mpdf->WriteHTML($html);

            $filename = "medicine.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went Wrong Please Try again After Some time');
            return redirect(admin_url('master/medicine/list'));
        }
    }
}
