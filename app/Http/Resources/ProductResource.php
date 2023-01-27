<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    protected $product;

    public function __construct($product)
    {
        $this->product = $product;
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
            'name'=>$this->product->name,
            'price'=>$this->product->price,
            'quantity'=>$this->product->quantity,
            'total'=>$this->product->total,
        ];
    }
}
