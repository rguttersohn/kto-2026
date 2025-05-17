<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $connection = 'supabase';

    protected $table = 'assets.assets';

    protected $fillable = [
        'description',
        'location',
        'category_id'
    ];

    public function AssetCategory(){

        return $this->belongsTo(AssetCategory::class);
    }
}
