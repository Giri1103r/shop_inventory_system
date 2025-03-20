<?php

namespace App\Models\Inspection;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class GembaWalkInspectionEhsApproval extends Model
{
    use  HasFactory;
    protected $table = 'inspection_gemba_walk_ehs_officer_approval';

    protected $fillable = [
        'type',
        'gemba_walk_id',
        'name',
        'date',
        'capa',
        'remarks',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];


    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];


    public function capaSubmit($gembaWalk_id, $capa_type)
    {
        $request = request();

        $insert_array = array(
            'gemba_walk_id' => $gembaWalk_id,
            'type' => $capa_type,
            'name' => $request->officer_name,
            'date' => DBdateformat($request->capa_date),
            'capa' => $request->is_passed,
            'remarks' => $request->capa_remark,
            'created_by' => Auth::id()
        );

        return $this->create($insert_array);
    }

    public function getStatus($gembaWalk_id)
    {
        return  $this->select('remarks', 'capa')->where('gemba_walk_id', $gembaWalk_id)->where('type', 1)->where('status',1)->first();
    }

    public function getApproveStatus($gembaWalk_id)
    {
        return  $this->select('remarks', 'capa')->where('gemba_walk_id', $gembaWalk_id)->where('type', 4)->where('status',1)->first();
    }

    public function getFloorApproveStatus($gembaWalk_id)
    {
        return  $this->select('remarks', 'capa')->where('gemba_walk_id', $gembaWalk_id)->where('type', 2)->where('status',1)->first();
    }

    public function getEHSReview($gembaWalk_id)
    {
        return  $this->select('remarks', 'capa')->where('gemba_walk_id', $gembaWalk_id)->where('type', 3)->where('status',1)->first();
    }



    public function getEHSCapaReview()
    {
        $data = $this->select('inspection_gemba_walk_ehs_officer_approval.*', 'inspection_gemba_walk_ehs_inspection_files.file_path')
            ->leftJoin('inspection_gemba_walk_ehs_inspection_files', 'inspection_gemba_walk_ehs_inspection_files.ehs_id', '=', 'inspection_gemba_walk_ehs_officer_approval.id')
            ->first();

        return $data;
    }


    public function getEHSFloormanagerReview()
    {
        $data = $this->select('inspection_gemba_walk_ehs_officer_approval.*', 'inspection_gemba_walk_ehs_inspection_files.file_path')
            ->leftJoin('inspection_gemba_walk_ehs_inspection_files', 'inspection_gemba_walk_ehs_inspection_files.ehs_id', '=', 'inspection_gemba_walk_ehs_officer_approval.id')
            ->where('inspection_gemba_walk_ehs_officer_approval.type', 2)
            ->first();

        return $data;
    }

}
