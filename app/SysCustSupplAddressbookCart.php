<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCustSupplAddressbookCart extends Model
{
    protected $table = 'sys_cust_suppl_addressbook_cart';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','cart_id','cust_suppl_id','address','address2','city','country','state','zip_code','set_default','status','created_by','created_at','updated_by','updated_at','company_id','is_shipping','area',
    'building_name',
    'flat_office_no'
    ];
    
    
    public function countryname(){
        return $this->belongsTo('App\SysCountries', 'country', 'id');
    }
  public function statename(){
        return $this->belongsTo('App\SysStates', 'state', 'id');
    }
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function customername(){
        return $this->belongsTo('App\SysCustSuppl', 'cust_suppl_id', 'id');
    }
}