<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    protected $order,$withProducts;

    public function __construct($order,$withProducts=false)
    {
        $this->order = $order;
        $this->withProducts = $withProducts;
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->order->id,
            'customer_id'=>$this->order->customer_id,
            'customer_name'=>$this->order->customer_name,
            'customer_email'=>$this->order->customer_email,
            'customer_phone'=>$this->order->customer_phone,
            'payment_status'=>$this->order->payment_status,
            'status'=>$this->order->status,
            'total'=>$this->order->total,
            'created_at'=>$this->order->created_at->format('d M Y H:i A'),
            'products'=>$this->withProducts?$this->order->products->map(fn($product)=>new ProductResource($product)):[]
        ];
    }
}
