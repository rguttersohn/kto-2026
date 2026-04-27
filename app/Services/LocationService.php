<?php
namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use App\Models\LocationType;
use Illuminate\Database\Eloquent\Model;
use App\Models\Location;
use App\Models\Scopes\UninhabitedLocationScope;
use App\Support\PostGIS;
use App\Support\PostGres;
use App\Services\IndicatorService;
use App\Support\IndicatorFiltersFormatter;

class LocationService {

    public static function queryAllLocationTypes(array | null $location_type_ids = null, bool $wants_locations = false, array | null $filters = null):Collection{

        return LocationType::when($location_type_ids, fn($query)=>$query->whereIn('id', $location_type_ids))
            ->when($filters, fn($query)=>$query->filter($filters))
            ->when($wants_locations, fn($query)=> $query->with('locations'))
            ->get();

    }

    public static function queryLocationsByLocationType(int $location_type_id, ?bool $wants_geojson = false):Collection | null{
        return Location::select('location_type_id','name','locations.id','fips','district_id','is_uninhabited')
            ->where('location_type_id', $location_type_id)
            ->when($wants_geojson, function($query){

                $query->join('locations.geometries as geo', 'locations.id', 'geo.location_id')
                    ->selectRaw(PostGIS::getSimplifiedGeoJSON('geo','geometry'))
                    ->withoutGlobalScope(UninhabitedLocationScope::class);

            })
            ->get();
    }


    public static function queryLocation(int $location_id, ?bool $wants_geojson = false):Model | null{
        
        return Location::where('locations.id', $location_id)
            ->when($wants_geojson, function($query){

                $query->join('locations.geometries as geo', 'locations.id', 'geo.location_id','is_uninhabited')
                    ->selectRaw(PostGIS::getSimplifiedGeoJSON('geo','geometry'));

            })
            ->first();
    }

    public static function queryIsLocationTypeRanked(int $location_type_id): bool{

        $location_type = LocationType::where('id', $location_type_id)->first();

        return $location_type->has_ranking;
    }

    public static function queryLocationTypeWithLocation($location_type_id, bool $wants_geojson):Model{

        return LocationType::select('id', 'name', 'plural_name','scope', 'classification')
            ->with(['locations' => function($query)use($wants_geojson){
                $query->select('location_type_id', 'name','locations.id','fips','district_id','is_uninhabited')
                    ->when($wants_geojson, function($query){
                        $query->join('locations.geometries as geo', 'locations.id', 'geo.location_id')
                            ->selectRaw(PostGIS::getSimplifiedGeoJSON('geo','geometry'))
                            ->withoutGlobalScope(UninhabitedLocationScope::class);
                    });
            }])
            ->where('id', $location_type_id)
            ->first();
    }

    public static function queryLocationIndicators(Location $location, array | null $filters = null):Location{

        return $location->load(['indicators' => function($query)use($filters){

            $query->joinParents()
                ->when($filters, fn($query)=>$query->filter($filters));

        }]);

    }

    public static function queryLocationIndicatorsWithData(Location $location, array | null $filters = null):Location{
        
        $has_indicator_filter = array_key_exists('indicator', $filters);

        $location->load(['indicators' => function($query)use($has_indicator_filter, $filters){

            $query
                ->joinParents()
                ->filter($filters)
                ->when(!$has_indicator_filter, fn($query)=>$query->where('profile_default', true))
                ->with(['filterIDs', 'defaultFilters']);
                
        }]);

        $location->indicators->each(function($indicator)use($location){

            $timeframes = PostGres::parsePostgresArray($indicator->filterIDs->first()->timeframe);
            $breakdown_ids = PostGres::parsePostgresArray($indicator->filterIDs->first()->breakdown);
            $location_type_ids = PostGres::parsePostgresArray($indicator->filterIDs->first()->location_type);
            $data_format_ids = Postgres::parsePostgresArray($indicator->filterIDs->first()->format);

            $indicator->setRelation('filters',[
                'timeframe' => collect($timeframes),
                'breakdown' => IndicatorBreakdownsService::queryBreakdowns($breakdown_ids),
                'location_type' => LocationService::queryAllLocationTypes($location_type_ids, true),
                'format' => IndicatorDataFormatService::queryDataFormats($data_format_ids)    
            ]);

            $excluded_default_filters = $indicator->visualization_type?->excludedDefaultFilters() ?? ['timeframe'];
            $default_filters = $indicator->defaultfilters?->toArray() ?? [];

            $selected_filters_unformatted = IndicatorFiltersFormatter::mergeWithDefaultFilters(
                $indicator->filters,
                [
                    'location' => [
                        'eq' => $location->id
                    ]
                ],
                $excluded_default_filters,
                $default_filters
            );

            $selected_filters = IndicatorFiltersFormatter::toSelectedFilters($selected_filters_unformatted, $indicator->filters);

            $indicator->setRelation('selected_filters', $selected_filters);

            $indicator->setRelation('data', IndicatorService::queryData(
                    indicator_id: $indicator->id, 
                    filters: $selected_filters_unformatted,
                ));
            

        });


        return $location;

    }
        
}