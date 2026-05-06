<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysSalesInvoiceItems extends Model
{
    protected $table = 'sys_sales_invoice_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','si_id','part_number','description','tax','qty','unitprice','value','discount','customcharges','taxableamount','vatamount','status','delivery_status','serialno','sort_id','cost'
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