<?php

namespace App\Http\Requests\Company;

use App\Http\Requests\BaseFormRequest;

class ProductPriceRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'price_tax' => [
                'required',
                'integer',
                'min:0',
                'max:' . config('const.default_integer_maxvalue'),
            ],
        ];
    }

    public function attributes()
    {
        return [
            'price_tax' => '販売価格（税込）',
        ];
    }
}
