<?php

namespace Modules\Notification\Mail;

use Illuminate\Mail\Mailable;

class ResetPasswordEmail extends Mailable
{

    private $data;

    public function __construct($data)
    {
        //
        $this->data = $data;
    }

    public function build()
    {
        return $this->markdown('notification::email.buyer.reset-password', ['data' => $this->data]);
    }
}