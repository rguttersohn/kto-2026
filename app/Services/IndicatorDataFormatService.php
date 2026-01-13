<?php

namespace App\Services;

use App\Models\DataFormat;
use Illuminate\Support\Collection;

class IndicatorDataFormatService {


    public static function queryDataFormats(array | null $data_format_ids):Collection{

        return DataFormat::when($data_format_ids, fn($query)=>$query->whereIn('id', $data_format_ids))
            ->get();
    }
}