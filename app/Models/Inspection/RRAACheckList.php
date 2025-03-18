<?php

namespace App\Models\Inspection;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\TrashScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class RRAACheckList extends Model
{
    use  HasFactory;

    protected $table = 'inspection_rraa_checklist';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'rraa_details_id',
        'serial_number',
        'category',
        'ohs_compliance_index',
        'frequency',
        'scope',
        'responsibility',
        'authority',
        'accountability',
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

    public function store($rraa_id)
    {
        $request = request();
      
        $insertedData = [];

        foreach ($request->scope as $index => $Scope) {
            $insert_array = array(
                'rraa_details_id' => $rraa_id,
                'serial_number' =>$request->serial_number[$index],
                'category' =>decryptId($request->category[$index]),
                'ohs_compliance_index' =>$request->ohs_compliance_index[$index],
                'frequency' =>decryptId($request->frequency[$index]),
                'scope' => $Scope,  
                'responsibility' =>$request->emp_id[$index],
                'authority' => $request->authority[$index], 
                'accountability' => $request->accountability[$index], 
                'remark' => $request->remark[$index],  
                'created_by' => Auth::id(),
            );

            $insertedData []=  $this->create($insert_array);

        }
        
        return $insertedData;
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
        return $this->where('rraa_details_id', $id)->update($update_data);
    }

    public function selectOne($id)
    {
        return $this->where('rraa_details_id', $id)->get();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_rraa_checklist'));
    }
}
