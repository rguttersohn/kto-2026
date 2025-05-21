<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use App\Support\PostGIS;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AssetCategory extends Model
{   

    use HasFactory;
    
    protected $connection = 'supabase';

    protected $table = 'assets.asset_categories';

    protected $fillable = [
        'name',
        'slug',
        'parent_id'
    ];


    public function setNameAttribute($value)
    {
        if (isset($this->attributes['slug'])) {
            return;
        }

        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($this->attributes['name']);
    }

    public function children(){
        return $this->hasMany(AssetCategory::class, 'parent_id','id');
    }


    public function parent(){
        return $this->belongsTo(AssetCategory::class, 'parent_id', 'id');
    }

    public function assets(){
        return $this->hasMany(Asset::class, 'asset_category_id', 'id');
    }

    #[Scope]

    protected function defaultSelects(Builder $query){
        return $query->select('id','name', 'slug', 'parent_id');
    }

}
