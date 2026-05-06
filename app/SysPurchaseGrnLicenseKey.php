<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysPurchaseGrnLicenseKey extends Model
{
    protected $table = 'sys_purchase_grn_license_key';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','cart_id','grn_id','item_id','license_key','exp_date','license_qty','status','created_by','created_at','updated_by','updated_at','company_id','dn_id','opening_stock_id','type','sales_return_id','purchase_return_id'
    ];
    
    // public function grn(){    
    //     return $this->belongsTo('App\SysPurchaseGRN', 'grn_id', 'id');
    // }
    // public function ops(){    
    //     return $this->belongsTo('App\SysItemOpeningStock', 'opening_stock_id', 'id');
    // }
    // public function dn(){    
    //     return $this->belongsTo('App\SysDeliveryNote', 'dn_id', 'id');
    // }
    // public function sr(){    
    //     return $this->belongsTo('App\SysSalesReturn', 'sales_return_id', 'id');
    // }
    // public function pr(){    
    //     return $this->belongsTo('App\SysPurchaseReturn', 'purchase_return_id', 'id');
    // }
}