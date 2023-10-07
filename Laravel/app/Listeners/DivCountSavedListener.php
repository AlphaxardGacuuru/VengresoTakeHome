<?php

namespace App\Listeners;

use App\Events\DivCountSavedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DivCountSavedListener
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
     * @param  \App\Events\DivCountSavedEvent  $event
     * @return void
     */
    public function handle(DivCountSavedEvent $event)
    {
        //
    }
}
