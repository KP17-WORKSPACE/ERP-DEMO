<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmAmcTableServiceComments extends Model
{
    protected $table = 'sys_crm_amc_table_service_comments';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','amc_id','comments','engineer_id','status','created_by','created_at','updated_by','updated_at','work_id','work_date','work_time_from','work_time_to'
    ];
    
    public function engineerid(){
        return $this->belongsTo('App\SmStaff', 'engineer_id', 'user_id');
    }
}