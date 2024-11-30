<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

use App\Models\Master\Location;
use App\Scopes\TrashScope;

use DB;
use Exception;

class UserLog extends Model
{
    use  HasFactory;


    protected $table = 'template_user_tracking';
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


    public function list()
    {
        $request = request();
        $search = '';

        $query = $this->select('template_user_tracking.*', 'users.name')
            ->leftJoin('users', 'template_user_tracking.user_login_id', '=', 'users.id');

        if ($request->user != null || $request->user != '') {
            $user = $request->user;
            $query->orWhere('users.id', 'LIKE', '%' . decryptId($user) . '%');
        }



        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query->orWhere('session_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('client_ip', 'LIKE', '%' . $search . '%')
                    ->orWhere('request_uri', 'LIKE', '%' . $search . '%')
                    ->orWhere('users.name', 'LIKE', '%' . $search . '%');
            });
        }


        $data_count = $query;
        $total_records = $data_count->count();

        $query->orderBy('id', 'DESC');

        if ($request->length != -1) {
            $query->offset($request->start)->limit($request->length);
        }

        $data = $query->get();

        $datas = [
            'data' => $data,
            'total_records' => $total_records
        ];

        return $datas;
    }


    public function exportdata()
    {

        try {
            $request = request();
            $search = '';


            $query = $this->select('template_user_tracking.*', 'users.name')
                ->leftJoin('users', 'template_user_tracking.user_login_id', '=', 'users.id');

            if ($request->search != null || $request->search != '') {
                $search = $request->search;

                $query->where(function ($query) use ($search) {
                    $query->orWhere('session_id', 'LIKE', '%' . $search . '%')
                        ->orWhere('client_ip', 'LIKE', '%' . $search . '%')
                        ->orWhere('request_uri', 'LIKE', '%' . $search . '%');
                });
            }
            if ($request->user != null || $request->user != '') {
                $user = $request->user;
                $query->Where('users.id', decryptId($user));
            }

            $query = $query->orderBy('created_at', 'Desc');


            return  $query->get();
        } catch (Exception $ex) {
           report($ex);
           return [];
        }

    }



    protected static function booted()
    {
        //static::addGlobalScope(new TrashScope('template_user_tracking'));
    }
}
