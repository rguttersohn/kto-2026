<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;


class IndicatorEmbedding extends Model
{
    protected $connection = 'supabase';
    protected $table = 'indicators.indicator_embeddings';

    protected $fillable = [
        'indicator_id',
        'embedding'
    ];

    public function indicators(){
        return $this->belongsTo(Indicator::class);
    }

    #[Scope]
    protected function joinParents(Builder $query){

        $query->join('indicators.indicators', 'indicators.indicator_embeddings.indicator_id', 'indicators.indicators.id')
            ->join('indicators.categories', 'indicators.indicators.category_id', 'indicators.categories.id')
            ->join('domains.domains', 'indicators.categories.domain_id', 'domains.domains.id');
    
    }




}
