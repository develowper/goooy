<?php

namespace App\Listeners;

use App\Events\SendToTelegram;
use App\Http\Helpers\Telegram;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendToTelegramListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param \App\Events\SendToTelegram $event
     * @return void
     */
    public function handle(SendToTelegram $event)
    {

        Telegram::sendLog($event->to, $event->type, $event->data);
    }
}
