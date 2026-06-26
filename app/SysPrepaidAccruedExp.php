<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysPrepaidAccruedExp extends Model
{
    protected $table = 'sys_prepaid_accrued_exp';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','account_id','amount','from_date','to_date','no_of_days','per_day_amount','status','created_by','updated_by','created_at','updated_at','cart_id','jv_id','company_id'
    ];
    public function account(){
	    return $this->belongsTo('App\SysChartofAccounts', 'account_id', 'id');
	}
}
