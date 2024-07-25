<?php

namespace App\Traits;

trait CalcTrait
{
    public function getWithTax(mixed $price, int $tax_rate)
    {
        if ($price === null) {
            return null;
        }
        return $price + round($price * ($tax_rate / 100));
    }

    public function getPriceTax(int $price_tax, int $list_price_tax, int $list_price, float $tax_rate)
    {
        if ($price_tax) {
            $price_tax = $price_tax;

        } else if ($list_price_tax) {
            $price_tax = $list_price_tax;

        } else {
            $price_tax = $this->getWithTax($list_price, $tax_rate);
        }

        return $price_tax;
    }
}
