<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetSchema extends Model
{
    protected $connection = 'supabase';

    protected $table = 'assets.asset_schema';

    protected $casts = [
        'schema' => 'array'
    ];

    protected $fillable = [
        'asset_category_id',
        'schema'
    ];

    public function assetCategory():BelongsTo{

        return $this->belongsTo(AssetCategory::class, 'asset_category_id', 'id');

    }
}
