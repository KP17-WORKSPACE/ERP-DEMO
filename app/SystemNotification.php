<?php

namespace App;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class SystemNotification extends Model
{
    protected $table = 'system_notifications';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'role',
        'type',
        'record_id',

        'deal_id',
        'company_id',
        'customer_name',
        'sales_person',
        'submitted_time',
        'value',

        'title',
        'message',

        'is_shown',
        'is_resolved',

        'is_account_rejected',
        'is_sales_rejected',
        'is_purchase_rejected',
        'is_invoice_rejected',
        'is_delivery_rejected',
        'is_receivables_rejected',
    ];

    public $timestamps = true;

    protected static function boot()
    {
        parent::boot();
        

        // When creating a record → set Dubai time
        static::creating(function ($model) {
            $model->created_at = Carbon::now('Asia/Dubai');
            $model->updated_at = Carbon::now('Asia/Dubai');
        });

        // When updating a record → set Dubai time
        static::updating(function ($model) {
            $model->updated_at = Carbon::now('Asia/Dubai');
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function company()
    {
        return $this->belongsTo(SysCompany::class, 'company_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\SysCustSuppl', 'customer_name', 'id');
    }

    public function salesperson()
    {
        return $this->belongsTo('App\SmStaff', 'sales_person', 'user_id');
    }


    public static function resolveNotification($type, $recordId)
    {
        self::where('type', $type)
            ->where('record_id', $recordId)
            ->where('is_resolved', false)
            ->update([
                'is_resolved' => true,
                'is_shown' => false
            ]);
    }

    public static function updateNotification($type, $recordId, array $data)
    {
      
        return self::where('type', $type)
            ->where('record_id', $recordId)
            ->update($data);
    }


}
