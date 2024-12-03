<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\PpeType;
use App\Models\Master\PpeTypeMaster;
use App\Models\UploadLog;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\DataTables;

class PpeTypeMasterController extends Controller
{
    private $ppetypemaster;
    private $ppetype;
    private $uploadlog;

    public function __construct()
    {
        $this->ppetypemaster = new PpeTypeMaster();
        $this->ppetype = new PpeType();
        $this->uploadlog = new UploadLog();
    }

    public function index(Request $request)
    {

        if (Auth::check()) {
            if ($request->ajax()) {
                try {

                    $data =  $this->ppetypemaster->list();
                    $datatables = DataTables::of($data['data'])
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
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('ppe_type', function ($row) {
                            return getPpeType($row->ppe_type);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';

                            $btn = '<a href="' . admin_url('ppe_ppetype_master/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('ppe_ppetype_master/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            $btn .= '<a href="javascript:void(0);"  data-id="' . encryptId($row->id) . '" class="recordDelete" title="Delete"><i class="fa-solid fa-trash text-danger" ></i></i></a> ';
                            return $btn;
                        })
                        ->rawColumns(['action','created_date', 'created_by', 'status'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);
                    return $datatables;
                } catch (Exception $ex) {
                    return response()->json(['status' => 'error', 'msg' => __('ppe.please_try_after_some_time')], 406);
                }
            }
        }

        $data = array();

        return view('master.ppetypemaster.list', $data);
    }

    public function add(){

        $ppetype =$this->ppetype->getPpetypedata();
        // $quantity =$this->ppetypemaster->getquantity();
        $data =[
            'ppetype'=>$ppetype,
            // 'quantity'=>$quantity
        ];
        return view('master.ppetypemaster.add',$data);
    }

    public function store(Request $request){
        try {
            $rules = [
                'item_code' => 'required',
                'ppe_name' => 'required',
                'ppe_type' => 'required',
                'ppe_standard' => 'required',
                'protection_category' => 'required',

            ];
            $messages = [
                'item_code.required' => __('Item Code  is required'),
                'ppe_name.required' => __('PPE Name  is required'),
                'ppe_type.required' => __('PPE Type  is required'),
                'ppe_standard.required' => __('PPE Standard  is required'),
                'protection_category.required' => __('Protection Category  is required'),

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

                $this->ppetypemaster->store();

                Session::flash('success', __('PPE Type master is taken  added successfully'));
            } catch (Exception $ex) {
                Session::flash('error', __('common.message_error'));
            }
            return redirect(admin_url('ppe_ppetype_master/list'));
        } catch (Exception $ex) {

            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ppe_ppetype_master/list'));
        }
    }

    public function view(Request $request){
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $ppetypemaster = $this->ppetypemaster->selectOne($id);
            }
            $data =[
              'ppetypemaster'=>  $ppetypemaster
            ];
            return view('master.ppetypemaster.view',$data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function edit(Request $request){
        try {
            $id = decryptId($request->id);
            $ppetypemaster = $this->ppetypemaster->find($id);
            $ppetype =$this->ppetype->getPpetypedata();
            $data = array(
                'ppetypemaster' => $ppetypemaster,
                'ppetype'=>$ppetype,
                'encryptid' => $request->id,
            );
            return view('master.ppetypemaster.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function update(Request $request){
        try {
              $id = decryptId($request->id);
            $rules = [
                'item_code' => 'required',
                'ppe_name' => 'required',
                'ppe_type' => 'required',
                'ppe_standard' => 'required',
                'protection_category' => 'required',

            ];
            $messages = [
                'item_code.required' => __('Item Code  is required'),
                'ppe_name.required' => __('PPE Name  is required'),
                'ppe_type.required' => __('PPE Type  is required'),
                'ppe_standard.required' => __('PPE Standard  is required'),
                'protection_category.required' => __('Protection Category  is required'),

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

                $this->ppetypemaster->updates($id);

                Session::flash('success', __('PPE Type master is taken updated successfully'));
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', __('common.message_error'));
            }
            return redirect(admin_url('ppe_ppetype_master/list'));
        } catch (Exception $ex) {
             dd($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('ppe_ppetype_master/list'));
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->ppetypemaster->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => __('PPE Type Master to be taken status changed')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('administration.please_try_after_some_time')], 406);
        }
    }

    public function Delete(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $this->ppetypemaster->deleterecord($id);

            return response()->json(['status' => 'success', 'msg' => __('PPE Type Master to be taken deleted successfully')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('administration.please_try_after_some_time')], 406);
        }
    }



    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->ppetypemaster->exportdata();

            $header = [
                __("common.sno"),
                __("Item Code"),
                __('PPE Name'),
                __("PPE Type"),
                __("PPE Standard"),
                __("Protection Category"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->item_code;
                $export[] =  $data->ppe_name;
                $export[] = getPpeType( $data->ppe_type);
                $export[] =  $data->ppe_standard;
                $export[] =  $data->ppe_category;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('PPE Type Master to be taken .xlsx')
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

            ini_set("pcre.backtrack_limit", "5000000");

            $allData = $this->ppetypemaster->exportdata();

            $header = [
                __("common.sno"),
                __("Item Code"),
                __('PPE Name'),
                __("PPE Type"),
                __("PPE Standard"),
                __("Protection Category"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "PPE Type Master",
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

            $view = view('master.ppetypemaster.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Precation to be takens Details.pdf";
            $mpdf->Output($filename, 'I');
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
        }
    }

    public function list(Request $request)
    {
        dd(1);
        if ($request->ajax()) {
            $PPEtypeId = $request->input('id');
            return $this->ppetypemaster->ajaxlist($PPEtypeId);
        }
    }


    public function imageList(Request $request) {

        if ($request->ajax()) {
            $ppeNameId = $request->input('id');
            return $this->ppetypemaster->imageList($ppeNameId);
        }

    }


}
