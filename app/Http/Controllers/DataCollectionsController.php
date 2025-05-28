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

    public function getCollectionData($collection_slug){


        return Collection::select('id', 'name', 'slug')
            ->with(['data' => fn($query)=>$query->select('collection_id', 'data')])
            ->where('slug', $collection_slug)
            ->get();
        
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
