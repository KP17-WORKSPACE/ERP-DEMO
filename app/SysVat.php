<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysVat extends Model
{
    protected $table = 'sys_vat';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'vat_country','vat_state','vat_type','vat_percentage','vat_from','status','created_by','updated_by','created_at','updated_at','company_id'
    ];

    public function country(){
	    return $this->belongsTo('App\SysCountries', 'vat_country', 'id');
	}
    public function state(){
	    return $this->belongsTo('App\SysStates', 'vat_state', 'id');
	}
    public function vattype(){
	    return $this->belongsTo('App\SysVatType', 'vat_type', 'id');
	}
}