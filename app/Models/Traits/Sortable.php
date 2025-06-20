<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;

trait Sortable
{   

    #[Scope]
    protected function sort(Builder $query, array $sorts): Builder
    {
        $aliases = $this->sort_aliases ?? [];
        $whitelist = $this->sort_whitelist ?? [];

        foreach ($sorts as $column => $direction) {

            $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

            $matched_column = $aliases[$column] ?? $column;

            if(in_array($matched_column, $whitelist)) {
                $query->orderBy($matched_column, $direction);
            }
        }

        return $query;
    }
}
