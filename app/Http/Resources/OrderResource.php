<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'user_id'=>$this->user_id,
            'status'=>$this->status,
            'paying_amount'=>$this->paying_amount,
            'payment_status'=>$this->payment_status,
            'order_item'=> OrderItemResource::collection($this->whenLoaded('orders_item',function (){
                return $this->orders_item->load('products');
            })),

        ];
    }
}
