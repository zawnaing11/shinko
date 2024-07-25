<?php

namespace App\Http\Resources\User;

use App\Traits\CalcTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartProductResource extends JsonResource
{
    use CalcTrait;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $price_tax = $this->getPriceTax($this->price_tax, $this->list_price_tax, $this->list_price, $this->tax_rate);
        return [
            'jan_cd' => $this->jan_cd,
            'quantity' => $this->quantity,
            'price_tax' => $price_tax,
        ];
    }
}
