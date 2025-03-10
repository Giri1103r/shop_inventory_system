<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkerCompanyDetails extends Model
{
    use  HasFactory;


    protected $table = 'worker_company_details';
    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_type',
        'company_name',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    public function getofficeid(){
        return $this->get();
    }
}
