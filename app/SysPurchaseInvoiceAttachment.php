<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysPurchaseInvoiceAttachment extends Model
{
    protected $table = 'sys_purchase_invoice_attachment';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','pi_id','file_name','description','validtill','pi_attach_file','status','created_by','updated_by','created_at','updated_at'
    ];

    // public function createdby(){
		//   return $this->belongsTo('App\SmStsffs', 'id', 'created_by');
    // }
    // public function updatedby(){
		//   return $this->belongsTo('App\SmStsffs', 'id', 'updated_by');
    // }
    
}