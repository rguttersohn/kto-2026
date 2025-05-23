<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Collection extends Model
{
   
    protected $connection = 'supabase';

    protected $table = 'collections.collections';
   
    protected $fillable = [
        'name',
        'slug',
        'description'
    ];


    public function setNameAttribute($value)
    {
        if (isset($this->attributes['slug'])) {
            return;
        }

        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($this->attributes['name']);
    }

    public function data(){
        return $this->hasMany(DataCollection::class);
    }


}
