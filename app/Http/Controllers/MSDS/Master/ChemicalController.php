<?php

namespace App\Http\Controllers\MSDS\Master;

use Exception;
use App\Models\UploadLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inspection\MSDS\Master\Chemical;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;

class ChemicalController extends Controller
{
    private $chemical;
    private $uploadlog;

    public function __construct()
    {
        $this->chemical = new Chemical();
        $this->uploadlog = new UploadLog();
    }

    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->chemical->list();
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
                        ->addColumn('created_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('msds/master/chemicals/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('msds/master/chemicals/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            // $btn .= '<a href="javascript:void(0);"  data-id="' . encryptId($row->id) . '" class="recordDelete" title="Delete"><i class="fa-solid fa-trash text-danger" ></i></i></a> ';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);
                    return $datatables;
                } catch (Exception $ex) {
                    return response()->json(['status' => 'error', 'msg' => __('Inspection.please_try_after_some_time')], 406);
                }
            }
        }

        $data = array();

        return view('inspection.msds.master.chemical.list', $data);
    }

    public function Add(Request $request)
    {
        try {

            $data = array();
            return view('inspection.msds.master.chemical.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('msds/master/chemicals/list'));
        }
    }

    public function Store(Request $request)
    {
        try {
            $rules = [
                'chemical' => 'required',
            ];
            $messages = [
                'chemical.required' => __('chemical  is required'),
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {
                $this->chemical->store();
                Session::flash('success', __('Your data has been created successfully'));
            } catch (Exception $ex) {
                Session::flash('error', __('common.message_error'));
            }
            return redirect(admin_url('msds/master/chemicals/list'));
        } catch (Exception $ex) {
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('msds/master/chemicals/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $chemical = $this->chemical->selectOne($id);

                $data = array(
                    'chemical' => $chemical,
                );
            }
            return view('inspection.msds.master.chemical.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('msds/master/chemicals/list'));
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $chemical = $this->chemical->find($id);

            $data = array(
                'chemical' => $chemical,
            );
            return view('inspection.msds.master.chemical.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('msds/master/chemicals/list'));
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $rules = [
                'chemical' => 'required',

            ];
            $messages = [
                'chemical.required' => __('chemical to be taken is required'),

            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }


            $this->chemical->updates($id);

            Session::flash('success', __('Your data has been updated successfully'));
            return redirect(admin_url('msds/master/chemicals/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('Something went wrong try again'));
            return redirect(admin_url('msds/master/chemicals/list'));
        }
    }

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $chemical = $request->chemical;
            $id = $request->id;
            if ($id == '') {
                $record = $this->chemical->uniqueCheck($chemical);
            } else {
                $id = decryptId($id);
                $record = $this->chemical->ExistuniqueCheck($chemical, $id);
            }
            if ($record->count()) {
                return Response::json(false);
            }
            return Response::json(true);
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->chemical->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => __('Chemical Status is changed')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('administration.please_try_after_some_time')], 406);
        }
    }

    public function Delete(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $this->chemical->deleterecord($id);

            return response()->json(['status' => 'success', 'msg' => __('Chemical was deleted successfully')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('administration.please_try_after_some_time')], 406);
        }
    }

    public function ExportExcel(Request $request)
    {

        try {
            $allData = $this->chemical->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __('inspection.chemical'),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {
                $export = [];
                $export[] =  $i;
                $export[] =  $data->chemical;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);
                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Chemical.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('Something went wrong try again'));
            return redirect(admin_url('msds/master/chemicals/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            ini_set("pcre.backtrack_limit", "5000000");

            $allData = $this->chemical->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __('inspection.chemical'),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Chemical",
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

            $view = view('inspection.msds.master.chemical.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Chemical.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('Something went wrong try again'));
            return redirect(admin_url('msds/master/chemicals/list'));
        }
    }
}
