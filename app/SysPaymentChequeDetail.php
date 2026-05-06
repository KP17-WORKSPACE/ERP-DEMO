<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SysPaymentChequeDetail extends Model
{
    use SoftDeletes;

    protected $table = 'sys_payment_cheque_details';

    protected $fillable = [
        'transaction_id',
        'payment_id',
        'chequebook_id',
        'cheque_number',
        'cheque_date',
        'status',
        'deal_id',
        'account_id',
        'amount',
        'payment_date',
        'narration',
        'company_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'no_of_days'
    ];

    protected $casts = [
        'cheque_date' => 'date',
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function chequebook()
    {
        return $this->belongsTo(Chequebook::class, 'chequebook_id');
    }

    public function payment()
    {
        return $this->belongsTo(SysPayment::class, 'payment_id');
    }

    public function deal()
    {
        return $this->belongsTo(SysCrmDeals::class, 'deal_id');
    }

    public function account()
    {
        return $this->belongsTo(SysChartofAccounts::class, 'account_id');
    }

    public function creator()
    {
        return $this->belongsTo(SmStaff::class, 'created_by', 'user_id');
    }

    public function updater()
    {
        return $this->belongsTo(SmStaff::class, 'updated_by', 'user_id');
    }

    public function deleter()
    {
        return $this->belongsTo(SmStaff::class, 'deleted_by', 'user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function transaction()
    {
        return $this->belongsTo(SysChartofAccountsTransaction::class, 'transaction_id');
    }

 
}