<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysPurchaseOrderAttachment extends Model
{
    protected $table = 'sys_purchase_order_attachment';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','po_id','file_name','description','validtill','po_attach_file','status','created_by','updated_by','created_at','updated_at'
    ];

    // public function createdby(){
		//   return $this->belongsTo('App\SmStsffs', 'id', 'created_by');
    // }
    // public function updatedby(){
		//   return $this->belongsTo('App\SmStsffs', 'id', 'updated_by');
    // }
    
}