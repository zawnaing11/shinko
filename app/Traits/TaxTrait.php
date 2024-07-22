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

    public function calcPWithoutTax(mixed $price_tax, int $tax_rate)
    {
        if ($price_tax === null) {
            return null;
        }
        return $price_tax - round($price_tax * ($tax_rate / 100) / (1 + $tax_rate / 100));
    }
}
