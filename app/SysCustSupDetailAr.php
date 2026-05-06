<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCustSupDetailAr extends Model
{
    protected $table = 'sys_cust_sup_detail_ar';
    protected $primaryKey = 'id';

    protected $fillable = [
      'id','account_id','company_name_ar','contact_person_ar','address_ar','status','created_by','updated_by','created_at','updated_at'
    ];

    public function createdby(){
		   return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function accountname(){
		  return $this->belongsTo('App\SysChartofAccounts', 'account_id', 'id');
    }
}