<?php

namespace App\Http\Resources\User;

use App\Traits\TaxTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartProductResource extends JsonResource
{
    use TaxTrait;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'jan_cd' => $this->jan_cd,
            'quantity' => $this->quantity,
            'price_tax' => $this->price_tax,
            'list_price' => $this->list_price,
            'list_price_tax' => $this->calcTax($this->list_price, $this->tax_rate),
        ];
    }
}
