<?php

namespace App\Traits;

trait TaxTrait
{
    public function calcTax(mixed $price, int $tax_rate)
    {
        if ($price === null) {
            return null;
        }
        return $price + round($price * ($tax_rate / 100));
    }
}
