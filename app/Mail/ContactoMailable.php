<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactoMailable extends Mailable
{
    use Queueable, SerializesModels;

   public $details;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        //
        $this->details = $details;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->details['tipo']==1){
            return $this->subject("Usuario Activado")
            ->view('enviarMail');
        }else if($this->details['tipo']==2){
            return $this->subject("Usuario Registrado")
            ->view('enviarMailRegistro');
        }else if($this->details['tipo']==3){
            return $this->subject($this->details['asunto'])
            ->view('enviarMailUsuarios', ['datos' => $this->details]);
        }
        
    }
}
