<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

use App\Models\Master\Shift;
use App\Models\User;
use App\Models\Administration\UploadLog;


class ShiftController extends Controller
{


    private $user;
    private $uploadlog;

    private $shift;


    public function __construct()
    {


        $this->user = new User();
        $this->shift = new Shift();

        $this->uploadlog = new UploadLog();
    }


    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {

                try {

                    $data =  $this->shift->list();

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
                            if (CheckUserPermission('view')) {
                                $btn = '<a href="' . admin_url('master/shift/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            }
                            if (CheckUserPermission('edit')) {
                                $btn .= '<a href="' . admin_url('master/shift/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            }
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
        $data = array();
        return view('admin.master.shift.list', $data);
    }

    public function Add(Request $request)
    {

        try {

            $data = array();
            return view('admin.master.shift.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('master/shift/list'));
        }
    }

    public function Store(Request $request)
    {
        try {
            DB::beginTransaction();

            $rules = [
                'shift_name' => 'required|unique:masters_shift,shift_name',
                'start_time' => 'required',
                'end_time' => 'required',
                'total_hours' => 'required|numeric',
            ];
            $messages = [
                'shift_name.required' => 'Please Enter Shift Name',
                'shift_name.unique' => 'Shift Name should be unique',
                'start_time.required' => 'Please Select Start Time',
                'end_time.required' => 'Please Select End Time',
                'total_hours.required' => 'Please Enter Total Hours',
                'total_hours.numeric' => 'Total Hours should be a number',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }


            $this->shift->store();
            DB::commit();

            Session::flash('success', 'Your data has been created successfully!');
            return redirect(admin_url('master/shift/list'));
        } catch (Exception $ex) {

            report($ex);
            DB::rollBack();
            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('master/shift/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $shift = $this->shift->selectOne($id);

            if (!$shift) {
                Session::flash('error', __('common.error_msg'));
                return redirect(admin_url('master/shift/list'));
            }

            $data = array(
                'shift' => $shift,
            );

            return view('admin.master.shift.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('master/shift/list'));
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $shift = $this->shift->find($id);

            if (!$shift) {
                Session::flash('error', __('common.error_msg'));
                return redirect(admin_url('master/shift/list'));
            }

            $data = array(
                'shift' => $shift,
            );

            return view('admin.master.shift.edit', $data);
        } catch (Exception $error) {
            report($error);

            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('master/shift/list'));
        }
    }

    public function Update(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = decryptId($request->id);
            $rules = [
                'shift_name' => 'required|unique:masters_shift,shift_name,' . $id . ',id',
                'start_time' => 'required',
                'end_time' => 'required',
                'total_hours' => 'required|numeric',
            ];
            $messages = [
                'shift_name.required' => 'Please Enter Shift Name',
                'shift_name.unique' => 'Shift Name should be unique',
                'start_time.required' => 'Please Select Start Time',
                'end_time.required' => 'Please Select End Time',
                'total_hours.required' => 'Please Enter Total Hours',
                'total_hours.numeric' => 'Total Hours should be a number',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->shift->updates($id);
            DB::commit();

            Session::flash('success', __('common.updated_msg'));
            return redirect(admin_url('master/shift/list'));
        } catch (Exception $ex) {
            report($ex);
            DB::rollBack();
            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('master/shift/list'));
        }
    }

    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->shift->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Shift status changed'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function Delete(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $this->shift->deleterecord($id);

            return response()->json(['status' => 'success', 'msg' => 'Shift deleted successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }


    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->shift->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Shift ID',
                'Shift Name',
                'Start Time',
                'End Time',
                'Total Hours',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $exportData = [];
            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->shift_code;
                $export[] =  $data->shift_name;
                $export[] =  $data->start_time;
                $export[] =  $data->end_time;
                $export[] =  $data->total_hours;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Shift Master.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('master/shift/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->shift->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Shift ID',
                'Shift Name',
                'Start Time',
                'End Time',
                'Total Hours',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Shift Details",
            );

            $property = [
                'tempDir' => public_path('pdf/temp'),
                'mode' => 'c',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,

            ];

            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';

            $view = view('admin.master.shift.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "Shift Master.pdf";

            /* Hand the PDF back as a response instead of echoing it: mPDF sends
               its own Content-Length and then returns, so letting the action fall
               through made the framework append a second body to the download. */
            return response($mpdf->Output($filename, \Mpdf\Output\Destination::STRING_RETURN), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', __('common.error_msg'));
            return redirect(admin_url('master/shift/list'));
        }
    }

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {

            $shift_name = $request->shift_name;
            $id = $request->id;

            if ($id == '') {

                $record = $this->shift->uniqueCheck($shift_name);
            } else {

                $id = decryptId($id);
                $record = $this->shift->ExistuniqueCheck($shift_name, $id);
            }
            if ($record->count()) {
                return Response::json(false);
            }
            return Response::json(true);
        }
    }

    public function list(Request $request)
    {
        $id = decryptId($request->id);
        $shifts = $this->shift->ajaxList($id);

        return response()->json($shifts);
    }

    public function multipleList(Request $request)
    {
        $preselectedIds = array_map('decryptId', $request->input('preselected_ids', []));

        $shifts = $this->shift->multipleAjaxList($preselectedIds);

        return response()->json($shifts);
    }
}
