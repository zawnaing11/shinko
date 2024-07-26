<?php

namespace App\Exports;

use App\Repositories\Company\ProductPriceRepository;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductPriceExport implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        return [
            '店舗ID',
            '店舗名',
            'JANコード',
            '商品名',
            '納価（税抜）',
            '税込価格',
        ];
    }

    public function collection()
    {
        $product_price_repository = new ProductPriceRepository();
        $base_products = $product_price_repository->all()
            ->where('company_admin_user_stores.company_admin_user_id', auth()->user()->id)
            ->orderBy('store_id', 'ASC')
            ->orderBy('base_products.jan_cd', 'ASC')
            ->get();

        if ($base_products !== null) {
            foreach ($base_products as $base_product) {
                $datas[] = [
                    $base_product->store_id,
                    $base_product->store_name,
                    $base_product->jan_cd,
                    $base_product->product_name,
                    $base_product->wholesale_price,
                    $base_product->price_tax ?: $base_product->list_price_tax_calc,
                ];
            }
        }

        return collect($datas ?? []);
    }
}
