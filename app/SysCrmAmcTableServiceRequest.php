<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmAmcTableServiceRequest extends Model
{
    protected $table = 'sys_crm_amc_table_service_request';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','amc_id','location_of_work','scope_of_work','service_date','service_time','source','service_engineer','attachment','status','created_by','created_at','updated_by','updated_at','company_id'
    ];
    
    public function amcdetail(){
        return $this->belongsTo('App\SysCrmAmcTable', 'amc_id', 'id');
    }
    public function serviceengineer(){
        return $this->belongsTo('App\SmStaff', 'service_engineer', 'user_id');
    }
}