<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asset extends Model
{
    use HasFactory;
    
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
