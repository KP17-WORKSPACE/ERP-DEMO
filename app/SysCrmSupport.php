<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmSupport extends Model
{
    protected $table = 'sys_crm_support';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','doc_number','deal_id','customer_id','contact_person','mobile','sales_person_id','support_person_id','support_date','time_from','time_to','site_name','remarks','file','status','created_by','updated_by','created_at','updated_at','open_remarks','close_remarks','close_by','close_at','closingdoc','company_id','is_delete'
    ];
    
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function customer(){
        return $this->belongsTo('App\SysCustSuppl', 'customer_id', 'id');
    }
    public function dealid(){
        return $this->belongsTo('App\SysCrmDeals', 'deal_id', 'id');
    }
    public function salesperson(){
        return $this->belongsTo('App\SmStaff', 'sales_person_id', 'user_id');
    }
    public function supportperson(){
        return $this->belongsTo('App\SmStaff', 'support_person_id', 'user_id');
    }
    public function closeby(){
        return $this->belongsTo('App\SmStaff', 'close_by', 'user_id');
    }
}