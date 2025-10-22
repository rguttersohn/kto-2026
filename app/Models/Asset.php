<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use App\Support\PostGIS;
use App\Models\Traits\Filterable;
use App\Models\Traits\SpatialQueryable;
use App\Models\Traits\HasAdminPublishPolicy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\PublishedScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ScopedBy(PublishedScope::class)]

class Asset extends Model
{
    use HasFactory, Filterable, SpatialQueryable, HasAdminPublishPolicy;
    
    protected $connection = 'supabase';

    protected $table = 'assets.assets';

    protected $fillable = [
        'data',
        'geometry',
        'asset_category_id',
        'is_published',
        'data',
        'import_id'
    ];

    protected $filter_aliases = [
        'category' => 'asset_category_id'
    ];

    protected $filter_whitelist = [
        'asset_category_id'
    ];

    protected $casts = [
        'data' => 'array'
    ];
   
    public function assetCategory():BelongsTo{

        return $this->belongsTo(AssetCategory::class);
    }

    public function import():BelongsTo{

        return $this->belongsTo(Import::class);
    }

    #[Scope]

    protected function assetsByCategoryID(Builder $query, array $filters, bool $wants_geojson){

        $query
            ->filter($filters)
            ->when(!$wants_geojson, fn($query)=>$query->selectRaw(PostGIS::getLongLatFromPoint('assets.assets', 'geometry')))
            ->when($wants_geojson, fn($query)=>$query->selectRaw(PostGIS::getGeoJSON('assets.assets', 'geometry')));
    }


}
