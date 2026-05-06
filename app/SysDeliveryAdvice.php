<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysDeliveryAdvice extends Model
{
    protected $table = 'sys_delivery_advice';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'doc_number','doc_date','customer_id','narration', 'salesman','contact_person','mobile_no','landline_no','da_si_numbers','invoice_date','vehicle_no','driver','do_no','do_date','payment_terms','delivery_date','delivery_time','delivery_address','invoice_amount','remarks','status','created_by','updated_by','created_at','updated_at'
    ];

    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function updatedby(){
        return $this->belongsTo('App\SmStaff', 'updated_by', 'user_id');
    }    
}