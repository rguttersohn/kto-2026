<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\PublishedScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;


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

    protected function getDataHeaders(Builder $query, int $collection_id){

        $query
            ->selectRaw('distinct jsonb_object_keys(data.data) as headers')
            ->where('collection_id', $collection_id);

    }


}
