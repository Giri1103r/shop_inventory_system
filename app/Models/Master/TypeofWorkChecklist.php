<?php

namespace App\Models\Master;

use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TypeofWorkChecklist extends Model
{
    use  HasFactory;


    protected $table = 'ptw_masters_typeofwork_checklist';
    protected $primaryKey = 'id';

    protected $fillable = [
        'typeofwork_id',
        'type',
        'checked',
        'check_points',
        'default_enable',
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

    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select('ptw_masters_safe_work.*');
        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('safe_work', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('safe_work') && $request->safe_work) {
            $query = $query->where('safe_work', 'LIKE', '%' . $request->safe_work . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('status', decryptId($request->status));
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

    public function UniqueCheck($data)
    {

        return $this->where('safe_work',  $data)->get();
    }

    public function ExistuniqueCheck($data, $id)
    {
        return $this->where('safe_work',  $data)
            ->where('id', '!=', $id)
            ->get();
    }

    public function store1($id)
    {
        $request = request();
        if (isset($request->protective['protective_check'])) {
            foreach ($request->protective['protective_check'] as $type => $checkedItems) {
                foreach ($checkedItems as $checked => $value) {

                    $checkPoint = isset($request->protective['protective_equip'][$type][$checked])
                        ? $request->protective['protective_equip'][$type][$checked]
                        : null;


                    $defaultEnable = isset($request->protective['protectivequip_checklist'][$type][$checked])
                        ? $request->protective['protectivequip_checklist'][$type][$checked]
                        : 0;

                    $insert_array = [
                        'typeofwork_id' => $id,
                        'type' => $type,
                        'checked' => $value,
                        'check_points' => $checkPoint,
                        'default_enable' => $defaultEnable,
                        'created_by' => Auth::id(),
                    ];

                    $this->create($insert_array);
                }
            }
        }

        return true;
    }

    public function store2($id)
    {
        $request = request();

        if (isset($request->equipment['equipinvalve_check'])) {
            foreach ($request->equipment['equipinvalve_check'] as $type => $checkedItems) {
                foreach ($checkedItems as $checked => $value) {

                    $checkPoint = isset($request->equipment['equip_involve'][$type][$checked])
                        ? $request->equipment['equip_involve'][$type][$checked]
                        : null;


                    $defaultEnable = isset($request->equipment['equipinvalve_checklist'][$type][$checked])
                        ? $request->equipment['equipinvalve_checklist'][$type][$checked]
                        : 0;

                    $insert_array = [
                        'typeofwork_id' => $id,
                        'type' => $type,
                        'checked' => $value,
                        'check_points' => $checkPoint,
                        'default_enable' => $defaultEnable,
                        'created_by' => Auth::id(),
                    ];

                    $this->create($insert_array);
                }
            }
        }

        return true;
    }


    public function store3($id)
    {
        $request = request();

        if (isset($request->manual['precaution_check'])) {
            foreach ($request->manual['precaution_check'] as $type => $checkedItems) {
                foreach ($checkedItems as $checked => $value) {


                    $checkPoint = isset($request->manual['precaution'][$type][$checked])
                        ? $request->manual['precaution'][$type][$checked]
                        : null;


                    $defaultEnable = isset($request->manual['precaution_checklist'][$type][$checked])
                        ? $request->manual['precaution_checklist'][$type][$checked]
                        : 0;

                    $insert_array = [
                        'typeofwork_id' => $id,
                        'type' => $type,
                        'checked' => $value,
                        'check_points' => $checkPoint,
                        'default_enable' => $defaultEnable,
                        'created_by' => Auth::id(),
                    ];

                    $this->create($insert_array);
                }
            }
        }

        return true;
    }



    public function store4($id)
    {
        $request = request();

        if (isset($request->check['equipchecklist_check'])) {
            foreach ($request->check['equipchecklist_check'] as $type => $checkedItems) {
                foreach ($checkedItems as $checked => $value) {

                    $checkPoint = isset($request->check['checklist'][$type][$checked])
                        ? $request->check['checklist'][$type][$checked]
                        : null;


                    $defaultEnable = isset($request->check['equipchecklist_checklist'][$type][$checked])
                        ? $request->check['equipchecklist_checklist'][$type][$checked]
                        : 0;

                    $insert_array = [
                        'typeofwork_id' => $id,
                        'type' => $type,
                        'checked' => $value,
                        'check_points' => $checkPoint,
                        'default_enable' => $defaultEnable,
                        'created_by' => Auth::id(),
                    ];

                    $this->create($insert_array);
                }
            }
        }

        return true;
    }


    public function store5($id)
    {
        $request = request();

        if (isset($request->instruction['safework_check'])) {
            foreach ($request->instruction['safework_check'] as $type => $checkedItems) {
                foreach ($checkedItems as $checked => $value) {
                    $checkPoint = isset($request->instruction['safe_work'][$type][$checked])
                        ? $request->instruction['safe_work'][$type][$checked]
                        : null;


                    $defaultEnable = isset($request->instruction['safework_checklist'][$type][$checked])
                        ? $request->instruction['safework_checklist'][$type][$checked]
                        : 0;

                    $insert_array = [
                        'typeofwork_id' => $id,
                        'type' => $type,
                        'checked' => $value,
                        'check_points' => $checkPoint,
                        'default_enable' => $defaultEnable,
                        'created_by' => Auth::id(),
                    ];

                    $this->create($insert_array);
                }
            }
        }

        return true;
    }


    public function update1($id)
    {
        $request = request();

        if (isset($request->protective['protective_check'])) {
            foreach ($request->protective['protective_check'] as $type => $checkedItems) {

                foreach ($checkedItems as $checked => $value) {

                    $checkPoint = isset($request->protective['protective_equip'][$type][$checked])
                        ? $request->protective['protective_equip'][$type][$checked]
                        : null;

                    $defaultEnable = isset($request->protective['protectivequip_checklist'][$type][$checked])
                        ? $request->protective['protectivequip_checklist'][$type][$checked]
                        : 0;

                    foreach ($request->protective['record_id'] as $name => $recordId) {

                        $existingRecord = $this->where('id', $recordId)->where('typeofwork_id', $id)
                            ->where('type', $type)
                            ->where('check_points', $checkPoint)
                            ->first();
                        $data = [
                            'typeofwork_id' => $id,
                            'type' => $type,
                            'checked' => $value,
                            'check_points' => $checkPoint,
                            'default_enable' => $defaultEnable,
                            'updated_by' => Auth::id(),
                        ];
                        if ($existingRecord) {
                            $existingRecord->update($data);
                        }
                    }
                }
            }
        }

        return true;
    }

    public function update2($id)
    {
        $request = request();

        if (isset($request->equipment['equipinvalve_check'])) {
            foreach ($request->equipment['equipinvalve_check'] as $type => $checkedItems) {

                foreach ($checkedItems as $checked => $value) {

                    $checkPoint = isset($request->equipment['equip_involve'][$type][$checked])
                        ? $request->equipment['equip_involve'][$type][$checked]
                        : null;

                    $defaultEnable = isset($request->equipment['equipinvalve_checklist'][$type][$checked])
                        ? $request->equipment['equipinvalve_checklist'][$type][$checked]
                        : 0;

                    foreach ($request->equipment['equipmentrecord_id'] as $name => $equipmentrecord_id) {


                        $existingRecord = $this->where('id', $equipmentrecord_id)->where('typeofwork_id', $id)
                            ->where('type', $type)
                            ->where('check_points', $checkPoint)
                            ->first();
                        // dd($equipmentrecord_id,$id,$type,$checkPoint,$existingRecord);
                        $data = [
                            'typeofwork_id' => $id,
                            'type' => $type,
                            'checked' => $value,
                            'check_points' => $checkPoint,
                            'default_enable' => $defaultEnable,
                            'updated_by' => Auth::id(),
                        ];
                        if ($existingRecord) {
                            $existingRecord->update($data);
                        }
                    }
                }
            }
        }

        return true;
    }

    public function update3($id)
    {
        $request = request();

        if (isset($request->manual['precaution_check'])) {
            foreach ($request->manual['precaution_check'] as $type => $checkedItems) {

                foreach ($checkedItems as $checked => $value) {

                    $checkPoint = isset($request->manual['precaution'][$type][$checked])
                        ? $request->manual['precaution'][$type][$checked]
                        : null;

                    $defaultEnable = isset($request->manual['precaution_checklist'][$type][$checked])
                        ? $request->manual['precaution_checklist'][$type][$checked]
                        : 0;

                    foreach ($request->manual['manualrecord_id'] as $name => $manualrecord_id) {


                        $existingRecord = $this->where('id', $manualrecord_id)->where('typeofwork_id', $id)
                            ->where('type', $type)
                            ->where('check_points', $checkPoint)
                            ->first();
                        // dd($equipmentrecord_id,$id,$type,$checkPoint,$existingRecord);
                        $data = [
                            'typeofwork_id' => $id,
                            'type' => $type,
                            'checked' => $value,
                            'check_points' => $checkPoint,
                            'default_enable' => $defaultEnable,
                            'updated_by' => Auth::id(),
                        ];
                        if ($existingRecord) {
                            $existingRecord->update($data);
                        }
                    }
                }
            }
        }

        return true;
    }

    public function update4($id)
    {
        $request = request();

        if (isset($request->check['equipchecklist_check'])) {
            foreach ($request->check['equipchecklist_check'] as $type => $checkedItems) {

                foreach ($checkedItems as $checked => $value) {

                    $checkPoint = isset($request->check['checklist'][$type][$checked])
                        ? $request->check['checklist'][$type][$checked]
                        : null;

                    $defaultEnable = isset($request->check['equipchecklist_checklist'][$type][$checked])
                        ? $request->check['equipchecklist_checklist'][$type][$checked]
                        : 0;

                    foreach ($request->check['checkrecord_id'] as $name => $checkrecord_id) {


                        $existingRecord = $this->where('id', $checkrecord_id)->where('typeofwork_id', $id)
                            ->where('type', $type)
                            ->where('check_points', $checkPoint)
                            ->first();
                        // dd($equipmentrecord_id,$id,$type,$checkPoint,$existingRecord);
                        $data = [
                            'typeofwork_id' => $id,
                            'type' => $type,
                            'checked' => $value,
                            'check_points' => $checkPoint,
                            'default_enable' => $defaultEnable,
                            'updated_by' => Auth::id(),
                        ];
                        if ($existingRecord) {
                            $existingRecord->update($data);
                        }
                    }
                }
            }
        }

        return true;
    }

    public function update5($id)
    {
        $request = request();

        if (isset($request->instruction['safework_check'])) {
            foreach ($request->instruction['safework_check'] as $type => $checkedItems) {

                foreach ($checkedItems as $checked => $value) {

                    $checkPoint = isset($request->instruction['safe_work'][$type][$checked])
                        ? $request->instruction['safe_work'][$type][$checked]
                        : null;

                    $defaultEnable = isset($request->instruction['safework_checklist'][$type][$checked])
                        ? $request->instruction['safework_checklist'][$type][$checked]
                        : 0;

                    foreach ($request->instruction['instructionrecord_id'] as $name => $instructionrecord_id) {


                        $existingRecord = $this->where('id', $instructionrecord_id)->where('typeofwork_id', $id)
                            ->where('type', $type)
                            ->where('check_points', $checkPoint)
                            ->first();
                        // dd($equipmentrecord_id,$id,$type,$checkPoint,$existingRecord);
                        $data = [
                            'typeofwork_id' => $id,
                            'type' => $type,
                            'checked' => $value,
                            'check_points' => $checkPoint,
                            'default_enable' => $defaultEnable,
                            'updated_by' => Auth::id(),
                        ];
                        if ($existingRecord) {
                            $existingRecord->update($data);
                        }
                    }
                }
            }
        }

        return true;
    }


    public function getprotectivechecklistdetails($id, $type)
    {

        $data =  $this->select('ptw_masters_typeofwork_checklist.*', 'ptw_masters_protective_equip.protective_equip')
            ->leftJoin('ptw_masters_protective_equip', 'ptw_masters_protective_equip.id', '=', 'ptw_masters_typeofwork_checklist.check_points')->where('ptw_masters_typeofwork_checklist.typeofwork_id', $id)
            ->where('ptw_masters_typeofwork_checklist.type', $type)
            ->get();

            // dd($data);

        return $data;
    }

    public function getequipmentchecklistdetails($id, $type)
    {

        $data =  $this->select('ptw_masters_typeofwork_checklist.*', 'ptw_masters_equip_involved.equip_involve')
            ->leftJoin('ptw_masters_equip_involved', 'ptw_masters_equip_involved.id', '=', 'ptw_masters_typeofwork_checklist.check_points')->where('ptw_masters_typeofwork_checklist.typeofwork_id', $id)
            ->where('ptw_masters_typeofwork_checklist.type', $type)
            ->get();

            // dd($data);

        return $data;
    }


    public function getmanualchecklistdetails($id, $type)
    {

        $data =  $this->select('ptw_masters_typeofwork_checklist.*', 'ptw_masters_precaution.precaution')
            ->leftJoin('ptw_masters_precaution', 'ptw_masters_precaution.id', '=', 'ptw_masters_typeofwork_checklist.check_points')->where('ptw_masters_typeofwork_checklist.typeofwork_id', $id)
            ->where('ptw_masters_typeofwork_checklist.type', $type)
            ->get();

            // dd($data);

        return $data;
    }

    public function getcheckchecklistdetails($id, $type)
    {

        $data =  $this->select('ptw_masters_typeofwork_checklist.*', 'ptw_masters_checklist.checklist')
            ->leftJoin('ptw_masters_checklist', 'ptw_masters_checklist.id', '=', 'ptw_masters_typeofwork_checklist.check_points')->where('ptw_masters_typeofwork_checklist.typeofwork_id', $id)
            ->where('ptw_masters_typeofwork_checklist.type', $type)
            ->get();

            // dd($data);

        return $data;
    }


    public function getinstructionchecklistdetails($id, $type)
    {

        $data =  $this->select('ptw_masters_typeofwork_checklist.*', 'ptw_masters_safe_work.safe_work')
            ->leftJoin('ptw_masters_safe_work', 'ptw_masters_safe_work.id', '=', 'ptw_masters_typeofwork_checklist.check_points')->where('ptw_masters_typeofwork_checklist.typeofwork_id', $id)
            ->where('ptw_masters_typeofwork_checklist.type', $type)
            ->get();

            // dd($data);

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

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('ptw_masters_safe_work.*');
        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhereRaw('safe_work LIKE "%' . $search . '%"');
            });
        }

        if ($request->has('safe_work') && $request->safe_work) {
            $query = $query->where('safe_work', 'LIKE', '%' . $request->safe_work . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('company_management.status', decryptId($request->status));
        }

        return  $query->get();
    }

    public function selectOne($id)
    {

        $data = $this->select(
            'ptw_masters_safe_work.*'
        )
            ->where('ptw_masters_safe_work.id', $id)
            ->first();

        return $data;
    }

    public function selectchecklist()
    {

        $data =  $this->select('ptw_masters_safe_work.*')
            ->where('ptw_masters_safe_work.status', '1')
            ->where('ptw_masters_safe_work.trash', 'NO')
            ->get();

        return $data;
    }
    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ptw_masters_typeofwork_checklist'));

        // static::created(function ($model) {

        //     $uniqueId = 'CMP-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
        //     $model->update(['company_id' => $uniqueId]);
        // });
    }
}
