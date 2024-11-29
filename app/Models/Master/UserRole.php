<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

use App\Scopes\TrashScope;

class UserRole extends Model
{
    use  HasFactory;


    protected $table = 'template_user_role';
    protected $primaryKey = 'id';

    protected $fillable = [
        'role_id',
        'role_name',
        'role_permission',
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

    public function users()
    {
        return $this->hasMany(User::class, 'role');
    }

    public function list()
    {
        $request = request();
        $search = '';

        $query = $this->select('template_user_role.*');
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];
            $query->where(function ($query) use ($search) {
                $query->orWhere('role_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('role_name', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('role_id') && $request->role_id) {
            $query = $query->where('role_id', 'LIKE', '%' . $request->role_id . '%');
        }
        if ($request->has('role_name') && $request->role_name) {
            $query = $query->where('role_name', 'LIKE', '%' . $request->role_name . '%');
        }

        if ($request->has('status') && $request->status) {

            $query = $query->where('status', decryptId($request->status));
        }

        $data_count = $query;
        $total_records = $data_count->count();


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

    public function store()
    {

        $request = request();

        $insert_array = array(
            'role_id' => $request->role_id,
            'role_name' => $request->role_name,
            'role_permission' => $request->role_permission,
            'created_by' => Auth::id()
        );

        return $this->create($insert_array);
    }

    public function updates($id)
    {

        $request = request();

        $update_array = array(
            'role_id' => $request->role_id,
            'role_name' => $request->role_name,
            'role_permission' => $request->role_permission,
            'updated_by' => Auth::id()
        );

        return $this->where('id', $id)->update($update_array);
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

    public function exportdata()
    {
        $request = request();
        $search = '';

        $query = $this->select('*');

        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query->orWhere('role_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('role_name', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('role_id') && $request->role_id) {
            $query = $query->where('role_id', 'LIKE', '%' . $request->role_id . '%');
        }
        if ($request->has('role_name') && $request->role_name) {
            $query = $query->where('role_name', 'LIKE', '%' . $request->role_name . '%');
        }

        if ($request->has('status') && $request->status) {

            $query = $query->where('status', decryptId($request->status));
        }

        $query = $query->orderBy('id', 'Desc');
        return  $query->get();
    }

    public function selectOne($id)
    {

        $data = $this->select('*')
            ->where('id', $id)
            ->first();

        return $data;
    }

    public function UniqueCheck($role_name)
    {
        return $this->where('role_name',  $role_name)->get();
    }

    public function ExistuniqueCheck($role_name, $id)
    {
        return $this->where('role_name',  $role_name)
            ->where('id', '!=', $id)
            ->get();
    }
    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('template_user_role'));

        static::created(function ($model) {

            $uniqueId = 'ROL-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['role_id' => $uniqueId]);
        });
    }
}
