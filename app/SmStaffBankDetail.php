<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmStaffBankDetail extends Model
{
    //
    protected $fillable = [
        'staff_id',
        'bank_name',
        'bank_branch',
        'bank_ac_holder',
        'bank_ac_number',
        'iban_number',
        'swift_code',
        'bank_currency',
        'att_iban_letter',
    ];

    public function staff()
    {
        return $this->belongsTo(\App\SmStaff::class, 'staff_id', 'id');
    }
    public function currency()
    {
        return $this->belongsTo(\App\SmCurrency::class, 'bank_currency', 'id');
    }
}
