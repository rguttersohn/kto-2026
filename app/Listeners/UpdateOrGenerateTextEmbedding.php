<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\IndicatorUpdateOrCreated;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\IndicatorEmbedding;

class UpdateOrGenerateTextEmbedding
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
    public function handle(IndicatorUpdateOrCreated $event): void
    {

        $indicator = $event->indicator;


        $text = "$indicator->name:$indicator->definition";

        $response = Http::withHeaders([
            'Authorization' => "Bearer " . env('SUPABASE_EMBED_AUTH'),
            'Content-Type' => 'application/json',
        ])->post(env('SUPABASE_EMBED_ENDPOINT'),[
            'name' => 'Functions',
            'input' => $text
        ]);

        if(!$response->successful()){

            Log::debug("Creating text embedding for $indicator->name failed");

            return;

        }

        $body = json_decode($response->body());

        $embedding = $body->embedding;
        $embedding_string = '[' . implode(',', $embedding) . ']';

        
        IndicatorEmbedding::updateOrCreate(
            ['indicator_id' => $indicator->id],
            ['embedding' => $embedding_string]
        );
        
                
    }
}
