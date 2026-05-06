<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmAmcTable extends Model
{
    protected $table = 'sys_crm_amc_table';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','deal_id','date','cust_name','contact_person','mobile_no','start_date','end_date','invoice','amount','sales_person','description','status','created_by','created_at','updated_by','updated_at','company_id','is_auto','is_delete','is_expired','comment'
    ];
    
    public function custname(){
        return $this->belongsTo('App\SysCustSuppl', 'cust_name', 'id');
    } 

    public function deal_code(){    
        return $this->belongsTo('App\SysCrmDeals', 'deal_id', 'id');
    }    
    public function salesperson(){
        return $this->belongsTo('App\SmStaff', 'sales_person', 'user_id');
    } 
    public function engineername(){
        return $this->belongsTo('App\SmStaff', 'engineer', 'user_id');
    } 
    public function deal(){
        return $this->belongsTo('App\SysCrmDeals', 'deal_id', 'user_id');
    }
}