<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\PublishedScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;


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


}
