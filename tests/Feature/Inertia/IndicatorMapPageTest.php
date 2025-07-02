<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Indicator;
use Inertia\Testing\AssertableInertia as Assert;

class IndicatorMapPageTest extends TestCase
{   

    public function test_returns_404_for_invalid_indicator_id(): void
    {
        $nonExistentId = 999999;

        $response = $this->get("/indicators/{$nonExistentId}/map");

        $response->assertStatus(404);
    }

    public function test_indicator_map_response_is_fast()
    {
        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $start = microtime(true);

        $response = $this->get("/indicators/{$indicator->id}/map");

        $duration = microtime(true) - $start;

        dump("response time: $duration");

        $this->assertLessThan(0.50, $duration, "Response took too long: {$duration}s");
    }

    
    public function test_indicator_map_data(): void
    {
        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $response = $this->get("/indicators/{$indicator->id}/map");

        $response->assertInertia(function (Assert $page) {
            $page
                ->has('indicator', fn (Assert $indicator) =>
                    $indicator->hasAll(['id', 'name', 'definition', 'source', 'note'])
                )
                ->has('data', fn (Assert $data) =>
                    $data
                        ->where('type', 'FeatureCollection')
                        ->has('features.0', fn (Assert $feature) =>
                            $feature
                                ->where('type', 'Feature')
                                ->has('geometry')
                                ->has('properties', function(Assert $property){
                                    
                                    $property->hasAll([
                                        'data',
                                        'indicator_id', 
                                        'location_id', 
                                        'location', 
                                        'location_type_id', 
                                        'location_type', 
                                        'timeframe', 
                                        'breakdown',
                                        'format'
                                    ]);
                                })
                        )
                )
                ->has('filters', function(Assert $filter){

                    $filter->has('timeframe')
                    ->has('breakdown.0', fn (Assert $b) =>
                        $b->hasAll(['id', 'name', 'sub_breakdowns'])
                    )
                    ->has('location_type.0', fn (Assert $lt) =>
                        $lt->hasAll(['id', 'name', 'plural_name', 'classification', 'scope'])
                    )
                    ->has('format.0', fn (Assert $f) =>
                        $f->hasAll(['id', 'name'])
                    );

                })
                ->has('initial_selected_filters.0', function(Assert $init_filter){
                    $init_filter->has('id')
                        ->has('filterName', fn (Assert $f) =>
                            $f->hasAll(['label', 'value'])
                        )
                        ->has('operator', fn (Assert $o) =>
                            $o->hasAll(['label', 'value'])
                        )
                        ->has('value', fn (Assert $v) =>
                            $v->hasAll(['label', 'value'])
                        );
                });
        });
    }


    
}
