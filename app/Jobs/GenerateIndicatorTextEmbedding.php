<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Indicator;
use App\Services\IndicatorService;
use App\Models\IndicatorEmbedding;

class GenerateIndicatorTextEmbedding implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Indicator $indicator)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $indicator = $this->indicator;

        // Build embedding text and remove null values
        $parts = array_filter([
            $indicator->name,
            $indicator->definition,
        ]);
        
        $text = implode('. ', $parts);

        $response = IndicatorService::fetchSearchAsVector($text);

        if (!$response->successful()) {
            throw new \Exception("Failed to create embedding for indicator {$indicator->id}: {$response->body()}");
        }

        $body = json_decode($response->body());
        $embedding_string = '[' . implode(',', $body->embedding) . ']';

        // Store embedding
        IndicatorEmbedding::updateOrCreate(
            ['indicator_id' => $indicator->id],
            ['embedding' => $embedding_string]
        );  
    }
}
