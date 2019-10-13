<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class TimedIn extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var \App\Models\Attendance
     */
    protected $attendance;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($attendance)
    {
        $this->attendance = $attendance;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return SlackMessage
     */
    public function toSlack($user)
    {
        return (new SlackMessage)
            ->content('User timed in!')
            ->attachment(function ($attachment) use ($user) {
                $attachment->fields(array_filter([
                    'Name'       => $user->name,
                    'IP Address' => $this->attendance->ip_address,
                    'Time'       => $this->attendance->time->toDayDateTimeString(),
                    'Location'   => $this->attendance->ip->name ?? null,
                ]));
            });
    }
}
