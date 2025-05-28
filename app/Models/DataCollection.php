<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\PublishedScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;


#[ScopedBy([PublishedScope::class])]

class DataCollection extends Model
{
    protected $connection = 'supabase';

    protected $table = 'collections.data';

    protected $fill = [
        'geometry',
        'data',
        'is_published'
    ];

    protected $casts = [
        'data' => 'array'
    ];


    public function collection(){

        return $this->belongsTo(Collection::class);
        
    }

    #[Scope]

    protected function getFilters(Builder $query, int $collection_id){

        $query
            ->selectRaw('distinct jsonb_object_keys(data.data) as filters')
            ->where('collection_id', $collection_id);

    }

    public static function formatFilters(Collection $filters_unformatted){
        
        $filters_array = $filters_unformatted->toArray();

        return array_map(function($filter){

            return $filter['filters'];

        }, $filters_array);

    
    }


}
