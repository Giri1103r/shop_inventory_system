<?php

namespace App\Http\Controllers\Ppemanagement;

use App\Http\Controllers\Controller;
use App\Models\Master\PpeStockinventory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;

class PpeStockInventoryController extends Controller
{
    private $ppestock;

    private $uploadlog;

    public function __construct()
    {
        $this->ppestock = new PpeStockinventory();

    }
    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->ppestock->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('status', function ($row) {
                            $text = "<span style='color:red'>In-Active</span>";
                            if ($row->status == 1) {
                                $text = "<span style='color:green;cursor:pointer' class='statusChange' data-id='" . encryptId($row->id) . "' data-type='1'>Active</span>";
                            } else if ($row->status == 0) {
                                $text = "<span style='color:red;cursor:pointer' class='statusChange' data-id='" . encryptId($row->id) . "' data-type='0'>In-Active</span>";
                            }
                            return $text;
                        })
                        ->addColumn('created_at', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })

                        ->addColumn('action', function ($row) {
                            $btn = '';

                            $btn .= '<a href="' . admin_url('ppe_stock_inventory/view/' . encryptId($row->id)) . '" class="" title="View"><i class="fa-solid fa-eye"></i></a> ';

                            return $btn;
                        })

                        ->rawColumns(['action', 'created_at', 'created_by','status'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);

                    return response()->json($datatables->getData());
                } catch (Exception $ex) {
                    report($ex);
                    return response()->json(['status' => 'error', 'msg' => __('ppe.please try after some time')], 406);
                }
            }
        }


     $data = array();

        return view('ppemanagement.ppestock.list', $data);
    }

    public function view(Request $request)
    {

        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $ppestock = $this->ppestock->selectOne($id);
            }

            $data = [
                'ppestock' =>  $ppestock,

            ];
            return view('ppemanagement.ppestock.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->ppestock->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => __('PPE Request status changed sucessfully')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('Please try after some time')], 406);
        }
    }


    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->ppestock->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __("Org"),
                __('Inventory Item Id'),
                __("Item Code"),
                __("SUB"),
                __("UOM"),
                __("Quantity"),
                __("Item Description"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];


            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->org;
                $export[] =  $data->inventory_item_id;
                $export[] =  $data->item_code;
                $export[] =  $data->sub;
                $export[] =  $data->uom;
                $export[] =  $data->quantity;
                $export[] =  $data->item_description;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('PPE Stock Inventory.xlsx')
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

            $allData = $this->ppestock->exportdata();



            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __("Org"),
                __('Inventory Item Id'),
                __("Item Code"),
                __("SUB"),
                __("UOM"),
                __("Quantity"),
                __("Item Description"),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];



            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "PPE Stock Inventory",
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

            $view = view('ppemanagement.ppestock.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "PPE Exemption.pdf";
            $mpdf->Output($filename, 'I');
        } catch (Exception $ex) {
            dd($ex);

        }
    }
}
