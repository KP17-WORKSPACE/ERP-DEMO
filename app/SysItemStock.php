<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysItemStock extends Model
{
    protected $table = 'sys_item_stock';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','doc_number','doc_date','refno','ops_id','grn_id','pri_id','dln_id','slr_id','stock_in_id','stock_out_id','deal_id','groupname','account_id','partno','description','slno','qty_in','price_in','qty_out','price_out','bal_qty','remarks','status','created_by','updated_by','created_at','updated_at','company_id','currency_id','sales_person','item_id'
    ];
    
    // public function createdby(){
		//    return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    //  }
    
    public function productdet(){
		  return $this->belongsTo('App\SmItem', 'partno', 'id');
    }

    public function accountname(){
		  return $this->belongsTo('App\SysChartofAccounts', 'account_id', 'id');
    }
    
    public function openingStock()
  {
      return $this->belongsTo(SysItemOpeningStock::class, 'ops_id', 'id');
  }

    public function deal_code(){    
      return $this->belongsTo('App\SysCrmDeals', 'deal_id', 'id');
    }

    public function salesperson(){
        return $this->belongsTo('App\SmStaff', 'sales_person', 'user_id');
    }

    // public function suppliertype(){
		//   return $this->belongsTo('App\SysSupplierType', 'supplier_type', 'id');
    // }
    // public function paymentterms(){
		//   return $this->belongsTo('App\SysPaymentTerms', 'payment_terms', 'id');
    // }
    
    // public function updatedby(){
		//   return $this->belongsTo('App\SmStsffs', 'id', 'updated_by');
    // }    
}