<?php

namespace App\Models;

use App\Traits\MultiTenant;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model {
    
    use MultiTenant;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invoices';

    public function items() {
        return $this->hasMany(InvoiceItem::class, 'invoice_id')->withoutGlobalScopes();
    }

    public function taxes() {
        return $this->hasMany(InvoiceItemTax::class, "invoice_id")
            ->withoutGlobalScopes()
            ->selectRaw('invoice_item_taxes.*, sum(amount) as amount')
            ->groupBy('tax_id');
    }



    public function customer() {
        return $this->belongsTo(Customer::class, 'customer_id')->withDefault()->withoutGlobalScopes();
    }

    public function business() {
        return $this->belongsTo(Business::class, 'business_id')->withDefault()->withoutGlobalScopes();
    }

    public function transactions() {
        return $this->hasMany(Transaction::class, 'ref_id')->where('ref_type', 'invoice')->withoutGlobalScopes();
    }

    public function invoice_template() {
        return $this->belongsTo(InvoiceTemplate::class, 'template')->withDefault()->withoutGlobalScopes();
    }

    protected function subTotal(): Attribute {
        $decimal_place = get_business_option('decimal_places', 2);

        return Attribute::make(
            get:fn(string $value) => number_format($value, $decimal_place, '.', ''),
        );
    }

    protected function discount(): Attribute {
        $decimal_place = get_business_option('decimal_places', 2);

        return Attribute::make(
            get:fn(string $value) => number_format($value, $decimal_place, '.', ''),
        );
    }

    protected function grandTotal(): Attribute {
        $decimal_place = get_business_option('decimal_places', 2);

        return Attribute::make(
            get:fn(string $value) => number_format($value, $decimal_place, '.', ''),
        );
    }

    protected function paid(): Attribute {
        $decimal_place = get_business_option('decimal_places', 2);

        return Attribute::make(
            get:fn(string $value) => number_format($value, $decimal_place, '.', ''),
        );
    }

    protected function invoiceDate(): Attribute {
        $date_format = get_date_format();

        return Attribute::make(
            get:fn(string $value) => \Carbon\Carbon::parse($value)->format("$date_format"),
        );
    }

    protected function dueDate(): Attribute {
        $date_format = get_date_format();

        return Attribute::make(
            get:fn(string $value) => \Carbon\Carbon::parse($value)->format("$date_format"),
        );
    }

    protected function recurringStart(): Attribute {
        $date_format = get_date_format();

        return Attribute::make(
            get:fn($value) => $value != null ? \Carbon\Carbon::parse($value)->format("$date_format") : null,
        );
    }

    protected function recurringInvoiceDate(): Attribute {
        $date_format = get_date_format();

        return Attribute::make(
            get:fn($value) => $value != null ? \Carbon\Carbon::parse($value)->format("$date_format") : null,
        );
    }

    protected function recurringDueDate(): Attribute {
        $date_format = get_date_format();
        $recurring_due_date = date("Y-m-d", strtotime($this->getRawOriginal('recurring_invoice_date').' '.$this->getRawOriginal('recurring_due_date')));

        return Attribute::make(
            get:fn($value) => \Carbon\Carbon::parse($recurring_due_date)->format("$date_format"),
        );
    }

}