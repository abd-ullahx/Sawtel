<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewSupportMessage extends Notification {
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(private SupportTicket $supportTicket, private SupportTicketMessage $supportMessage) {

    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable) {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable) {
        return [
            'subject'     => _lang('New Support Message'),
            'message'     => $this->supportMessage->message,
            'url'         => $this->supportMessage->sender->getTable() == 'users' ? 'client/support_tickets/' . $this->supportTicket->uuid : 'admin/support_tickets/' . $this->supportTicket->uuid,
            'button_text' => _lang('Reply Message'),
        ];
    }
}
