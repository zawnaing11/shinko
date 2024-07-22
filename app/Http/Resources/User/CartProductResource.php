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
            'price' => $this->calcPWithoutTax($this->price_tax, $this->tax_rate),
            'price_tax' => $this->price_tax,
            'list_price' => $this->list_price,
            'list_price_tax' => $this->calcTax($this->list_price, $this->tax_rate),
            'wholesale_price' => $this->wholesale_price,
            'wholesale_price_tax' => $this->calcTax($this->wholesale_price, $this->tax_rate),
        ];
    }
}
