<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use App\Events\IndicatorSaved;
use Illuminate\Database\Eloquent\Attributes\Scope;
use App\Models\IndicatorData;
use App\Models\Scopes\PublishedScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use App\Policies\IndicatorPolicy;
use App\Models\Traits\HasAdminPublishPolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Traits\Filterable;
use Mews\Purifier\Facades\Purifier;

#[ScopedBy([PublishedScope::class])]
#[UsePolicy(IndicatorPolicy::class)]

class Indicator extends Model
{
    use Searchable, HasAdminPublishPolicy, HasFactory, Filterable;

    protected $connection = 'supabase';

    protected $table = 'indicators.indicators';

    protected $fillable = [
        'name',
        'category_id',
        'definition',
        'source',
        'note',
        'is_published',
        'data_flag'
    ];

    protected $casts = [
        'is_published' => 'boolean'
    ];

    protected $dispatchesEvents = [
        'saved' => IndicatorSaved::class
    ];

    /**
     * 
     * Filter stuff
     * 
     */

    protected array $filter_whitelist = ['indicators.indicators.id', 'domain_id', 'category_id'];

    protected array $filter_aliases = [
        'indicator' => 'indicators.indicators.id',
        'domain' => 'domain_id',
        'category' => 'category_id'
    ];

    /**
     * 
     * Relations
     * 
     * 
     */

    public function data(){
        return $this->hasMany(IndicatorData::class, 'indicator_id');
    }

    public function category()
    {
        return $this->belongsTo(IndicatorCategory::class, 'category_id', 'id');
    }

    public function defaultFilters(){

        return $this->hasOne(IndicatorDefaultFilter::class, 'indicator_id');
        
    }

    #[Scope]
    protected function joinParents(Builder $query){

        $query
            ->join('indicators.categories', 'indicators.indicators.category_id', 'indicators.categories.id')
            ->join('domains.domains', 'indicators.categories.domain_id', 'domains.domains.id')
            ->select(
                'indicators.indicators.*',
                'domains.domains.id as domain_id',
                'domains.domains.name as domain',
                'indicators.categories.id as category_id',
                'indicators.categories.name as category',
                'indicators.categories.domain_id as category_domain_id'
            );

    }

    /**
     * 
     * Scout methods
     */


    public function searchableAs()
    {
        return config('scout.algolia.indices.indicators');
    }

    public function toSearchableArray()
    {
        
        return [
            'id' => $this->id,
            'name' => $this->name,
            'definition' => $this->definition,
            'is_published' => $this->is_published,
            'category' => $this->category->name,
            'category_id' => $this->category->id,
            'domain' => $this->category->domain->name,
            'domain_id' => $this->category->domain->id,
            'note' => $this->note,
            'source' => $this->source
        ];

    }


    protected static function booted(){

        static::saving(function ($indicator) {
            $indicator->source = Purifier::clean($indicator->source, 'a[href|target],p,br,strong,em,ul,ol,li');
            $indicator->note = Purifier::clean($indicator->note, 'a[href|target],p,br,strong,em,ul,ol,li');
        });

    }

}
