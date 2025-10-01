<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndicatorCategory extends Model
{
    protected $connection = 'supabase';

    protected $table = 'indicators.categories';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'name',
        'domain_id'
    ];

  
    public function indicators()
    {
        return $this->hasMany(Indicator::class, 'category_id', 'id');
    }

    public function domain(){
        return $this->belongsTo(Domain::class, 'domain_id', 'id');
    }


}
