<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmDeals extends Model
{
    protected $table = 'sys_crm_deals';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'code',
        'date',
        'deal_name',
        'cust_id',
        'cust_name',
        'cust_no',
        'cust_email',
        'company_name',
        'deal_value',
        'deal_currency',
        'source',
        'source_o',
        'tags',
        'stage',
        'owner',
        'doc',
        'note',
        'estimated_close_date',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'company_id',
        'lead_id',
        'terms_and_condition',
        'deal_discount',
        'isproject',
        'quote_validity',
        'is_partial_invoice',
        'is_partial_delivery',
        'quote_id',
        'deal_percent',
        'deal_profit',
        'won_date',
        'invoice_date',
        'pfi_id',
        'sales_invoice_id',
        'dln_id',
        'clearance_id',
        'sales_return_id',
        'designation',
        'status',
        'address',
        'delivery_company',
        'delivery_name',
        'delivery_number',
        'delivery_email',
        'delivery_address',
        'delivery_address1',
        'delivery_address2',
        'delivery_area1',
        'delivery_building',
        'delivery_flat_office_no',
        'delivery_city',
        'delivery_zip_code',
        'delivery_country',
        'delivery_state',
        'is_professional_service',
        'deleted_at',
        'deal_discount_vat',
        'delivery_address_select'
    ];

    public function deal_code()
    {
        return $this->belongsTo('App\SysCrmDeals', 'id', 'id');
    }

    public function createdby()
    {
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function ownername()
    {
        return $this->belongsTo('App\SmStaff', 'owner', 'user_id');
    }
    public function companyname()
    {
        return $this->belongsTo('App\SysCompany', 'company_id', 'id');
    }
    public function customername()
    {
        return $this->belongsTo('App\SysCustSuppl', 'cust_id', 'id');
    }
    public function dealcurrency()
    {
        return $this->belongsTo('App\SysCurrency', 'deal_currency', 'id');
    }
    public function country()
    {
        return $this->belongsTo('App\SysCountries', 'delivery_country', 'id');
    }
    public function state()
    {
        return $this->belongsTo('App\SysStates', 'delivery_state', 'id');
    }
    public function track()
    {
        return $this->hasOne(SysCrmDealTrack::class, 'deal_id', 'id');
    }

    public function deliverycompany()
    {
        return $this->belongsTo('App\SysCustSuppl', 'delivery_company', 'id');
    }

    public function deliverycompany_address()
    {
        return $this->belongsTo('App\SysCustSupplAddress', 'delivery_address_select', 'id');
    }

}