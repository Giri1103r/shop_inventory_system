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
    public function storeProtectiveEquipment($id)
    {
        $request = request();
        if (isset($request->protectiveequipment)) {

            foreach ($request->protectiveequipment as $key => $record) {

                $typeofwork_id = $id;
                $type = 'type1';
                $checked =  isset($record['left_check']) ?  $record['left_check'] : 0;
                $check_points = $record['checklist_id'];
                $default_enable = isset($record['right_check']) ?  $record['right_check'] : 0;;
                $created_by =   Auth::id();

                $data = [
                    'typeofwork_id' => $typeofwork_id,
                    'type' => $type,
                    'checked' => $checked,
                    'check_points' => $check_points,
                    'default_enable' => $default_enable,
                    'created_by' => $created_by,

                ];
                $this->create($data);
            }
        }
        return true;
    }
    public function storeEquipmentInvolved($id)
    {
        $request = request();

        if (isset($request->equipmentinvolved)) {

            foreach ($request->equipmentinvolved as $key => $record) {

                $typeofwork_id = $id;
                $type = 'type2';
                $checked =  isset($record['left_check']) ?  $record['left_check'] : 0;
                $check_points = $record['checklist_id'];
                $default_enable = isset($record['right_check']) ?  $record['right_check'] : 0;;
                $created_by =   Auth::id();


                $data = [
                    'typeofwork_id' => $typeofwork_id,
                    'type' => $type,
                    'checked' => $checked,
                    'check_points' => $check_points,
                    'default_enable' => $default_enable,
                    'created_by' => $created_by,

                ];
                $this->create($data);
            }
        }
        return true;
    }

    public function storeManualList($id)
    {
        $request = request();

        if (isset($request->manuallist)) {

            foreach ($request->manuallist as $key => $record) {

                $typeofwork_id = $id;
                $type = 'type3';
                $checked =  isset($record['left_check']) ?  $record['left_check'] : 0;
                $check_points = $record['checklist_id'];
                $default_enable = isset($record['right_check']) ?  $record['right_check'] : 0;;
                $created_by =  Auth::id();

                $data = [
                    'typeofwork_id' => $typeofwork_id,
                    'type' => $type,
                    'checked' => $checked,
                    'check_points' => $check_points,
                    'default_enable' => $default_enable,
                    'created_by' => $created_by,

                ];

                $this->create($data);
            }
        }

        return true;
    }
    public function storeCheckList($id)
    {
        $request = request();

        if (isset($request->checklist)) {

            foreach ($request->checklist as $key => $record) {

                $typeofwork_id = $id;
                $type = 'type4';
                $checked =  isset($record['left_check']) ?  $record['left_check'] : 0;
                $check_points = $record['checklist_id'];
                $default_enable = isset($record['right_check']) ?  $record['right_check'] : 0;;
                $created_by =  Auth::id();


                $data = [
                    'typeofwork_id' => $typeofwork_id,
                    'type' => $type,
                    'checked' => $checked,
                    'check_points' => $check_points,
                    'default_enable' => $default_enable,
                    'created_by' => $created_by,

                ];
                $this->create($data);
            }
        }


        return true;
    }
    public function storeInstructionList($id)
    {
        $request = request();

        if (isset($request->instructionList)) {

            foreach ($request->instructionList as $key => $record) {

                $typeofwork_id = $id;
                $type = 'type5';
                $checked =  isset($record['left_check']) ?  $record['left_check'] : 0;
                $check_points = $record['checklist_id'];
                $default_enable = isset($record['right_check']) ?  $record['right_check'] : 0;;
                $created_by =  Auth::id();



                $data = [
                    'typeofwork_id' => $typeofwork_id,
                    'type' => $type,
                    'checked' => $checked,
                    'check_points' => $check_points,
                    'default_enable' => $default_enable,
                    'created_by' => $created_by,

                ];
                $this->create($data);
            }
        }


        return true;
    }

    public function updateProtectiveEquipment($id)
    {
        $request = request();

        if (isset($request->protectiveequipment)) {

            foreach ($request->protectiveequipment as $key => $record) {

                $typeofwork_id = $id;
                $type = 'type1';
                $checked =  isset($record['left_check']) ?  $record['left_check'] : 0;
                $check_points = $record['checklist_id'];
                $default_enable = isset($record['right_check']) ?  $record['right_check'] : 0;;
                $created_by =   $updated_by  = Auth::id();

                $recordId =  $record['record_id'];

                $data = [
                    'typeofwork_id' => $typeofwork_id,
                    'type' => $type,
                    'checked' => $checked,
                    'check_points' => $check_points,
                    'default_enable' => $default_enable,

                ];

                if ($recordId != null && $recordId != '') {
                    /**
                     * Update query
                     */

                    $data['updated_by'] = $updated_by;
                    $this->where('id', $recordId)->update($data);
                } else {
                    /**
                     * Insert Query
                     */
                    $data['created_by'] = $created_by;
                    $this->create($data);
                }
            }
        }



        return true;
    }
    public function updateEquipmentInvolved($id)
    {
        $request = request();

        if (isset($request->equipmentinvolved)) {

            foreach ($request->equipmentinvolved as $key => $record) {

                $typeofwork_id = $id;
                $type = 'type2';
                $checked =  isset($record['left_check']) ?  $record['left_check'] : 0;
                $check_points = $record['checklist_id'];
                $default_enable = isset($record['right_check']) ?  $record['right_check'] : 0;;
                $created_by =   $updated_by  = Auth::id();

                $recordId =  $record['record_id'];

                $data = [
                    'typeofwork_id' => $typeofwork_id,
                    'type' => $type,
                    'checked' => $checked,
                    'check_points' => $check_points,
                    'default_enable' => $default_enable,

                ];

                if ($recordId != null && $recordId != '') {
                    /**
                     * Update query
                     */

                    $data['updated_by'] = $updated_by;
                    $this->where('id', $recordId)->update($data);
                } else {
                    /**
                     * Insert Query
                     */
                    $data['created_by'] = $created_by;
                    $this->create($data);
                }
            }
        }
        return true;
    }
    public function updateManualList($id)
    {
        $request = request();

        if (isset($request->manuallist)) {

            foreach ($request->manuallist as $key => $record) {

                $typeofwork_id = $id;
                $type = 'type3';
                $checked =  isset($record['left_check']) ?  $record['left_check'] : 0;
                $check_points = $record['checklist_id'];
                $default_enable = isset($record['right_check']) ?  $record['right_check'] : 0;;
                $created_by =   $updated_by  = Auth::id();

                $recordId =  $record['record_id'];

                $data = [
                    'typeofwork_id' => $typeofwork_id,
                    'type' => $type,
                    'checked' => $checked,
                    'check_points' => $check_points,
                    'default_enable' => $default_enable,

                ];

                if ($recordId != null && $recordId != '') {
                    /**
                     * Update query
                     */

                    $data['updated_by'] = $updated_by;
                    $this->where('id', $recordId)->update($data);
                } else {
                    /**
                     * Insert Query
                     */
                    $data['created_by'] = $created_by;
                    $this->create($data);
                }
            }
        }

        return true;
    }
    public function updateCheckList($id)
    {
        $request = request();

        if (isset($request->checklist)) {

            foreach ($request->checklist as $key => $record) {

                $typeofwork_id = $id;
                $type = 'type4';
                $checked =  isset($record['left_check']) ?  $record['left_check'] : 0;
                $check_points = $record['checklist_id'];
                $default_enable = isset($record['right_check']) ?  $record['right_check'] : 0;;
                $created_by =   $updated_by  = Auth::id();

                $recordId =  $record['record_id'];

                $data = [
                    'typeofwork_id' => $typeofwork_id,
                    'type' => $type,
                    'checked' => $checked,
                    'check_points' => $check_points,
                    'default_enable' => $default_enable,

                ];

                if ($recordId != null && $recordId != '') {
                    /**
                     * Update query
                     */

                    $data['updated_by'] = $updated_by;
                    $this->where('id', $recordId)->update($data);
                } else {
                    /**
                     * Insert Query
                     */
                    $data['created_by'] = $created_by;
                    $this->create($data);
                }
            }
        }


        return true;
    }
    public function updateInstructionList($id)
    {
        $request = request();

        if (isset($request->instructionList)) {

            foreach ($request->instructionList as $key => $record) {

                $typeofwork_id = $id;
                $type = 'type5';
                $checked =  isset($record['left_check']) ?  $record['left_check'] : 0;
                $check_points = $record['checklist_id'];
                $default_enable = isset($record['right_check']) ?  $record['right_check'] : 0;;
                $created_by =   $updated_by  = Auth::id();

                $recordId =  $record['record_id'];

                $data = [
                    'typeofwork_id' => $typeofwork_id,
                    'type' => $type,
                    'checked' => $checked,
                    'check_points' => $check_points,
                    'default_enable' => $default_enable,

                ];

                if ($recordId != null && $recordId != '') {
                    /**
                     * Update query
                     */

                    $data['updated_by'] = $updated_by;
                    $this->where('id', $recordId)->update($data);
                } else {
                    /**
                     * Insert Query
                     */
                    $data['created_by'] = $created_by;
                    $this->create($data);
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


        return $data;
    }

    public function getequipmentchecklistdetails($id, $type)
    {

        $data =  $this->select('ptw_masters_typeofwork_checklist.*', 'ptw_masters_equip_involved.equip_involve')
            ->leftJoin('ptw_masters_equip_involved', 'ptw_masters_equip_involved.id', '=', 'ptw_masters_typeofwork_checklist.check_points')->where('ptw_masters_typeofwork_checklist.typeofwork_id', $id)
            ->where('ptw_masters_typeofwork_checklist.type', $type)
            ->get();


        return $data;
    }


    public function getmanualchecklistdetails($id, $type)
    {

        $data =  $this->select('ptw_masters_typeofwork_checklist.*', 'ptw_masters_precaution.precaution')
            ->leftJoin('ptw_masters_precaution', 'ptw_masters_precaution.id', '=', 'ptw_masters_typeofwork_checklist.check_points')->where('ptw_masters_typeofwork_checklist.typeofwork_id', $id)
            ->where('ptw_masters_typeofwork_checklist.type', $type)
            ->get();


        return $data;
    }

    public function getcheckchecklistdetails($id, $type)
    {

        $data =  $this->select('ptw_masters_typeofwork_checklist.*', 'ptw_masters_checklist.checklist')
            ->leftJoin('ptw_masters_checklist', 'ptw_masters_checklist.id', '=', 'ptw_masters_typeofwork_checklist.check_points')->where('ptw_masters_typeofwork_checklist.typeofwork_id', $id)
            ->where('ptw_masters_typeofwork_checklist.type', $type)
            ->get();


        return $data;
    }


    public function getinstructionchecklistdetails($id, $type)
    {

        $data =  $this->select('ptw_masters_typeofwork_checklist.*', 'ptw_masters_safe_work.safe_work')
            ->leftJoin('ptw_masters_safe_work', 'ptw_masters_safe_work.id', '=', 'ptw_masters_typeofwork_checklist.check_points')->where('ptw_masters_typeofwork_checklist.typeofwork_id', $id)
            ->where('ptw_masters_typeofwork_checklist.type', $type)
            ->get();


        return $data;
    }


    public function getprotectiveequipment($id, $type)
    {

        $data = $this->select('ptw_masters_typeofwork_checklist.*', 'ptw_masters_protective_equip.protective_equip')
            ->leftJoin('ptw_masters_protective_equip', 'ptw_masters_protective_equip.id', '=', 'ptw_masters_typeofwork_checklist.check_points')
            ->where('ptw_masters_typeofwork_checklist.typeofwork_id', $id)
            ->where('ptw_masters_typeofwork_checklist.type', $type)->where('ptw_masters_typeofwork_checklist.checked', 1)
            ->get();
        return $data;
    }

    public function getProtectivecheckpoints($ids)
    {
        return $this->where('trash', 'NO')
            ->where('status', 1)
            ->whereIn('id', $ids)
            ->select('ptw_masters_typeofwork_checklist.checked', 1)
            ->get();
    }


    public function getequipmentinvolved($id, $type)
    {

        $data =  $this->select('ptw_masters_typeofwork_checklist.*', 'ptw_masters_equip_involved.equip_involve')
            ->leftJoin('ptw_masters_equip_involved', 'ptw_masters_equip_involved.id', '=', 'ptw_masters_typeofwork_checklist.check_points')->where('ptw_masters_typeofwork_checklist.typeofwork_id', $id)
            ->where('ptw_masters_typeofwork_checklist.type', $type)->where('ptw_masters_typeofwork_checklist.checked', 1)
            ->get();
        return $data;
    }

    public function getprecaution($id, $type)
    {

        $data =  $this->select('ptw_masters_typeofwork_checklist.*', 'ptw_masters_precaution.precaution')
            ->leftJoin('ptw_masters_precaution', 'ptw_masters_precaution.id', '=', 'ptw_masters_typeofwork_checklist.check_points')->where('ptw_masters_typeofwork_checklist.typeofwork_id', $id)
            ->where('ptw_masters_typeofwork_checklist.type', $type)->where('ptw_masters_typeofwork_checklist.checked', 1)
            ->get();

        return $data;
    }

    public function getchecklist($id, $type)
    {

        $data =  $this->select('ptw_masters_typeofwork_checklist.*', 'ptw_masters_checklist.checklist')
            ->leftJoin('ptw_masters_checklist', 'ptw_masters_checklist.id', '=', 'ptw_masters_typeofwork_checklist.check_points')->where('ptw_masters_typeofwork_checklist.typeofwork_id', $id)
            ->where('ptw_masters_typeofwork_checklist.type', $type)->where('ptw_masters_typeofwork_checklist.checked', 1)
            ->get();


        return $data;
    }

    public function getinstruction($id, $type)
    {

        $data =  $this->select('ptw_masters_typeofwork_checklist.*', 'ptw_masters_safe_work.safe_work')
            ->leftJoin('ptw_masters_safe_work', 'ptw_masters_safe_work.id', '=', 'ptw_masters_typeofwork_checklist.check_points')->where('ptw_masters_typeofwork_checklist.typeofwork_id', $id)
            ->where('ptw_masters_typeofwork_checklist.type', $type)->where('ptw_masters_typeofwork_checklist.checked', 1)
            ->get();


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

            $query = $query->where('master_company.status', decryptId($request->status));
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
