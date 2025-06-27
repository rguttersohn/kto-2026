<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
                    'name' => $name,
                    'operator' => $operator,
                    'value' => (int) $value,
                ];
            }
        }

        return $selectedFilters;
    
    }
}
