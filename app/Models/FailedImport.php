<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class FailedImport extends Model
{
    
    protected $table = 'app.failed_import_rows';

    protected $casts = [
        'data' => 'array'
    ];


}
