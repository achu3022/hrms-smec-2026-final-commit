<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class LeaveRequestSubmitted extends Notification implements ShouldBroadcastNow
{
    use Queueable;

    protected $leaveRequest;
    protected $message;

    /**
     * Create a new notification instance.
     */
    public function __construct($leaveRequest, $message)
    {
        $this->leaveRequest = $leaveRequest;
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
            'type' => 'leave_request',
            'leave_id' => $this->leaveRequest->id,
            'message' => $this->message,
        ];
    }
}
