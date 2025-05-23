<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataCollection extends Model
{
    protected $connection = 'supabase';

    protected $table = 'collections.data';

    protected $fill = [
        'geometry',
        'data'
    ];

    protected $casts = [
        'data' => 'array'
    ];


    public function collection(){

        return $this->belongsTo(Collection::class);
        
    }


}
