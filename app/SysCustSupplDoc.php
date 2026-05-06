<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCustSupplDoc extends Model
{
    protected $table = 'sys_cust_suppl_doc';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'cust_suppl_id',
        'doc_name',
        'doc_file',
        'doc_exp_date',
        'status',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'deleted_by',
        'deleted_at'
    ];

    public function deletedby()
    {
        return $this->belongsTo('App\SmStaff', 'deleted_by', 'user_id');
    }

    public function softDelete($userId)
    {
        $this->deleted_by = $userId;
        $this->deleted_at = now();
        return $this->save();
    }

    /** ✅ Restore */
    public function restoreComment()
    {
        $this->deleted_by = null;
        $this->deleted_at = null;
        return $this->save();
    }
}