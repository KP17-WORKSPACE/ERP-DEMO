<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCompanyBanking extends Model
{
    //
    protected $table = 'sys_company_banking';

    protected $fillable = [
        'company_id',
        'account_name',
        'bank_name',
        'branch_name',
        'account_number',
        'iban_number',
        'swift_code',
        'finance_code',
        'currency',
        'bank_letter',
    ];

    public function company()
    {
        return $this->belongsTo(SysCompany::class, 'company_id');
    }
    public function currencyDetails()
    {
        return $this->belongsTo(SysCurrency::class, 'currency', 'id');
    }
    public function AccountsBank(){
        // A bank has ONE chart account record where sys_chartofaccounts.company_bank_id = sys_company_banking.id
        return $this->hasOne(SysChartofAccounts::class,'company_bank_id','id');
    }
}
