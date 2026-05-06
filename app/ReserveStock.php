<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\SysHelper;

class ReserveStock extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'sys_reserve_stock';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'stock_id',
        'part_number',
        'customer_name',
        'sales_person_id',
        'reserve_qty',
        'reserve_date',
 
        'company_id',
        'created_by',
        'updated_by',
        'deleted_by',

        'customer_id',
        'doc_number',
        'deal_id',
        'delivered'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'reserve_date' => 'date',
        'reserve_qty' => 'integer',
        
        'stock_id' => 'integer',
        'sales_person_id' => 'integer',
        'company_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * The attributes that should be mutated to dates.
     */
    protected $dates = [
        'reserve_date',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Default values for attributes
     */
    protected $attributes = [
        
        'reserve_qty' => 0
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically set company_id, created_by, updated_by
        static::creating(function ($model) {
            if (session('logged_session_data.company_id')) {
                $model->company_id = session('logged_session_data.company_id');
            }
            if (auth()->check()) {
                $model->created_by = auth()->id();
            }
            // Auto-generate doc_number if not set
            if (empty($model->doc_number)) {
                try {
                    $model->doc_number = SysHelper::get_new_code('sys_reserve_stock', 'RQ', 'doc_number');
                } catch (\Throwable $th) {
                    // fallback to timestamp-based code if SysHelper throws
                    $model->doc_number = 'RS-' . time();
                }
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });

        static::deleting(function ($model) {
            if (auth()->check()) {
                $model->deleted_by = auth()->id();
                $model->save(); // Save the deleted_by before soft delete
            }
        });
    }


    public function deal()
    {
        return $this->belongsTo('App\SysCrmDeals', 'deal_id', 'id');
    }

    /**
     * Relationship: ReserveStock belongs to Item (Product)
     */
    public function SysItemStock()
    {
        return $this->belongsTo('App\SysItemStock', 'stock_id', 'id');
    }

    /**
     * Relationship: ReserveStock belongs to Sales Person (Staff)
     */
    public function salesPerson()
    {
        return $this->belongsTo('App\SmStaff', 'sales_person_id', 'user_id');
    }

    /**
     * Relationship: ReserveStock belongs to Company
     */
    public function company()
    {
        return $this->belongsTo('App\SysCompany', 'company_id', 'id');
    }

    /**
     * Relationship: ReserveStock belongs to User (Created By)
     */
    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by', 'id');
    }

    /**
     * Relationship: ReserveStock belongs to User (Updated By)
     */
    public function updatedBy()
    {
        return $this->belongsTo('App\User', 'updated_by', 'id');
    }

    /**
     * Relationship: ReserveStock belongs to User (Deleted By)
     */
    public function deletedBy()
    {
        return $this->belongsTo('App\User', 'deleted_by', 'id');
    }

    /**
     * Scope: Active records only
     */
  

    /**
     * Scope: Filter by company
     */
    public function scopeForCompany($query, $companyId = null)
    {
        $companyId = $companyId ?: session('logged_session_data.company_id');
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope: Filter by part number
     */
    public function scopeByPartNumber($query, $partNumber)
    {
        return $query->where('part_number', $partNumber);
    }

    /**
     * Scope: Filter by stock/item ID
     */
    public function scopeByStockId($query, $stockId)
    {
        return $query->where('stock_id', $stockId);
    }

    /**
     * Scope: Filter by reserve date range
     */
    public function scopeReservedBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('reserve_date', [$startDate, $endDate]);
    }

    /**
     * Scope: Filter by sales person
     */
    public function scopeBySalesPerson($query, $salesPersonId)
    {
        return $query->where('sales_person_id', $salesPersonId);
    }

    /**
     * Accessor: Get formatted reserve date
     */
    public function getFormattedReserveDateAttribute()
    {
        return $this->reserve_date ? $this->reserve_date->format('d/m/Y') : null;
    }

    /**
     * Accessor: Get full sales person name
     */
    public function getSalesPersonNameAttribute()
    {
        return $this->salesPerson ? $this->salesPerson->full_name : 'N/A';
    }

    /**
     * Accessor: Get item part number
     */
    public function getItemPartNumberAttribute()
    {
        return $this->item ? $this->item->part_number : $this->part_number;
    }

    /**
     * Accessor: Get item description
     */
    public function getItemDescriptionAttribute()
    {
        return $this->item ? $this->item->description : 'N/A';
    }

    /**
     * Method: Check if reservation is expired
     */
    public function isExpired()
    {
        return $this->reserve_date < now()->toDateString();
    }

    /**
     * Method: Check if reservation is active and not expired
     */
 

    /**
     * Static method: Get total reserved quantity for a product
     */
    public static function getTotalReservedQty($stockId, $companyId = null)
    {
        $companyId = $companyId ?: session('logged_session_data.company_id');
        
        return static::active()
            ->forCompany($companyId)
            ->byStockId($stockId)
            ->where('reserve_date', '>=', now()->toDateString())
            ->sum('reserve_qty');
    }

    /**
     * Static method: Get available stock after reservations
     */
    public static function getAvailableStock($stockId, $totalStock, $companyId = null)
    {
        $reservedQty = static::getTotalReservedQty($stockId, $companyId);
        return max(0, $totalStock - $reservedQty);
    }

    public function customer()
    {
        return $this->belongsTo('App\SysCustSuppl', 'customer_id', 'id');
    }
}
