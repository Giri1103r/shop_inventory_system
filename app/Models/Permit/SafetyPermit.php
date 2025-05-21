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
        'to_date',
        'location',
        'time_from',
        'time_to',
        'company_id',
        'location_id',
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
        'isolationpanel_checkbox',
        'isolationpanel_description',
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
        'cancel_remarks',
        'cancelled_date',
        'cancelled_by',
        'close_remarks',
        'closed_date',
        'closed_by',
        'permit_status',
        'permit_extension_status',
        'reference_id',
        'pdf_download',
        'select_employee_shut_down',
        'select_employee_loto_takenby',
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
        $empid = Auth::id();

        $user = Auth::user();
        $empId = $user->employee_id;
        $userRole = $user->role;
        $unit_id = $user->unit_id;
        $empid = $user->id;
        $companyId = $user->company_id;

        $userRole = string_to_array($userRole);
        if (isAdmin()) {
            $query = $this->select('ptw_safety.*', 'masters_unit.unit_name', 'ptw_status.status_name', 'ptw_status.bg_color')->leftJoin('masters_unit', 'masters_unit.id', '=', 'ptw_safety.unit_id')->leftJoin('ptw_status', 'ptw_status.id', '=', 'ptw_safety.permit_status');
        } elseif (in_array(ROLE_EHS_OFFICER, $userRole)) {
            $query = $this->select('ptw_safety.*', 'masters_unit.unit_name', 'ptw_status.status_name', 'ptw_status.bg_color')->leftJoin('masters_unit', 'masters_unit.id', '=', 'ptw_safety.unit_id')->leftJoin('ptw_status', 'ptw_status.id', '=', 'ptw_safety.permit_status')->where('ptw_safety.company_id', $companyId);;
        } elseif (in_array(ROLE_PLANT_HEAD, $userRole)) {
            $query = $this->select('ptw_safety.*', 'masters_unit.unit_name', 'ptw_status.status_name', 'ptw_status.bg_color')->leftJoin('masters_unit', 'masters_unit.id', '=', 'ptw_safety.unit_id')->leftJoin('ptw_status', 'ptw_status.id', '=', 'ptw_safety.permit_status')->where('ptw_safety.unit_id', $unit_id);
        } elseif (in_array(ROLE_EHS_HEAD, $userRole)) {
            $query = $this->select('ptw_safety.*', 'masters_unit.unit_name', 'ptw_status.status_name', 'ptw_status.bg_color')->leftJoin('masters_unit', 'masters_unit.id', '=', 'ptw_safety.unit_id')->leftJoin('ptw_status', 'ptw_status.id', '=', 'ptw_safety.permit_status');
        } else {
            $query = $this->select('ptw_safety.*', 'masters_unit.unit_name', 'ptw_status.status_name', 'ptw_status.bg_color')->leftJoin('masters_unit', 'masters_unit.id', '=', 'ptw_safety.unit_id')->leftJoin('ptw_status', 'ptw_status.id', '=', 'ptw_safety.permit_status')->where('ptw_safety.created_by', $empid);
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
            $query = $query->where('ptw_safety.unit_id',  $unit_id);
        }

        if ($request->has('company_id') && $request->company_id) {

            $company_id = decryptId($request->company_id);
            $query = $query->where('ptw_safety.company_id',  $company_id);
        }
        if ($request->has('location_id') && $request->location_id) {

            $location_id = decryptId($request->location_id);
            $query = $query->where('ptw_safety.location_id',  $location_id);
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
            $query = $query->where('ptw_safety.permit_status',  $status);
        }


        if ($request->has('dashboard_permitStatus') && $request->dashboard_permitStatus) {
            $dashboard_permitStatus = decryptId($request->dashboard_permitStatus);
            $query = $query->where('ptw_safety.permit_status',  $dashboard_permitStatus);
        }

        if ($request->has('dashboard_openCloseStatus') && $request->dashboard_openCloseStatus) {

            $openCloseStatus = decryptId($request->dashboard_openCloseStatus);
            if ($openCloseStatus == "1") {
                $query = $query->whereNotIn('permit_status', [STATUS_CLOSED, STATUS_PERMIT_EXPIRED])
                    ->where('permit_status', '>=', STATUS_EHS_VERIFICATION_PENDING)
                    ->where('ptw_safety.status', 1)
                    ->where('ptw_safety.trash', 'NO');
            } else {
                $query = $query->where('permit_status', STATUS_CLOSED)->orWhere('permit_status', STATUS_PERMIT_EXPIRED);
            }
        }
        if ($request->has('dashboard_permitType') && $request->dashboard_permitType) {
            $query = $query->whereRaw('FIND_IN_SET(?, ptw_safety.sub_permit)', [$request->dashboard_permitType]);
        }

        if ($request->has('dashboard_month') && $request->dashboard_month) {
            $query = $query->whereMonth('ptw_safety.created_at', $request->dashboard_month);
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


        if (is_array($request->sub_permit)) {
            $sub_permit = implode(',', array_map(function ($item) {
                return decryptId($item);
            }, $request->sub_permit));
        } else {

            $sub_permit = decryptId($request->sub_permit);
        }

        $protective_equip = [];
        $data = $request->protective_equip;
        foreach ($data as $index => $data) {
            // Decrypt the index (workId)
            $id = decryptId($index);

            $protective_equip[$id] = $data;
        }

        $equiment_involved = [];
        $data = $request->equiment_involved;
        foreach ($data as $index => $data) {
            // Decrypt the index (workId)
            $id = decryptId($index);

            $equiment_involved[$id] = $data;
        }
        // Precaution Taken
        $precaution_taken = [];

        if (!empty($request->precaution_taken) && is_array($request->precaution_taken)) {
            foreach ($request->precaution_taken as $index => $data) {

                $id = decryptId($index);
                $precaution_taken[$id] = $data;
            }
        }



        $equipment_checklist = [];
        if (!empty($request->equipment_checklist) && is_array($request->equipment_checklist)) {

            foreach ($request->equipment_checklist as $index => $data) {
                // Decrypt the index (workId)
                $id = decryptId($index);

                $equipment_checklist[$id] = $data;
            }
        }
        // Instruction
        $safework_instruction = [];

        if (!empty($request->safework_instruction) && is_array($request->safework_instruction)) {
            foreach ($request->safework_instruction as $index => $data) {
                // Decrypt the index (workId)
                $id = decryptId($index);

                $safework_instruction[$id] = $data;
            }
        }
        $protective_equip = !empty($protective_equip) ? json_encode($protective_equip, true) : null;
        $equiment_involved = !empty($equiment_involved) ? json_encode($equiment_involved, true) : null;
        $precaution_taken = !empty($precaution_taken) ? json_encode($precaution_taken, true) : null;
        $equipment_checklist = !empty($equipment_checklist) ? json_encode($equipment_checklist, true) : null;
        $safework_instruction = !empty($safework_instruction) ? json_encode($safework_instruction, true) : null;

        $state_isolation_loto = !empty($request->state_isolation_loto) ? json_encode($request->state_isolation_loto) : null;
        $confined_space_entry = !empty($request->confined_space_entry) ? json_encode($request->confined_space_entry) : null;
        // $protective_equip = !empty($request->protective_equip) ? json_encode($request->protective_equip) : null;
        // $equiment_involved = !empty($request->equiment_involved) ? json_encode($request->equiment_involved) : null;
        // $precaution_taken = !empty($request->precaution_taken) ? json_encode($request->precaution_taken) : null;
        // $equipment_checklist = !empty($request->equipment_checklist) ? json_encode($request->equipment_checklist) : null;
        // $safework_instruction = !empty($request->safework_instruction) ? json_encode($request->safework_instruction) : null;

        $shutdownReq = $request->has('shutdown_req') ? 1 : 0;
        $lotoReq = $request->has('loto_req') ? 1 : 0;
        $tagfield = $request->has('tagfield') ? 1 : 0;
        $toolboxTalk = $request->has('toolbox_talk') ? 1 : 0;
        $assignedJob = $request->has('assigned_job') ? 1 : 0;
        $equipment_checklist_inspection = $request->has('equipment_checklist_inspection') ? 1 : 0;
        $company = Auth::user()->company_id;
        $location = Auth::user()->location_id;
        $insert_array = array(
            // 'permit_id' => $request->permit_id,
            'date' => DBdateformat($request->date),
            'to_date' => DBdateformat($request->to_date),
            'time_from' => $request->time_from,
            'time_to' => $request->time_to,
            'unit_id' => decryptId($request->unit_id),
            'company_id' => decryptId($request->company_id),
            'location_id' => decryptId($request->location_id),
            'exact_location_job' => $request->exact_location_job,
            'job_location_area' => $request->job_location_area,
            'sub_permit' => $sub_permit,
            'job_description' => $request->job_description,
            'shutdown_req' => $shutdownReq,
            'shut_down_takenby' => $request->shut_down_takenby,
            'loto_req' => $lotoReq,
            'loto_takenby' => $request->loto_takenby,
            'loto_no' => $request->loto_no,
            'select_employee_loto_takenby' => $request->request_for_loto_down,
            'select_employee_shut_down' => $request->request_for_shut_down,
            'tagfield' => $tagfield,
            'state_isolation_loto' => $state_isolation_loto,
            'isolationpanel_checkbox' => $request->isolationpanel_checkbox,
            'isolationpanel_description' => $request->isolationpanel_description,
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
        $company = Auth::user()->company_id;
        $location = Auth::user()->location_id;

        $update_array = [];
        $update_array['permit_id'] = $request->permit_id ?? $safetypermit->permit_id;
        $update_array['date'] = DBdateformat($request->date ?? $safetypermit->date);
        $update_array['to_date'] = DBdateformat($request->to_date ?? $safetypermit->to_date);
        $update_array['company_id'] = decryptId($request->company_id) ?? $safetypermit->company_id;
        $update_array['location_id'] = decryptId($request->location_id) ?? $safetypermit->location_id;
        $update_array['time_from'] = $request->time_from ?? $safetypermit->time_from;
        $update_array['time_to'] = $request->time_to ?? $safetypermit->time_to;
        $update_array['unit_id'] = decryptId($request->unit_id) ?? $safetypermit->unit_id;
        $update_array['exact_location_job'] = $request->exact_location_job ?? $safetypermit->exact_location_job;
        $update_array['job_location_area'] = $request->job_location_area ?? $safetypermit->job_location_area;
        $update_array['isolationpanel_checkbox'] = $request->isolationpanel_checkbox ?? $safetypermit->isolationpanel_checkbox;
        $update_array['isolationpanel_description'] = $request->isolationpanel_description ?? $safetypermit->isolationpanel_description;

        $update_array['sub_permit'] = is_array($request->sub_permit)
            ? implode(',', array_map('decryptId', $request->sub_permit))
            : ($request->sub_permit ? decryptId($request->sub_permit) : $safetypermit->sub_permit);
        // Protective Equip


        $protective_equip = [];
        $data = $request->protective_equip ?? [];

        foreach ($data as $index => $ProtectiveequipData) {
            $safetydata = decryptId($index);
            $protective_equip[$safetydata] = $ProtectiveequipData;
        }

        // $existingProtectiveEquip = json_decode($safetypermit->protective_equip, true) ?? [];

        $existingProtectiveEquip =   !empty($safetypermit->protective_equip)
            ? json_decode($safetypermit->protective_equip, true)
            : [];

        $update_array['protective_equip'] = !empty($protective_equip) && $protective_equip !== $existingProtectiveEquip
            ? json_encode($protective_equip)
            : $safetypermit->protective_equip;

        //    dd($update_array['protective_equip']);
        // Equipment Invlved

        $equiment_involved = [];
        $data = $request->equiment_involved ?? [];

        foreach ($data as $index => $equipInvoleData) {
            $safetydata = decryptId($index);
            $equiment_involved[$safetydata] = $equipInvoleData;
        }

        // $existingEquipInvolve = json_decode($safetypermit->equiment_involved, true) ?? [];

        $existingEquipInvolve =   !empty($safetypermit->equiment_involved)
            ? json_decode($safetypermit->equiment_involved, true)
            : [];

        $update_array['equiment_involved'] = !empty($equiment_involved) && $equiment_involved !== $existingEquipInvolve
            ? json_encode($equiment_involved)
            : $safetypermit->equiment_involved;

        // Precaution TO Be Taken


        $precaution_taken = [];
        $data = $request->precaution_taken ?? [];


        foreach ($data as $index => $EquipPrecautionData) {
            $safetydata = decryptId($index);
            $precaution_taken[$safetydata] = $EquipPrecautionData;
        }

        // $existingPrecaution = json_decode($safetypermit->precaution_taken, true) ?? [];

        $existingPrecaution =   !empty($safetypermit->precaution_taken)
            ? json_decode($safetypermit->precaution_taken, true)
            : [];

        $update_array['precaution_taken'] = !empty($precaution_taken) && $precaution_taken !== $existingPrecaution
            ? json_encode($precaution_taken)
            : $safetypermit->precaution_taken;
        // dd($update_array['precaution_taken']);

        // Check List

        $equipment_checklist = [];
        $data = $request->equipment_checklist ?? [];

        foreach ($data as $index => $EquipChecklistData) {
            $safetydata = decryptId($index);
            $equipment_checklist[$safetydata] = $EquipChecklistData;
        }

        // $existingChecklistEquip = json_decode($safetypermit->equipment_checklist, true) ?? [];

        $existingChecklistEquip =   !empty($safetypermit->equipment_checklist)
            ? json_decode($safetypermit->equipment_checklist, true)
            : [];

        $update_array['equipment_checklist'] = !empty($equipment_checklist) && $equipment_checklist !== $existingChecklistEquip
            ? json_encode($equipment_checklist)
            : $safetypermit->equipment_checklist;

        // Instruction

        $safework_instruction = [];
        $data = $request->safework_instruction ?? [];

        foreach ($data as $index => $equipIntructionData) {
            $safetydata = decryptId($index);
            $safework_instruction[$safetydata] = $equipIntructionData;
        }

        // $existingEquip = json_decode($safetypermit->safework_instruction, true) ?? [];

        $existingEquip =   !empty($safetypermit->safework_instruction)
            ? json_decode($safetypermit->safework_instruction, true)
            : [];

        $update_array['safework_instruction'] = !empty($safework_instruction) && $safework_instruction !== $existingEquip
            ? json_encode($safework_instruction)
            : $safetypermit->safework_instruction;


        $update_array['state_isolation_loto'] = !empty($request->state_isolation_loto) && $request->state_isolation_loto !== $safetypermit->state_isolation_loto
            ? json_encode($request->state_isolation_loto)
            : $safetypermit->state_isolation_loto;

        $update_array['confined_space_entry'] = !empty($request->confined_space_entry) && $request->confined_space_entry !== $safetypermit->confined_space_entry
            ? json_encode($request->confined_space_entry)
            : $safetypermit->confined_space_entry;

        // $update_array['protective_equip'] = !empty($request->protective_equip) && $request->protective_equip !== $safetypermit->protective_equip
        //     ? json_encode($request->protective_equip)
        //     : $safetypermit->protective_equip;

        // $update_array['equiment_involved'] = !empty($request->equiment_involved) && $request->equiment_involved !== $safetypermit->equiment_involved
        //     ? json_encode($request->equiment_involved)
        //     : $safetypermit->equiment_involved;

        // $update_array['precaution_taken'] = !empty($request->precaution_taken) && $request->precaution_taken !== $safetypermit->precaution_taken
        //     ? json_encode($request->precaution_taken)
        //     : $safetypermit->precaution_taken;

        // $update_array['equipment_checklist'] = !empty($request->equipment_checklist) && $request->equipment_checklist !== $safetypermit->equipment_checklist
        //     ? json_encode($request->equipment_checklist)
        //     : $safetypermit->equipment_checklist;

        // $update_array['safework_instruction'] = !empty($request->safework_instruction) && $request->safework_instruction !== $safetypermit->safework_instruction
        //     ? json_encode($request->safework_instruction)
        //     : $safetypermit->safework_instruction;

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
        $update_array['select_employee_loto_takenby'] = $request->request_for_loto_down ?? $safetypermit->request_for_loto_down;
        $update_array['select_employee_shut_down'] = $request->request_for_shut_down ?? $safetypermit->request_for_shut_down;
        $update_array['permit_status'] = STATUS_EHS_VERIFICATION_PENDING;
        $update_array['updated_by'] = Auth::id();

        // dd($update_array);
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

    public function laststatus()
    {
        $employeeId = Auth::id();
        $laststatus = $this->where('created_by', $employeeId)
            ->orderBy('id', 'DESC')
            ->pluck('permit_status')
            ->first();
        return $laststatus;
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

    public function CreateData($safetypermit)
    {
        $request = request();
        $permitId = SafetyPermit::orderBy('id', 'DESC')->pluck('permit_id')->first();
        preg_match('/(\d+)$/', $permitId, $matches);

        $newPermitNumeric = str_pad((int)$matches[0] + 1, 5, '0', STR_PAD_LEFT);

        $newPermitID = 'ORD/' . $newPermitNumeric;

        $insert_array = array(
            'permit_id' => $newPermitID,
            'date' => DBdateformat(now()),
            'to_date' => DBdateformat($request->date),
            'time_from' => $safetypermit->time_from,
            'time_to' => $request->time_to,
            'unit_id' => $safetypermit->unit_id,
            'company_id' => $safetypermit->company_id,
            'location_id' => $safetypermit->location_id,
            'exact_location_job' => $safetypermit->exact_location_job,
            'job_location_area' => $safetypermit->job_location_area,
            'sub_permit' => $safetypermit->sub_permit,
            'job_description' => $safetypermit->job_description,
            'shutdown_req' => $safetypermit->shutdown_req,
            'shut_down_takenby' => $safetypermit->shut_down_takenby,
            'loto_req' => $safetypermit->loto_req,
            'loto_takenby' => $safetypermit->loto_takenby,
            'loto_no' => $safetypermit->loto_no,
            'tagfield' => $safetypermit->tagfield,
            'state_isolation_loto' => $safetypermit->state_isolation_loto,
            'isolationpanel_checkbox' => $safetypermit->isolationpanel_checkbox,
            'isolationpanel_description' => $safetypermit->isolationpanel_description,
            'confined_space_entry' => $safetypermit->confined_space_entry,
            'protective_equip' => $safetypermit->protective_equip,
            'equiment_involved' => $safetypermit->equiment_involved,
            'equiment_involved_others' => $safetypermit->equiment_involved_others,
            'precaution_taken' => $safetypermit->precaution_taken,
            'equipment_checklist' => $safetypermit->equipment_checklist,
            'equipment_checklist_inspection' => $safetypermit->equipment_checklist_inspection,
            'safework_instruction' => $safetypermit->safework_instruction,
            'toolbox_talk' => $safetypermit->toolbox_talk,
            'talk_givenby' => $safetypermit->talk_givenby,
            'assigned_job' => $safetypermit->assigned_job,
            'attendance_toolbox_talk' => $safetypermit->attendance_toolbox_talk,
            'permit_status' => STATUS_EHS_VERIFICATION_PENDING,
            'created_by' => Auth::id(),
        );

        return $this->create($insert_array);
    }


    public function deleterecord($id, $remarks)
    {
        $update_data = [
            'permit_status' => 14,
            'cancel_remarks' =>  $remarks,
            'cancelled_date' =>  todayDbdate(),
            'cancelled_by' =>  Auth::id(),
        ];

        return $this->where('id', $id)->update($update_data);

        // $WorkmanInvolved = WorkmanInvolved::where('permit_id', $id)->update($update_data);
        // return [
        //     'main_result' => $main_result,
        //     'WorkmanInvolved' => $WorkmanInvolved,
        // ];
    }
    public function closePermit($id, $remarks)
    {
        $update_data = [
            'permit_status' => 15,
            'close_remarks' =>  $remarks,
            'closed_date' =>  todayDbdate(),
            'closed_by' =>  Auth::id(),
        ];

        return $this->where('id', $id)->update($update_data);
    }


    public function selectOne($id)
    {

        // dd($id);
        $data = $this->select(
            'ptw_safety.*',
            // 'shut_down_takenby_employee.emp_name as shut_down_takenby',
            // 'loto_takenby_employee.emp_name as loto_takenby',
            DB::raw("(SELECT GROUP_CONCAT(work_name SEPARATOR ', ')
                      FROM ptw_masters_typeofwork
                      WHERE FIND_IN_SET(ptw_masters_typeofwork.id, ptw_safety.sub_permit)
                     ) as sub_permit_names"),
            'ptw_masters_typeofwork_checklist.id as checklist_id',
            'ptw_masters_typeofwork_checklist.type',
            'ptw_masters_typeofwork_checklist.checked',
            'ptw_masters_typeofwork_checklist.check_points',
            'ptw_masters_typeofwork_checklist.default_enable',
            'ptw_status.status_name',
            'ptw_status.id as status_id',
            DB::raw("(SELECT GROUP_CONCAT(file_path SEPARATOR ', ')
                      FROM ptw_masters_typeofwork_upload
                      WHERE FIND_IN_SET(ptw_masters_typeofwork_upload.typeofwork_id, ptw_safety.sub_permit)
                      AND ptw_masters_typeofwork_upload.trash = 'NO'
                     ) as sub_permit_images")
        )

            ->leftJoin('ptw_masters_typeofwork', function ($join) {
                $join->on('ptw_masters_typeofwork.id', '=', DB::raw('SUBSTRING_INDEX(ptw_safety.sub_permit, ",", 1)'));
            })
            ->leftJoin('ptw_status', 'ptw_status.id', '=', 'ptw_safety.permit_status')
            ->leftJoin('ptw_masters_typeofwork_checklist', 'ptw_masters_typeofwork_checklist.typeofwork_id', '=', 'ptw_masters_typeofwork.id')
            ->leftJoin('ptw_masters_typeofwork_upload', 'ptw_masters_typeofwork_upload.typeofwork_id', '=', 'ptw_masters_typeofwork.id')

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
            // $protectiveEquip = json_decode($data->protective_equip, true);

            $protectiveEquip =   !empty($data->protective_equip)
                ? json_decode($data->protective_equip, true)
                : null;
            $mappedProtectiveEquip = [];

            if ($protectiveEquip) {
                foreach ($protectiveEquip as $typeofWorkId => $checklistIds) {
                    $workName = DB::table('ptw_masters_typeofwork')
                        ->where('id', $typeofWorkId)
                        ->value('work_name');
                    $workid = DB::table('ptw_masters_typeofwork')
                        ->where('id', $typeofWorkId)
                        ->value('id');

                    $checkpoints = DB::table('ptw_masters_typeofwork_checklist')
                        ->where('type', 'type1')
                        ->whereIn('id', $checklistIds)
                        ->pluck('check_points')
                        ->toArray();
                    $checkid = DB::table('ptw_masters_typeofwork_checklist')
                        ->where('type', 'type1')
                        ->whereIn('id', $checklistIds)
                        ->pluck('id')
                        ->toArray();

                    $checkPointNames = DB::table('ptw_masters_protective_equip')
                        ->whereIn('id', $checkpoints)
                        ->pluck('protective_equip')
                        ->toArray();

                    $mappedProtectiveEquip[$workid] = [
                        'checkpoints' => $checkpoints,
                        'checkpoint_names' => $checkPointNames,
                        'checkid' => $checkid
                    ];
                }
            }


            $data->mapped_protective_equip = $mappedProtectiveEquip;


            // $equiment_involved = json_decode($data->equiment_involved, true);

            $equiment_involved =   !empty($data->equiment_involved)
                ? json_decode($data->equiment_involved, true)
                : null;

            $mappedequiment_involved = [];

            if ($equiment_involved) {
                foreach ($equiment_involved as $typeofWorkId => $checklistIds) {

                    $workName = DB::table('ptw_masters_typeofwork')
                        ->where('id', $typeofWorkId)
                        ->value('work_name');
                    $workid = DB::table('ptw_masters_typeofwork')
                        ->where('id', $typeofWorkId)
                        ->value('id');

                    $checkpoints = DB::table('ptw_masters_typeofwork_checklist')
                        ->where('type', 'type2')
                        ->whereIn('id', $checklistIds)
                        ->pluck('check_points')
                        ->toArray();
                    $checkid = DB::table('ptw_masters_typeofwork_checklist')
                        ->where('type', 'type2')
                        ->whereIn('id', $checklistIds)
                        ->pluck('id')
                        ->toArray();

                    $checkPointNames = DB::table('ptw_masters_equip_involved')
                        ->whereIn('id', $checkpoints)
                        ->pluck('equip_involve')
                        ->toArray();

                    $mappedequiment_involved[$workid] = [
                        'checkpoints' => $checkpoints,
                        'checkpoint_names' => $checkPointNames,
                        'checkid' => $checkid
                    ];
                }
            }

            $data->mapped_equiment_involved = $mappedequiment_involved;


            // $precaution_taken = json_decode($data->precaution_taken, true);

            $precaution_taken =   !empty($data->precaution_taken)
                ? json_decode($data->precaution_taken, true)
                : null;
            $mappeprecaution_taken = [];
            if ($precaution_taken) {
                foreach ($precaution_taken as $typeofWorkId => $checklistIds) {

                    $workName = DB::table('ptw_masters_typeofwork')
                        ->where('id', $typeofWorkId)
                        ->value('work_name');
                    $workid = DB::table('ptw_masters_typeofwork')
                        ->where('id', $typeofWorkId)
                        ->value('id');
                    $checkpoints = DB::table('ptw_masters_typeofwork_checklist')
                        ->where('type', 'type3')
                        ->whereIn('id', $checklistIds)
                        ->pluck('check_points')
                        ->toArray();

                    $checkid = DB::table('ptw_masters_typeofwork_checklist')
                        ->where('type', 'type3')
                        ->whereIn('id', $checklistIds)
                        ->pluck('id')
                        ->toArray();
                    $checkPointNames = DB::table('ptw_masters_precaution')
                        ->whereIn('id', $checkpoints)
                        ->pluck('precaution')
                        ->toArray();
                    $mappeprecaution_taken[$workid] = [
                        'checkpoints' => $checkpoints,
                        'checkpoint_names' => $checkPointNames,
                        'checkid' => $checkid
                    ];
                }
            }

            $data->mapped_precaution_taken = $mappeprecaution_taken;

            // $equipment_checklist = json_decode($data->equipment_checklist, true);

            $equipment_checklist =   !empty($data->equipment_checklist)
                ? json_decode($data->equipment_checklist, true)
                : null;
            $mappeequipment_checklist = [];

            if ($equipment_checklist) {
                foreach ($equipment_checklist as $typeofWorkId => $checklistIds) {
                    $workName = DB::table('ptw_masters_typeofwork')
                        ->where('id', $typeofWorkId)
                        ->value('work_name');
                    $workid = DB::table('ptw_masters_typeofwork')
                        ->where('id', $typeofWorkId)
                        ->value('id');
                    $checkpoints = DB::table('ptw_masters_typeofwork_checklist')
                        ->where('type', 'type4')
                        ->whereIn('id', $checklistIds)
                        ->pluck('check_points')
                        ->toArray();
                    $checkid = DB::table('ptw_masters_typeofwork_checklist')
                        ->where('type', 'type4')

                        ->whereIn('id', $checklistIds)
                        ->pluck('id')
                        ->toArray();

                    $checkPointNames = DB::table('ptw_masters_checklist')
                        ->whereIn('id', $checkpoints)
                        ->pluck('checklist')
                        ->toArray();



                    $mappeequipment_checklist[$workid] = [
                        'checkpoints' => $checkpoints,
                        'checkpoint_names' => $checkPointNames,
                        'checkid' => $checkid
                    ];
                }
            }

            $data->mapped_equipment_checklist = $mappeequipment_checklist;


            // $safework_instruction = json_decode($data->safework_instruction, true);

            $safework_instruction = !empty($data->safework_instruction)
                ? json_decode($data->safework_instruction, true)
                : null;

            $mappesafework_instruction = [];

            if ($safework_instruction) {
                foreach ($safework_instruction as $typeofWorkId => $checklistIds) {
                    $workName = DB::table('ptw_masters_typeofwork')
                        ->where('id', $typeofWorkId)
                        ->value('work_name');
                    $workid = DB::table('ptw_masters_typeofwork')
                        ->where('id', $typeofWorkId)
                        ->value('id');
                    $checkpoints = DB::table('ptw_masters_typeofwork_checklist')
                        ->where('type', 'type5')
                        ->whereIn('id', $checklistIds)
                        ->pluck('check_points')
                        ->toArray();
                    $checkid = DB::table('ptw_masters_typeofwork_checklist')
                        ->where('type', 'type5')
                        ->whereIn('id', $checklistIds)
                        ->pluck('id')
                        ->toArray();

                    $checkPointNames = DB::table('ptw_masters_safe_work')
                        ->whereIn('id', $checkpoints)
                        ->pluck('safe_work')
                        ->toArray();


                    $mappesafework_instruction[$workid] = [
                        'checkpoints' => $checkpoints,
                        'checkpoint_names' => $checkPointNames,
                        'checkid' => $checkid
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
            ->where('ptw_safety_workman_involved.status', 1)
            ->get();

        return $data;
    }

    public function selectmail($id)
    {
        $data =  $this->select('ptw_safety.permit_id', 'ptw_safety.unit_id', 'ptw_safety.date', 'ptw_safety.time_from', 'ptw_safety.time_to', 'ptw_safety.exact_location_job', 'ptw_safety.job_location_area')
            ->where('ptw_safety.id', $id)->where('ptw_safety.trash', 'NO')
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
        // $contractor = ContractorCompanyUser::where('login_id', $id)->first(['login_id', 'id']);

        $query = $this->select('ptw_safety.*', 'masters_unit.unit_name', 'ptw_status.status_name', 'ptw_status.bg_color')->leftJoin('masters_unit', 'masters_unit.id', '=', 'ptw_safety.unit_id')->leftJoin('ptw_status', 'ptw_status.id', '=', 'ptw_safety.permit_status');

        if (isset($params['unit_id']) && $params['unit_id']) {
            $query = $query->whereIn('ptw_safety.unit_id', $params['unit_id']);
        }
        if (isset($params['from_date']) && isset($params['to_date'])) {
            $query = $query->whereBetween('date', [DBdateformat($params['from_date']), DBdateformat($params['to_date'])]);
        } elseif (isset($params['from_date'])) {
            $query = $query->where('date', '>=', DBdateformat($params['from_date']));
        } elseif (isset($params['to_date'])) {
            $query = $query->where('work_to_date', '<=', DBdateformat($params['to_date']));
        }


        switch ($type) {
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
            case 6:
            case 7:
            case 8:
            case 9:
            case 10:
            case 11:
            case 12:
            case 13:
            case 14:
            case 15:

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

        $user = Auth::user();
        $empId = $user->employee_id;
        $userRole = $user->role;
        $unit_id = $user->unit_id;
        $company_id = $user->company_id;
        $id = $user->id;

        $userRole = string_to_array($userRole);
        if (isAdmin()) {
            $query = $this->select('ptw_safety.*', 'masters_unit.unit_name', 'ptw_status.status_name', 'ptw_status.to_status', 'ptw_status.bg_color')->leftJoin('masters_unit', 'masters_unit.id', '=', 'ptw_safety.unit_id')->leftJoin('ptw_status', 'ptw_status.id', '=', 'ptw_safety.permit_status');
        } elseif (in_array(ROLE_EHS_OFFICER, $userRole)) {
            $query = $this->select('ptw_safety.*', 'masters_unit.unit_name', 'ptw_status.status_name', 'ptw_status.to_status', 'ptw_status.bg_color')->leftJoin('masters_unit', 'masters_unit.id', '=', 'ptw_safety.unit_id')->leftJoin('ptw_status', 'ptw_status.id', '=', 'ptw_safety.permit_status')->where('ptw_safety.company_id', $company_id);
        } elseif (in_array(ROLE_PLANT_HEAD, $userRole)) {
            $query = $this->select('ptw_safety.*', 'masters_unit.unit_name', 'ptw_status.status_name', 'ptw_status.to_status', 'ptw_status.bg_color')->leftJoin('masters_unit', 'masters_unit.id', '=', 'ptw_safety.unit_id')->leftJoin('ptw_status', 'ptw_status.id', '=', 'ptw_safety.permit_status')->where('ptw_safety.unit_id', $unit_id);
        } elseif (in_array(ROLE_EHS_HEAD, $userRole)) {
            $query = $this->select('ptw_safety.*', 'masters_unit.unit_name', 'ptw_status.status_name', 'ptw_status.to_status', 'ptw_status.bg_color')->leftJoin('masters_unit', 'masters_unit.id', '=', 'ptw_safety.unit_id')->leftJoin('ptw_status', 'ptw_status.id', '=', 'ptw_safety.permit_status');
        } else {
            $query = $this->select('ptw_safety.*', 'masters_unit.unit_name', 'ptw_status.status_name', 'ptw_status.to_status', 'ptw_status.bg_color')->leftJoin('masters_unit', 'masters_unit.id', '=', 'ptw_safety.unit_id')->leftJoin('ptw_status', 'ptw_status.id', '=', 'ptw_safety.permit_status')->where('ptw_safety.created_by', $id);
        }

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
            $query = $query->where('ptw_safety.unit_id', 'LIKE',  $unit_id);
        }
        if ($request->has('company_id') && $request->company_id) {

            $company_id = decryptId($request->company_id);
            $query = $query->where('ptw_safety.company_id',  $company_id);
        }
        if ($request->has('location_id') && $request->location_id) {

            $location_id = decryptId($request->location_id);
            $query = $query->where('ptw_safety.location_id',  $location_id);
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
            $query = $query->where('ptw_safety.permit_status',  $status);
        }

        return  $query->orderBy('ptw_safety.id', 'DESC')->get();
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


    public function permitData($id)
    {
        return SafetyPermit::where('id', $id)->first();
    }

    public function DuplicatepermitData($id)
    {
        return SafetyPermit::where('reference_id', $id)->exists();
    }

    public function PermitExtensionUpdate($id)
    {
        return $this->where('id', $id)->update(['reference_id' => $id]);
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ptw_safety'));

        static::created(function ($model) {

            $uniqueId = 'ORD/' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['permit_id' => $uniqueId]);
        });
    }

    public function reassignto_api($reassignto, $id)
    {

        $reassignto = [
            'reassign_to' => $reassignto,
        ];

        return $this->where('id', $id)->update($reassignto);
    }

    public function ActiveVsClose()
    {
        $request = request();
        $from_date = $request->input('Fromdate');
        $to_date = $request->input('Todate');
        $company_id = $request->input('CompanyId');


        $openQuery = $this->whereNotIn('permit_status', [STATUS_CLOSED, STATUS_PERMIT_EXPIRED])
            ->where('permit_status', '>=', STATUS_EHS_VERIFICATION_PENDING)
            ->where('status', 1)
            ->where('trash', 'NO');


        $closeQuery = $this->where(function ($query) {
            $query->where('permit_status', STATUS_CLOSED)
                ->orWhere('permit_status', STATUS_PERMIT_EXPIRED);
        })
            ->where('status', 1)
            ->where('trash', 'NO');


        if ($company_id) {
            $companyId = decryptId($company_id);
            $openQuery->where('company_id', $companyId);
            $closeQuery->where('company_id', $companyId);
        }


        if ($from_date && $to_date) {
            $openQuery->whereBetween('created_at', [
                DBdateformat($from_date),
                DBdateformat($to_date) . ' 23:59:59'
            ]);
            $closeQuery->whereBetween('created_at', [
                DBdateformat($from_date),
                DBdateformat($to_date) . ' 23:59:59'
            ]);
        } elseif ($from_date) {
            $openQuery->where('created_at', '>=', DBdateformat($from_date));
            $closeQuery->where('created_at', '>=', DBdateformat($from_date));
        } elseif ($to_date) {
            $openQuery->where('created_at', '<=', DBdateformat($to_date) . ' 23:59:59');
            $closeQuery->where('created_at', '<=', DBdateformat($to_date) . ' 23:59:59');
        }


        $open_count = $openQuery->count();
        $close_count = $closeQuery->count();

        return [
            'PTW Open Count' => $open_count,
            'PTW Close Count' => $close_count
        ];
    }


    public function GetTypeWiseCount()
    {
        $request = request();
        $from_date = $request->input('Fromdate');
        $to_date = $request->input('Todate');
        $company_id = $request->input('CompanyId');

        $query = $this->where('status', 1)->where('trash', 'NO');

        if ($company_id) {
            $companyId = decryptId($company_id);
            $query->where('company_id', $companyId);
        }


        if ($from_date && $to_date) {
            $query->whereBetween('created_at', [
                DBdateformat($from_date),
                DBdateformat($to_date) . ' 23:59:59'
            ]);
        } elseif ($from_date) {
            $query->where('created_at', '>=', DBdateformat($from_date));
        } elseif ($to_date) {
            $query->where('created_at', '<=', DBdateformat($to_date) . ' 23:59:59');
        }


        $data = $query->get();

        $type_of_work = GetPTWTypes();

        $idToWorkNameMap = [];
        $workCounts = [];

        foreach ($type_of_work as $type) {
            $idToWorkNameMap[$type['id']] = $type['work_name'];
            $workCounts[$type['id']] = [
                'id' => $type['id'],
                'name' => $type['work_name'],
                'count' => 0
            ];
        }


        foreach ($data as $details) {
            $subpermits = string_to_array($details->sub_permit);
            foreach ($subpermits as $subpermit) {
                if ($subpermit && isset($workCounts[$subpermit])) {
                    $workCounts[$subpermit]['count']++;
                }
            }
        }

        return $workCounts;
    }
    public function getHoldStatus($request)
    {
        $query = DB::table('ptw_safety')
            ->join('masters_unit', 'ptw_safety.unit_id', '=', 'masters_unit.id')
            ->select(
                'masters_unit.unit_name',
                'ptw_safety.unit_id',
                DB::raw('COUNT(*) as hold_count')
            )
            ->where('ptw_safety.permit_status', STATUS_EHS_HOLD)
            ->groupBy('masters_unit.unit_name', 'masters_unit.id');

        $company_id = $request->input('CompanyId');
        if ($company_id) {
            $companyId = decryptId($company_id);
            $query->where('ptw_safety.company_id', $companyId);
        }

        if ($request->Fromdate && $request->Todate) {
            $query->whereBetween('ptw_safety.created_at', [
                DBdateformat($request->Fromdate),
                DBdateformat($request->Todate) . ' 23:59:59'
            ]);
        } elseif ($request->Fromdate) {
            $query->where('ptw_safety.created_at', '>=', DBdateformat($request->Fromdate));
        } elseif ($request->Todate) {
            $query->where('ptw_safety.created_at', '<=', DBdateformat($request->Todate) . ' 23:59:59');
        }

        $results = $query->get();


        return $results->map(function ($item) {
            return [
                'unit_id' => $item->unit_id,
                'unit_name' => $item->unit_name,
                'hold_count' => $item->hold_count,
            ];
        })->toArray();
    }
}
