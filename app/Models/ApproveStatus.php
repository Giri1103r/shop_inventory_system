<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApproveStatus extends Model
{
    protected $table = 'status';
    protected $primaryKey = 'id';

    protected $fillable = [
       'approve_status'
    ];

    public function status(){
        return ApproveStatus::all();
    }
}
