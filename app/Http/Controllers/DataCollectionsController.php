<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataCollection;
use Illuminate\Support\Facades\DB;

class DataCollectionsController extends Controller
{
    public function getCollections(){

        return DataCollection::select('name', 'slug')->get();
    }

    public function getCollection($collection_slug){

        return DataCollection::select('name', 'slug', 'description')
            ->where('slug', $collection_slug)
            ->get();
    }

    public function getCollectionData($collection_slug){

        // $results = DB::connection('supabase')->select("
        //     SELECT count(value)
        //     FROM collections.data_collections AS dc,
        //         jsonb_array_elements(dc.data)
        //     WHERE value->>'FRISKED_FLAG' = 'Y'
        // ", []);

        // $results_array = array_map(array: $results, callback: fn($result)=>json_decode($result->value));

        // return $results;
    }
}
