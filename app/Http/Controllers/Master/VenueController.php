<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Facades\File;
use App\Models\Master\Unit;

use Str;
use PDF;
use Mail;
use Illuminate\Support\Facades\Auth;
use Session;
use Exception;
use DataTables;
use Response;
use App\Models\Master\Venue;
use App\Models\User;
use App\Models\UploadLog;
use App\Jobs\ImportVenueJob;


class VenueController extends Controller
{

    private $venue;
    private $user;
    private $uploadlog;
    private $unit;


    public function __construct()
    {

        $this->venue = new Venue();
        $this->user = new User();
        $this->unit = new Unit();
        $this->uploadlog = new UploadLog();
    }


    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {

                try {

                    $data =  $this->venue->list();

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
                                $btn = '<a href="' . admin_url('venue/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            }
                            if (CheckUserPermission('edit')) {
                                $btn .= '<a href="' . admin_url('venue/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            }

                            if (CheckUserPermission('delete')) {
                                $btn .= '<a href="javascript:void(0);"  data-id="' . encryptId($row->id) . '"  data-login_id="' . encryptId($row->login_id) . '" class="recordDelete" title="Delete"><i class="fa-solid fa-trash text-danger" ></i></i></a> ';
                            }
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status'])
                        ->setFilteredRecords($data['total_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);
                    return $datatables;
                } catch (Exception $ex) {

                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }
        $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();

        $data = array(
            'unitList' => $unitList
        );

        return view('master.venue.list', $data);
    }

    public function Add(Request $request)
    {

        try {

            $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();

            $data = array(
                'unitList' => $unitList
            );

            return view('master.venue.add', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Store(Request $request)
    {
        try {

            $rules = [
                'name_of_the_conference_hall' => 'required',
                'unit_id' => 'required',
                'capacity' => 'required',
                'projector_or_lcd_availability' => 'required',
            ];
            $messages = [
                'name_of_the_conference_hall.required' => 'Please Enter Name of the Conference Hall',
                'unit_id.required' => 'Please Select the Unit Name',
                'capacity.required' => 'Please enter Capacity',
                'projector_or_lcd_availability.required' => 'Please Select the Projector/LCD Availability',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {

                $venue = $this->venue->store();
                Session::flash('success', 'Venue added successfully!');
            } catch (Exception $ex) {
                report($ex);
                Session::flash('error', 'Something went wrong, Please try after sometimes!');
            }

            return redirect(admin_url('venue/list'));
        } catch (Exception $ex) {


            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('venue/list'));
        }
    }

    public function View(Request $request)
    {
        try {
            $id = decryptId($request->id);
            if (Auth::check()) {
                $venue = $this->venue->selectOne($id);

                $data = array(
                    'venue' => $venue,
                );
            }
            return view('master.venue.view', $data);
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $unitList  = $this->unit->select('id', 'unit_name')->where('status', '1')->get();


            $venue = $this->venue->find($id);
            $data = array(
                'venue' => $venue,
                'unitList' => $unitList,
            );


            return view('master.venue.edit', $data);
        } catch (Exception $error) {
            report($error->getMessage());
        }
    }

    public function Update(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $rules = [
                'name_of_the_conference_hall' => 'required',
                'unit_id' => 'required',
                'capacity' => 'required',
                'projector_or_lcd_availability' => 'required',
            ];
            $messages = [
                'name_of_the_conference_hall.required' => 'Please Enter Name of the Conference Hall',
                'unit_id.required' => 'Please Select the Unit Name',
                'capacity.required' => 'Please enter Capacity',
                'projector_or_lcd_availability.required' => 'Please Select the Projector/LCD Availability',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->venue->updates($id);

            Session::flash('success', 'Venue updated successfully!');
            return redirect(admin_url('venue/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went wrong, Please try after sometimes!');
            return redirect(admin_url('venue/list'));
        }
    }


    public function StatusChange(Request $request)
    {

        try {
            $id = decryptId($request->id);

            $this->venue->statuschange($id);

            return response()->json(['status' => 'success', 'msg' => 'Venue status changed'], 200);
        } catch (Exception $ex) {

            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }

    public function Delete(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $this->venue->deleterecord($id);

            return response()->json(['status' => 'success', 'msg' => 'Venue deleted successfully'], 200);
        } catch (Exception $ex) {
            report($ex);
            return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
        }
    }
    public function Import(Request $request)
    {
        $data = array();
        return view('master.venue.import', $data);
    }
    public function ImportSubmit(Request $request)
    {
        try {
            $file = $request->file('venue_upload');

            $rules = [
                'venue_upload' => 'required',
            ];
            $messages = [
                'venue_upload.required' => 'Please upload a file',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }


            if ($file != null) {

                $uploadpath = 'public/uploads/venue';

                $folderPath = public_path('uploads/venue');

                if (!File::exists($folderPath)) {

                    File::makeDirectory($folderPath, 0755, true);
                }

                $filenewname = time() . Str::random('10') . '.' . $file->getClientOriginalExtension();

                $fileName = $file->getClientOriginalName();
                $fileSize = $file->getSize();

                $fileExt = $file->getClientOriginalExtension();

                $file->move($uploadpath, $filenewname);

                $path = $uploadpath . "/" . $filenewname;
                $user_id = Auth::id();

                $insert_data = array(
                    'upload_type' => 1,
                    'upload_status' => 0,
                    'file_name' => $filenewname,
                    'file_orgname' => $fileName,
                    'file_path' => $path,
                    'file_size' => $fileSize,
                    'file_extension' => $fileExt,
                    'created_by' => $user_id,
                );

                $insert_id =  $this->uploadlog->create($insert_data)->id;



                $details = [
                    "user_id" => $user_id,
                    "log_id" => $insert_id,
                    "path" => $path,
                ];

                dispatch(new ImportVenueJob($details));
                //    dispatch((new ImportVenueJob($details))->onQueue('empimport'));
            }

            $insert_data['log_id'] = $insert_id;
            $insert_data['Uploded_by'] = Auth::user()->toArray();

            Session::flash('success', __('Venue uploaded sucessfully'));
            return redirect(admin_url('venue/list'));
        } catch (Exception $ex) {

            Session::flash('error', __('Venue upload failed'));
            return redirect(admin_url('venue/list'));
        }
    }
    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->venue->exportdata();

            $header = [
                __("common.sno"),
                'Name of the Conference Hall',
                'Unit Name',
                'Capacity',
                'Projector/LCD Availability',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->name_of_the_conference_hall;
                $export[] =  $data->unit_name;
                $export[] =  $data->capacity;
                $export[] =  $data->projector_or_lcd_availability;
                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Venue Details.xlsx')
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

            $allData = $this->venue->exportdata();

            $header = [
                __("common.sno"),
                'Name of the Conference Hall',
                'Unit Name',
                'Capacity',
                'Projector/LCD Availability',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Venue Details",
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

            $view = view('master.venue.pdf', $data);
            $html = $view->render();



            $mpdf->WriteHTML($html);

            $filename = "Venue.pdf";
            $mpdf->Output($filename, 'I');
        } catch (Exception $ex) {

            report($ex);
        }
    }

    public function DownloadSample(Request $request)
    {

        $filedetails =  exportsamplefile('venue');

        $filePath = $filedetails->sample_file;
        $customFileName = $filedetails->file_name;

        //return Response::download($filePath, $customFileName);
        return redirect(url($filePath));
    }
}
