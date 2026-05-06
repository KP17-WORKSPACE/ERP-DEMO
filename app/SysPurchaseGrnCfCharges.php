<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysPurchaseGrnCfCharges extends Model
{
    protected $table = 'sys_purchase_grn_cf_charges';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'grn_id',
        'date',
        'bill_number',
        'cfc_name',
        'cfc_credit_account',
        'cfc_amount',
        'cfc_cal_amount',
        'cfc_remarks',
        'cfc_currency',
        'cfc_exe_rate',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];
}
