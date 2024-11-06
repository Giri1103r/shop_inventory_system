<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use App\Scopes\TrashScope;

class NotificationLog extends Model
{
    use  HasFactory;


    protected $table = 'template_notification_log';
    protected $primaryKey = 'id';

    protected $fillable = [
        'notification_id',
        'user_id',
        'created_at',
        'updated_at'

    ];

    protected $attributes = [
        // 'status' => 1,
        // 'trash' => 'NO',
    ];



    public static function booted()
    {
        //static::addGlobalScope(new TrashScope('template_notification_log'));

    }
}
