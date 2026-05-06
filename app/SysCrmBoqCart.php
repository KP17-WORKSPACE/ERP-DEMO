<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmBoqCart extends Model
{
    protected $table = 'boq_cart';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','user_id','cart_id','company_id','nooflocation','connectivity','telephonetype','nolines','status','created_at','updated_at',
        'currency_id','customer_type','deal_id','payment_terms','terms_and_condition','quotevalidity','delivery_time','deliverydate','company'
    ];
}