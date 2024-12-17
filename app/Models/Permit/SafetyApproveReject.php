<?php

namespace App\Models\Permit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

use App\Scopes\TrashScope;

class SafetyApproveReject extends Model
{
    use HasFactory;

    protected $table = 'ptw_aprove_reject';
    protected $primaryKey = 'id';

    protected $fillable = [
        'permit_id',
        'approve_reject_type',
        'approve_reject_by',
        'date',
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


    public function list()
    {

        $request = request();
        $search = '';
        $query = $this->select('ptw_hot_cold_permit.*');
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhereRaw('uauc_notification LIKE "%' . $search . '%"');
            });
        }
        if($request->has('uauc_notification') && $request->uauc_notification){
            $query = $query->where('uauc_notification','LIKE', '%'.$request->uauc_notification.'%' );
        }
        if($request->has('status') && $request->status){

            $query = $query->where('status', decryptId($request->status) );
        }


        if (isset($request->order)) {

            $columnName = $request->order[0]['name'];
            $columnorder = $request->order[0]['dir'];

            switch ($columnName) {

                case "uauc_notification":
                    $query = $query->orderBy('uauc_notification', $columnorder);
                    break;
                case "status":
                    $query = $query->orderBy('status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('id', 'DESC');
                    break;
            }
        }

        $data_count = $query ;
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


    public function ehsverification($ptw_status)
    {
        $request = request();
    
        $insert_array = array(
            'permit_id' => $request->permit_id,
            'approve_reject_type' => 1,
            'approve_reject_by' => $request->approver_name,
            'date' => DBdatetimeformat($request->date),
            'remarks' => $request->ehs_verification_remarks,
            'approve_reject_status' => $ptw_status,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }

    public function plantheadApproval($ptw_status)
    {
        $request = request();
    
        $insert_array = array(
            'permit_id' => $request->permit_id,
            'approve_reject_type' => 3,
            'approve_reject_by' => $request->approver_name,
            'date' => DBdatetimeformat($request->date),
            'remarks' => $request->planthead_approval_remarks,
            'approve_reject_status' => $ptw_status,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }


    public function ehsapproval($ptw_status)
    {
        $request = request();
    
        $insert_array = array(
            'permit_id' => $request->permit_id,
            'approve_reject_type' => 2,
            'approve_reject_by' => $request->approver_name,
            'date' => DBdatetimeformat($request->date),
            'remarks' => $request->ehs_approval_remarks,
            'approve_reject_status' => $ptw_status,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }


  
    public function getEhSverification($ptw_id) {
        $data = $this->select('ptw_aprove_reject.*')
                    ->where('ptw_aprove_reject.permit_id', $ptw_id)
                    ->where('ptw_aprove_reject.approve_reject_status', 2) ->where('ptw_aprove_reject.approve_reject_type', 1)->where('ptw_aprove_reject.trash', 'NO')
                    ->first();

        return $data;
    }

    public function getEhsapproval($ptw_id) {
        $data = $this->select('ptw_aprove_reject.*')
                    ->where('ptw_aprove_reject.permit_id', $ptw_id)
                    ->where('ptw_aprove_reject.approve_reject_status', 6) ->where('ptw_aprove_reject.approve_reject_type', 2)->where('ptw_aprove_reject.trash', 'NO')
                    ->first();

        return $data;
    }


    
    public function getplantheadapproval($ptw_id) {
        $data = $this->select('ptw_aprove_reject.*')
                    ->where('ptw_aprove_reject.permit_id', $ptw_id)
                    ->where('ptw_aprove_reject.approve_reject_status', 7) ->where('ptw_aprove_reject.approve_reject_type', 3)->where('ptw_aprove_reject.trash', 'NO')
                    ->first();

        return $data;
    }

   
   
    public function updates($id)
    {
        $request = request();

        $update_array = array(
            'uauc_notification' => $request->uauc_notification,
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

    public function selectOne($id)
    {

        $data =  $this->select('uauc_master_uauccategory.*')
            ->where('uauc_master_uauccategory.id', $id)
            ->first();

        return $data;
    }

    public function exportdata()
    {
        $request = request();
        $search = '';

        $query = $this->select('uauc_master_uauccategory.*');

        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhereRaw('uauc_notification LIKE "%' . $search . '%"');
            });
        }

        if($request->has('uauc_notification') && $request->uauc_notification){
            $query = $query->where('uauc_notification','LIKE', '%'.$request->uauc_notification.'%' );
        }
        if($request->has('status') && $request->status){
            $query = $query->where('status','LIKE', decryptId($request->status) );
        }

        if (isset($request->order)) {

            $columnName = $request->order[0]['name'];
            $columnorder = $request->order[0]['dir'];

            switch ($columnName) {

                case "uauc_notification":
                    $query = $query->orderBy('uauc_notification', $columnorder);
                    break;
                case "status":
                    $query = $query->orderBy('status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('id', 'DESC');
                    break;
            }
        }


        return  $query->get();
    }

    public function uniqueCheck($data)
    {
        return $this->where('uauc_notification',  $data)->get();
    }

    public function existUniqueCheck($data,$id)
    {
        return $this->where('uauc_notification',  $data)
            ->where('id', '!=', $id)
            ->get();
    }

    public function ajaxList($companyId = '')
    {
        $query = $this->select('id', 'uauc_notification');

        if ($companyId != '') {

            $query = $query->where('company_id', $companyId);
        }

        $datas = $query->get();

        $list = [];
        foreach ($datas as $data) {
            $listvalue = [];
            $listvalue['id'] = encryptId($data->id);
            $listvalue['name'] = $data->uauc_notification;

            $list[] = $listvalue;
        }
        return $list;
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ptw_aprove_reject'));

        static::created(function ($model) {

            $uniqueId = 'UAUC-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['uauc_notification_id' => $uniqueId]);
        });
    }
}
