<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IndicatorResource extends JsonResource
{
    public function toArray($request){

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'definition' => $this->definition,
            'source' => $this->source,
            'note' => $this->note,
        ];
        
    }
}
