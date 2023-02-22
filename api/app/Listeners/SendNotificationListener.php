<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Mail;
use App\Mail\GeneralMail;

class SendNotificationListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        $receivers = config('app.env') === 'production' ? $event->data['receivers'] : collect(config('mail.local.to'));
        $receivers->each(
            function(string $receiverEmail) use($event) {
                Mail::to($receiverEmail)->send(new GeneralMail($event->data['text']));
            }
        );
    }
}
