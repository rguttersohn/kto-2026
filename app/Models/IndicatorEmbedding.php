<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
