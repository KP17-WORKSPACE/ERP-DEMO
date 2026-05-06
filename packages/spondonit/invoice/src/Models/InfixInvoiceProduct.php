<?php

namespace Spondonit\Invoice\Models;

use Illuminate\Database\Eloquent\Model;

class InfixInvoiceProduct extends Model
{
    public function productDetail()
    {
        return $this->belongsTo('App\SmItem', 'product_id', 'id');
    }
}
