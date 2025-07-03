<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetCategoriesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'group_name' => $this->name,
            'subcategories' => $this->whenLoaded('children', function(){

                return $this->children->map(fn($child)=>$child->makeHidden('parent_id'));
                
            })
        ];
    }
}
