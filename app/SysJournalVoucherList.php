<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysJournalVoucherList extends Model
{
    protected $table = 'sys_journalvoucher_list';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'jv_id','account_id','amount_dr','amount_cr','remarks','status','created_by','updated_by','created_at','updated_at'
    ];

    public function accountid(){
	    return $this->belongsTo('App\SysChartofAccounts', 'account_id', 'id');
    }
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function updatedby(){
        return $this->belongsTo('App\SmStaff', 'updated_by', 'user_id');
    }
    
}
