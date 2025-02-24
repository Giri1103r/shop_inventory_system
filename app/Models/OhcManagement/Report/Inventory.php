<?php

namespace App\Models\OhcManagement\Report;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Inventory extends Model
{
    protected $table = 'ohc_report_inventory';
    protected $primaryKey = 'id';

    protected $fillable = [
        'medicine_id',
        'unit_id',
        'total_purchase',
        'total_issue',
        'total_first_aid',
        'total_prescribe',
        'total_received',
        'threshold_limit',
        'previous_month_total',
        'balance',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO',
    ];

    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select('ohc_report_inventory.*') ->where('unit_id', Auth::user()->unit_id);

        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('medicine_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('unit_id', 'LIKE', '%' . $search . '%');
            });
        }

        $data_count = $query;
        $total_records = $data_count->count();

        $query->orderBy('id', 'DESC');

        if ($request->length != -1) {
            $query->offset($request->start)->limit($request->length);
        }

        $data = $query->get();

        $datas = array(
            'data' => $data,
            'total_records' => $org_total_counts,
            'filter_records' => $total_records,
        );
        return $datas;
    }

    public function store($details, $unitIds)
    {
        $insert_array = [];

        foreach ($unitIds as $unitId) {
            $insert_array[] = [
                'unit_id' => $unitId,
                'medicine_id' => $details->id,
                'total_purchase' => 0,
                'total_issue' => 0,
                'total_received' => 0,
                'total_first_aid' => 0,
                'total_prescribe' => 0,
                'balance' => 0,
                'threshold_limit' => $details->threshold_limit,
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return $this->insert($insert_array);
    }

    public function storepurchasedata($details)
    {
        return $this->where('unit_id', $details->unit_id)->where('medicine_id', $details->medicine_id)->update(['total_purchase' => $details->quantity]);
    }
    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ohc_report_inventory'));

        // static::created(function ($model) {

        //     $uniqueId = 'CMP-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
        //     $model->update(['company_id' => $uniqueId]);
        // });
    }

    public function getmedicinedata()
    {
        return $this->where('unit_id', 1)->where('status', 1)->whereColumn('balance', '<', 'threshold_limit')->get();
    }

    public function getstockdata()
    {
        return $this->where('unit_id', 1)->where('status', 1)->get();
    }
    public function getAvailableQuantity($id)
    {
        return $this->where('medicine_id', $id)->where('trash', 'NO')->where('status', 1)->select('balance')->where('unit_id', 1)->first();
    }

    public function issuestockupdate($user_medicine_requisition, $medicinedata)
    {

        foreach ($medicinedata as $data) {

            $oldissue = $this->where('unit_id', 1)
                ->where('medicine_id', $data['medicine_id'])->first();
            $newIssue  =  $oldissue->total_issue + $data['quantity'];
            $this->where('unit_id', 1)
                ->where('medicine_id', $data['medicine_id'])
                ->update(['total_issue' => $newIssue]);

            $newbalance  =  $oldissue->balance - $data['quantity'];
            $this->where('unit_id', 1)
                ->where('medicine_id', $data['medicine_id'])
                ->update(['balance' => $newbalance]);
        }
        foreach ($medicinedata as $data) {
            $olddata =   $this->where('unit_id', $user_medicine_requisition->unit_id)
                ->where('medicine_id', $data['medicine_id'])->first();
            $newdata =  $olddata->total_purchase + $data['quantity'];
            $this->where('unit_id', $user_medicine_requisition->unit_id)
                ->where('medicine_id', $data['medicine_id'])
                ->update(['total_purchase' => $newdata]);
            $balancedata = $olddata->balance + $data['quantity'];
            $this->where('unit_id', $user_medicine_requisition->unit_id)
                ->where('medicine_id', $data['medicine_id'])
                ->update(['balance' => $balancedata]);
        }
    }

    public function getmedicineUnitwise()
    {
        return $this->where('unit_id', Auth::user()->unit_id)->where('status', 1)->get();
    }
    public function getunitwiseAvailableQuantity($id)
    {
        return $this->where('medicine_id', $id)->where('trash', 'NO')->where('unit_id', Auth::user()->unit_id)->select('balance')->first();
    }

    public function getMonthlyInventoryReport($selectedUnit, $selectedMonth, $selectedYear)
    {
        return $this->when($selectedUnit, function ($query) use ($selectedUnit) {
                return $query->where('ohc_report_inventory.unit_id', $selectedUnit);
            })
            ->when($selectedMonth && $selectedYear, function ($query) use ($selectedMonth, $selectedYear) {
                return $query->whereMonth('ohc_report_inventory.created_at', $selectedMonth)
                    ->whereYear('ohc_report_inventory.created_at', $selectedYear);
            })
            ->join('ohc_master_medicine', 'ohc_report_inventory.medicine_id', '=', 'ohc_master_medicine.id')
            ->select(
                'ohc_master_medicine.medicine as medicine_name',
                'balance',
                'previous_month_total',
                'total_purchase',
                'total_issue'
            )
            ->groupBy(
                'ohc_master_medicine.medicine',
                'balance',
                'previous_month_total',
                'total_purchase',
                'total_issue'
            )
            ->get();
    }

    public function getyearlyinventoryreport($selectedUnit, $selectedYear)
    {
        return $this->when($selectedUnit, function ($query) use ($selectedUnit) {
                return $query->where('ohc_report_inventory.unit_id', $selectedUnit);
            })
            ->when($selectedYear, function ($query) use ($selectedYear) {
                return $query->whereYear('ohc_report_inventory.created_at', $selectedYear); // FIXED
            })
            ->join('ohc_master_medicine', 'ohc_report_inventory.medicine_id', '=', 'ohc_master_medicine.id')
            ->select(
                'ohc_master_medicine.medicine as medicine_name',
                'balance',
                'total_purchase',
                'total_issue'
            )
            ->groupBy(
                'ohc_master_medicine.medicine',
                'balance',
                'total_purchase',
                'total_issue'
            )
            ->get();
    }



    // public function firstaidstockupdate(){
    //     $request -
    //     foreach ($request->medicine_id as $index => $medicine_id) {
    //         $medicineRecord = $medicine_first_aid->where('medicine_id', $medicine_id)->first();
    //         $newQuantity = $request->quantity[$index];

    //         if ($medicineRecord) {
    //             $oldquantity = $medicineRecord->quantity;
    //             $newquantity = $request->quantity[$index];
    //             if ($oldquantity > $newquantity) {
    //                 $difference = $oldquantity - $newquantity;

    //                 $this->inventory
    //                     ->where('medicine_id', $medicine_id)
    //                     ->where('unit_id',  $user_medicine_first_aid->unit_id)
    //                     ->decrement('total_first_aid', $difference);

    //                 $this->inventory
    //                     ->where('medicine_id', $medicine_id)
    //                     ->where('unit_id', $user_medicine_first_aid->unit_id)
    //                     ->decrement('balance', $difference);
    //             } elseif ($oldquantity < $newquantity) {
    //                 $difference = $newquantity - $oldquantity;

    //                 $this->inventory
    //                     ->where('medicine_id', $medicine_id)
    //                     ->where('unit_id', $user_medicine_first_aid->unit_id)
    //                     ->increment('total_first_aid', $difference);

    //                 $this->inventory
    //                     ->where('medicine_id', $medicine_id)
    //                     ->where('unit_id', $user_medicine_first_aid->unit_id)
    //                     ->increment('balance', $difference);
    //             }
    //         } else {
    //             $newquantity = $request->quantity[$index];
    //             $this->inventory
    //                 ->where('medicine_id', $medicine_id)
    //                 ->where('unit_id', $user_medicine_first_aid->unit_id)
    //                 ->increment('total_first_aid',  $newquantity);

    //             $this->inventory
    //                 ->where('medicine_id', $medicine_id)
    //                 ->where('unit_id', $user_medicine_first_aid->unit_id)
    //                 ->decrement('balance',  $newquantity);
    //         }
    //     }
    // }
}
