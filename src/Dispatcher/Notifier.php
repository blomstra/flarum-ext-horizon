<?php

namespace Blomstra\Horizon\Dispatcher;

use Illuminate\Contracts\Notifications\Dispatcher;

class Notifier implements Dispatcher
{

    /**
     * Send the given notification to the given notifiable entities.
     *
     * @param \Illuminate\Support\Collection|array|mixed $notifiables
     * @param mixed                                      $notification
     * @return void
     */
    public function send($notifiables, $notification)
    {
        // TODO: Implement send() method.
    }

    /**
     * Send the given notification immediately.
     *
     * @param \Illuminate\Support\Collection|array|mixed $notifiables
     * @param mixed                                      $notification
     * @return void
     */
    public function sendNow($notifiables, $notification)
    {
        // TODO: Implement sendNow() method.
    }
}
