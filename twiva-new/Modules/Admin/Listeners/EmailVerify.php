<?php

namespace Modules\Admin\Listeners;

use Illuminate\Support\Facades\Mail;

class EmailVerify
{


    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        sleep(5);
        Mail::send('admin::emails.email_verify', ['details' => $event->user->toArray(), 'msg' => 'Please verify your email.'],
            function ($m) use ($event) {
                $m->from(config('mail.from.address'), config('app.name'));
                $m->to($event->user->email)->subject('Email verification');
            });
    }
}
