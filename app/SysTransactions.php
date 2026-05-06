<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysTransactions extends Model
{
    protected $table = 'sys_transactions';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'transaction_type','transaction_id','account_id','amount','remarks','status','created_by','updated_by','created_at','updated_at'
    ];

    // public function accountid(){
	//     return $this->belongsTo('App\SysCustSuppl', 'account_id', 'id');
    // }
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function updatedby(){
        return $this->belongsTo('App\SmStaff', 'updated_by', 'user_id');
    }
    
}
