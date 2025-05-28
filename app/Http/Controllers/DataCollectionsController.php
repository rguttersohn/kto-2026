<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataCollection;
use App\Models\Collection;
use App\Support\StandardizeResponse;

class DataCollectionsController extends Controller
{
    public function getCollections(){

        return Collection::select('id','name', 'slug')->get();
    }

    public function getCollection($collection_slug){

        return Collection::select('id','name', 'slug', 'description')
            ->where('slug', $collection_slug)
            ->get();
    }

    public function getCollectionData(Request $request, $collection_slug){

        $collection = Collection::select('id','name', 'slug')->where('slug', $collection_slug)->firstOrFail();

        $requests = $request->query();

        $request_filters = array_filter($requests, fn($request)=>$request !== 'offset' && $request !== 'limit', ARRAY_FILTER_USE_KEY);
        
        $filters_list_unformatted = DataCollection::getFilters($collection->id)->get();

        $filters_list = DataCollection::formatFilters($filters_list_unformatted);
        
        $unverified_filters = array_diff(array_keys($request_filters), $filters_list);
        
        if(!empty($unverified_filters)){

            $unverified_filters_string = implode(',', $unverified_filters);

            return StandardizeResponse::APIResponse(
                error_status: true,
                error_message: "Unknown filters: $unverified_filters_string",
                status_code: 400
            );
        }

        $offset = $request->has('offset') ? $request->offset : 0;

        $limit = $request->has('limit') ? $request->limit : 3000;

        $data = DataCollection::select('id','data')
            ->where('collection_id', $collection->id)
            ->offset($offset)
            ->limit($limit);

        foreach($request_filters as $key=>$value){
            
            $data->whereRaw("data->>? = ?", [$key, $value]);
        
        }

        
        $data_result = $data->get();

        return StandardizeResponse::APIResponse(
            data: [
                'collection' => $collection,
                'data' => $data_result
            ]
            );
        
    }

    public function getCollectionFilters($collection_slug){

        $collection = Collection::select('id', 'name', 'slug')
            ->where('slug', $collection_slug)
            ->firstOrFail();

        $filters = DataCollection::getFilters($collection->id)->get();

        $filters_formatted = DataCollection::formatFilters($filters);

        return StandardizeResponse::APIResponse(
                data: [
                        'collection' => $collection, 
                        'filters' => $filters_formatted
                ]
            );
    }
}
