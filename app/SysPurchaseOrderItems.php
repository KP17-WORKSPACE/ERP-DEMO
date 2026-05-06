<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysPurchaseOrderItems extends Model
{
    protected $table = 'sys_purchase_order_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','po_id','part_number','description','tax','qty','issue_qty','grn_qty','unitprice','value','discount','fright','customcharges','taxableamount','vatamount','status','serialno','sort_id'
    ];

    public function productname(){
        return $this->belongsTo('App\SmItem', 'part_number', 'id');
    }

    public function productdet(){
		  return $this->belongsTo('App\SmItem', 'id', 'part_number');
    }

    public function createdby(){
		  return $this->belongsTo('App\SmStsffs', 'id', 'created_by');
    }
    public function updatedby(){
		  return $this->belongsTo('App\SmStsffs', 'id', 'updated_by');
    }
    
}