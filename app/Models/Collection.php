<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Scopes\PublishedScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

#[ScopedBy([PublishedScope::class])]

class Collection extends Model
{
   
    protected $connection = 'supabase';

    protected $table = 'collections.collections';
   
    protected $fillable = [
        'name',
        'description',
        'is_published'
    ];

    public function data(){
        return $this->hasMany(DataCollection::class);
    }

    #[Scope]

    protected function withDataDetails(
        Builder $query,
        int $offset,
        int $limit,
        array $filters
        ){
        
        dd($filters);

        $query->with(['data' => function($query)use($offset, $limit){

            $query->select('id', 'collection_id', 'data')
                ->offset($offset)
                ->limit($limit);

        }]);
    }



}
