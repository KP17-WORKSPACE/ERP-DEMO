<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmDealTrackApprovalPurchease extends Model
{
    protected $table = 'sys_crm_deal_track_approval_purchease';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'deal_track_id','deal_id','purchease_quote','three_quote_request','ref_supplier_id','validation','other','remarks','status','created_by','updated_by','created_at','updated_at','fileone','filetwo','filethree','lpo_no','delivery_date','part_no','supplier_name','cost_of_purchase','cost_of_purchase_currency','partial_delivery_note','created_date'
    ];
        
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function updatedby(){
        return $this->belongsTo('App\SmStaff', 'updated_by', 'user_id');
    }

 public function getSupplierListAttribute()
{
    if (empty($this->ref_supplier_id)) {
        return collect([]);
    }

    $ids = explode(',', $this->ref_supplier_id);

    return \App\SysChartofAccounts::whereIn('id', $ids)->get();
}



}