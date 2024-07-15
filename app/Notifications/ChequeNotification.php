<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChequeNotification extends Notification
{
    use Queueable;

    private $numero;
    private $date_cheque;
    private $date_promise;
    private $montant;
    private $name;
    private $bank;
    private $idcompany;

    /**
     * Create a new notification instance.
     */
    public function __construct($numero , $date_cheque, $date_promise , $montant , $name , $bank , $idcompany)
    {
        $this->numero           = $numero;
        $this->date_cheque      = $date_cheque;
        $this->date_promise     = $date_promise;
        $this->montant          = $montant;
        $this->name             = $name;
        $this->bank             = $bank;
        $this->idcompany        = $idcompany;
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
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return
        [
            'id'            => $this->numero,
            'text'          =>  'Numéro de chèque bancaire ' . $this->numero .
                                'au nom de ' . $this->name .' vous devez le présenter à la banque ' .$this->bank .
                                ' et le prix du chèque bancaire est de ' . $this->montant . 'DH',
            'idcompany'     => $this->idcompany,


        ];
    }
}
