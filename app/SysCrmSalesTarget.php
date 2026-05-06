<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmSalesTarget extends Model
{
    protected $table = 'sys_crm_sales_target';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'user_id','target','target_month','status','created_by','updated_by','created_at','updated_at','company_id','company_access','type','revenue_target_weekly','revenue_target_monthly','revenue_target_quaterly','revenue_target_yearly','gp_target_weekly','gp_target_monthly','gp_target_quaterly','gp_target_yearly','target_month_from','combind_user_id'
    ];
    
    public function userid(){
        return $this->belongsTo('App\SmStaff', 'user_id', 'user_id');
    }
}