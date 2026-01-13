<?php

namespace App\Services;

use App\Models\Breakdown;
use Illuminate\Support\Collection;

class IndicatorBreakdownsService{


    public static function queryBreakdowns(array | null $breakdown_ids = null):Collection{

        return Breakdown::when($breakdown_ids, fn($query)=>$query->whereIn('id', $breakdown_ids))
            ->with('subBreakdowns')
            ->get();

    }
}