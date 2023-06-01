<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
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
            'order_id'=>$this->order_id,
            'price'=>$this->price,
            'quantity'=>$this->quantity,
            'subtotal'=>$this->subtotal,
            'product_id'=>$this->product_id,
            'products'=>ProductResource::collection($this->whenLoaded('products')),

        ];
    }
}
