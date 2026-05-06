<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmDealTrackApprovalPurcheaseGrnList extends Model
{
    protected $table = 'sys_crm_deal_track_approval_purchease_grn_list';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'grn_id','partnumber','qty','supplier','expected_date','status'
    ];
    
}