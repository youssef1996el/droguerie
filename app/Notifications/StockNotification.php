<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StockNotification extends Notification
{
    use Queueable;

    private $numero_bon;
    private $product;
    private $iduser;


    /**
     * Create a new notification instance.
     */
    public function __construct($numero_bon,$product,$iduser)
    {
        $this->numero_bon= $numero_bon;
        $this->product= $product;
        $this->iduser= $iduser;


    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return
        [
            'id'    => $this->iduser,
            'text' => 'Le produit ' . $this->product . ' est presque en rupture de stock (Numero Bon ' . $this->numero_bon . ')',

        ];
    }
}
