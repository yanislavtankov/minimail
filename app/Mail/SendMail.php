<?php

namespace App\Mail;

use App\Models\Mailbox;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class sendMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The mailbox instance.
     *
     * @var Mailbox
     */
    public $mailbox;


    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Mailbox
     * @return void
     */
    public function __construct(Mailbox $mailbox)
    {
        $this->mailbox = $mailbox;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->from($this->mailbox->from)
            ->view('emails.email')
            ->subject($this->mailbox->subject)
            ->text('emails.text');
        foreach(explode(',',$this->mailbox->attachment) as $attachment){
                $email->attachFromStorage('storage', $attachment);
        }
        return $email;
    }
}
