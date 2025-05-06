<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CategoryEmbedding extends Model
{
    
    protected $connection = 'supabase';
    protected $table = 'indicators.category_embeddings';

    protected $fillable = [
        'category_id',
        'embedding'
    ];

    public function categories(){
        return $this->belongsTo(Category::class);
    }

    public function getSimilarCategory(string $input_vector, float $threshold){

        $results = DB::connection('supabase')->select("
            SELECT c.*, ce.embedding <=> ?::vector AS distance
            FROM indicators.category_embeddings as ce
            JOIN indicators.categories as c ON c.id =ce.category_id
            WHERE ce.embedding <=> ?::vector < ?
            ORDER BY distance ASC
            LIMIT 1
        ", [$input_vector, $input_vector, $threshold]);

        return $results;
    }


}
