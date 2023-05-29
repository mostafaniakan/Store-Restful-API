<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\ApiResponse;
class CategoryResource extends JsonResource
{
    use ApiResponse;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'parent_id'=>$this->parent_id,
            'description'=>$this->description,
            'children'=>CategoryResource::collection($this->whenLoaded('children')),
            'parent'=>new CategoryResource($this->whenLoaded('parent')),
            'products' =>
                ProductResource::collection($this->whenLoaded('products', function () {
                    return $this->products->load('images');
                })),
        ];
    }
}
