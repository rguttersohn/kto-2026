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
    
        return $this->resource;
    }


}
