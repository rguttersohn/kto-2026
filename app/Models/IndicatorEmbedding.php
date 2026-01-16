<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Filterable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Pgvector\Laravel\HasNeighbors;
use Pgvector\Laravel\Vector;
use Illuminate\Database\Eloquent\Builder;

class IndicatorEmbedding extends Model
{   

    use HasNeighbors, Filterable;

    protected $connection = 'supabase';
    protected $table = 'indicators.indicator_embeddings';

    protected $fillable = [
        'indicator_id',
        'embedding'
    ];

    protected $casts = ['embedding' => Vector::class];

    /**
     * 
     * Filter stuff
     * 
     */

    protected array $filter_whitelist = ['indicator_id', 'domain_id', 'category_id'];

    protected array $filter_aliases = [
        'indicator' => 'indicator_id',
        'domain' => 'domain_id',
        'category' => 'category_id'
    ];

    public function indicators(){
        return $this->belongsTo(Indicator::class);
    }


    #[Scope]
    protected function joinParents(Builder $query){

        $query
            ->join('indicators.indicators', 'indicators.indicator_embeddings.indicator_id', 'indicators.indicators.id')
            ->join('indicators.categories', 'indicators.indicators.category_id', 'indicators.categories.id')
            ->join('domains.domains', 'indicators.categories.domain_id', 'domains.domains.id')
            ->addSelect(
                    'indicators.indicators.id as id',
                    'indicators.indicators.name as name',
                    'indicators.indicators.definition as definition',
                    'indicators.categories.id as category_id', 
                    'indicators.categories.name as category',
                    'domains.domains.id as domain_id',
                    'domains.domains.name as domain'
                );

    }    


}
