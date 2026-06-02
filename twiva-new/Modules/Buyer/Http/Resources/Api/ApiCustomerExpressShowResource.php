<?php

namespace Modules\Buyer\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Buyer\Entities\Buyer;
use Modules\User\Entities\User;

class ApiCustomerExpressShowResource extends JsonResource
{
    public function toArray($request)
    {
        $name = Buyer::splitName($this->name);
        return [
            "id" => $this->id,
            "customer_name" => $this->buyer->name,
            "customer_id" => $this->buyer->id,
            "status" => $this->status,
            "products_count" => $this->products_count,
            "order_id" => $this->order_id,
            "amount" => $this->calculateAmount(),
            "products" => $this->listProducts(),
            "created_at" => $this->created_at,
            "link" => $this->buyer_link,
        ];
    }

    private function calculateAmount()
    {
        $amount = 0;
        if ($this->user->type == User::InfluencerType) {
            foreach ($this->products as $product) {
                $amount += $product->quantity * $product->influencerProduct?->product_price;
            }
        } elseif ($this->user->type == User::BusinessType) {
            foreach ($this->products as $product) {
                $amount += $product->quantity * $product?->businessProduct?->product_price;
            }
        }
        return $amount;
    }

    private function listProducts()
    {
        $products = collect();
        if ($this->user->type == User::InfluencerType) {
            foreach ($this->products as $product) {
                $product->influencerProduct->quantity = $product->quantity;
                $products->push($product->influencerProduct);
            }
        } elseif ($this->user->type == User::BusinessType) {
            foreach ($this->products as $product) {
                $product->businessProduct->quantity = $product->quantity;
                $products->push($product->businessProduct);
            }
        }
        return $products;
    }
}
