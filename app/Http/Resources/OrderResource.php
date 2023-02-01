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
            'customer_id'=>$this->customer_id,
            'customer_name'=>$this->customer_name,
            'customer_email'=>$this->customer_email,
            'customer_phone'=>$this->customer_phone,
            'payment_status'=>$this->payment_status,
            'status'=>$this->status,
            'total'=>$this->total,
            'created_at'=>$this->created_at->format('d M Y H:i A'),
            'products'=>$this->relationLoaded('products')?$this->products->map(fn($product)=>new ProductResource($product)):[]
        ];
    }
}
