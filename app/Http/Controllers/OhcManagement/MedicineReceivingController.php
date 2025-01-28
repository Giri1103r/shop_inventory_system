<?php

namespace App\Http\Controllers\OhcManagement;

use App\Http\Controllers\Controller;

use App\Models\Master\Department;
use App\Models\Master\Employee;
use App\Models\OhcManagement\Master\Medicine;
use App\Models\OhcManagement\Master\Vendor;
use App\Models\OhcManagement\MedicineReceiving;
use App\Models\UploadLog;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;

class MedicineReceivingController extends Controller
{
    private $medicine;
    private $vendor;
    private $medicine_receiving;

    public function __construct()
    {
        $this->medicine = new Medicine();
        $this->vendor = new Vendor();
        $this->medicine_receiving = new MedicineReceiving();
    }
    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->medicine_receiving->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->editColumn('medicine_id', function ($row) {
                            return$row->medicine;
                        })
                        ->editColumn('pack_id', function ($row) {
                            return$row->pack;
                        })
                        ->editColumn('hsn_id', function ($row) {
                            return$row->hsn;
                        })
                        ->editColumn('vendor_id', function ($row) {
                            return$row->vendor_name;
                        })
                        ->editColumn('expire_date', function ($row) {
                            return displaydateformat($row->expire_date);
                        })
                        ->editColumn('action', function ($row) {
                            $btn = '';
                            // if (CheckUserPermission('view')) {
                            $btn .= '<a href="' . admin_url('ohc/medicine-receiving-form/view/' . encryptId($row->ohc_management_medicine_receiving_id)) . '" class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            // if (CheckUserPermission('edit')) {
                            $btn .= '<a href="' . admin_url('ohc/medicine-receiving-form/edit/' . encryptId($row->ohc_management_medicine_receiving_id)) . '" class="" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a> ';
                            // }
                            // $btn .= '<a href="javascript:void(0);" data-id="' . encryptId($row->id) . '" class="recordDelete" title="Delete"><i class="fa-solid fa-trash text-danger"></i></a> ';


                            return $btn;
                        })

                        ->rawColumns(['action','expire_date'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);

                    return response()->json($datatables->getData());
                } catch (Exception $ex) {
                    dd($ex);
                    return response()->json(['status' => 'error', 'msg' => __('ppe.please_try_after_some_time')], 406);
                }
            }
        }

        $medicine = $this->medicine->getMedicineData();
        $vendor = $this->vendor->getVendordata();
        $data = [
            'medicine' => $medicine,
            'vendor' => $vendor
        ];

        return view('ohcmanagement.medicine_receiving.list', $data);
    }
    public function add()
    {
        try {
            $medicine = $this->medicine->getMedicineData();
            $vendor = $this->vendor->getVendordata();
            $data = [
                'medicine' => $medicine,
                'vendor' => $vendor
            ];

            return view('ohcmanagement.medicine_receiving.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medicine-receiving-form/list'));
        }
    }

    public function store(Request $request)
    {
        try {

            $rules = [
                'medicine_id' => 'required',
                'vendor_id' => 'required',
                'quantity' => 'required',
                'batch_number' => 'required',
                'expire_date' => 'required',
                'hsn_id' => 'required',
                'rate' => 'required',
                'pack_id' => 'required',

            ];


            $messages = [
                'medicine_id.required' => 'Medicine Name is required',
                'vendor_id.required' => 'Vendor Name is required',
                'quantity.required' => 'Quantity is required',
                'batch_number.required' => 'Quantity is required',
                'expire_date.required' => 'Expire Date is required',
                'hsn_id.required' => 'HSN Numner is required',
                'rate.required' => 'Rate is required',
                'pack_id.required => Pack Details is required',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {


                $this->medicine_receiving->store();


                Session::flash('success', 'Your data has been created successfully!');
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/medicine-receiving-form/list'));
        } catch (Exception $ex) {

            dd($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medicine-receiving-form/list'));
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);


            $medicine_receiving = $this->medicine_receiving->find($id);
            $medicine = $this->medicine->getMedicineData();
            $vendor = $this->vendor->getVendordata();
            $hsn = $this->medicine->where('id', $id)->select('medicine', 'hsn', 'pack')->first();

            $data = [
                'medicine' => $medicine,
                'vendor' => $vendor,
                'medicine_receiving' => $medicine_receiving,
                'hsn' => $hsn


            ];

            return view('ohcmanagement.medicine_receiving.edit', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medicine-receiving-form/list'));
        }
    }
    public function update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rules = [
                'medicine_id' => 'required',
                'vendor_id' => 'required',
                'quantity' => 'required',
                'batch_number' => 'required',
                'expire_date' => 'required',
                'hsn_id' => 'required',
                'rate' => 'required',
                'pack_id' => 'required',

            ];


            $messages = [
                'medicine_id.required' => 'Medicine Name is required',
                'vendor_id.required' => 'Vendor Name is required',
                'quantity.required' => 'Quantity is required',
                'batch_number.required' => 'Quantity is required',
                'expire_date.required' => 'Expire Date is required',
                'hsn_id.required' => 'HSN Numner is required',
                'rate.required' => 'Rate is required',
                'pack_id.required => Pack Details is required',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {
                $this->medicine_receiving->updates($id);
                Session::flash('success', 'Your data has been updated successfully!');
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('ohc/medicine-receiving-form/list'));
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medicine-receiving-form/list'));
        }
    }
    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $medicine_receiving = $this->medicine_receiving->selectOne($id);
                $medicine = $this->medicine->where('id', $id)->select('medicine', 'hsn', 'pack')->first();
                $vendor = $this->vendor->where('id', $id)->select('vendor_name')->first();

                $data = array(
                    'medicine_receiving' => $medicine_receiving,
                    'medicine' => $medicine,
                    'vendor' => $vendor

                );
            }
            return view('ohcmanagement.medicine_receiving.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('ohc/medicine-receiving-form/list'));
        }
    }


    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->medicine_receiving->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Medicine Name',
                'HSN Number',
                'Pack Details',
                'Quantity',
                'Batch Number',
                'Rate',
                'Expiry Date',
                'Vendor Name',
                'Created_by',
                'Created_at'
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->medicine;
                $export[] =  $data->hsn;
                $export[] =  $data->pack;
                $export[] =  $data->quantity;
                $export[] =  $data->batch_number;
                $export[] =  $data->rate;
                $export[] =  Displaydateformat($data->expire_date);
                $export[] =  $data->vendor_name;
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Medicine Receiving.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            dd($ex);
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->medicine_receiving->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Medicine Name',
                'HSN Number',
                'Pack Details',
                'Quantity',
                'Batch Number',
                'Rate',
                'Expiry Date',
                'Vendor Name',
                'Created_by',
                'Created_at'
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Medicine Receiving",
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

            $view = view('ohcmanagement.medicine_receiving.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Medicine Receiving.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            dd($ex);
        }
    }
    public function hsnnumber(Request $request)
    {
        $medicineID = decryptId($request->medicine_id);
        $hsnnumber = $this->medicine->hsnajaxData($medicineID);

        if ($hsnnumber) {
            return response()->json([
                'id' => $hsnnumber->id,
                'text' => $hsnnumber->hsn,
                'encrypted_id' => encrypt($hsnnumber->id),
            ]);
        }

        return response()->json(['error' => 'No HSN number found'], 404);
    }
}
