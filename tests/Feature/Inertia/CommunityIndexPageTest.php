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

class CommunityIndexPageTest extends TestCase
{
    
    public function test_200_response(): void
    {
        
        $location = Location::inRandomOrder()->firstOrFail();

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
}
