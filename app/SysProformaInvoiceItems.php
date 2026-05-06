<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysProformaInvoiceItems extends Model
{
    protected $table = 'sys_proforma_invoice_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','profo_id','deal_id','quote_id','part_number','tax','qty','unitprice','value','discount','customcharges','taxableamount','vatamount','status'
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