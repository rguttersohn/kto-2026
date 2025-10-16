<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Traits\HasAdminPublishPolicy;

class AssetCategory extends Model
{   

    use HasFactory, HasAdminPublishPolicy;
    
    protected $connection = 'supabase';

    protected $table = 'assets.asset_categories';

    protected $fillable = [
        'name',
        'parent_id',
        'is_published'
    ];


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
        return $query->select('id','name', 'parent_id');
    }

}
