<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TradeConfirmedNotification extends Notification
{
    use Queueable;

    public $trade_confirmation;
    public $trade_structure_slug;
    public $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($trade_confirmation,$trade_structure_slug,$user)
    {
        $this->user = $user;
        $this->trade_confirmation = $trade_confirmation->setOrgContext($user->organisation);
        $this->trade_structure_slug = $trade_structure_slug;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        //dd($this->trade_confirmation);
        return (new MailMessage)
            ->from(config("mail.from.confirmation_address"), config("mail.from.name"))
            ->cc(config("mail.admin_email")) // Set the cc address for the mail message.
            ->markdown(
            'emails.tradeconfirmations.'.$this->trade_structure_slug,
            ['trade_confirmation' => $this->trade_confirmation->preFormatted()]
        );
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
            //
        ];
    }
}
