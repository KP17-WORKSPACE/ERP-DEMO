<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysBankReceiptList extends Model
{
    protected $table = 'sys_bankreceipt_list';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'br_id','account_id','amount','remarks','status','created_by','updated_by','created_at','updated_at','company_id'
    ];

    public function accountid(){
	    return $this->belongsTo('App\SysCustSuppl', 'account_id', 'id');
    }
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function updatedby(){
        return $this->belongsTo('App\SmStaff', 'updated_by', 'user_id');
    }
    
}
