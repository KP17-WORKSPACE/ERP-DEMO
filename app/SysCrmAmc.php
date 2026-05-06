<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmAmc extends Model
{
    protected $table = 'sys_crm_amc';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','deal_id','from_date','to_date','remarks','file','status','created_by','updated_by','created_at','updated_at','cust_id','cust_name','cust_no','cust_email','address','country','tags','owner','company_id','amc_value'
    ];
    
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function ownername(){
        return $this->belongsTo('App\SmStaff', 'owner', 'user_id');
    }
    public function customername(){
        return $this->belongsTo('App\SysCustSuppl', 'cust_id', 'id');
    }
}