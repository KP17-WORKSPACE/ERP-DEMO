<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyWarehouse extends Model
{
    protected $table = 'company_warehouses';

    protected $fillable = [
        'company_id',
        'warehouse_code',
        'warehouse_name',
        'warehouse_country',
        'warehouse_state',
        'warehouse_city',
        'warehouse_area',
        'warehouse_building_name',
        'warehouse_flat_office_no',
        'contact_salutation',
        'contact_first_name',
        'contact_last_name',
        'contact_mobile',
        'contact_email',
        'contact_designation',
        'fire_safety_compliance_status',
        'fire_noc_certificate_number',
        'safety_equipment_available',
        'fire_noc_expiry_date',
        'last_safety_inspection_date',
        'contact_documents'
    ];

    protected $dates = [
        'fire_noc_expiry_date',
        'last_safety_inspection_date',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'fire_noc_expiry_date' => 'date',
        'last_safety_inspection_date' => 'date',
        'contact_documents' => 'array'
    ];

    // Relationship with company
    public function company()
    {
        return $this->belongsTo(SysCompany::class, 'company_id');
    }

    // Relationship with country
    public function country()
    {
        return $this->belongsTo('App\SysCountries', 'warehouse_country', 'id');
    }

    // Relationship with state
    public function state()
    {
        return $this->belongsTo('App\SysStates', 'warehouse_state', 'id');
    }

    // Helper method to get full contact name
    public function getContactFullNameAttribute()
    {
        $salutation = $this->contact_salutation ? $this->contact_salutation . ' ' : '';
        return $salutation . trim($this->contact_first_name . ' ' . $this->contact_last_name);
    }

    // Helper method to get full address
    public function getFullAddressAttribute()
    {
        $address = [];
        
        if ($this->warehouse_flat_office_no) {
            $address[] = $this->warehouse_flat_office_no;
        }
        
        if ($this->warehouse_building_name) {
            $address[] = $this->warehouse_building_name;
        }
        
        if ($this->warehouse_area) {
            $address[] = $this->warehouse_area;
        }
        
        if ($this->warehouse_city) {
            $address[] = $this->warehouse_city;
        }
        
        if ($this->state) {
            $address[] = $this->state->name;
        }
        
        if ($this->country) {
            $address[] = $this->country->name;
        }
        
        return implode(', ', array_filter($address));
    }

    // Scope for active warehouses (if needed)
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope for warehouses by company
    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}