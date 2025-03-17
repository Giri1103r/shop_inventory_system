<?php

namespace App\Http\Controllers\Inspection;

use App\Http\Controllers\Controller;
use App\Models\Inspection\GembaWalk;
use App\Models\Inspection\GembaWalkChecklist;
use App\Models\Inspection\GembaWalkChecklistObservation;
use App\Models\Master\Employee;
use App\Models\Master\Location;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;


class GembaWalkController extends Controller
{
    private $location;
    private $unit;
    private $employee;
    private $gembaWalk;
    private $gembaWalkCheckList;
    private $gembaWalkCheckListObservation;



    public function __construct()
    {
       $this->location = new Location();
       $this->unit = new Unit();
       $this->employee = new Employee();
       $this->gembaWalk = new GembaWalk();
       $this->gembaWalkCheckList = new GembaWalkChecklist();
       $this->gembaWalkCheckListObservation = new GembaWalkChecklistObservation();


    }
    


    public function index(Request $request)
    {
        // dd(11111);
        if (Auth::check()) {
            if ($request->ajax()) {

                try {

                    $data =  $this->gembaWalk->list();


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
                        ->addColumn('issue_date', function ($row) {
                            return Displaydateformat($row->issue_date);
                        })
                        ->addColumn('revision_date', function ($row) {
                            return Displaydateformat($row->revision_date);
                        })
                        ->addColumn('created_at', function ($row) {
                            return Displaydateformat($row->created_at);
                        })
                        ->addColumn('created_by', function ($row) {
                            return getUsername($row->created_by);
                        })
                        ->addColumn('action', function ($row) {
                            $btn = '';
                            // if (CheckUserPermission('view')) {
                            $btn = '<a href="' . admin_url('inspection/gemba-walk/view/' . encryptId($row->id)) . '"   class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            // }
                            return $btn;
                        })
                        ->rawColumns(['action', 'created_date', 'created_by', 'status', 'status_batch'])
                        ->setFilteredRecords($data['filter_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);
                    return $datatables;
                } catch (Exception $ex) {

                    dd($ex);
                    report($ex);
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }
       

        return view('inspection.gembaWalk.list');
    }

    
    public function Add(Request $request)
    {
        try {

            $locationList= $this->location->getLocationName();
            $unitList = $this->unit->getUnitList();
            $employeeList = $this->employee->getEmployeeList();



            $data = array(
                'locationList' => $locationList,
                'unitList' => $unitList,
                'employeeList'=>$employeeList

            );
            
            return view('inspection.gembaWalk.add',$data);
        } catch (Exception $ex) {
            report($ex);
        }
    }



    public function Store(Request $request)
    {
        // dd($request->all());

        try{
             $gembaWalk = $this->gembaWalk->store();

             $gembaWalkChecklist = $this->gembaWalkCheckList->store($gembaWalk->id);
            
             Session::flash('success', 'Your data has been created successfully!');

            return redirect(admin_url('inspection/gemba-walk/list'));

        }catch(Exception $ex){
            dd($ex);
            report($ex);
        }
       
    }

    public function view($id){
        try{
                $id = decryptId($id);
                if (Auth::check()) {
                    $gembaWalk_details = $this->gembaWalk->selectOne($id);
    
                    $data = array(
                        'gembaWalk_details' => $gembaWalk_details,
                    );
                }
                return view('inspection.gembaWalk.view',$data);
        }catch(Exception $ex){
            dd($ex);
            report($ex);
        }
    }
}
