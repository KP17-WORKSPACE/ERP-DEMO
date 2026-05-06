<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DealTrackPoPaymentCart extends Model
{
    protected $table = 'deal_track_po_payment_cart';

    protected $fillable = [
        'supplier_id',
        'po_id',
        'payment',
        'company_id',
        'deal_id',
        'deal_track_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'payment' => 'decimal:2',
    ];

    /**
     * Supplier (Customer)
     */
    public function supplier()
    {
        return $this->belongsTo(SysChartofAccounts::class, 'supplier_id');
        // adjust model name if different
    }

    /**
     * Purchase Order
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(SysPurchaseOrder::class, 'po_id');
    }
}
