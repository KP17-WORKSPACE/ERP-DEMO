<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysSalesInvoiceAttachment extends Model
{
    protected $table = 'sys_sales_invoice_attachment';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','si_id','file_name','description','validtill','si_attach_file','status','created_by','updated_by','created_at','updated_at'
    ];

    // public function createdby(){
		//   return $this->belongsTo('App\SmStsffs', 'id', 'created_by');
    // }
    // public function updatedby(){
		//   return $this->belongsTo('App\SmStsffs', 'id', 'updated_by');
    // }
    
}