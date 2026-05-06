<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysDealItemInvoiced extends Model
{
    protected $table = 'sys_deal_item_invoiced';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','deal_id','invoice_id','pid','qty','status','created_by','created_at'
    ];

    // public function createdby(){
		//   return $this->belongsTo('App\SmStsffs', 'id', 'created_by');
    // }
    // public function updatedby(){
		//   return $this->belongsTo('App\SmStsffs', 'id', 'updated_by');
    // }
    
}