<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysPaymentAttachment extends Model
{
    protected $table = 'sys_payment_attachments';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'sys_payment_id',
        'file_name',
        'file_path',
        'file_type',
        'created_by',
        'created_at',
        'updated_at',
    ];

    public function payment()
    {
        return $this->belongsTo(SysPayment::class, 'sys_payment_id', 'id');
    }

    public function createdby()
    {
        return $this->belongsTo(SmStaff::class, 'created_by', 'user_id');
    }
}
