<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysChartofAccountsImport extends Model
{
    protected $table = 'sys_chartofaccounts_import';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','group','subgroup','subgroup2','account_code','account_name','debit_amount','credit_amount','status','created_by','created_at','company_id','date','yes_no','department'
    ];
}
