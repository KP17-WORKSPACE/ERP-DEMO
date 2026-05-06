<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmReimbursement extends Model
{
    protected $table = 'sys_crm_reimbursement';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','date','deal_id','site_name','scope_of_work','invoice_no','amount','remarks','head_count_name','attachmant','created_by','created_at','updated_by','updated_at','dept_head_status','dept_head_by','dept_head_remarks','acco_head_status','acco_head_by','acco_head_remarks','accounts_status','accounts_by','accounts_remarks','company_id','status'
    ];
    
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function deal_code(){    
        return $this->belongsTo('App\SysCrmDeals', 'deal_id', 'id');
    }
    public function deptheadby(){
        return $this->belongsTo('App\SmStaff', 'dept_head_by', 'user_id');
    }
    public function accoheadby(){
        return $this->belongsTo('App\SmStaff', 'acco_head_by', 'user_id');
    }
    public function accountsby(){
        return $this->belongsTo('App\SmStaff', 'accounts_by', 'user_id');
    }
}