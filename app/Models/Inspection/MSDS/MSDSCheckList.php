<?php

namespace App\Models\Inspection\MSDS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use App\Scopes\TrashScope;

class MSDSCheckList extends Model
{
    use  HasFactory;

    protected $table = 'inspection_msds_checklist';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'msds_details_id',
        'serial_number',
        'item_code',
        'name_of_chemical',
        'msds_availability_status',
        'remark',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];

    public function store($msdsId)
    {
        $request = request();

        $insertedData = [];

        foreach ($request->item_code as $index => $itemCode) {
            $insert_array = array(
                'msds_details_id' => $msdsId,
                'serial_number' =>$request->serial_number[$index],
                'item_code' => $itemCode,
                'name_of_chemical' => $request->name_of_chemical[$index],
                'msds_availability_status' => decryptId($request->msds_availability_status[$index]),
                'remark' => $request->remark[$index],
                'created_by' => Auth::id(),
            );

            $insertedData []=  $this->create($insert_array);
        }

        return $insertedData;
    }

    public function selectOne($id)
    {
        return $this->where('msds_details_id', $id)->get();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_msds_checklist'));
    }

}
