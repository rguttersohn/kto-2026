<?php

namespace App\Listeners;

use App\Events\IndicatorSaved;
use App\Jobs\GenerateIndicatorTextEmbedding;


class SaveIndicatorEmbedding
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
    public function handle(IndicatorSaved $event): void
    {
        
        GenerateIndicatorTextEmbedding::dispatch($event->indicator);
         
    }
}
