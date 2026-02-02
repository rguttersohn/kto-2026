<?php

namespace Tests\Feature;

use App\Services\IndicatorFiltersFormatter;
use Tests\TestCase;
use Mockery;

class MergeWithDefaultFilters extends TestCase
{
    
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    
    public function test_it_uses_request_filter_when_key_exists_in_both_arrays()
    {
        $indicatorFilters = [
            'timeframe' => ['2020', '2021', '2022'],
        ];

        $requestFilters = [
            'timeframe' => ['eq' => '2020'],
        ];

        $result = IndicatorFiltersFormatter::mergeWithDefaultFilters($indicatorFilters, $requestFilters);

        $this->assertEquals(['timeframe' => ['eq' => '2020']], $result);
    }

    
    public function test_uses_last_timeframe_value_when_not_in_request()
    {
        $indicatorFilters = [
            'timeframe' => ['2020', '2021', '2022'],
        ];

        $requestFilters = [];

        $result = IndicatorFiltersFormatter::mergeWithDefaultFilters($indicatorFilters, $requestFilters);

        $this->assertEquals(['timeframe' => ['eq' => '2022']], $result);
    }

    
    public function test_uses_first_breakdown_id_when_no_sub_breakdowns()
    {
        $breakdown = Mockery::mock();
        $breakdown->id = 123;
        $breakdown->subBreakdowns = collect([]);
        
        $breakdowns = collect([$breakdown]);

        $indicatorFilters = [
            'breakdown' => $breakdowns,
        ];

        $requestFilters = [];

        $result = IndicatorFiltersFormatter::mergeWithDefaultFilters($indicatorFilters, $requestFilters);

        $this->assertEquals(['breakdown' => ['eq' => 123]], $result);
    }

    
    public function test_uses_first_sub_breakdown_id_when_sub_breakdowns_exist()
    {
        $subBreakdown = Mockery::mock();
        $subBreakdown->id = 456;

        $breakdown = Mockery::mock();
        $breakdown->id = 123;
        $breakdown->subBreakdowns = collect([$subBreakdown]);
        
        $breakdowns = collect([$breakdown]);

        $indicatorFilters = [
            'breakdown' => $breakdowns,
        ];

        $requestFilters = [];

        $result = IndicatorFiltersFormatter::mergeWithDefaultFilters($indicatorFilters, $requestFilters);

        $this->assertEquals(['breakdown' => ['eq' => 456]], $result);
    }

    
    public function test_uses_first_location_type_id_when_not_in_request()
    {
        $locationType = Mockery::mock();
        $locationType->id = 789;

        $location1 = Mockery::mock();
        $location1->id = 1;
        $location1->name = 'Test 1';

        $location2 = Mockery::mock();
        $location2->id = 2;
        $location2->name = 'Test 2';

        $location3 = Mockery::mock();
        $location3->id = 3;
        $location3->name = 'Test 3';

        $locationType = Mockery::mock();
        $locationType->id = 789;
        $locationType->locations = collect([$location1, $location2, $location3]);

        $locationTypes = collect([$locationType]);

        $indicatorFilters = [
            'location_type' => $locationTypes,
        ];

        $requestFilters = [];

        $result = IndicatorFiltersFormatter::mergeWithDefaultFilters($indicatorFilters, $requestFilters);

        $this->assertEquals([
                            'location_type' => ['eq' => 789],
                            'location' => ['eq' => 1],
                        ], $result);
    }

    
    public function test_uses_first_format_id_when_not_in_request()
    {
        $format = Mockery::mock();
        $format->id = 999;

        $formats = collect([$format]);

        $indicatorFilters = [
            'format' => $formats,
        ];

        $requestFilters = [];

        $result = IndicatorFiltersFormatter::mergeWithDefaultFilters($indicatorFilters, $requestFilters);

        $this->assertEquals(['format' => ['eq' => 999]], $result);
    }

    
    public function test_handles_multiple_filters_with_mixed_sources()
    {
        $breakdown = Mockery::mock();
        $breakdown->id = 123;
        $breakdown->subBreakdowns = collect([]);

        $locationType = Mockery::mock();
        $locationType->id = 789;

        $location1 = Mockery::mock();
        $location1->id = 1;
        $location1->name = 'Test 1';

        $location2 = Mockery::mock();
        $location2->id = 2;
        $location2->name = 'Test 2';

        $location3 = Mockery::mock();
        $location3->id = 3;
        $location3->name = 'Test 3';

        $locationType = Mockery::mock();
        $locationType->id = 789;
        $locationType->locations = collect([$location1, $location2, $location3]);

        $indicatorFilters = [
            'timeframe' => ['2020', '2021', '2022'],
            'breakdown' => collect([$breakdown]),
            'location_type' => collect([$locationType]),
        ];

        $requestFilters = [
            'timeframe' => ['eq' => '2021'], // Override
            // breakdown will use default
            // location_type will use default
        ];

        $result = IndicatorFiltersFormatter::mergeWithDefaultFilters($indicatorFilters, $requestFilters);

        $this->assertEquals([
            'timeframe' => ['eq' => '2021'],
            'breakdown' => ['eq' => 123],
            'location_type' => ['eq' => 789],
            'location' => ['eq' => 1]
        ], $result);
    }

    
    public function test_returns_empty_array_when_both_inputs_are_empty()
    {
        $result = IndicatorFiltersFormatter::mergeWithDefaultFilters([], []);

        $this->assertEquals([], $result);
    }

    
    public function test_handles_all_request_filters_overriding_defaults()
    {
        $breakdown = Mockery::mock();
        $breakdown->id = 123;
        $breakdown->shouldReceive('getAttribute')
            ->with('subBreakdowns')
            ->andReturn(collect([]));

        $indicatorFilters = [
            'timeframe' => ['2020', '2021'],
            'breakdown' => collect([$breakdown]),
        ];

        $requestFilters = [
            'timeframe' => ['eq' => '2019'],
            'breakdown' => ['eq' => 999],
        ];

        $result = IndicatorFiltersFormatter::mergeWithDefaultFilters($indicatorFilters, $requestFilters);

        $this->assertEquals([
            'timeframe' => ['eq' => '2019'],
            'breakdown' => ['eq' => 999],
        ], $result);
    }

