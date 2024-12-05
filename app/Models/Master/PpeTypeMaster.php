<?php

namespace App\Models\Master;

use App\Scopes\TrashScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class PpeTypeMaster extends Model
{
    use  HasFactory;


    protected $table = 'ppe_master_ppetypemaster';
    protected $primaryKey = 'id';

    protected $fillable = [
        'item_code',
        'ppe_name',
        'ppe_category',
        'ppe_standard',
        'ppe_type',
        'ppe_image',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'

    ];

    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select('ppe_master_ppetypemaster.*','masters_ppetype.ppe_type')
        ->join('masters_ppetype','ppe_master_ppetypemaster.ppe_type', '=', 'masters_ppetype.id')
        ->where('masters_ppetype.trash','NO');

        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('ppe_name', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('item_code') && $request->item_code) {
            $query = $query->where('item_code', 'LIKE', '%' . $request->item_code . '%');
        }
        if ($request->has('ppe_name') && $request->ppe_name) {
            $query = $query->where('ppe_name', 'LIKE', '%' . $request->ppe_name . '%');
        }
        if ($request->has('ppe_status') && $request->ppe_status) {

            $query = $query->where('status', decryptId($request->ppe_status));
        }

        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('created_at', '<=', $endDate);
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

        $destinationPath = 'uploads/ppe_files';

        if (!File::exists(public_path($destinationPath))) {
            File::makeDirectory(public_path($destinationPath), 0777, true, true);
        }

        $ppe_file_path = null;

        if ($request->hasFile('ppe_file')) {
            $ppe_file = $request->file('ppe_file');

            $ppe_file_name = time() . '_' . $ppe_file->getClientOriginalName();
            $ppe_file->move(public_path($destinationPath), $ppe_file_name);

            $ppe_file_path = $destinationPath . '/' . $ppe_file_name;
        }

        $insert_array = array(
            'item_code' => $request->item_code,
            'ppe_name' => $request->ppe_name,
            'ppe_type' => $request->ppe_type,
            'ppe_category' => $request->protection_category,
            'ppe_standard' => $request->ppe_standard,
            'ppe_image' => $ppe_file_path,
            'quantity' => $request->quantity,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }
    public function updates($id)
    {
        $request = request();

        $destinationPath = 'uploads/ppe_files';
        $ppe_file_path = $request->input('existing_pre_image');

        if ($request->hasFile('ppe_file')) {
            $ppe_file = $request->file('ppe_file');
            $ppe_file_name = time() . '_' . $ppe_file->getClientOriginalName();

            while (File::exists(public_path($destinationPath . '/' . $ppe_file_name))) {
                $ppe_file_name = time() . '_' . uniqid() . '_' . $ppe_file->getClientOriginalName();
            }

            $ppe_file->move(public_path($destinationPath), $ppe_file_name);
            $ppe_file_path = $destinationPath . '/' . $ppe_file_name;

            if ($request->input('existing_pre_image') && File::exists(public_path($request->input('existing_pre_image')))) {
                File::delete(public_path($request->input('existing_pre_image')));
            }
        }


        $update_array = array(
            'item_code' => $request->item_code,
            'ppe_name' => $request->ppe_name,
            'ppe_type' => $request->ppe_type,
            'ppe_category' => $request->protection_category,
            'ppe_standard' => $request->ppe_standard,
            'ppe_image' => $ppe_file_path,
            'quantity' => $request->quantity,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id()
        );
        return $this->where('id', $id)->update($update_array);
    }
    public function selectOne($id)
    {

        $data = $this->select('ppe_master_ppetypemaster.*')
            ->where('ppe_master_ppetypemaster.id', $id)
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
    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('ppe_master_ppetypemaster.*');
        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhereRaw('ppe_name LIKE "%' . $search . '%"');
            });
        }

        if ($request->has('item_code') && $request->item_code) {
            $query = $query->where('item_code', 'LIKE', '%' . $request->item_code . '%');
        }
        if ($request->has('ppe_name') && $request->ppe_name) {
            $query = $query->where('ppe_name', 'LIKE', '%' . $request->ppe_name . '%');
        }
        if ($request->has('ppe_status') && $request->ppe_status) {

            $query = $query->where('status', decryptId($request->ppe_status));
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('created_at', '<=', $endDate);
        }

        return  $query->orderBy('id', 'DESC')->get();
    }

    public function ajaxlist($PPEtypeId)
    {

        return response()->json(
            $this->where('ppe_type', $PPEtypeId)
                 ->select('id', 'ppe_name')
                 ->get()
        )->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
         ->header('Pragma', 'no-cache')
         ->header('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
    }


    public function ImageList($ppeNameId)
    {
        $ppeImage = $this->where('id', $ppeNameId)->pluck('ppe_image')->first();
        if ($ppeImage) {
            return response()->json(['image_url' => asset('uploads/ppe_type_files/' . $ppeImage)]);
        } else {
            return response()->json(['image_url' => null]);
        }
    }


    public function getppetypemaster()
    {
        return PpeTypeMaster::all();
    }
    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ppe_master_ppetypemaster'));
    }
}
