<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DataFormat extends Model
{
    protected $connection = 'supabase';

    protected $table = 'indicators.data_formats';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'name',
    ];

}
