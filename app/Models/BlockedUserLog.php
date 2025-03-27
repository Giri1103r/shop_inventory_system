<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

use App\Models\Master\Location;
use App\Scopes\TrashScope;

use DB;
use Exception;

class BlockedUserLog extends Model
{
    use  HasFactory;


    protected $table = 'template_blocked_userlog';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_login_id',
        'session_id',
        'user_identifier',
        'request_uri',
        'timestamp',
        'client_ip',
        'client_user_agent',
        'referer_page',
        'created_at',
        'updated_at'
    ];


    protected static function booted()
    {
        //static::addGlobalScope(new TrashScope('template_user_tracking'));
    }
}
