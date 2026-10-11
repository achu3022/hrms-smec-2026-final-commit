<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class EarlyGoingRequestSubmitted extends Notification implements ShouldBroadcastNow
{
    use Queueable;

    protected $earlyGoingRequest;
    protected $message;

    /**
     * Create a new notification instance.
     */
    public function __construct($earlyGoingRequest, $message)
    {
        $this->earlyGoingRequest = $earlyGoingRequest;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'early_going',
            'early_going_id' => $this->earlyGoingRequest->id,
            'message' => $this->message,
        ];
    }
}
