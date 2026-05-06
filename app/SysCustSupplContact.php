<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCustSupplContact extends Model
{
    protected $table = 'sys_cust_suppl_contact';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','cust_suppl_id','salutation','first_name','last_name','email_address','work_phone','mobile','designation','department','set_default','status','created_by','created_at','updated_by','updated_at','company_id'
    ];
    
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function customername(){
        return $this->belongsTo('App\SysCustSuppl', 'cust_suppl_id', 'id');
    }
}