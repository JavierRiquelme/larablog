<?php

namespace App\Listeners;

use App\Events\UserCreated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendEmailWelcomeUser
{
    private $event;
    
    /**
     * Handle the event.
     *
     * @param  UserCreated  $event
     * @return void
     */
    public function handle(UserCreated $event)
    {
        $data['title'] = "Bienvenido ".$event->user->name;

        $this->event = $event;

        Mail::send('emails.email', $data, function ($message) {
            $message->to($this->event->user->email, $this->event->user->name)
                ->subject("Gracias por formar parte de nuestra familia ".$this->event->user->name);
        });
    }
}
