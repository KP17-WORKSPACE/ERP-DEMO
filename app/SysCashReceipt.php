<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCashReceipt extends Model
{
    protected $table = 'sys_cashreceipt';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'doc_number','doc_date','receipt_mode','narration','status','created_by','updated_by','created_at','updated_at','company_id'
    ];

    public function account(){
        return $this->belongsTo('App\SysChartofAccounts', 'receipt_mode', 'id');
    }

    public function receiptmode(){
	     return $this->belongsTo('App\SysReceiptMode', 'receipt_mode', 'id');
     }
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function updatedby(){
        return $this->belongsTo('App\SmStaff', 'updated_by', 'user_id');
    }
    
}
