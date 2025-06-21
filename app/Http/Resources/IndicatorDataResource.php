<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\Model;

class IndicatorDataResource extends JsonResource
{
    
    
    
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
            $data_filtered = $this->data->map(function ($item) {
                return $item->makeHidden('indicator_id');
            });

            return [

                'id' => $this->id,
                'name' => $this->name,
                'slug' => $this->slug,
                'data' => $data_filtered

            ];
        
    }
}
