<?php

namespace App\Http\Controllers\OhcManagement;

use App\Http\Controllers\Controller;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\OhcManagement\Report\Inventory;

class OhcDashboardController extends Controller
{
    private $unit;
    public function __construct() {
        $this->unit = new Unit();
    }

    public function index(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();

            $data = [];
            $unit_id = null;
            if (checkUserRole(ROLE_PARAMEDICS) || checkUserRole(ROLE_CERTIFIED_FIRST_AIDER)) {

                $unit_id = $user->unit_id;
            } else if ($request->has('unit_id') && !empty($request->unit_id) && (checkUserRole(ROLE_SUPERADMIN) || checkUserRole(ROLE_EHS_HEAD))) {
                // dd( decryptId($request->unit_id));
                $unit_id = decryptId($request->unit_id);
            } else {
                // dd( 3);
                $unit_id = $user->unit_id;
            }


                // dd( $unit_id);
                $masterLink = [
                    [
                        'link' => 'ohc/medicine-requisition/list',
                        'name' => 'Pending Requests',
                        'count' => getohctotalCount('requisition',$unit_id),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],
                    [

                        'name' => 'Current Stock',
                        'count' => getohctotalCount('medicine'),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],
                    [

                        'name' => 'Today’s Purchase',
                        'count' => getohctotalCount('medicineReceiving'),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],
                    [

                        'name' => 'Medicine Issuance',
                        'count' => getohctotalCount('usermedicineissuance',$unit_id),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],

                    [

                        'name' => "Unit 1",
                        'count' => getohctotalCount('prescribetopatient1'),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],
                    [

                        'name' => "Unit 2",
                        'count' => getohctotalCount('prescribetopatient2'),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],
                    [

                        'name' => "Unit 3",
                        'count' => getohctotalCount('prescribetopatient3'),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],
                    [

                        'name' => "Unit 4",
                        'count' => getohctotalCount('prescribetopatient4'),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],

                    [

                        'name' => "Today's OPD",
                        'count' => getohctotalCount('prescribetopatient',$unit_id),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],
                    [
                        'link' => 'ohc/medicine-issuance/list',
                        'name' => "Today's Issue",
                        'count' => getohctotalCount('medicineissuance',$unit_id),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],

                    [

                        'name' => "Certified First Aiders",
                        'count' => getohctotalCount('certifiedFirstAider',$unit_id),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],

                ];

                $medicines = Inventory::where('ohc_report_inventory.unit_id',$unit_id)
                    ->join('ohc_master_medicine', 'ohc_report_inventory.medicine_id', '=', 'ohc_master_medicine.id')
                    ->select('ohc_master_medicine.medicine', 'ohc_report_inventory.balance')
                    ->where('ohc_report_inventory.trash', 'NO')
                    ->get();
                    $unit = $this->unit->getUnitList();

                $data = [
                    'masterLink' => $masterLink,
                    'medicines' => $medicines,
                    'unit' => $unit,
                ];



                return view('ohcmanagement.dashboard.dashboard', $data);

        }
    }

    public function medicineRequisition(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();

            $data = [];
            $unit_id = null;
            if ($request->has('unit_id') && !empty($request->unit_id) && (checkUserRole(ROLE_SUPERADMIN) || checkUserRole(ROLE_EHS_HEAD))) {

                $unit_id = decryptId($request->unit_id);
            }



                $masterLink = [
                    [
                        'link' => 'ohc/medicine-requisition/list',
                        'name' => 'Pending Requests',
                        'count' => getohctotalCount('requisition',$unit_id),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],
                    [

                        'name' => 'Current Stock',
                        'count' => getohctotalCount('medicine'),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],
                    [

                        'name' => 'Today’s Purchase',
                        'count' => getohctotalCount('medicineReceiving'),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],
                    [

                        'name' => 'Medicine Issuance',
                        'count' => getohctotalCount('usermedicineissuance',$unit_id),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],

                    [

                        'name' => "Unit 1",
                        'count' => getohctotalCount('prescribetopatient1'),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],
                    [

                        'name' => "Unit 2",
                        'count' => getohctotalCount('prescribetopatient2'),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],
                    [

                        'name' => "Unit 3",
                        'count' => getohctotalCount('prescribetopatient3'),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],
                    [

                        'name' => "Unit 4",
                        'count' => getohctotalCount('prescribetopatient4'),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],

                    [

                        'name' => "Today's OPD",
                        'count' => getohctotalCount('prescribetopatient',$unit_id),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],
                    [
                        'link' => 'ohc/medicine-issuance/list',
                        'name' => "Today's Issue",
                        'count' => getohctotalCount('medicineissuance',$unit_id),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],

                    [

                        'name' => "Certified First Aiders",
                        'count' => getohctotalCount('certifiedFirstAider',$unit_id),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],

                ];

                $medicines = Inventory::where('ohc_report_inventory.unit_id',$unit_id)
                    ->join('ohc_master_medicine', 'ohc_report_inventory.medicine_id', '=', 'ohc_master_medicine.id')
                    ->select('ohc_master_medicine.medicine', 'ohc_report_inventory.balance','ohc_report_inventory.unit_id')
                    ->where('ohc_report_inventory.trash', 'NO')
                    ->get();
                    $unit = $this->unit->getUnitList();

                $data = [
                    'masterLink' => $masterLink,
                    'medicines' => $medicines,
                    'unit' => $unit,
                ];
           


                return response()->json($data);

        }
    }


    public function store(Request $request)
    {
        //
    }


    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
