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
            'timeframe' => ['2020', '2021', '2022', '2023'],
        ];

        $requestFilters = [];

        $result = IndicatorFiltersFormatter::mergeWithDefaultFilters($indicatorFilters, $requestFilters);

        $this->assertEquals(['timeframe' => ['eq' => '2023']], $result);
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


    public function test_it_adds_default_location_when_location_type_has_default(){
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

    public function test_it_uses_request_location_when_provided(){
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

    public function test_it_adds_default_location_for_requested_location_type(){
        
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

    public function test_it_still_uses_request_location_even_when_excluded_from_defaults(){
        
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


    public function test_it_uses_selected_default_timeframe_over_last_value(){
        
        $indicatorFilters = [
            'timeframe' => ['2020', '2021', '2022', '2023'],
        ];

        $requestFilters = [];

        $selectedDefaults = [
            'timeframe' => '2021',
        ];

        $result = IndicatorFiltersFormatter::mergeWithDefaultFilters(
            $indicatorFilters, 
            $requestFilters,
            [],
            $selectedDefaults
        );

        $this->assertEquals(['timeframe' => ['eq' => '2021']], $result);

    }

    public function test_it_uses_selected_default_breakdown(){
        
        $breakdown1 = Mockery::mock();
        $breakdown1->id = 123;
        $breakdown1->subBreakdowns = collect([]);

        $breakdown2 = Mockery::mock();
        $breakdown2->id = 456;
        $breakdown2->subBreakdowns = collect([]);

        $indicatorFilters = [
            'breakdown' => collect([$breakdown1, $breakdown2]),
        ];

        $requestFilters = [];

        $selectedDefaults = [
            'breakdown' => 456,
        ];

        $result = IndicatorFiltersFormatter::mergeWithDefaultFilters(
            $indicatorFilters, 
            $requestFilters,
            [],
            $selectedDefaults
        );

        $this->assertEquals(['breakdown' => ['eq' => 456]], $result);
    }

    public function test_it_uses_selected_default_location_type_and_location(){
        
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

        $requestFilters = [];

        $selectedDefaults = [
            'location_type' => 999,
            'location' => 222,
        ];

        $result = IndicatorFiltersFormatter::mergeWithDefaultFilters(
            $indicatorFilters, 
            $requestFilters,
            [],
            $selectedDefaults
        );

        $this->assertEquals([
            'location_type' => ['eq' => 999],
            'location' => ['eq' => 222],
        ], $result);
    }

    public function test_it_uses_selected_default_format(){
        $format1 = Mockery::mock();
        $format1->id = 100;

        $format2 = Mockery::mock();
        $format2->id = 200;

        $indicatorFilters = [
            'format' => collect([$format1, $format2]),
        ];

        $requestFilters = [];

        $selectedDefaults = [
            'format' => 200, // Prefer second format
        ];

        $result = IndicatorFiltersFormatter::mergeWithDefaultFilters(
            $indicatorFilters, 
            $requestFilters,
            [],
            $selectedDefaults
        );

        $this->assertEquals(['format' => ['eq' => 200]], $result);
    }

    public function test_request_filters_override_selected_defaults()
    {
        $indicatorFilters = [
            'timeframe' => ['2020', '2021', '2022', '2023'],
        ];

        $requestFilters = [
            'timeframe' => ['eq' => '2020'], // User explicitly chose 2020
        ];

        $selectedDefaults = [
            'timeframe' => '2021', // Selected default is 2021
        ];

        $result = IndicatorFiltersFormatter::mergeWithDefaultFilters(
            $indicatorFilters, 
            $requestFilters,
            [],
            $selectedDefaults
        );

        // Request should win over selected default
        $this->assertEquals(['timeframe' => ['eq' => '2020']], $result);
    }

    public function test_it_uses_multiple_selected_defaults_together(){
        $breakdown = Mockery::mock();
        $breakdown->id = 123;
        $breakdown->subBreakdowns = collect([]);

        $format = Mockery::mock();
        $format->id = 999;

        $location = Mockery::mock();
        $location->id = 111;

        $locationType = Mockery::mock();
        $locationType->id = 789;
        $locationType->locations = collect([$location]);

        $indicatorFilters = [
            'timeframe' => ['2020', '2021', '2022', '2023'],
            'breakdown' => collect([$breakdown]),
            'location_type' => collect([$locationType]),
            'format' => collect([$format]),
        ];

        $requestFilters = [];

        $selectedDefaults = [
            'timeframe' => '2021',
            'breakdown' => 123,
            'location_type' => 789,
            'location' => 111,
            'format' => 999,
        ];

        $result = IndicatorFiltersFormatter::mergeWithDefaultFilters(
            $indicatorFilters, 
            $requestFilters,
            [],
            $selectedDefaults
        );

        $this->assertEquals([
            'timeframe' => ['eq' => '2021'],
            'breakdown' => ['eq' => 123],
            'location_type' => ['eq' => 789],
            'location' => ['eq' => 111],
            'format' => ['eq' => 999],
        ], $result);
    }

    public function test_selected_defaults_are_ignored_when_filter_is_excluded(){
        
        $indicatorFilters = [
            'timeframe' => ['2020', '2021', '2022', '2023'],
        ];

        $requestFilters = [];

        $selectedDefaults = [
            'timeframe' => '2021', // This should be ignored
        ];

        $result = IndicatorFiltersFormatter::mergeWithDefaultFilters(
            $indicatorFilters, 
            $requestFilters,
            ['timeframe'], // Exclude timeframe
            $selectedDefaults
        );

        // Should not have timeframe at all
        $this->assertEquals([], $result);
        $this->assertArrayNotHasKey('timeframe', $result);
    }

    public function test_it_adds_location_from_correct_location_type_when_multiple_types_exist(){
        
        // Location Type 1 locations
        $location1a = Mockery::mock();
        $location1a->id = 111;
        $location1a->name = 'Location 1A';

        $location1b = Mockery::mock();
        $location1b->id = 112;
        $location1b->name = 'Location 1B';

        // Location Type 2 locations
        $location2a = Mockery::mock();
        $location2a->id = 221;
        $location2a->name = 'Location 2A';

        $location2b = Mockery::mock();
        $location2b->id = 222;
        $location2b->name = 'Location 2B';

        // Location Type 1
        $locationType1 = Mockery::mock();
        $locationType1->id = 100;
        $locationType1->locations = collect([$location1a, $location1b]);

        // Location Type 2
        $locationType2 = Mockery::mock();
        $locationType2->id = 200;
        $locationType2->locations = collect([$location2a, $location2b]);

        $indicatorFilters = [
            'location_type' => collect([$locationType1, $locationType2]),
        ];

        // Test with selected default
        $selectedDefaults = [
            'location_type' => 200, // Select location_type 2
        ];

        $result = IndicatorFiltersFormatter::mergeWithDefaultFilters(
            $indicatorFilters,
            [],
            [],
            $selectedDefaults
        );

        // Should use first location from location_type 200, not location_type 100
        $this->assertEquals([
            'location_type' => ['eq' => 200],
            'location' => ['eq' => 221], // First location of type 200
        ], $result);

        // Verify the location is NOT from location_type 100
        $this->assertNotEquals(111, $result['location']['eq']);
        $this->assertNotEquals(112, $result['location']['eq']);

        // Now test with request filter
        $requestFilters = [
            'location_type' => ['eq' => 100], // Select location_type 1
        ];

        $result2 = IndicatorFiltersFormatter::mergeWithDefaultFilters(
            $indicatorFilters,
            $requestFilters,
            []
        );

        // Should use first location from location_type 100
        $this->assertEquals([
            'location_type' => ['eq' => 100],
            'location' => ['eq' => 111], // First location of type 100
        ], $result2);

        // Verify the location is NOT from location_type 200
        $this->assertNotEquals(221, $result2['location']['eq']);
        $this->assertNotEquals(222, $result2['location']['eq']);
    }

    public function test_it_fills_unselected_filters_with_defaults_when_only_one_filter_is_selected()
{
    // Set up breakdown
    $breakdown = Mockery::mock();
    $breakdown->id = 123;
    $breakdown->subBreakdowns = collect([]);

    // Set up location type
    $location = Mockery::mock();
    $location->id = 111;

    $locationType = Mockery::mock();
    $locationType->id = 789;
    $locationType->locations = collect([$location]);

    // Set up format
    $format = Mockery::mock();
    $format->id = 999;

    $indicatorFilters = [
        'timeframe' => ['2020', '2021', '2022', '2023'],
        'breakdown' => collect([$breakdown]),
        'location_type' => collect([$locationType]),
        'format' => collect([$format]),
    ];

    $requestFilters = [];

    // Only timeframe is manually selected
    $selectedDefaults = [
        'timeframe' => '2021',
        // breakdown, location_type, location, format are NOT selected (null or missing)
    ];

    $result = IndicatorFiltersFormatter::mergeWithDefaultFilters(
        $indicatorFilters,
        $requestFilters,
        [],
        $selectedDefaults
    );

    // Should use selected timeframe AND fill in defaults for everything else
    $this->assertEquals([
        'timeframe' => ['eq' => '2021'],        // From selected defaults
        'breakdown' => ['eq' => 123],           // Default (first breakdown)
        'location_type' => ['eq' => 789],       // Default (first location_type)
        'location' => ['eq' => 111],            // Default (first location)
        'format' => ['eq' => 999],              // Default (first format)
    ], $result);
}

    public function test_it_fills_unselected_filters_when_only_breakdown_is_selected()
    {
        $breakdown1 = Mockery::mock();
        $breakdown1->id = 100;
        $breakdown1->subBreakdowns = collect([]);

        $breakdown2 = Mockery::mock();
        $breakdown2->id = 200;
        $breakdown2->subBreakdowns = collect([]);

        $format = Mockery::mock();
        $format->id = 999;

        $indicatorFilters = [
            'timeframe' => ['2020', '2021', '2022'],
            'breakdown' => collect([$breakdown1, $breakdown2]),
            'format' => collect([$format]),
        ];

        $requestFilters = [];

        // Only breakdown is manually selected to second option
        $selectedDefaults = [
            'breakdown' => 200,
            // timeframe and format are NOT selected
        ];

        $result = IndicatorFiltersFormatter::mergeWithDefaultFilters(
            $indicatorFilters,
            $requestFilters,
            [],
            $selectedDefaults
        );

        $this->assertEquals([
            'timeframe' => ['eq' => '2022'],        // Default (last timeframe)
            'breakdown' => ['eq' => 200],           // From selected defaults
            'format' => ['eq' => 999],              // Default (first format)
        ], $result);
    }

    public function test_it_uses_location_in_request_when_location_type_is_not_in_request()
    {
        $location1 = Mockery::mock();
        $location1->id = 111;

        $location2 = Mockery::mock();
        $location2->id = 321;

        $locationType = Mockery::mock();
        $locationType->id = 789;
        $locationType->locations = collect([$location1, $location2]);

        $indicatorFilters = [
            'location_type' => collect([$locationType]),
        ];

        $requestFilters = [
            'location' => ['eq' => 321], // location in request, no location_type
        ];

        $result = IndicatorFiltersFormatter::mergeWithDefaultFilters($indicatorFilters, $requestFilters);

        // Should use requested location and derive location_type from it
        $this->assertEquals([
            'location_type' => ['eq' => 789],
            'location' => ['eq' => 321],
        ], $result);
    }

    public function test_it_excludes_locations_from_filter_when_in_excluded_list()
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
        ];

        $result = IndicatorFiltersFormatter::mergeWithDefaultFilters(
            $indicatorFilters,
            $requestFilters,
            ['location'] // location excluded from defaults
        );

        // Should have location_type but no location
        $this->assertEquals([
            'location_type' => ['eq' => 789],
        ], $result);

        $this->assertArrayNotHasKey('location', $result);
    }

}
