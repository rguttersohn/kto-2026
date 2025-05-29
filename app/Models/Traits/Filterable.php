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
        'like' => 'like',
        'null' => 'null',
        'notnull' => 'not null',
    ];

    #[Scope]

    protected function filter(Builder $query, array $filters){

        $aliases = $this->filter_aliases ?? [];

        $whitelist = $this->filter_whitelist ?? [];
        
        foreach($filters as $column => $conditions){

            $matched_column = $aliases[$column] ?? $column;
            
            $is_allowed = in_array($matched_column, $whitelist);

            if(!$is_allowed){
                continue;
            }
            
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

        return $query;
    }


}