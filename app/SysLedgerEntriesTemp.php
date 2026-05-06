<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysLedgerEntriesTemp extends Model
{
    protected $table = 'sys_ledger_entries_temp';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','transaction_id','transaction_type','account_id','entry_date','acc_type','dr_amount','cr_amount','status','created_by','updated_by','created_at','updated_at','company_id','process_id','process_status'
    ];

    // public function accounttype(){
	//     return $this->belongsTo('App\SysAccountType', 'account_type', 'id');
    // }
    // public function groupname(){
	//     return $this->belongsTo('App\SysAccountGroup', 'group', 'id');
	// }
    // public function subgroupname(){
	//     return $this->belongsTo('App\SysAccountGroupSub', 'subgroup', 'id');
	// }
}
