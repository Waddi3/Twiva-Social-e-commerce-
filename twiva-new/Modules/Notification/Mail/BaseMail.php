<?php

namespace Modules\Notification\Mail;

use Illuminate\Mail\Mailable;

class BaseMail extends Mailable
{
    private $details;

    public function __construct($details)
    {
        //
        $this->details = $details;
    }

    public function build()
    {
        return $this->view('notification::email.original', ['details' => $this->details]);
    }
}