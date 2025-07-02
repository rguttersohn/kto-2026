<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Indicator;
use Inertia\Testing\AssertableInertia as Assert;

class IndicatorQueryPageTest extends TestCase
{
    
    public function test_returns_404_for_invalid_indicator_id(): void
    {
        $nonExistentId = 999999;

        $response = $this->get("/indicators/{$nonExistentId}/query");

        $response->assertStatus(404);
    }

    public function test_200_response(){

        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $response = $this->get("indicators/$indicator->id/query");

        $response->assertStatus(200);
    }

    public function test_indicator_query_response_is_fast()
    {
        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $start = microtime(true);

        $this->get("/indicators/{$indicator->id}/query");

        $duration = microtime(true) - $start;

        dump("response time: $duration");

        $this->assertLessThan(0.50, $duration, "Response took too long: {$duration}s");
    }


    public function test_props_data_with_no_filters(){

        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $response = $this->get("/indicators/{$indicator->id}/query");

        $response->assertInertia(function (Assert $page) {
            $page
                ->has('indicator', fn (Assert $indicator) =>
                    $indicator->hasAll(['id', 'name', 'definition', 'source', 'note'])
                )
                ->has('data.0', function(Assert $d){
                    $d->hasAll([
                        'data',
                        'indicator_id',
                        'location_id',
                        'location',
                        'location_type_id',
                        'location_type',
                        'timeframe',
                        'breakdown',
                        'format',
                    ]);
                })
                ->has('data_count')
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
                ->has('initial_filters');
        });

    
    }

    public function test_props_data_with_eq_filter(){

        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $response_with_eq_filter = $this->get("/indicators/{$indicator->id}/query?filter[timeframe][eq]=2021");

        $response_with_eq_filter->assertInertia(function (Assert $page) {
            $page
                ->has('indicator', fn (Assert $indicator) =>
                    $indicator->hasAll(['id', 'name', 'definition', 'source', 'note'])
                )
                ->has('data.0', function(Assert $d){
                    $d->hasAll([
                        'data',
                        'indicator_id',
                        'location_id',
                        'location',
                        'location_type_id',
                        'location_type',
                        'timeframe',
                        'breakdown',
                        'format',
                    ]);
                })
                ->has('data_count')
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
                ->has('initial_filters.0', function(Assert $filter){
                    $filter
                        ->has('id')
                        ->has('filterName', fn (Assert $f) => $f->hasAll(['label', 'value']))
                        ->has('operator', fn (Assert $o) => $o->hasAll(['label', 'value']))
                        ->has('value', fn (Assert $v) =>
                            $v->has('label')->has('value')
                        );
                });
        });

    }

    public function test_props_data_with_in_filter(){

        $indicator = Indicator::inRandomOrder()->firstOrFail();

        $response_with_in_filter = $this->get("/indicators/{$indicator->id}/query?filter[timeframe][in][]=2021&filter[timeframe][in][]=2023");

        $response_with_in_filter->assertInertia(function (Assert $page) {
            $page
                ->has('indicator', fn (Assert $indicator) =>
                    $indicator->hasAll(['id', 'name', 'definition', 'source', 'note'])
                )
                ->has('data.0', function(Assert $d){
                    $d->hasAll([
                        'data',
                        'indicator_id',
                        'location_id',
                        'location',
                        'location_type_id',
                        'location_type',
                        'timeframe',
                        'breakdown',
                        'format',
                    ]);
                })
                ->has('data_count')
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
                ->has('initial_filters.0', function(Assert $filter){
                    $filter
                        ->has('id')
                        ->has('filterName', fn (Assert $f) => $f->hasAll(['label', 'value']))
                        ->has('operator', fn (Assert $o) => $o->hasAll(['label', 'value']))
                        ->has('value', function(Assert $v){

                            $v->has('label.0')
                            ->has('value.0');

                        }
                            
                        );
                });
        });

    }



}
