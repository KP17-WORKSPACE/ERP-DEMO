<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyAddress extends Model
{
    //

    protected $table = 'sys_company_addresses';

    protected $fillable = [
        'company_id',
        'address_type',
        'label',
        'country',
        'state',
        'city',
        'area',
        'street',
        'building',
        'pincode',
        'address_line_1',
        'address_line_2',
        'is_primary',
        'is_active',
    ];

    public function company()
    {
        return $this->belongsTo(SysCompany::class, 'company_id', 'id');
    }

}
