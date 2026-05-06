<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmPSServiceTable extends Model
{
    protected $table = 'sys_crm_ps_service_table';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','doc_number','deal_id','date','cust_name','contact_person','mobile','location_of_work','amount','sales_person','deal_description','service_date_time','service_cust_name','service_contact_person','service_mobile','service_location_of_work','scope_of_work','service_date','service_time','engineer','attachment','status','created_by','created_at','updated_by','updated_at','company_id','is_auto','is_delete'
    ];
    
    public function deal_code(){    
        return $this->belongsTo('App\SysCrmDeals', 'deal_id', 'id');
    }    
    public function ownername(){
        return $this->belongsTo('App\SmStaff', 'sales_person', 'user_id');
    } 
       public function engineername(){
        return $this->belongsTo('App\SmStaff', 'engineer', 'user_id');
    } 
    public function custname(){    
        return $this->belongsTo('App\SysCustSuppl', 'cust_name', 'id');
    } 
    public function deal(){
        return $this->belongsTo('App\SysCrmDeals', 'deal_id', 'user_id');
    }
}