<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\IndicatorService;
use App\Models\IndicatorData;
use App\Models\Indicator;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

class CacheTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_indicator_filter_cache_returns_expected_key_structure(): void
    {

        $user = User::factory()->editor()->create();
        $this->actingAs($user);

        $indicator = Indicator::withoutGlobalScopes()->first(); 

        Cache::shouldReceive('tags')
            ->once()
            ->with([ "indicator_{$indicator->id}", "filters"])
            ->andReturnSelf();

        Cache::shouldReceive('rememberForever')
            ->once()
            ->with("indicator_breakdowns_{$indicator->id}", \Mockery::type('Closure'))
            ->andReturn(collect(['test' => 'data']));

        IndicatorService::rememberFilter($indicator->id, 'breakdowns', function() use($indicator) {
            return IndicatorData::select('indicators.breakdowns.name', 'indicators.breakdowns.id')
                ->where('indicators.data.indicator_id', $indicator->id)
                ->join('indicators.breakdowns', 'indicators.data.breakdown_id', 'indicators.breakdowns.id')
                ->distinct()
                ->pluck('name', 'id');
        });


    }

    public function test_indicator_filter_cache_throws_exception_when_invalid_filter_name_added(){
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Indicator filter name is not valid');
    
        IndicatorService::rememberFilter(1, 'invalid_filter_name', function() {
            return collect();
        });

    }
}
