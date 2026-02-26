<?php

namespace App\Services;

use App\Models\Breakdown;
use Illuminate\Support\Collection;

class IndicatorBreakdownsService{


    public static function queryBreakdowns(array | null $breakdown_ids = null):Collection{

        return Breakdown::whereNull('parent_id')
            ->when($breakdown_ids, fn($q) =>
                $q->where(fn($q) =>
                    // Either this top-level breakdown is directly in the data...
                    $q->whereIn('id', $breakdown_ids)
                    // ...or it has children that are
                    ->orWhereHas('subBreakdowns', fn($q) =>
                        $q->whereIn('id', $breakdown_ids)
                    )
                ))   
            ->with(['subBreakdowns' => fn($q) =>
                $q->when($breakdown_ids, fn($q) =>
                    $q->whereIn('id', $breakdown_ids)
                )
            ])
            ->get();

    }
}