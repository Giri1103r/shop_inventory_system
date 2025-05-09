<?php

namespace App\Models\Inspection\MSDS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use App\Scopes\TrashScope;

class MSDSDetails extends Model
{

    use  HasFactory;

    protected $table = 'inspection_msds_details';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'serial_number',
        'item_code',
        'msds_id',
        'name_of_chemical',
        'storage_capacity',
        'nfa_rating',
        'nfa_rating_value',
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

    public function store($id)
    {
        $request = request();

        foreach ($request->item_code as $index => $itemCode) {
            $insert_array = array(
                'serial_number' => $request->serial_number[$index],
                'item_code' => $itemCode,
                'msds_id' => $id,
                'name_of_chemical' => decryptId($request->name_of_chemical[$index]),
                'storage_capacity' => ($request->storage_capacity[$index]),
                'nfa_rating' => decryptId($request->nfa_rating[$index]),
                'nfa_rating_value' => decryptId($request->value_nfa_rating[$index]),
                'msds_availability_status' => decryptId($request->msds_availability_status[$index]),
                'remark' => $request->remark[$index],
                'created_by' => Auth::id(),
            );
            $this->create($insert_array);
        }
    }


    public function uniqueCheck($item_code, $name_of_chemical)
    {

        return $this->where('item_code', $item_code)->orWhere('name_of_chemical', $name_of_chemical)->get();
    }

    public function existUniqueCheck($item_code, $id)
    {
        return $this->where('item_code', $item_code)
            ->where('id', '!=', $id)
            ->get();
    }

    public function getDetails($id)
    {
        return $this->where('msds_id', $id)->get();
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_msds_details'));
    }
}
