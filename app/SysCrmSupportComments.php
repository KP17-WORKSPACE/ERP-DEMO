<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmSupportComments extends Model
{
    protected $table = 'sys_crm_support_comments';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','support_id','comments','engineer_id','status','created_by','created_at','updated_by','updated_at','work_date','work_time_from','work_time_to'
    ];
    
    public function engineerid(){
        return $this->belongsTo('App\SmStaff', 'engineer_id', 'user_id');
    }
}