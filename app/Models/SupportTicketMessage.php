<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SupportTicketMessage extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'support_ticket_messages';

    public function customer(): BelongsTo {
        return $this->belongsTo(Customer::class, 'customer_id')->withDefault();
    }

    public function sender(): MorphTo {
        return $this->morphTo('send_by', 'sender_type', 'sender_id');
    }
}