<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\IndicatorService;
use App\Models\IndicatorData;
use App\Models\Indicator;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

class IndicatorCacheTest extends TestCase
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

    public function test_indicator_filter_cache_empties_on_save(){

        // Arrange: Create an IndicatorData record
        $indicator = Indicator::first();

        if(!$indicator){

            $indicator = Indicator::factory()->create();
        }

        // Put some data in the cache with the indicator tag
        $cacheKey = "test_data_for_indicator_$indicator->id";
        $cacheValue = ['some' => 'data'];
        
        Cache::tags("indicator_$indicator->id")->put($cacheKey, $cacheValue, 3600);
        
        // Verify the cache was set
        $this->assertEquals(
            $cacheValue, 
            Cache::tags("indicator_$indicator->id")->get($cacheKey)
        );


        $indicator_data = IndicatorData::where('indicator_id', $indicator->id)->first();


        // Act: Update and save the model (this should trigger the cache flush)
        $indicator_data->data = 1000;
        $indicator_data->save();

        // Assert: The cache should be empty now
        $this->assertNull(
            Cache::tags("indicator_$indicator->id")->get($cacheKey),
            'Cache should be flushed after saving IndicatorData'
        );
    }
}
