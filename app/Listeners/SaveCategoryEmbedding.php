<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\CategorySaved;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\CategoryEmbedding;
use App\Models\Indicator;

class SaveCategoryEmbedding
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
    public function handle(CategorySaved $event): void
    {
        $category = $event->category;

        $response = Http::withHeaders([
            'Authorization' => "Bearer " . env('SUPABASE_EMBED_AUTH'),
            'Content-Type' => 'application/json',
        ])->post(env('SUPABASE_EMBED_ENDPOINT'),[
            'name' => 'Functions',
            'input' =>  $category->name
        ]);


        if(!$response->successful()){

            Log::debug("Creating text embedding for $category->name failed");

            return;

        }

        $body = json_decode($response->body());

        $embedding = $body->embedding;

        $embedding_string = '[' . implode(',', $embedding) . ']';


        CategoryEmbedding::updateOrCreate(
            ['category_id' => $category->id],
            ['embedding' => $embedding_string]
        );


    }
}