    public function test_it_excludes_timeframe_default_when_specified()
    {
        
        $indicatorFilters = [
            'timeframe' => ['2020', '2021', '2022'],
            'breakdown' => $this->createBreakdownMock(123),
        ];

        $requestFilters = [];

        $result = IndicatorFiltersFormatter::mergeWithDefaultFilters(
            $indicatorFilters, 
            $requestFilters,
            ['timeframe'] // Exclude timeframe from defaults
        );

        // Should only have breakdown, not timeframe
        $this->assertEquals(['breakdown' => ['eq' => 123]], $result);
        $this->assertArrayNotHasKey('timeframe', $result);
    }

    public function it_excludes_multiple_filters_from_defaults()
    {
        $locationType = Mockery::mock();
        $locationType->id = 789;

        $format = Mockery::mock();
        $format->id = 999;

        $indicatorFilters = [
            'timeframe' => ['2020', '2021', '2022'],
            'breakdown' => $this->createBreakdownMock(123),
            'location_type' => collect([$locationType]),
            'format' => collect([$format]),
        ];

        $requestFilters = [];

        $result = IndicatorFiltersFormatter::mergeWithDefaultFilters(
            $indicatorFilters, 
            $requestFilters,
            ['timeframe', 'location_type'] // Exclude both
        );

        // Should only have breakdown and format
        $this->assertEquals([
            'breakdown' => ['eq' => 123],
            'format' => ['eq' => 999],
        ], $result);

        $this->assertArrayNotHasKey('timeframe', $result);
        $this->assertArrayNotHasKey('location_type', $result);
    }

    public function test_it_still_uses_request_filters_even_when_excluded_from_defaults()
    {
        $indicatorFilters = [
            'timeframe' => ['2020', '2021', '2022'],
            'breakdown' => $this->createBreakdownMock(123),
        ];

        $requestFilters = [
            'timeframe' => ['eq' => '2019'], // User explicitly set this
        ];

        $result = IndicatorFiltersFormatter::mergeWithDefaultFilters(
            $indicatorFilters, 
            $requestFilters,
            ['timeframe'] // Even though excluded, request should still apply
        );

        // Request filter should still be present
        $this->assertEquals([
            'timeframe' => ['eq' => '2019'],
            'breakdown' => ['eq' => 123],
        ], $result);
    }


    public function it_works_normally_with_empty_exclusion_array()
    {
        $indicatorFilters = [
            'timeframe' => ['2020', '2021', '2022'],
        ];

        $requestFilters = [];

        $result = IndicatorFiltersFormatter::mergeWithDefaultFilters(
            $indicatorFilters, 
            $requestFilters,
            [] // Empty exclusion array
        );

        // Should behave like before - apply defaults
        $this->assertEquals(['timeframe' => ['eq' => '2022']], $result);
    }

    private function createBreakdownMock($id)
    {
        $breakdown = Mockery::mock();
        $breakdown->id = $id;
        $breakdown->subBreakdowns = collect([]);
        
        return collect([$breakdown]);
    }


