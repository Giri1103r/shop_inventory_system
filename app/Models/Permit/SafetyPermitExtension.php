<?php

namespace App\Models\Permit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

use App\Scopes\TrashScope;

class SafetyPermitExtension extends Model
{
    use HasFactory;

    protected $table = 'ptw_safety_extension';
    protected $primaryKey = 'id';

    protected $fillable = [
        'permit_id',
        'extended_time',
        'date',
        'to_time',
        'remarks',
        'approve_reject_status',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'

    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO',
    ];

    protected function casts(): array
    {
        return [
            'uauc_notification' => 'string',
        ];
    }


    public function permitextensionelectOne($id)
    {
        $data =  $this->select('ptw_safety_extension.*')->leftJoin('ptw_safety', 'ptw_safety.id', '=', 'ptw_safety_extension.permit_id')
            ->where('ptw_safety.id', $id)
            ->get();

        return $data;
    }



    public function store($permit_status, $id)
    {
        $request = request();

        $currentExtension = $this->select ? $this->select->where('permit_id', $id)->count() : 0;

        if ($currentExtension === 0) {
            $newExtendedTime = 1;
        } else {
            $newExtendedTime = $currentExtension + 1;
        }

        $insert_array = array(
            'permit_id' => $id,
            'extended_time' => $newExtendedTime,
            'date' => DBdateformat($request->date),
            'to_time' => $request->time_to,
            'remarks' => $request->extension_remarks,
            'approve_reject_status' => $permit_status,
            'created_by' => Auth::id()
        );

        return $this->create($insert_array);
    }


    public function getPermitExtension($id)
    {
        $data = $this->select('hotcold_permit_extension.*')
            ->where('hotcold_permit_extension.permit_id', $id)
            ->where('hotcold_permit_extension.approve_reject_status', 15)
            ->orderBy('hotcold_permit_extension.id', 'desc')
            ->first();
        return $data;
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

        return $this->where('id', $id)->update($update_data);
    }

    public function deleterecord($id)
    {

        $update_data = array(
            'status' => 0,
            'trash' => 'YES',
        );

        return $this->where('id', $id)->update($update_data);
    }
}
