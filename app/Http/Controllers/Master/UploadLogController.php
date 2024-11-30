<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;

use PDF;
use Illuminate\Support\Facades\Auth;
use Session;
use Storage;
use Exception;
use DataTables;


use App\Models\UploadLog;
use App\Models\UploadLogError;


class UploadLogController extends Controller
{

    private $uploadlog;
    private $uploadlogerror;

    public function __construct()
    {

        $this->uploadlog = new UploadLog();
        $this->uploadlogerror = new UploadLogError();
    }


    public function index(Request $request)
    {

        if (Auth::check()) {
            if ($request->ajax()) {
                try {

                    $data =  $this->uploadlog->list();

                    $datatables = Datatables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('datetime', function ($row) {

                            return Displaydatetimeformat($row->created_at);
                        })

                        ->addColumn('uploadstatus', function ($row) {

                            if ($row->upload_status == 0) {
                                $text = "<span class='badge bg-info'>Pending</span>";
                            } else if ($row->upload_status == 1) {
                                $text = "<span class='badge bg-warning'>In-Progress</span>";
                            } else if ($row->upload_status == 2) {
                                $text = "<span class='badge bg-success '>Completed</span>";
                            }
                            return $text;
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('uploadlog/list/' . encryptId($row->id)) . '"   class="view-icon" title="View"><i class="fa-solid fa-eye"></i></a> ';

                            return $btn;
                        })
                        ->rawColumns(['action', 'uploadstatus', 'created_by', 'status'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);
                    return $datatables;
                } catch (Exception $ex) {

                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }

        $data = array();

        return view('master.uploadlog.list', $data);
    }

    public function view(Request $request)
    {

        if (Auth::check()) {
            if ($request->ajax()) {
                try {

                    $data =  $this->uploadlogerror->list();

                    $datatables = Datatables::of($data['data'])
                        ->addIndexColumn()
                       ->rawColumns(['action', 'uploadstatus', 'created_by', 'status'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);
                    return $datatables;
                } catch (Exception $ex) {

                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }

        $data = array();

        return view('master.uploadlog.view', $data);
    }

    public function download(Request $request)
    {

        $file_id = decryptId($request->logid);


        $file = UploadLog::where('id', $file_id)->first();

        return response()->download($file->file_path,$file->file_orgname);

    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->uploadlogerror->exportdata();

            $header = [
                'No.',
                'Line No',
                'Error',
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export['No.'] =  $i;
                $export['Line No'] =  $data->line_no;
                $export['Error'] =  $data->error;

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Upload Error Log.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            report($ex);
        }
    }
}
