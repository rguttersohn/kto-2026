<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class IndicatorInitialFiltersResource extends JsonResource
{
    /**
     * 
     * Handles the initial filters passed to the front end page load
     * 
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray(Request $request): array
    {
        $selectedFilters = [];

        foreach ($this->resource as $name => $conditions) {
            foreach ($conditions as $operator => $value) {
                $selectedFilters[] = [
                    'id' => (string) Str::uuid(),
                    'filterName' => [
                        'label' => ucfirst(str_replace('_', ' ', $name)), // Human-readable
                        'value' => $name,
                    ],
                    'operator' => [
                        'label' => match ($operator) {
                            'eq' => 'Equals',
                            'neq' => 'Not equal to',
                            'gt' => 'Greater than',
                            'gte' => 'Greater than or equal to',
                            'lt' => 'Less than',
                            'lte' => 'Less than or equal to',
                            'in' => 'In list',
                            'nin' => 'Not in list',
                            'null' => 'Is null',
                            'notnull' => 'Is not null',
                            default => ucfirst($operator),
                        },
                        'value' => $operator,
                    ],
                    'value' => [
                        'label' => is_array($value) ? implode(', ', $value) : (string) $value,
                        'value' => $value,
                    ],
                ];
            }
        }

        return $selectedFilters;
    }


}
