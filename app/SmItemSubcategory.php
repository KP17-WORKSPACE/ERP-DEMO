<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmItemSubcategory extends Model
{
    protected $table = 'sm_item_subcategories';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','category_id ','sub_category_name','created_at','updated_at','created_by','updated_by','company_id'
    ];

    public function createdby(){
		  return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function updatedby(){
		  return $this->belongsTo('App\SmStaff', 'updated_by', 'user_id');
	}
    public function companyid(){
		  return $this->belongsTo('App\SysCompany', 'company_id', 'id');
	}
}