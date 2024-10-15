<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WalletCreation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $walletID;
    public $password;

    public function __construct($walletID,$password)
    {
        $this->walletID = $walletID;
        $this->password = $password;
    }

    public function build()
    {
        return $this->subject('Wallet Information')
            ->view('wallet');
    }

}
