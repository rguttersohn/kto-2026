<?php


namespace App\Support;


use Illuminate\Support\Str;

/**
 * 
 * A class of static functions that help with parsing data retrieved from postgres
 * 
 */
class Postgres {

    public static function parsePostgresArray(string $postgres_array):array{

        return Str::of($postgres_array)
                ->trim('{}')
                ->explode(',')
                ->map(fn ($val) => (int) $val)
                ->toArray();
    }
    
}