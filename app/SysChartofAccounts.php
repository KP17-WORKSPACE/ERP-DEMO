<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysChartofAccounts extends Model
{
    protected $table = 'sys_chartofaccounts';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','group','subgroup','subgroup2','account_code','account_type','account_name','billwise','debitlimit','status','created_by','updated_by','created_at','updated_at','company_id','company_access','main_account_id','beneficiary_name','bank_name','acc_no','iban','swift_code','routing_code','branch','stl_limit','stl','internal','branch_location','stl_dept','created_by_company','company_bank_id','grn_select','department_id','yes_no'
    ];

    public function accounttype(){
	    return $this->belongsTo('App\SysAccountType', 'account_type', 'id');
    }
    public function groupname(){
	    return $this->belongsTo('App\SysAccountGroup', 'group', 'id');
	}
    public function subgroupname(){
	    return $this->belongsTo('App\SysAccountGroupSub', 'subgroup', 'id');
	}
    public function subgroup2name(){
	    return $this->belongsTo('App\SysAccountGroupSub2', 'subgroup2', 'id');
	}
    public function mainaccount(){
	    return $this->belongsTo('App\SysChartofAccounts', 'main_account_id', 'id');
	}
    public function cust_suppl(){
	    return $this->belongsTo('App\SysCustSuppl', 'account_code', 'code');
	}

    public function companybank(){
        // Chart account belongs to a company bank via company_bank_id -> sys_company_banking.id
        return $this->belongsTo(SysCompanyBanking::class,'company_bank_id','id');
    }
    
}
