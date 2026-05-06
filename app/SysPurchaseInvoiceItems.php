<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysPurchaseInvoiceItems extends Model
{
    protected $table = 'sys_purchase_invoice_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','ref_po_id','grn_id','pi_id','part_number','tax','qty','unitprice','value','discount','fright','customcharges','taxableamount','vatamount','status','return_qty','sort_id','description'
    ];
    public function productname(){
        return $this->belongsTo('App\SmItem', 'part_number', 'id');
    }

    // public function createdby(){
		//   return $this->belongsTo('App\SmStsffs', 'id', 'created_by');
    // }
    // public function updatedby(){
		//   return $this->belongsTo('App\SmStsffs', 'id', 'updated_by');
    // }    
}