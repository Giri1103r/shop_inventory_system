<?php

namespace App\Http\Controllers\Inspection\MSDS;

use App\Http\Controllers\Controller;
use App\Mail\Inspection\MSDS\MSDSEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\Inspection\MSDS\MSDSDetails;
use App\Models\Inspection\MSDS\MSDSCheckList;
use Exception;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Models\Inspection\MSDS\MSDSStatusLog;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Admin\AdminController;
use App\Models\Inspection\MSDS\MSDSSignatureUpload;

class MSDSController extends Controller
{

    private $msdsDetails;
    private $msdsCheckList;
    private $statusLog;
    private $signature;

    public function __construct()
    {
        $this->msdsDetails = new MSDSDetails();
        $this->msdsCheckList = new MSDSCheckList();
        $this->statusLog = new MSDSStatusLog();
        $this->signature = new MSDSSignatureUpload();
    }

    public function Index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->msdsDetails->list();
                    $datatables = DataTables::of($data['data'])
                        ->addIndexColumn()
                        ->addColumn('created_date', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('issue_date', function ($row) {
                            return Displaydateformat($row->issue_date);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            $btn = '<a href="' . admin_url('msds/view/' . encryptId($row->id)) . '"   class="view-icon" title="' . __('common.view') . '"><i class="fa-solid fa-eye"></i></a> ';
                            $btn .= '<a href="' . admin_url('msds/generalpdf/' . encryptId($row->id)) . '" style="margin-right: 5px;" title="PDF">
                                <i class="fas fa-file-pdf"  style="color: #e67265;" aria-hidden="true"></i>
                            </a>';
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date','issue_date','created_by'])
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

        $data = [];

        return view('inspection.msds.list', $data);
    }

    public function add(Request $request)
    {
        try {
            $data = array();
            return view('inspection.msds.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {

        try {
            $rules = [
                'document_number' => 'required',
                'issue_date' => 'required',
                'revision_date' => 'required',
                'serial_number' => 'required',
                'item_code' => 'required',
                'name_of_chemical' => 'required',
                'msds_availability_status' => 'required',
                'remark' => 'required',

            ];
            $messages = [
                'document_number.required' => __('Document Number is required'),
                'issue_date.required' => __('Issue Date is required'),
                'revision_date.required' => __('Revision Date is required'),
                'serial_number.required' => __('Serial Number is required'),
                'item_code.required' => __('Item Code is required'),
                'name_of_chemical.required' => __('Name of Chemical is required'),
                'msds_availability_status.required' => __('MSDS Availability Status is required'),
                'remark.required' => __('Remark is required'),

            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

               $msds = $this->msdsDetails->store();
               $msdsId = $msds->id;
               $this->msdsCheckList->store($msdsId);

               $this->signature->signatureStrore(MSDS_INSPECTION,$msds->id);

                Session::flash('success', __('Your data has been created successfully'));
            } catch (Exception $ex) {
                dd($ex);
                Session::flash('error', __('common.message_error'));
            }
            return redirect(admin_url('msds/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',  __('common.message_error'));
            return redirect(admin_url('msds/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $status_log = $this->statusLog->selectOne($id);
                $msdsDetails = $this->msdsDetails->find($id);
                $inspection_details = $this->msdsDetails->selectOne($id);
                $msdsCheckList = $this->msdsCheckList->selectOne($id);

                $data = array(
                    'msdsDetails' => $msdsDetails,
                    'msdsCheckList' => $msdsCheckList  ?? [],
                    'status_log' => $status_log,
                    'inspection_details' => $inspection_details,
                );
            }
            return view('inspection.msds.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->msdsDetails->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Document Number',
                'Issue Date',
                'Revision Date',
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->document_number;
                $export[] =  $data->issue_date;
                $export[] = $data->revision_date;
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('MSDS.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('msds/list'));
        }
    }

    public function ExportPdf(Request $request)
    {

        try {

            $allData = $this->msdsDetails->exportdata();

            if ($allData->isEmpty()) {
                return redirect()->back()->with('error', 'No data found');
            }

            $header = [
                __("common.sno"),
                'Document Number',
                'Issue Date',
                'Revision Date',
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "MSDS Details",
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

            $view = view('inspection.msds.pdf', $data);
            $html = $view->render();

            $mpdf->WriteHTML($html);

            $filename = "MSDS.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('msds/list'));
        }
    }

    public function generalpdf(Request $request)
    {
        try {
            $id = decryptId($request->id);

            if (Auth::check()) {
                $status_log = $this->statusLog->selectOne($id);
                $msdsDetails = $this->msdsDetails->find($id);
                $inspection_details = $this->msdsDetails->selectOne($id);
                $msdsCheckList = $this->msdsCheckList->selectOne($id);

                $data = [
                    'status_log' => $status_log,
                    'msdsDetails' => $msdsDetails,
                    'inspection_details' => $inspection_details,
                    'msdsCheckList' => $msdsCheckList,
                    'pagetitle' => "MSDS Details",
                ];

            }

            $property = [
                'tempDir' => 'public/pdf/temp/',
                'mode' => 'c',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,

            ];

            $mpdf = new \Mpdf\Mpdf($property);
            $mpdf->setAutoTopMargin = 'stretch';

            $html = view('inspection.msds.generalpdf', $data)->render();
            $mpdf->WriteHTML($html);

            $filename = "MSDS Details.pdf";
            return $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {
            report($ex);
            return redirect()->back()->withErrors(['error' => 'An error occurred while generating the PDF.']);
        }
    }


}
