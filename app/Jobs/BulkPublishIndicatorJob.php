<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use App\Models\IndicatorData;
use App\Models\Scopes\PublishedScope;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\Log;

class BulkPublishIndicatorJob implements ShouldQueue
{
    use Queueable, Batchable;

    protected int $indicator_id;
    protected array $ids;
    protected ?string $action_flag;

    /**
     * 
     *
     * @param $ids array
     * 
     * @param $idndicator_id int
     * 
     * @param $action_flag string 'UNPUBLISH' | 'PUBLISH'
     * 
     */
    public function __construct(array $ids, $indicator_id, ?string $action_flag = null)
    {
        $this->ids = $ids;
        $this->indicator_id = $indicator_id;
        $this->action_flag = $action_flag;

    }

    protected function getActionBoolean():bool{

        return match($this->action_flag){
           'PUBLISH' => true,
           'UNPUBLISH' => false,
           default => true
        };
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        IndicatorData::withoutGlobalScope(PublishedScope::class)
            ->whereIn('id', $this->ids)
            ->update(['is_published' => $this->getActionBoolean()]);

            
        Cache::tags(["indicator_{$this->indicator_id}"])->flush();
    }
}
