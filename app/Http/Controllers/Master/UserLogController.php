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


use App\Models\UserLog;
use App\Models\User;

class UserLogController extends Controller
{

    private $userlog;

    public function __construct()
    {

        $this->userlog = new UserLog();
    }


    public function index(Request $request)
    {

        if (Auth::check()) {
            if ($request->ajax()) {
                try {

                    $data =  $this->userlog->list();

                    $datatables = Datatables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('datetime', function ($row) {
                            return Displaydatetimeformat($row->created_at);
                        })
                        ->rawColumns(['datetime'])
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

        $userdetails = User::get();
        $data = array(
            'userdetails' =>  $userdetails ,
        );

        return view('master.userlog.list', $data);
    }


    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->userlog->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                'No.',
                'User Name',
                'URL',
                'Session ID',
                'IP Address',
                'Date & Time',
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export['No.'] =  $i;
                $export['User Name'] =  $data->name;
                $export['URL'] =  $data->request_uri;
                $export['Session ID'] =  $data->session_id;
                $export['IP Address'] =  $data->client_ip;
                $export['Date & Time'] =  Displaydatetimeformat($data->created_at);

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
