<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerProductPrice extends Model
{

    protected $fillable = [
        'customer_id',
        'product_id',
        'selling_price',
        'customer_item_code',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

}
