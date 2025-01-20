<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Bloodgroup extends Model
{
    use  HasFactory;


    protected $table = 'masters_blood_group';
    protected $primaryKey = 'id';

    protected $fillable = [
         'blood_group_name',
        'status',
        'trash',
        'created_by',
        'created_at',


    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO',
    ];

    public function getBloodgroup(){
        return $this->where('status',1)->where('trash','no')->get();
    }
}
