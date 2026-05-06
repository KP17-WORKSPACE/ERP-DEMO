<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysReceiptAttachment extends Model
{
    protected $table = 'sys_receipt_attachments';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'sys_receipt_id',
        'file_name',
        'file_path',
        'file_type',
        'created_by',
        'created_at',
        'updated_at',
    ];

    public function receipt()
    {
        return $this->belongsTo(SysReceipt::class, 'sys_receipt_id', 'id');
    }

    public function createdby()
    {
        return $this->belongsTo(SmStaff::class, 'created_by', 'user_id');
    }
}
