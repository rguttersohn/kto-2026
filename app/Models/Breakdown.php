<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Breakdown extends Model
{
    protected $connection = 'supabase';

    protected $table = 'indicators.breakdowns';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'name',
        'parent_id',
    ];

    public function breakdowns()
    {
        return $this->belongsTo(Breakdown::class, 'parent_id', 'id');
    }

    public function subBreakdowns()
    {
        return $this->hasMany(Breakdown::class, 'parent_id', 'id');
    }
    
}
