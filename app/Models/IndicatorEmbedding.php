<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Filterable;

class IndicatorEmbedding extends Model
{
    protected $connection = 'supabase';
    protected $table = 'indicators.indicator_embeddings';

    protected $fillable = [
        'indicator_id',
        'embedding'
    ];


    protected array $filter_whitelist = ['indicator_id', 'domain_id', 'category_id'];

    protected array $filter_aliases = [
        'indicator' => 'indicator_id',
        'domain' => 'domain_id',
        'category' => 'category_id'
    ];

    public function indicators(){
        return $this->belongsTo(Indicator::class);
    }


}
