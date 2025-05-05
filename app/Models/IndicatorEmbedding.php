<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public function getSimilarIndicators(string $input_vector, float $threshold){

        $results = DB::connection('supabase')->select("
            SELECT i.*, e.embedding <=> ?::vector AS distance
            FROM indicators.indicator_embeddings e
            JOIN indicators.indicators i ON i.id = e.indicator_id
            WHERE e.embedding <=> ?::vector < ?
            ORDER BY distance ASC
            LIMIT 5
        ", [$input_vector, $input_vector, $threshold]);

        return $results;
    }


}