    public function test_it_adds_default_location_when_location_type_has_default()
{
    $location = Mockery::mock();
    $location->id = 111;

    $locationType = Mockery::mock();
    $locationType->id = 789;
    $locationType->locations = collect([$location]);

    $indicatorFilters = [
        'location_type' => collect([$locationType]),
    ];

    $requestFilters = [];

    $result = IndicatorFiltersFormatter::mergeWithDefaultFilters($indicatorFilters, $requestFilters);

    $this->assertEquals([
        'location_type' => ['eq' => 789],
        'location' => ['eq' => 111],
    ], $result);
}

public function test_it_uses_request_location_when_provided()
{
    $location = Mockery::mock();
    $location->id = 111;

    $locationType = Mockery::mock();
    $locationType->id = 789;
    $locationType->locations = collect([$location]);

    $indicatorFilters = [
        'location_type' => collect([$locationType]),
    ];

    $requestFilters = [
        'location_type' => ['eq' => 789],
        'location' => ['eq' => 222], // User explicitly set location
    ];

    $result = IndicatorFiltersFormatter::mergeWithDefaultFilters($indicatorFilters, $requestFilters);

    $this->assertEquals([
        'location_type' => ['eq' => 789],
        'location' => ['eq' => 222], // Should use user's value
    ], $result);
}

public function test_it_adds_default_location_for_requested_location_type()
{
    $location1 = Mockery::mock();
    $location1->id = 111;

    $location2 = Mockery::mock();
    $location2->id = 222;

    $locationType1 = Mockery::mock();
    $locationType1->id = 789;
    $locationType1->locations = collect([$location1]);

    $locationType2 = Mockery::mock();
    $locationType2->id = 999;
    $locationType2->locations = collect([$location2]);

    $indicatorFilters = [
        'location_type' => collect([$locationType1, $locationType2]),
    ];

    $requestFilters = [
        'location_type' => ['eq' => 999], // User chose second location type
    ];

    $result = IndicatorFiltersFormatter::mergeWithDefaultFilters($indicatorFilters, $requestFilters);

    $this->assertEquals([
        'location_type' => ['eq' => 999],
        'location' => ['eq' => 222], // Should use first location of location_type 999
    ], $result);
}

    public function test_it_excludes_location_default_when_specified()
    {
        $location = Mockery::mock();
        $location->id = 111;

        $locationType = Mockery::mock();
        $locationType->id = 789;
        $locationType->locations = collect([$location]);

        $indicatorFilters = [
            'location_type' => collect([$locationType]),
        ];

        $requestFilters = [];

        $result = IndicatorFiltersFormatter::mergeWithDefaultFilters(
            $indicatorFilters, 
            $requestFilters,
            ['location'] // Exclude location from defaults
        );

        // Should have location_type but not location
        $this->assertEquals(['location_type' => ['eq' => 789]], $result);
        $this->assertArrayNotHasKey('location', $result);
    }

    public function test_it_skips_default_location_when_location_type_filter_is_array()
    {
        $location = Mockery::mock();
        $location->id = 111;

        $locationType = Mockery::mock();
        $locationType->id = 789;
        $locationType->locations = collect([$location]);

        $indicatorFilters = [
            'location_type' => collect([$locationType]),
        ];

        $requestFilters = [
            'location_type' => ['in' => [789, 999]], // Array value (using 'in' operator)
        ];

        $result = IndicatorFiltersFormatter::mergeWithDefaultFilters($indicatorFilters, $requestFilters);

        // When location_type filter value is an array, no default location should be added
        $this->assertEquals([
            'location_type' => ['in' => [789, 999]],
        ], $result);
        $this->assertArrayNotHasKey('location', $result);
    }

    public function test_it_still_uses_request_location_even_when_excluded_from_defaults()
    {
        $location = Mockery::mock();
        $location->id = 111;

        $locationType = Mockery::mock();
        $locationType->id = 789;
        $locationType->locations = collect([$location]);

        $indicatorFilters = [
            'location_type' => collect([$locationType]),
        ];

        $requestFilters = [
            'location_type' => ['eq' => 789],
            'location' => ['eq' => 333], // User explicitly set this
        ];

        $result = IndicatorFiltersFormatter::mergeWithDefaultFilters(
            $indicatorFilters, 
            $requestFilters,
            ['location'] // Even though excluded, request should still apply
        );

        // Request filter should still be present
        $this->assertEquals([
            'location_type' => ['eq' => 789],
            'location' => ['eq' => 333],
        ], $result);
    }

}
