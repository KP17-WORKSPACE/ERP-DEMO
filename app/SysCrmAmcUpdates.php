<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmAmcUpdates extends Model
{
    protected $table = 'sys_crm_amc_updates';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'amc_id','comments','commentsdoc','status','created_by','updated_by','created_at','updated_at','support_type','support_date','support_time_from','support_time_to','support_person_id'
    ];
    
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
}