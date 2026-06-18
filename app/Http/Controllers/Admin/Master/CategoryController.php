<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Exceptions\Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;

class CategoryController extends Controller
{

    private $category;
    public function __construct()
    {

        $this->category = new Category();
    }
    public function index(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data =  $this->category->list();

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
                            // if (CheckUserPermission('view')) {
                            $btn = '<a href="' . admin_url('master/category/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            // if (CheckUserPermission('edit')) {
                            $btn .= '<a href="' . admin_url('master/category/edit/' . encryptId($row->id)) . '" class=" " title="Edit"><i class="fa-solid fa-pen-to-square"></i> ';
                            // }


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
        $categoryList = $this->category->where('trash', 'No')->get();
        $data = array(
            'categoryList' => $categoryList,
        );
        return view('admin.master.category.list', $data);
    }

    public function add()
    {
        try {
            $data = [];
            return view('admin.master.category.add', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',);
            return redirect(admin_url('master/category/list'));
        }
    }
    public function Edit(Request $request)
    {
        try {
            $id = decryptId($request->id);
            $category = $this->category->find($id);
            $data = [
                'category' => $category,
            ];
            return view('admin.master.category.edit', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error',);
            return redirect(admin_url('master/category/list'));
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'category_name' => 'required',
                'category_code' => 'required',
            ];
            $messages = [

                'category_name.required' => 'Please Enter Category Name',
                'category_code.required' => 'Please Enter Category Code',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $category = $this->category->store();
            Session::flash('Success', 'Your Data has been Created Successfully');
            return redirect(admin_url('master/category/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Some thing went wrong Please try again after some time');
            return redirect(admin_url('master/category/list'));
        }
    }
    public function update(Request $request)
    {
        try {
            $rules = [
                'category_name' => 'required',
                'category_code' => 'required',
            ];
            $messages = [

                'category_name.required' => 'Please Enter Category Name',
                'category_code.required' => 'Please Enter Category Code',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $id = decryptId($request->id);
            $category = $this->category->updates($id);
            Session::flash('Success', 'Your Data has been Created Successfully');
            return redirect(admin_url('master/category/list'));
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Some thing went wrong Please try again after some time');
            return redirect(admin_url('master/category/list'));
        }
    }

    public function view(Request $request)
    {
        try {
            $id = decryptId($request->id);

            $category =  $this->category->selectOne($id);
            $data = [
                'category' => $category,
            ];
            return view('admin.master.category.view', $data);
        } catch (Exception $ex) {
            report($ex);
            Session::flash('error', 'Something went Wrong Please Try again After Some time');
            return redirect(admin_url('master/category/list'));
        }
    }

    public function Uniquecheck(Request $request)
    {
        if ($request->ajax()) {
            $category = $request->category_code;
            $category_name = $request->category_name;

            $id = $request->id ? decryptId($request->id) : null;

            if (empty($id)) {
                $exists = $this->category->uniqueCheck($category, $category_name)->exists();
            } else {

                $exists = $this->category->ExistuniqueCheck($category, $category_name, $id)->exists();
            }
            return Response::json(!$exists);
        }
    }

    public function ExportExcel(Request $request)
    {

        try {

            $allData = $this->category->exportdata();
           

            $header = [
                __("common.sno"),
                'Category ID',
                'Category Code',
                'Category Name',
                'Description',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $i = 1;
            foreach ($allData as $data) {

                $export = [];
                $export[] =  $i;
                $export[] =  $data->category_id;
                $export[] =  $data->category_code;
                $export[] =  $data->category_name;
                $export[] =  $data->description;

                $export[] =  $data->status == 1 ? 'Active' : 'In-Active';
                $export[] =  getusername($data->created_by);
                $export[] =  Displaydateformat($data->created_at);

                $exportData[] = $export;

                $i++;
            }

            $writer = SimpleExcelWriter::streamDownload('Category Details.xlsx')
                ->addHeader($header)
                ->addRows(
                    $exportData
                );
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went Wrong Please Try again After Some time');
            return redirect(admin_url('master/category/list'));
        }
    }

    public function ExportPdf(Request $request)
    {
        try {

            $allData = $this->category->exportdata();
            
           
            $header = [
                __("common.sno"),
                'Category ID',
                'Category Code',
                'Category Name',
                'Description',
                __("common.status"),
                __("common.created_by"),
                __("common.created_date"),
            ];

            $data = array(
                'header' => $header,
                'content' => $allData,
                'pagetitle' => "Category Details",
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

            $view = view('admin.master.category.pdf', $data);
            $html = $view->render();


            $mpdf->WriteHTML($html);

            $filename = "Category.pdf";
            $mpdf->Output($filename, 'D');
        } catch (Exception $ex) {

            report($ex);
            Session::flash('error', 'Something went Wrong Please Try again After Some time');
            return redirect(admin_url('master/category/list'));
        }
    }
}
