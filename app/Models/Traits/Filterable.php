<?php
namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

trait Filterable{

    protected array $operators = [
        'eq' => '=',
        'neq' => '!=',
        'gt' => '>',
        'gte' => '>=',
        'lt' => '<',
        'lte' => '<=',
        'in' => 'in',
        'nin' => 'not in',
        'null' => 'null',
        'notnull' => 'not null',
    ];

    protected function parseFilterKey(string $key): array
    {
        return str_contains($key, '.') ? explode('.', $key, 2) : [$key, null];
    }

    #[Scope]

    protected function filter(Builder $query, array $filters):Builder{

        $aliases = $this->filter_aliases ?? [];

        $whitelist = $this->filter_whitelist ?? [];

        $jsonb_columns = $this->jsonb_columns ?? [];
        
        foreach($filters as $column => $conditions){

            [$base_column, $subkey] = $this->parseFilterKey($column);

            $matched_column = $aliases[$base_column] ?? $base_column;
            
            $is_allowed = in_array($matched_column, $whitelist);

            if(!$is_allowed){
                continue;
            }

            $is_jsonb = in_array($matched_column, $jsonb_columns);

            if($is_jsonb){

                foreach($conditions as $operator => $value){
                    
                    $sqlOperator = $this->operators[$operator] ?? '=';
                    
                    match ($sqlOperator) {
                        'null', 'not null' => $query->whereRaw("$matched_column->>'$subkey' $sqlOperator"),
                        'in', 'not in' => $query->whereRaw(
                            "$matched_column->>'$subkey' " . strtoupper($sqlOperator) . " (" .
                            implode(', ', array_fill(0, count(Arr::wrap($value)), '?')) . ")",
                            Arr::wrap($value)
                        ),
                        default => $query->whereRaw("$matched_column->>'$subkey' $sqlOperator ?", [$value]),
                    };

                    continue;
    
                }

            } else {

                foreach($conditions as $operator => $value){

                    $sqlOperator = $this->operators[$operator] ?? '=';
                    
                    match ($sqlOperator) {
                        'null' => $query->whereNull($matched_column),
                        'not null' => $query->whereNotNull($matched_column),
                        'in' => $query->whereIn($matched_column, Arr::wrap($value)),
                        'not in' => $query->whereNotIn($matched_column, Arr::wrap($value)),
                        default => $query->where($matched_column, $sqlOperator, $value),
                    };
    
                }

            }
            
            
        }

        return $query;
    }


}