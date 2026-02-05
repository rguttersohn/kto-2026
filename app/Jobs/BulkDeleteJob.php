<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use App\Models\IndicatorData;
use Illuminate\Bus\Batchable;

class BulkDeleteJob implements ShouldQueue
{
    use Queueable, Batchable;


    public array $ids;
    public int $indicator_id;

    /**
     * Create a new job instance.
     * 
     * @param array<int> $ids
     * 
     * @param int $indicator_id
     * 
     * 
     */
    public function __construct(array $ids, $indicator_id)
    {
        $this->ids = $ids;
        $this->indicator_id = $indicator_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

    
        IndicatorData::whereIn('id', $this->ids)->delete();

        Cache::tags(["indicator_{$this->indicator_id}"])->flush();
        
    }
}
