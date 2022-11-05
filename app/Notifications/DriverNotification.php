<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DriverNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $driver;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($driver)
    {
        $this->driver = $driver;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'driver_id' => $this->driver['id'],
            'en_name'   => $this->driver['en_name'],
            'ar_name'   => $this->driver['ar_name'],
            'email'     => $this->driver['email'],
            'mobile'    => $this->driver['mobile'],

        ];
    }
}
