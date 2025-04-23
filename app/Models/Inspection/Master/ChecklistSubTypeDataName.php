<?php

namespace App\Models\Inspection\Master;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class ChecklistSubTypeDataName extends Model
{

    protected $table = 'inspection_master_checklist_sub_type_data_name';
    protected $primaryKey = 'id';

    protected $fillable = [
        'checklist_sub_type_data_id',
        'name',
        'description',
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

    public function store($checklistSubTypeDataId)
    {
        $request = request();
        $checklistData = $request->input('checklist');

        if (!empty($checklistData) && is_array($checklistData)) {
            foreach ($checklistData as $datalist) {

                $data = [
                    'checklist_sub_type_data_id' => $checklistSubTypeDataId,
                    'name' => $datalist['name'],
                    'description' => $datalist['description'] ?? null,
                    'created_by' => Auth::id(),
                ];
                $this->create($data);
            }
        }
    }
    public function updates($checklistSubTypeDataId)
    {
        $request = request();
        $checklistData = $request->input('checklist');

        foreach ($checklistData as $datalist) {

            $data = [
                'checklist_sub_type_data_id' => $checklistSubTypeDataId,
                'name' => $datalist['name'],
                'description' => $datalist['description'] ?? null,
            ];

            if (!isset($datalist['subTypeDataNameId']) || empty($datalist['subTypeDataNameId'])) {
                $data['created_by'] = Auth::id();
                $this->create($data);
            } else {
                $this->where('id', $datalist['subTypeDataNameId'])
                    ->where('checklist_sub_type_data_id', $checklistSubTypeDataId)
                    ->update(array_merge($data, ['updated_by' => Auth::id()]));
            }
        }
    }

    // public function updates($checklistSubTypeDataId)
    // {
    //     $request = request();
    //     $checklistData = $request->input('checklist');

    //     if (!empty($checklistData) && is_array($checklistData)) {
    //         foreach ($checklistData as $datalist) {
    //             ChecklistSubTypeDataName::updateOrInsert(
    //                 [
    //                     'id' => $datalist['subTypeDataNameId'] ?? null,
    //                     'checklist_sub_type_data_id' => $checklistSubTypeDataId
    //                 ],
    //                 [
    //                     'name' => $datalist['name'],
    //                     'description' => $datalist['description'] ?? null,
    //                     'updated_by' => Auth::id(),
    //                     'created_by' => $datalist['subTypeDataNameId'] ? null : Auth::id(),
    //                 ]
    //             );
    //         }
    //     }
    // }



    public function selectOne($id)
    {
        return $this->where('id', $id)->first();
    }

    public function uniqueCheck($subcategory_name, $checklist_sub_type_id, $checklist_type_id)
    {
        return $this->join('inspection_master_checklist_sub_type_data as parent', 'parent.id', '=', 'inspection_master_checklist_sub_type_data_name.checklist_sub_type_data_id')
            ->where('inspection_master_checklist_sub_type_data_name.name', $subcategory_name)
            ->where('parent.checklist_type_id', $checklist_type_id)
            ->where('parent.checklist_sub_type_id', $checklist_sub_type_id)
            ->get();
    }

    public function existUniqueCheck($subcategory_name, $checklist_sub_type_id, $checklist_type_id, $id)
    {
        return $this->join('inspection_master_checklist_sub_type_data as parent', 'parent.id', '=', 'inspection_master_checklist_sub_type_data_name.checklist_sub_type_data_id')
            ->where('inspection_master_checklist_sub_type_data_name.name', $subcategory_name)
            ->where('parent.checklist_type_id', $checklist_type_id)
            ->where('parent.checklist_sub_type_id', $checklist_sub_type_id)
            ->where('inspection_master_checklist_sub_type_data_name.id', '!=', $id)
            ->get();
    }



    protected static function booted()
    {
        // static::addGlobalScope(new TrashScope('inspection_master_checklist_sub_type_data_name'));

        static::created(function ($model) {

            $uniqueId = 'SUBCAT-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['subcategory_id' => $uniqueId]);
        });
    }
}
