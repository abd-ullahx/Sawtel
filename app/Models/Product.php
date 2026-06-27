<?php

namespace App\Models;

use App\Traits\MultiTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use MultiTenant;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'products';

    protected $casts = [
        'stock' => 'integer'
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function product_unit(): BelongsTo
    {
        return $this->belongsTo(ProductUnit::class)->withDefault();
    }

    public function income_category(): BelongsTo
    {
        return $this->belongsTo(TransactionCategory::class, 'income_category_id')->withDefault();
    }

    public function expense_category(): BelongsTo
    {
        return $this->belongsTo(TransactionCategory::class, 'expense_category_id')->withDefault();
    }

    public function customerProductPrices(): HasMany
    {
        return $this->hasMany(CustomerProductPrice::class, 'product_id');
    }

    public function customer_products(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'customer_product_prices', 'product_id', 'customer_id');
    }

}