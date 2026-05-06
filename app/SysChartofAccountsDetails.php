<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysChartofAccountsDetails extends Model
{
    protected $table = 'sys_chartofaccounts_details';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','account_id','amount_dr','amount_cr','amount_date','status','created_by','updated_by','created_at','updated_at'
    ];

    public function accountid(){
	    return $this->belongsTo('App\SysChartofAccounts', 'account_id', 'id');
    }
    
}
