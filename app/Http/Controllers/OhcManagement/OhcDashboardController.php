<?php

namespace App\Http\Controllers\OhcManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


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
                        'name' => 'Medicine',
                        'count' => getohctotalCount('medicine'),
                        'icon' => 'bx bx-message-square-detail',
                        'icon_color' => 'text-primary',
                    ],
                    

                ];

                $data = [
                    'masterLink' => $masterLink,
                ];
            }
            if (Auth::user()->role == ROLE_SUPERADMIN || Auth::user()->role == ROLE_ADMIN) {
                return view('admin.dashboard', $data);
            } else {
                return view('admin.userdashboard', $data);
            }
        }
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
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
