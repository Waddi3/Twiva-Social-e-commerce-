<?php

namespace Modules\Notification\Emails\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyAdminForBadgeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $details = [
            'name' => 'Admin',
            'title' => 'Check Badge Page',
            'email_message' => "<a href='" . route('admin.badges.index') . "' target='_blank'>Check</a>",
        ];
        return $this->view('notification::email.admin.original', ['details' => $details]);
    }
}
