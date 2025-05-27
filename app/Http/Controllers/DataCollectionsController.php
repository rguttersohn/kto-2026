<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataCollection;
use App\Models\Collection;

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
}
