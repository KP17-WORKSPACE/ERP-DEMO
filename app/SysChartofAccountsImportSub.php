<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysChartofAccountsImportSub extends Model
{
    protected $table = 'sys_chartofaccounts_import_sub';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','group','subgroup','subgroup2','account_code','account_name','sub_account_code','sub_account_name','sub_account_date','debit_amount','credit_amount','status','created_by','created_at','company_id','department','yes_no'
    ];
}
