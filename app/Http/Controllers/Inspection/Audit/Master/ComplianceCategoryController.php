<?php

namespace App\Http\Controllers\Inspection\Audit\Master;

use Exception;
use App\Models\UploadLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;
use App\Jobs\ImportComplianceCategoryJob;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Models\Inspection\audit\Master\ComplianceCategory;

class ComplianceCategoryController extends Controller
{
    private $compliance_category;
    private $uploadlog;

    public function __construct()
    {
        $this->compliance_category = new ComplianceCategory();
        $this->uploadlog = new UploadLog();
    }

    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->compliance_category->list();
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
                            $btn = '<a href="' . admin_url('audit/master/compliance_category/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('audit/master/compliance_category/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
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

        return view('inspection.inspection_audit.master.compliance_category.list', $data);
    }

    public function Add(Request $request)
    {
        try {

            $data = array();
            return view('inspection.inspection_audit.master.compliance_category.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('audit/master/compliance_category/list'));
        }
    }

    public function Store(Request $request)
    {
        try {
            $rules = [
                'compliance_category' => 'required',
            ];
            $messages = [
                'compliance_category.required' => __('compliance_category  is required'),
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                dd($validator);
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {
                $this->compliance_category->store();
                Session::flash('success', __('Your data has been created successfully'));
            } catch (Exception $ex) {
                Session::flash('error', __('common.message_error'));
            }
            return redirect(admin_url('audit/master/compliance_category/list'));
        } catch (Exception $ex) {
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('audit/master/compliance_category/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $compliance_category = $this->compliance_category->selectOne($id);

                $data = array(
                    'compliance_category' => $compliance_category,
                );
            }
            return view('inspection.inspection_audit.master.compliance_category.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('audit/master/compliance_category/list'));
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $compliance_category = $this->compliance_category->find($id);

            $data = array(
                'compliance_category' => $compliance_category,
            );
            return view('inspection.inspection_audit.master.compliance_category.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('audit/master/compliance_category/list'));
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $rules = [
                'compliance_category' => 'required',

            ];
            $messages = [
                'compliance_category.required' => __('compliance_category to be taken is required'),

            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }


            $this->compliance_category->updates($id);

            Session::flash('success', __('Your data has been updated successfully'));
            return redirect(admin_url('audit/master/compliance_category/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('Something went wrong try again'));
            return redirect(admin_url('audit/master/compliance_category/list'));
        }
    }

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $compliance_category = $request->compliance_category;
            $id = $request->id;
            if ($id == '') {
                $record = $this->compliance_category->uniqueCheck($compliance_category);
            } else {
                $id = decryptId($id);
                $record = $this->compliance_category->ExistuniqueCheck($compliance_category, $id);
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

            $this->compliance_category->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => __('Compliance Category Status is changed')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('administration.please_try_after_some_time')], 406);
        }
    }

    public function Delete(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $this->compliance_category->deleterecord($id);

            return response()->json(['status' => 'success', 'msg' => __('Compliance Category was deleted successfully')], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => __('administration.please_try_after_some_time')], 406);
        }
    }

    public function ExportExcel(Request $request)
    {

        try {
            $allData = $this->compliance_category->exportdata();
            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __('inspection.compliance_category'),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {
                $export = [];
                $export[] =  $i;
                $export[] =  $data->compliance_category;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);
                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Compliance Category.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('Something went wrong try again'));
            return redirect(admin_url('audit/master/compliance_category/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            ini_set("pcre.backtrack_limit", "5000000");

            $allData = $this->compliance_category->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                __('inspection.compliance_category'),
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Compliance Category",
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

            $view = view('inspection.inspection_audit.master.compliance_category.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Compliance Category.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', __('Something went wrong try again'));
            return redirect(admin_url('audit/master/compliance_category/list'));
        }
    }
}
