<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EndOfServiceAsset extends Model
{
    protected $table = 'sm_end_of_service_assets';
    protected $guarded = [];

    public function assetClearance()
    {
        return $this->belongsTo(EndOfServiceAssetClearance::class, 'asset_clearance_id');
    }
}
