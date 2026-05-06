<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class SysCrmQuotation extends Model
{
   

    protected $table = 'sys_crm_quotation';

    protected $fillable = [
        'code',
        'date',
        'deal_id',
        'deal_name',
        'cust_id',
        'estimated_close_date',
        'owner',
        'company_id',
        'quote_validity',
        'payment_terms',
        'delivery_time',
        'currency',
        'terms_and_conditions',
    ];

    protected $casts = [
        'date' => 'date',
        'estimated_close_date' => 'date',
    ];

    /* Optional relationships */

    public function customer()
    {
        return $this->belongsTo(SysCustSuppl::class, 'cust_id');
    }

    public function company()
    {
        return $this->belongsTo(SysCompany::class, 'company_id');
    }

    public function deal()
    {
        return $this->belongsTo(SysCrmDeals::class, 'deal_id');
    }
}
