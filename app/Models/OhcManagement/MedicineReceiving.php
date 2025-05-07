<?php

namespace App\Models\OhcManagement;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MedicineReceiving extends Model
{
    protected $table = 'ohc_management_medicine_receiving';
    protected $primaryKey = 'id';

    protected $fillable = [
        'pack_id',
        'unit_id',
        'medicine_id',
        'hsn_id',
        'pack_id',
        'quantity',
        'expire_date',
        'rate',
        'batch_number',
        'vendor_id',
        'approve_status',
        'approved_date',
        'approved_by',
        'cron_time',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'

    ];


    public function list()
    {
        $request = request();
        $user = Auth::user();
        $userRole = string_to_array($user->role);
        $empId = $user->employee_id;
        $query = $this->select(
            'ohc_management_medicine_receiving.*',
          'ohc_management_medicine_receiving.approve_status as approvedStatus',
            'ohc_master_vendor.vendor_name',
            'ohc_master_medicine.pack',
        )
            ->join('ohc_master_vendor', 'ohc_management_medicine_receiving.vendor_id', '=', 'ohc_master_vendor.id')
            ->join('ohc_master_medicine', 'ohc_management_medicine_receiving.pack_id', '=', 'ohc_master_medicine.id')
            ->where([
                ['ohc_master_medicine.trash', '=', 'NO'],
                ['ohc_master_vendor.trash', '=', 'NO'],
                ['ohc_management_medicine_receiving.trash', '=', 'NO']
            ]);

        if (in_array(ROLE_EHS_OFFICER, $userRole)) {
            $query->orderBy('ohc_management_medicine_receiving.id', 'DESC');
        } elseif (in_array(ROLE_L1_EHS_OFFCIER, $userRole)) {

            $query
                ->orderBy('ohc_management_medicine_receiving.id', 'DESC');
        } elseif (in_array(ROLE_EHS_HEAD, $userRole)) {
            $query->orderBy('ohc_management_medicine_receiving.id', 'DESC');
        } elseif (in_array(ROLE_ADMIN, $userRole) || in_array(ROLE_SUPERADMIN, $userRole)) {
        } else {
            $query->where('ohc_management_medicine_receiving.created_by', Auth::id());
        }
        if ($request->search['value'] != null) {
            $search = $request->search['value'];
            $query->where(function ($query) use ($search) {
                $query->orWhere('ohc_master_medicine.medicine', 'LIKE', '%' . $search . '%')
               ->orWhere('ohc_management_medicine_receiving.hsn_id', 'LIKE', '%' . $search . '%')
               ->orWhere('batch_number', 'LIKE', '%' . $search . '%')
               ->orWhere('ohc_management_medicine_receiving.expire_date', 'LIKE', '%' . $search . '%')
               ->orWhere('rate', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('medicine_id') && $request->medicine_id) {
            $query = $query->where('ohc_management_medicine_receiving.medicine_id', 'LIKE', '%' . $request->medicine_id . '%');
        }
        if ($request->has('vendor_id') && $request->vendor_id) {
            $query = $query->where('ohc_management_medicine_receiving.vendor_id', 'LIKE', '%' . $request->vendor_id . '%');
        }

        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('ohc_management_medicine_receiving.created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_management_medicine_receiving.created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_management_medicine_receiving.created_at', '<=', $endDate);
        }
        if ($request->has('approve_status') && $request->approve_status) {

            $query = $query->where('ohc_management_medicine_receiving.approve_status',  $request->approve_status . '%');
        }
        if ($request->has('expire_date') && $request->expire_date) {

            $query = $query->where('ohc_management_medicine_receiving.expire_date', 'LIKE', '%' . DBdateformat($request->expire_date ). '%');
        }

        $totalFilteredRecords = $query->count();
        if ($request->length != -1) {
            $query->offset($request->start)->limit($request->length);
        }

        $query->orderBy('id', 'DESC');
        $data = $query->get();


        $org_total_counts = $this->count();

        return [
            'data' => $data,
            'total_records' => $org_total_counts,
            'filter_records' => $totalFilteredRecords,
        ];
    }

    public function store()
    {
        $request = request();

        $insert_array = [
            // 'unit_id' => decryptId($request->unit_id),
            'medicine_id' => decryptId($request->medicine_id),
            'vendor_id' => decryptId($request->vendor_id),
            'quantity' => $request->quantity,
            'batch_number' => $request->batch_number,
            'expire_date' => DBdateformat($request->expire_date),
            'hsn_id' => $request->hsn_id,
            'rate' => $request->rate,
            'pack_id' => ($request->pack_id),
            'approve_status' => STATUS_OHC_EHS_VERIFICATION_PENDING,
            'created_by' => Auth::id(),

        ];

        return $this->create($insert_array);
    }
    public function updates($id,$pack)
    {
        $request = request();

        $update_array = array(
            'unit_id' => decryptId($request->unit_id),
            'medicine_id' => decryptId($request->medicine_id),
            'vendor_id' => decryptId($request->vendor_id),
            'quantity' => $request->quantity,
            'batch_number' => $request->batch_number,
            'expire_date' => DBdateformat($request->expire_date),
            'hsn_id' => $request->hsn_id,
            'rate' => $request->rate,
            'pack_id' =>  $request->pack_id ?? $pack->id,
            'approve_status' => STATUS_OHC_EHS_VERIFICATION_PENDING,
            'updated_by' => Auth::id(),
        );

        return $this->where('id', $id)->update($update_array);
    }
    public function selectOne($id)
    {

        $data = $this->select(
            'ohc_management_medicine_receiving.*'
        )
            ->where('ohc_management_medicine_receiving.id', $id)
            ->first();

        return $data;
    }



    public function exportdata()
    {
        $request = request();

        $query = $this->select(
            'ohc_management_medicine_receiving.*',
            'ohc_master_vendor.vendor_name',
            'ohc_master_medicine.pack',
        )
            ->join('ohc_master_vendor', 'ohc_management_medicine_receiving.vendor_id', '=', 'ohc_master_vendor.id')
            ->join('ohc_master_medicine', 'ohc_management_medicine_receiving.pack_id', '=', 'ohc_master_medicine.id')
            ->where([
                ['ohc_master_medicine.trash', '=', 'NO'],
                ['ohc_master_vendor.trash', '=', 'NO'],
                ['ohc_management_medicine_receiving.trash', '=', 'NO']
            ]);

        if (!empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ohc_management_medicine_receiving.medicine_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('ohc_management_medicine_receiving.vendor_id', 'LIKE', '%' . $search . '%');
            });
        }

        // Additional filters
        if ($request->filled('approve_status')) {
            $query->where('ohc_management_medicine_receiving.approve_status', 'LIKE', '%' . $request->approve_status . '%');
        }
        if ($request->filled('medicine_id')) {
            $query->where('ohc_management_medicine_receiving.medicine_id', 'LIKE', '%' . $request->medicine_id . '%');
        }
        if ($request->filled('vendor_id')) {
            $query->where('ohc_management_medicine_receiving.vendor_id', 'LIKE', '%' . $request->vendor_id . '%');
        }

        // Date range filter
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $startDate = Carbon::parse($request->from_date)->startOfDay();
            $endDate = Carbon::parse($request->to_date)->endOfDay();
            $query->whereBetween('ohc_management_medicine_receiving.created_at', [$startDate, $endDate]);
        } elseif ($request->filled('from_date')) {
            $startDate = Carbon::parse($request->from_date)->startOfDay();
            $query->where('ohc_management_medicine_receiving.created_at', '>=', $startDate);
        } elseif ($request->filled('to_date')) {
            $endDate = Carbon::parse($request->to_date)->endOfDay();
            $query->where('ohc_management_medicine_receiving.created_at', '<=', $endDate);
        }
        if ($request->has('expire_date') && $request->expire_date) {
            $query = $query->where('ohc_management_medicine_receiving.expire_date', 'LIKE', '%' . $request->expire_date . '%');
        }
        return $query->orderByDesc('id')->get();
    }


    public function ehsverifcationstatus($id, $ehsverifydata)
    {
        $this->where('id', $id)->update([
            'approve_status' =>   $ehsverifydata['approve_status'],
            'approved_by' => Auth::id(),

        ]);
    }
    public function l1ehsstatus($id, $ehsverifydata)
    {
        $this->where('id', $id)->update([
            'approve_status' => $ehsverifydata['approve_status'],
            'approved_by' => Auth::id()
        ]);
    }
    public function ehsheadstatus($id, $updateStatus)
    {
        $this->where('id', $id)->update([
            'approve_status' => $updateStatus['approve_status'],
            'approved_by' =>  Auth::id(),
            'cron_time' => Carbon::now()
        ]);
    }

    public function stockupdate($id)
    {
        $this->where('id', $id)->update([
            'approve_status' => STATUS_OHC_CLOSE,
            'approved_by' =>  Auth::id(),
            'approved_date' => now(),
            'status' => 0,
        ]);
    }


    // Expire medicine list


    public function Expirelist()
    {
        $request = request();
        $user = Auth::user();
        $currentDate = now();
        $expiryLimit = now()->addDays(60);
        $query = $this->select('ohc_management_medicine_receiving.*')
            ->where('approve_status', STATUS_OHC_CLOSE);


        if ($request->search['value'] != null) {
            $search = $request->search['value'];
            $query->where(function ($query) use ($search) {
                $query->orWhere('medicine_id', 'LIKE', '%' . $search . '%');
            });
        }
        // dd($request->has('expire_date') && $request->expire_date);
        if ($request->has('medicine_id') && $request->medicine_id) {
            $query->where('ohc_management_medicine_receiving.medicine_id', 'LIKE', '%' . $request->medicine_id . '%');
        }
        if ($request->has('expire_date') && $request->expire_date) {
            $query->where('ohc_management_medicine_receiving.expire_date',DBdateformat( $request->expire_date));
        }

        // Apply ORDER BY conditionally


        $org_total_counts = $query->count();

        if ($request->length != -1) {
            $query->offset($request->start)->limit($request->length);
        }
        $query->orderBy('id', 'DESC');

        $data = $query->get();
        $total_records = $data->count();

        return [
            'data' => $data,
            'total_records' => $org_total_counts,
            'filter_records' => $total_records,
        ];
    }



    public function expireexportdata()
    {
        $request = request();

        $query = $this->select(
            'ohc_management_medicine_receiving.*',

        )->where('approve_status', STATUS_OHC_CLOSE);

        if (!empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ohc_management_medicine_receiving.medicine_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('ohc_management_medicine_receiving.vendor_id', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->filled('medicine_id')) {
            $query->where('ohc_management_medicine_receiving.medicine_id', 'LIKE', '%' . $request->medicine_id . '%');
        }

        if ($request->filled('expire_date')) {

            $query->where('ohc_management_medicine_receiving.expire_date',DBdateformat($request->expire_date));
        }
        return $query->orderByDesc('ohc_management_medicine_receiving.id')->get();
    }

    public function getPurchaseddate($selectedYear, $selectedMonth)
    {
        return $this->where('ohc_management_medicine_receiving.approve_status', STATUS_OHC_CLOSE)
            ->join('ohc_master_medicine', 'ohc_management_medicine_receiving.medicine_id', '=', 'ohc_master_medicine.id')
            ->whereYear('approved_date', $selectedYear)
            ->whereMonth('approved_date', $selectedMonth)
            ->select('ohc_master_medicine.medicine as medicine_name', 'approved_date', 'quantity')
            ->get();
    }

    public function getYearlyPurchaseddate($selectedYear)
    {
        return $this->where('ohc_management_medicine_receiving.approve_status', STATUS_OHC_CLOSE)
            ->join('ohc_master_medicine', 'ohc_management_medicine_receiving.medicine_id', '=', 'ohc_master_medicine.id')
            ->whereYear('approved_date', $selectedYear)
            ->select('ohc_master_medicine.medicine as medicine_name', 'approved_date', 'quantity')
            ->get();
    }
}
