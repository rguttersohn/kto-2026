<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\GeometryTypes;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\ValidLocationScope;


#[ScopedBy([ValidLocationScope::class])]

class Geometry extends Model
{
    protected $connection = 'supabase';

    protected $table = 'geometries';

    protected $fillable = [
        'id',
        'created_at',
        'updated-at',
        'type',
        'geometry',
        'valid_starting_on',
        'valid_ending_on' 
    ];

    protected $casts = [
        'type' => GeometryTypes::class
    ];

    public function locations():BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }


}
