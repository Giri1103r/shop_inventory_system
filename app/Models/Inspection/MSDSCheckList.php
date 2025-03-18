<?php

namespace App\Models\Inspection;

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
                'msds_availability_status' => $request->msds_availability_status[$index], 
                'remark' => $request->remark[$index],  
                'created_by' => Auth::id(),
            );
           
            $insertedData []=  $this->create($insert_array);
            // dd( $insertedData);
        }
        
        return $insertedData;
    }

    public function updates($msdsId)
    {
        $request = request();

        foreach ($request->item_code as $index => $itemCode) {
            $update_data = array(
                
                'msds_details_id' => $msdsId,
                'item_code' => $itemCode,  
                'serial_number' =>$request->serial_number[$index],
                'name_of_chemical' => $request->name_of_chemical[$index], 
                'msds_availability_status' => $request->msds_availability_status[$index], 
                'remark' => $request->remark[$index],  
                'updated_by' => Auth::id()
            );
            $existingRecord = $this->where('msds_details_id', $msdsId)
                                    ->where('item_code', $itemCode) 
                                    ->where('trash', 'NO')
                                    ->first();
            
            if ($existingRecord) {
                $existingRecord->update($update_data);
            } else {
                self::create($update_data);
            }
        }
    }

    public function statuschange($id)
    {
        $request = request();

        $type = $request->types;
        if ($type == 1) {
            $update_data = array(
                'status' => 0,
            );
        } else {
            $update_data = array(
                'status' => 1,
            );
        }
        return $this->where('msds_details_id', $id)->update($update_data);
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
