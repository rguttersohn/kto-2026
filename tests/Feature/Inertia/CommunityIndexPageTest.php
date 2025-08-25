<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Location;
use Inertia\Testing\AssertableInertia as Assert;
use App\Models\Indicator;
use App\Services\IndicatorFiltersFormatter;
use App\Services\IndicatorService;
use Illuminate\Support\Facades\DB;
use App\Models\LocationType;

class CommunityIndexPageTest extends TestCase
{
    
    public function test_200_response(): void
    {
        
        $location = Location::inRandomOrder()->where('location_type_id', '!=', 9)->first();


        dump("location id: " . $location->id);

        $response = $this->get("/community-profiles/$location->id");

        $response->assertStatus(200);

    }

    public function test_invalid_location_id (): void
    {
        
        $location_id = 9999999;
        
        $response = $this->get("/community-profiles/$location_id");

        $response->assertStatus(404);
    }

    public function test_response_data(): void
    {   

        $location = Location::inRandomOrder()->firstOrFail();

        $response = $this->get("/community-profiles/$location->id");

         $response->assertInertia(function(Assert $page){

            $page->has('location', function(Assert $location){

                $location->hasAll(['id', 'name', 'fips', 'geopolitical_id']);

            });

            $page->has('location_geojson', fn(Assert $geojson)=>$geojson->hasAll(['type', 'features']));

            $page->has('indicators');

            $page->has('current_indicator_filters');

            $page->has('current_indicator_data');

            $page->has('asset_categories.0', function(Assert $category){
                
                $category->hasAll(['id', 'group_name', 'subcategories']);

            });

         });

    }

    public function test_response_data_with_indicator(){

        $location_has_indicator = DB::connection('supabase')->table('indicators.data')->select('location_id')->distinct()->pluck('location_id');

        $location = Location::whereIn('id', $location_has_indicator)->inRandomOrder()->firstOrFail();
        
        $indicator_id = $location->indicators->first()->id;

        $filters_unformatted = IndicatorService::queryIndicatorFilters($indicator_id);

        $filters = IndicatorFiltersFormatter::formatFilters($filters_unformatted);
        
        $breakdown = $filters['data']['breakdown'][0]['sub_breakdowns'][0]['id'];

        $format = $filters['data']['format'][0]['id'];     
        
        $response = $this->get("/community-profiles/$location->id?filter[breakdown][eq]=$breakdown&filter[format][eq]=$format&indicator=$indicator_id");

        $response->assertStatus(200);
        
        $response->assertInertia(function(Assert $page){

            $page->has('location', function(Assert $location){

                $location->hasAll(['id', 'name', 'fips', 'geopolitical_id']);

            });

            $page->has('location_geojson', fn(Assert $geojson)=>$geojson->hasAll(['type', 'features']));

            $page->has('indicators.0', fn(Assert $indicator)=>$indicator->hasAll(['id', 'name']));

            $page->has('current_indicator_filters', fn(Assert $filter)=>$filter->hasAll(['timeframe', 'location_type', 'format', 'breakdown']));

            $page->has('asset_categories.0', function(Assert $category){
                    
                    $category->hasAll(['id', 'group_name', 'subcategories']);
                    
                });

            $page->has('current_indicator_data.0', fn(Assert $data)=>$data->hasAll(['data', 'indicator_id', 'location_id','location_type', 'location', 'location_type_id', 'timeframe', 'breakdown', 'format']));

        });

    }

    public function test_domains_included_in_data_if_location_type_is_rankable(){


        $rankable_location_type = LocationType::where('has_ranking', true)->with('locations:id,location_type_id')->first();

        $location_id = $rankable_location_type->locations->first()->id;

        $response = $this->get("/community-profiles/$location_id");

        $response->assertInertia(function(Assert $page){

            $page->has('well_being_domains.0', fn(Assert $domain)=>$domain->hasAll(['id', 'name']));

            $page->where('location_has_ranking', true);

        });

    }

    public function test_location_has_ranking_is_false_when_location_type_has_no_ranking(){

        $rankable_location_type = LocationType::where('has_ranking', false)->with('locations:id,location_type_id')->first();

        $location_id = $rankable_location_type->locations->first()->id;

        $response = $this->get("/community-profiles/$location_id");

        $response->assertInertia(function(Assert $page){

            $page->where('location_has_ranking', false);
        
        });

    }

    public function test_response_speed(){

        $location_has_indicator = DB::connection('supabase')->table('indicators.data')->select('location_id')->distinct()->pluck('location_id');

        $location = Location::whereIn('id', $location_has_indicator)->inRandomOrder()->firstOrFail();
        
        $indicator_id = $location->indicators->first()->id;

        $filters_unformatted = IndicatorService::queryIndicatorFilters($indicator_id);

        $filters = IndicatorFiltersFormatter::formatFilters($filters_unformatted);
        
        $breakdown = $filters['data']['breakdown'][0]['sub_breakdowns'][0]['id'];

        $format = $filters['data']['format'][0]['id'];    
        
        $start = microtime(true);

        $this->get("/community-profiles/$location->id?filter[breakdown][eq]=$breakdown&filter[format][eq]=$format&indicator=$indicator_id");

        $duration = microtime(true) - $start;

        dump("response time: $duration");

        $this->assertLessThan(0.3, $duration, "Respone too slow. Response time: $duration");

    }
}
