<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

use App\Scopes\TrashScope;

class FcmToken extends Model
{
    use  HasFactory;


    protected $table = 'users_fcm_token';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'token',
        'device_type',
        'status',
        'trash',
        'created_at',
        'updated_at'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('users_fcm_token'));
    }
}
