<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EndOfServiceAssetClearance extends Model
{
    protected $table = 'sm_end_of_service_asset_clearance';
    protected $guarded = [];

    public function endOfService()
    {
        return $this->belongsTo(EndOfService::class, 'end_of_service_id');
    }

    public function assets()
    {
        return $this->hasMany(EndOfServiceAsset::class, 'asset_clearance_id');
    }
}
