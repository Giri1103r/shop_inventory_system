<?php

namespace App\Models\Permit;

use App\Models\Master\Employee;
use App\Models\Permit\WorkmanInvolved;
use App\Models\Master\ContractorCompanyUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Scopes\TrashScope;

class SafetyPermit extends Model
{
    use HasFactory;

    protected $table = 'ptw_safety';
    protected $primaryKey = 'id';

    protected $fillable = [
        'permit_id',
        'date',
        'location',
        'time_from',
        'time_to',
        'unit_id',
        'exact_location_job',
        'job_location_area',
        'risk_assess_no',
        'sub_permit',
        'job_description',
        'shutdown_req',
        'shut_down_takenby',
        'loto_req',
        'loto_takenby',
        'loto_no',
        'tagfield',
        'state_isolation_loto',
        'confined_space_entry',
        'protective_equip',
        'equiment_involved',
        'equiment_involved_others',
        'precaution_taken',
        'equipment_checklist',
        'equipment_checklist_inspection',
        'safework_instruction',
        'toolbox_talk',
        'talk_givenby',
        'assigned_job',
        'attendance_toolbox_talk',
        'verified_by',
        'resume_hold_by',
        'reassign_to',
        'approved_by',
        'permit_status',
        'permit_extension_status',
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
        $id = Auth::id();

        $user = Auth::user();
        $empId = $user->employee_id;
        $userRole = $user->role;
        $unit_id = $user->unit_id;
        $id = $user->id;

        $userRole = string_to_array($userRole);
        if (isAdmin()) {
            $query = $this->select('ptw_safety.*', 'masters_unit.unit_name', 'ptw_status.status_name', 'ptw_status.bg_color')->leftJoin('masters_unit', 'masters_unit.id', '=', 'ptw_safety.unit_id')->leftJoin('ptw_status', 'ptw_status.id', '=', 'ptw_safety.permit_status');
        } elseif (in_array(ROLE_EHS_OFFICER, $userRole)) {
            $query = $this->select('ptw_safety.*', 'masters_unit.unit_name', 'ptw_status.status_name', 'ptw_status.bg_color')->leftJoin('masters_unit', 'masters_unit.id', '=', 'ptw_safety.unit_id')->leftJoin('ptw_status', 'ptw_status.id', '=', 'ptw_safety.permit_status')->where('ptw_safety.unit_id', $unit_id);
        } elseif (in_array(ROLE_PLANT_HEAD, $userRole)) {
            $query = $this->select('ptw_safety.*', 'masters_unit.unit_name', 'ptw_status.status_name', 'ptw_status.bg_color')->leftJoin('masters_unit', 'masters_unit.id', '=', 'ptw_safety.unit_id')->leftJoin('ptw_status', 'ptw_status.id', '=', 'ptw_safety.permit_status')->where('ptw_safety.unit_id', $unit_id);
        } else {
            $query = $this->select('ptw_safety.*', 'masters_unit.unit_name', 'ptw_status.status_name', 'ptw_status.bg_color')->leftJoin('masters_unit', 'masters_unit.id', '=', 'ptw_safety.unit_id')->leftJoin('ptw_status', 'ptw_status.id', '=', 'ptw_safety.permit_status')->where('ptw_safety.created_by', $id);
        }
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhereRaw('permit_id LIKE "%' . $search . '%"');
            });
        }


        if ($request->has('permit_id') && $request->permit_id) {
            $query = $query->where('ptw_safety.permit_id', 'LIKE', '%' . $request->permit_id . '%');
        }
        if ($request->has('unit_id') && $request->unit_id) {

            $unit_id = decryptId($request->unit_id);
            $query = $query->where('ptw_safety.unit_id', 'LIKE', '%' . $unit_id . '%');
        }

        if ($request->has('from_date') && !empty($request->from_date)) {
            $fromDate = $request->from_date;
            $query->where('ptw_safety.date', '>=', $fromDate);
        }

        if ($request->has('to_date') && !empty($request->to_date)) {
            $toDate = $request->to_date;
            $query->where('ptw_safety.date', '<=', $toDate);
        }

        if ($request->has('status') && $request->status) {

            $status = decryptId($request->status);
            $query = $query->where('ptw_safety.permit_status', 'LIKE', '%' . $status . '%');
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



    public function store()
    {
        $request = request();

        $sub_permit = is_array($request->sub_permit) ? implode(',', $request->sub_permit) : $request->sub_permit;

        $state_isolation_loto = !empty($request->state_isolation_loto) ? json_encode($request->state_isolation_loto) : null;
        $confined_space_entry = !empty($request->confined_space_entry) ? json_encode($request->confined_space_entry) : null;
        $protective_equip = !empty($request->protective_equip) ? json_encode($request->protective_equip) : null;
        $equiment_involved = !empty($request->equiment_involved) ? json_encode($request->equiment_involved) : null;
        $precaution_taken = !empty($request->precaution_taken) ? json_encode($request->precaution_taken) : null;
        $equipment_checklist = !empty($request->equipment_checklist) ? json_encode($request->equipment_checklist) : null;
        $safework_instruction = !empty($request->safework_instruction) ? json_encode($request->safework_instruction) : null;


        $shutdownReq = $request->has('shutdown_req') ? 1 : 0;
        $lotoReq = $request->has('loto_req') ? 1 : 0;
        $tagfield = $request->has('tagfield') ? 1 : 0;
        $toolboxTalk = $request->has('toolbox_talk') ? 1 : 0;
        $assignedJob = $request->has('assigned_job') ? 1 : 0;
        $equipment_checklist_inspection = $request->has('equipment_checklist_inspection') ? 1 : 0;

        $insert_array = array(
            'permit_id' => $request->permit_id,
            'date' => DBdateformat($request->date),
            'time_from' => $request->time_from,
            'time_to' => $request->time_to,
            'unit_id' => decryptId($request->unit_id),
            'exact_location_job' => $request->exact_location_job,
            'job_location_area' => $request->job_location_area,
            'sub_permit' => $sub_permit,
            'job_description' => $request->job_description,
            'shutdown_req' => $shutdownReq,
            'shut_down_takenby' => $request->shut_down_takenby,
            'loto_req' => $lotoReq,
            'loto_takenby' => $request->loto_takenby,
            'loto_no' => $request->loto_no,
            'tagfield' => $tagfield,
            'state_isolation_loto' => $state_isolation_loto,
            'confined_space_entry' => $confined_space_entry,
            'protective_equip' => $protective_equip,
            'equiment_involved' => $equiment_involved,
            'equiment_involved_others' => $request->equiment_involved_others,
            'precaution_taken' => $precaution_taken,
            'equipment_checklist' => $equipment_checklist,
            'equipment_checklist_inspection' => $equipment_checklist_inspection,
            'safework_instruction' => $safework_instruction,
            'toolbox_talk' => $toolboxTalk,
            'talk_givenby' => $request->talk_givenby,
            'assigned_job' => $assignedJob,
            'attendance_toolbox_talk' => $request->attendance_toolbox_talk,
            'permit_status' => STATUS_EHS_VERIFICATION_PENDING,
            'created_by' => Auth::id(),
        );

        return $this->create($insert_array);
    }


    public function updates($id)
    {
        $request = request();

        $safetypermit = $this->find($id);
        $update_array = [];

        $update_array['permit_id'] = $request->permit_id ?? $safetypermit->permit_id;
        $update_array['date'] = DBdateformat($request->date ?? $safetypermit->date);
        $update_array['time_from'] = $request->time_from ?? $safetypermit->time_from;
        $update_array['time_to'] = $request->time_to ?? $safetypermit->time_to;
        $update_array['unit_id'] = decryptId($request->unit_id) ?? $safetypermit->unit_id;
        $update_array['exact_location_job'] = $request->exact_location_job ?? $safetypermit->exact_location_job;
        $update_array['job_location_area'] = $request->job_location_area ?? $safetypermit->job_location_area;

        $update_array['sub_permit'] = is_array($request->sub_permit)
            ? implode(',', $request->sub_permit)
            : $request->sub_permit ?? $safetypermit->sub_permit;

        $update_array['state_isolation_loto'] = !empty($request->state_isolation_loto) && $request->state_isolation_loto !== $safetypermit->state_isolation_loto
            ? json_encode($request->state_isolation_loto)
            : $safetypermit->state_isolation_loto;

        $update_array['confined_space_entry'] = !empty($request->confined_space_entry) && $request->confined_space_entry !== $safetypermit->confined_space_entry
            ? json_encode($request->confined_space_entry)
            : $safetypermit->confined_space_entry;

        $update_array['protective_equip'] = !empty($request->protective_equip) && $request->protective_equip !== $safetypermit->protective_equip
            ? json_encode($request->protective_equip)
            : $safetypermit->protective_equip;

        $update_array['equiment_involved'] = !empty($request->equiment_involved) && $request->equiment_involved !== $safetypermit->equiment_involved
            ? json_encode($request->equiment_involved)
            : $safetypermit->equiment_involved;

        $update_array['precaution_taken'] = !empty($request->precaution_taken) && $request->precaution_taken !== $safetypermit->precaution_taken
            ? json_encode($request->precaution_taken)
            : $safetypermit->precaution_taken;

        $update_array['equipment_checklist'] = !empty($request->equipment_checklist) && $request->equipment_checklist !== $safetypermit->equipment_checklist
            ? json_encode($request->equipment_checklist)
            : $safetypermit->equipment_checklist;

        $update_array['safework_instruction'] = !empty($request->safework_instruction) && $request->safework_instruction !== $safetypermit->safework_instruction
            ? json_encode($request->safework_instruction)
            : $safetypermit->safework_instruction;

        $update_array['shutdown_req'] = $request->has('shutdown_req') ? 1 : $safetypermit->shutdown_req;
        $update_array['loto_req'] = $request->has('loto_req') ? 1 : $safetypermit->loto_req;
        $update_array['tagfield'] = $request->has('tagfield') ? 1 : $safetypermit->tagfield;
        $update_array['toolbox_talk'] = $request->has('toolbox_talk') ? 1 : $safetypermit->toolbox_talk;
        $update_array['assigned_job'] = $request->has('assigned_job') ? 1 : $safetypermit->assigned_job;
        $update_array['equipment_checklist_inspection'] = $request->has('equipment_checklist_inspection') ? 1 : $safetypermit->equipment_checklist_inspection;

        $update_array['job_description'] = $request->job_description ?? $safetypermit->job_description;
        $update_array['shut_down_takenby'] = $request->shut_down_takenby ?? $safetypermit->shut_down_takenby;
        $update_array['loto_takenby'] = $request->loto_takenby ?? $safetypermit->loto_takenby;
        $update_array['loto_no'] = $request->loto_no ?? $safetypermit->loto_no;
        $update_array['equiment_involved_others'] = $request->equiment_involved_others ?? $safetypermit->equiment_involved_others;
        $update_array['talk_givenby'] = $request->talk_givenby ?? $safetypermit->talk_givenby;
        $update_array['attendance_toolbox_talk'] = $request->attendance_toolbox_talk ?? $safetypermit->attendance_toolbox_talk;

        $update_array['updated_by'] = Auth::id();

        return  $this->where('id', $id)->update($update_array);
    }
    public function verifiedby($verifiedby, $id)
    {

        $verifiedby = [
            'verified_by' => $verifiedby,
        ];

        return $this->where('id', $id)->update($verifiedby);
    }
    public function resume_hold($resume_hold_by, $id)
    {

        $resume_hold_by = [
            'resume_hold_by' => $resume_hold_by,
        ];

        return $this->where('id', $id)->update($resume_hold_by);
    }

    public function reassignto($reassignto, $id)
    {

        $reassignto = [
            'reassign_to' => $reassignto,
        ];

        return $this->where('id', $id)->update($reassignto);
    }

    public function getprotetiveequip($id)
    {
        return $this->where('id', $id)->pluck('protective_equip')->first();
    }

    public function approved_by($approved_by, $id)
    {

        $approved_by = [
            'approved_by' => $approved_by,
        ];

        return $this->where('id', $id)->update($approved_by);
    }
    public function permitstatus($permit_status, $id)
    {

        $permit_status = [
            'permit_status' => $permit_status,
        ];

        return $this->where('id', $id)->update($permit_status);
    }

    public function permit_extended_status($id, $ptw_status)
    {

        if ($ptw_status == 10) {
            $extenstion_status = [
                'permit_extension_status' => 1,
            ];

            return $this->where('id', $id)->update($extenstion_status);
        } else {
            $extenstion_status = [
                'permit_extension_status' => 0,
            ];

            return $this->where('id', $id)->update($extenstion_status);
        }
    }

    public function permit_extended_time($id, $time)
    {

        $totime = [
            'time_to' => $time,
        ];

        return $this->where('id', $id)->update($totime);
    }


    public function permitCompletion($id)
    {

        $extenstion = [
            'permit_extension' => 1,
        ];

        return $this->where('id', $id)->update($extenstion);
    }

    public function permit_extended($id)
    {
        $currentValue = $this->where('id', $id)->value('permit_extended');
        $newValue = $currentValue ? $currentValue + 1 : 1;

        $extenstion = [
            'permit_extended' => $newValue,
        ];

        return $this->where('id', $id)->update($extenstion);
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
        $update_data = [
            'status' => 0,
            'trash' => 'YES',
        ];

        $main_result = $this->where('id', $id)->update($update_data);

        $WorkmanInvolved = WorkmanInvolved::where('permit_id', $id)->update($update_data);
        return [
            'main_result' => $main_result,
            'WorkmanInvolved' => $WorkmanInvolved,
        ];
    }


    public function selectOne($id)
    {
        $data = $this->select(
            'ptw_safety.*',
            'shut_down_takenby_employee.emp_name as shut_down_takenby',
            'loto_takenby_employee.emp_name as loto_takenby',
            DB::raw("(SELECT GROUP_CONCAT(work_name SEPARATOR ', ')
                      FROM ptw_masters_typeofwork
                      WHERE FIND_IN_SET(ptw_masters_typeofwork.id, ptw_safety.sub_permit)
                     ) as sub_permit_names"),
            'ptw_masters_typeofwork_checklist.id as checklist_id',
            'ptw_masters_typeofwork_checklist.type',
            'ptw_masters_typeofwork_checklist.checked',
            'ptw_masters_typeofwork_checklist.check_points',
            'ptw_masters_typeofwork_checklist.default_enable',
            DB::raw("(SELECT GROUP_CONCAT(file_path SEPARATOR ', ')
                      FROM ptw_masters_typeofwork_upload
                      WHERE FIND_IN_SET(ptw_masters_typeofwork_upload.typeofwork_id, ptw_safety.sub_permit)
                      AND ptw_masters_typeofwork_upload.trash = 'NO'
                     ) as sub_permit_images")
        )

            ->leftJoin('ptw_masters_typeofwork', function ($join) {
                $join->on('ptw_masters_typeofwork.id', '=', DB::raw('SUBSTRING_INDEX(ptw_safety.sub_permit, ",", 1)'));
            })
            ->leftJoin('ptw_masters_typeofwork_checklist', 'ptw_masters_typeofwork_checklist.typeofwork_id', '=', 'ptw_masters_typeofwork.id')
            ->leftJoin('ptw_masters_typeofwork_upload', 'ptw_masters_typeofwork_upload.typeofwork_id', '=', 'ptw_masters_typeofwork.id')
            ->leftJoin('masters_employee as shut_down_takenby_employee', 'shut_down_takenby_employee.id', '=', 'ptw_safety.shut_down_takenby')
            ->leftJoin('masters_employee as loto_takenby_employee', 'loto_takenby_employee.id', '=', 'ptw_safety.loto_takenby')
            ->where('ptw_safety.id', $id)
            ->where('ptw_safety.trash', 'NO')
            ->where('ptw_masters_typeofwork_upload.trash', 'NO')
            ->first();


        if ($data) {

            if (isset($data->sub_permit_names)) {
                $data->sub_permit_names = explode(', ', $data->sub_permit_names);
            }

            if (isset($data->sub_permit_images)) {
                $data->sub_permit_images = explode(', ', $data->sub_permit_images);
            }
            $protectiveEquip = json_decode($data->protective_equip, true);
            $mappedProtectiveEquip = [];

            if ($protectiveEquip) {
                foreach ($protectiveEquip as $typeofWorkId => $checklistIds) {
                    $workName = DB::table('ptw_masters_typeofwork')
                        ->where('id', $typeofWorkId)
                        ->value('work_name');

                    $checkpoints = DB::table('ptw_masters_typeofwork_checklist')
                        ->whereIn('id', $checklistIds)
                        ->pluck('check_points')
                        ->toArray();

                    $checkPointNames = DB::table('ptw_masters_protective_equip')
                        ->whereIn('id', $checkpoints)
                        ->pluck('protective_equip')
                        ->toArray();

                    $mappedProtectiveEquip[$workName] = [
                        'checkpoints' => $checkpoints,
                        'checkpoint_names' => $checkPointNames
                    ];
                }
            }


            $data->mapped_protective_equip = $mappedProtectiveEquip;


            $equiment_involved = json_decode($data->equiment_involved, true);
            $mappedequiment_involved = [];

            if ($equiment_involved) {
                foreach ($equiment_involved as $typeofWorkId => $checklistIds) {
                    $workName = DB::table('ptw_masters_typeofwork')
                        ->where('id', $typeofWorkId)
                        ->value('work_name');
                    $checkpoints = DB::table('ptw_masters_typeofwork_checklist')
                        ->whereIn('id', $checklistIds)
                        ->pluck('check_points')
                        ->toArray();

                    $checkPointNames = DB::table('ptw_masters_equip_involved')
                        ->whereIn('id', $checkpoints)
                        ->pluck('equip_involve')
                        ->toArray();

                    $mappedequiment_involved[$workName] = [
                        'checkpoints' => $checkpoints,
                        'checkpoint_names' => $checkPointNames
                    ];
                }
            }

            $data->mapped_equiment_involved = $mappedequiment_involved;


            $precaution_taken = json_decode($data->precaution_taken, true);
            $mappeprecaution_taken = [];

            if ($precaution_taken) {
                foreach ($precaution_taken as $typeofWorkId => $checklistIds) {
                    $workName = DB::table('ptw_masters_typeofwork')
                        ->where('id', $typeofWorkId)
                        ->value('work_name');
                    $checkpoints = DB::table('ptw_masters_typeofwork_checklist')
                        ->whereIn('id', $checklistIds)
                        ->pluck('check_points')
                        ->toArray();

                    $checkPointNames = DB::table('ptw_masters_precaution')
                        ->whereIn('id', $checkpoints)
                        ->pluck('precaution')
                        ->toArray();
                    $mappeprecaution_taken[$workName] = [
                        'checkpoints' => $checkpoints,
                        'checkpoint_names' => $checkPointNames
                    ];
                }
            }

            $data->mapped_precaution_taken = $mappeprecaution_taken;

            $equipment_checklist = json_decode($data->equipment_checklist, true);
            $mappeequipment_checklist = [];

            if ($equipment_checklist) {
                foreach ($equipment_checklist as $typeofWorkId => $checklistIds) {
                    $workName = DB::table('ptw_masters_typeofwork')
                        ->where('id', $typeofWorkId)
                        ->value('work_name');
                    $checkpoints = DB::table('ptw_masters_typeofwork_checklist')
                        ->whereIn('id', $checklistIds)
                        ->pluck('check_points')
                        ->toArray();

                    $checkPointNames = DB::table('ptw_masters_checklist')
                        ->whereIn('id', $checkpoints)
                        ->pluck('checklist')
                        ->toArray();



                    $mappeequipment_checklist[$workName] = [
                        'checkpoints' => $checkpoints,
                        'checkpoint_names' => $checkPointNames
                    ];
                }
            }

            $data->mapped_equipment_checklist = $mappeequipment_checklist;


            $safework_instruction = json_decode($data->safework_instruction, true);
            $mappesafework_instruction = [];

            if ($safework_instruction) {
                foreach ($safework_instruction as $typeofWorkId => $checklistIds) {
                    $workName = DB::table('ptw_masters_typeofwork')
                        ->where('id', $typeofWorkId)
                        ->value('work_name');
                    $checkpoints = DB::table('ptw_masters_typeofwork_checklist')
                        ->whereIn('id', $checklistIds)
                        ->pluck('check_points')
                        ->toArray();

                    $checkPointNames = DB::table('ptw_masters_safe_work')
                        ->whereIn('id', $checkpoints)
                        ->pluck('safe_work')
                        ->toArray();


                    $mappesafework_instruction[$workName] = [
                        'checkpoints' => $checkpoints,
                        'checkpoint_names' => $checkPointNames
                    ];
                }
            }

            $data->mapped_safework_instruction = $mappesafework_instruction;
        }

        return $data;
    }

    public function workmaninvolved($id)
    {

        $data =  $this->select('ptw_safety_workman_involved.*', 'masters_employee.emp_id as employee_id', 'masters_department.department_name')
            ->leftJoin('ptw_safety_workman_involved', 'ptw_safety_workman_involved.permit_id', '=', 'ptw_safety.id')
            ->leftJoin('masters_employee', 'masters_employee.id', '=', 'ptw_safety_workman_involved.emp_id')
            ->leftJoin('masters_department', 'masters_department.id', '=', 'ptw_safety_workman_involved.workman_dept')
            ->where('ptw_safety.id', $id)
            ->get();

        return $data;
    }

    public function selectmail($id)
    {
        $data =  $this->select('ptw_safety.permit_id', 'ptw_safety.unit_id', 'ptw_safety.date', 'ptw_safety.time_from', 'ptw_safety.time_to', 'ptw_safety.exact_location_job', 'ptw_safety.job_location_area')
            ->where('ptw_safety.id', $id)->where('ptw_safety.trash','NO')
            ->first();

        return $data;
    }

    public function permitapprovestatus($ptw_status, $id)
    {

        $permit_status = [
            'permit_status' => $ptw_status,
        ];

        return $this->where('id', $id)->update($permit_status);
    }
    public function statusCount($type, $params = [])
    {

        $query = $this;

        $id = Auth::id();

        $employeelocation = Employee::where('login_id', $id)->value('location');
        $contractor = ContractorCompanyUser::where('login_id', $id)->first(['login_id', 'id']);

        if ($id == 1) {
            $query = $this->select('ptw_safety.*', 'ptw_hot_cold_status.status_name', 'ptw_hot_cold_status.bg_color', 'master_operation_location_type.location_type_name');
            $query = $query->leftJoin('ptw_hot_cold_status', 'ptw_hot_cold_status.id', '=', 'ptw_safety.permit_status');

            $query = $query->leftJoin('master_operation_location_type', 'master_operation_location_type.id', '=', 'ptw_safety.location');
        } elseif (isset($contractor) && ($id == $contractor->login_id)) {
            $query = $this->select('ptw_safety.*', 'ptw_hot_cold_status.status_name', 'ptw_hot_cold_status.bg_color', 'master_operation_location_type.location_type_name');
            $query = $query->leftJoin('ptw_hot_cold_status', 'ptw_hot_cold_status.id', '=', 'ptw_safety.permit_status');

            $query = $query->leftJoin('master_operation_location_type', 'master_operation_location_type.id', '=', 'ptw_safety.location');
            $query = $query->where('ptw_safety.created_by', $contractor->login_id);
        } else {
            $query = $this->select('ptw_safety.*', 'ptw_hot_cold_status.status_name', 'ptw_hot_cold_status.bg_color', 'master_operation_location_type.location_type_name');
            $query = $query->leftJoin('ptw_hot_cold_status', 'ptw_hot_cold_status.id', '=', 'ptw_safety.permit_status');

            $query = $query->leftJoin('master_operation_location_type', 'master_operation_location_type.id', '=', 'ptw_safety.location');
            $query = $query->where('ptw_safety.location', $employeelocation);
        }


        if (isset($params['location_ids']) && $params['location_ids']) {
            $query = $query->whereIn('location', $params['location_ids']);
        }
        if (isset($params['from_date']) && isset($params['to_date'])) {
            $query = $query->whereBetween('work_from_date', [DBdateformat($params['from_date']), DBdateformat($params['to_date'])]);
        } elseif (isset($params['from_date'])) {
            $query = $query->where('work_from_date', '>=', DBdateformat($params['from_date']));
        } elseif (isset($params['to_date'])) {
            $query = $query->where('work_to_date', '<=', DBdateformat($params['to_date']));
        }

        switch ($type) {
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
            case 13:
            case 6:
            case 7:
            case 11:
            case 14:
            case 16:
            case 15:
            case 17:
            case 8:
            case 12:
            case 9:
                $query = $query->where('permit_status', $type);

                break;
        }

        $count = $query->count();

        return $count;
    }
    public function exportdata()
    {
        $request = request();
        $search = '';

        $id = Auth::id();

        $query = $this->select('ptw_safety.*', 'masters_unit.unit_name', 'ptw_status.status_name', 'ptw_status.bg_color')
            ->leftJoin(
                'masters_unit',
                'masters_unit.id',
                '=',
                'ptw_safety.unit_id'
            )->leftJoin('ptw_status', 'ptw_status.id', '=', 'ptw_safety.permit_status');

        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhereRaw('permit_id LIKE "%' . $search . '%"');
            });
        }


        if ($request->has('permit_id') && $request->permit_id) {
            $query = $query->where('ptw_safety.permit_id', 'LIKE', '%' . $request->permit_id . '%');
        }
        if ($request->has('unit_id') && $request->unit_id) {

            $unit_id = decryptId($request->unit_id);
            $query = $query->where('ptw_safety.unit_id', 'LIKE', '%' . $unit_id . '%');
        }

        if ($request->has('from_date') && !empty($request->from_date)) {
            $fromDate = $request->from_date;
            $query->where('ptw_safety.date', '>=', $fromDate);
        }

        if ($request->has('to_date') && !empty($request->to_date)) {
            $toDate = $request->to_date;
            $query->where('ptw_safety.date', '<=', $toDate);
        }

        if ($request->has('status') && $request->status) {

            $status = decryptId($request->status);
            $query = $query->where('ptw_safety.permit_status', 'LIKE', '%' . $status . '%');
        }

        return  $query->get();
    }

    public function uniqueCheck($data)
    {
        return $this->where('uauc_notification',  $data)->get();
    }

    public function existUniqueCheck($data, $id)
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
        static::addGlobalScope(new TrashScope('ptw_safety'));

        static::created(function ($model) {

            $uniqueId = 'UAUC-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['uauc_notification_id' => $uniqueId]);
        });
    }
}
