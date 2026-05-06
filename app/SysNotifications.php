<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysNotifications extends Model
{
    protected $table = 'sys_notifications';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'user_id','date','title','message','received_id','link','is_read','active_status','created_by','updated_by','created_at','updated_at'
    ];
}