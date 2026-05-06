<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCustSuppl extends Model
{
  protected $table = 'sys_cust_suppl';
  protected $primaryKey = 'id';

  protected $fillable = [
    'id',
    'group',
    'catid',
    'account_type',
    'customer_salutation',
    'first_name',
    'designation',
    'last_name',
    'name',
    'customer_name_display',
    'code',
    'address',
    'address2',
    'contcat_person',
    'contcat_number',
    'mobile',
    'email',
    'sales_person',
    'vat_type',
    'purchase_type',
    'customer_type',
    'sale_type',
    'supplier_type',
    'vat_country',
    'vat_state',
    'vat_percentage',
    'vat_number',
    'credit_limit',
    'credit_days',
    'payment_terms',
    'payment_terms_txt',
    'customer_documents',
    'status',
    'created_by',
    'updated_by',
    'created_at',
    'updated_at',
    'type',
    'company_id',
    'vat_is_fixed',
    'city',
    'zip_code',
    'transaction_type',
    'company_access',
    'is_file',
    'vendor_name',
    'beneficiary_name',
    'iban',
    'swift_code',
    'city_country',
    'stl',
    'stl_bank',
    'stl_limit',
    'stl_per_trn_limit',
    'stl_opb',
    'internal',
    'delete_reason',
    'country_telephone',
    'area',
    'building_name',
    'flat_office_no',
    'website',
    'maps_location',
    'place_id',
    'created_by_company',
    'taken_from_stock',
    'stock_order',
    'customer_id',
    'supplier_id',
    'company_ship_to_id',
    'grn_select'
  ];

  public function salesperson()
  {
    return $this->belongsTo('App\SmStaff', 'sales_person', 'user_id');
  }
  public function paymentterms()
  {
    return $this->belongsTo('App\SysPaymentTerms', 'payment_terms', 'id');
  }
  public function vatcountry()
  {
    return $this->belongsTo('App\SysCountries', 'vat_country', 'id');
  }
  public function vatstate()
  {
    return $this->belongsTo('App\SysStates', 'vat_state', 'id');
  }
  public function vattype()
  {
    return $this->belongsTo('App\SysVatType', 'vat_type', 'id');
  }

  public function purchasetype()
  {
    return $this->belongsTo('App\SysPurchaseType', 'purchase_type', 'id');
  }
  public function customertype()
  {
    return $this->belongsTo('App\SysCustomerType', 'customer_type', 'id');
  }
  public function saletype()
  {
    return $this->belongsTo('App\SysSaleType', 'sale_type', 'id');
  }
  public function suppliertype()
  {
    return $this->belongsTo('App\SysSupplierType', 'supplier_type', 'id');
  }

  public function assignedUsers()
  {
    return $this->belongsToMany(
      \App\User::class,              // related model
      'sys_cust_suppl_assign',       // pivot table name
      'cust_supp_id',                // foreign key on pivot referencing this model
      'user_id'                      // foreign key on pivot referencing users
    )
      ->select('users.id', 'users.full_name')  // only select needed fields
      ->distinct();
  }

  public function getAssignedUserNamesAttribute()
  {
    return $this->assignedUsers->pluck('full_name')->implode(', ');
  }

  public function createdBy()
  {
    return $this->belongsTo('App\User', 'created_by', 'id');
  }

  public function updatedBy()
  {
    return $this->belongsTo('App\User', 'updated_by', 'id');
  }

  public function SysCustSupplAddressbook(){
    return $this->belongsTo('App\SysCustSupplAddressbook', 'cust_suppl_id', 'id');
  }

  public function addresses()
{
    return $this->hasMany('App\SysCustSupplAddressbook', 'cust_suppl_id', 'id');
}

  
}
