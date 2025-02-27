<?php

namespace App\Http\Controllers\OhcManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\OhcManagement\Report\Inventory;

class OhcDashboardController extends Controller
{
    public function __construct() {}

    public function index()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $data = [];
            if ((in_array(ROLE_SUPERADMIN, getUserRoleId(Auth::id())) || in_array(ROLE_ADMIN, getUserRoleId(Auth::id())))) {
                $masterLink = [
                    [
                        'link' => 'ohc/medicine-requisition/list',
                        'name' => 'Pending Requests',
                        'count' => getohctotalCount('requisition'),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],
                    [
                        // 'link' => 'ohc/medicine/list',
                        'name' => 'Current Stock',
                        'count' => getohctotalCount('medicine'),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],
                    [
                        'link' => 'ohc/medicine-receiving-form/list',
                        'name' => 'Today’s Purchase',
                        'count' => getohctotalCount('medicineReceiving'),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],
                    [
                        'link' => 'ohc/medicine-issuance/list',
                        'name' => 'Medicine Issuance',
                        'count' => getohctotalCount('usermedicineissuance'),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],

                    [
                        'link' => 'ohc/prescribe-to-patient/list',
                        'name' => "Unit 1",
                        'count' => getohctotalCount('prescribetopatient1'),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],
                    [
                        'link' => 'ohc/prescribe-to-patient/list',
                        'name' => "Unit 2",
                        'count' => getohctotalCount('prescribetopatient2'),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],
                    [
                        'link' => 'ohc/prescribe-to-patient/list',
                        'name' => "Unit 3",
                        'count' => getohctotalCount('prescribetopatient3'),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],
                    [
                        'link' => 'ohc/prescribe-to-patient/list',
                        'name' => "Unit 4",
                        'count' => getohctotalCount('prescribetopatient4'),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],

                    [
                        'link' => 'ohc/prescribe-to-patient/list',
                        'name' => "Today's OPD",
                        'count' => getohctotalCount('prescribetopatient'),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],
                    [
                        'link' => 'ohc/medicine-issuance/list',
                        'name' => "Today's Issue",
                        'count' => getohctotalCount('medicineissuance'),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],

                ];

                $medicines = Inventory::where('ohc_report_inventory.unit_id',Auth::user()->unit_id)
                    ->join('ohc_master_medicine', 'ohc_report_inventory.medicine_id', '=', 'ohc_master_medicine.id')
                    ->select('ohc_master_medicine.medicine', 'ohc_report_inventory.balance')
                    ->where('ohc_report_inventory.trash', 'NO') 
                    ->get();

                $data = [
                    'masterLink' => $masterLink,
                    'medicines' => $medicines,
                ];
            }

                return view('ohcmanagement.dashboard.dashboard', $data);

        }
    }

    public function create()
    {
        //
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
