<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DealTrackGrnStatus extends Model
{
    protected $table = 'deal_track_grn_status';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'deal_id',
        'deal_track',
        'remarks',
        'grn_status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'grn_status' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    // Relationship with User (creator)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relationship with User (updater)
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}