<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * @var string
     */
    private $url;

    private $user;

    /**
     * Create a new notification instance.
     *
     * @param string $url
     * @param $user
     */
    public function __construct(string $url, $user)
    {
        
        $this->url = $url;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line("مرحبا بك{$this->user->name}")
            ->line("لقد إستلمت هذا البريد لكي تستعيد كلمة المرور الخاصة بك")
            ->line("إذا لم تكن طلبت تغيير كلمة المرور من فضلك تجاهل هذا البريد")
            ->line("قم بالضغط على الزر الاتي لإستعادة كلمة المرور")
            ->line("كود التفعيل: {$this->user->resetPasswordCode}")
            ->action('إضغط هنا لإستعادة كلمة المرور ', $this->url);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            
        ];
    }
}
