<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SupportTicket extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'support_tickets';

    public function customer() {
        return $this->belongsTo(Customer::class, 'customer_id')->withDefault();
    }

    public function operator() {
        return $this->belongsTo(User::class, 'operator_id')->withDefault();
    }

    public function messages() {
        return $this->hasMany(SupportTicketMessage::class, 'support_ticket_id');
    }

    public function closed_by(): MorphTo {
        return $this->morphTo('closed_by', 'closed_user_type', 'closed_user_id');
    }
}