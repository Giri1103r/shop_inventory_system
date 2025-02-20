<?php

namespace App\Models\IMS\Incident;



use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EHSReview extends Model
{
    use  HasFactory;


    protected $table = 'ims_ehs_review';
    protected $primaryKey = 'id';

    protected $fillable = [
        'inicdent_report_id',
        'accident_report_id',
        'fire_inicdent_report_id',
        'reviewer_emp_id',
        'reviewer_name',
        'date',
        'team_member',
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
        'trash' => 'NO',
    ];


    public function store()
    {
        $request = request();
        $decryptedTeamMemberIds = array_map('decryptId', $request->team_member);

        $commaSeparatedTeamMembers = implode(',', $decryptedTeamMemberIds);

        $insert_array = array(
            'inicdent_report_id' =>  decryptId($request->incident_id) ?? null,
            'accident_report_id' =>  decryptId($request->accident_report_id) ?? null,
            'fire_inicdent_report_id' =>  decryptId($request->fire_inicdent_report_id) ?? null,
            'date' => DBdateformat($request->date),
            'reviewer_emp_id' => $request->reviewer_emp_id,
            'reviewer_name' => $request->reviewer_name,
            'team_member' => $commaSeparatedTeamMembers,
            'remark' => $request->remark,
            'created_by' => Auth::id()
        );

        return $this->create($insert_array);
    }
}
