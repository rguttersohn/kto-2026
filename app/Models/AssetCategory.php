<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssetCategory extends Model
{   

    use HasFactory;
    
    protected $connection = 'supabase';

    protected $table = 'assets.asset_categories';

    protected $fillable = [
        'name',
        'slug'
    ];


    public function setNameAttribute($value)
    {
        if (isset($this->attributes['slug'])) {
            return;
        }

        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($this->attributes['name']);
    }

    public function assets(){
        return $this->hasMany(Asset::class);
    }


}
