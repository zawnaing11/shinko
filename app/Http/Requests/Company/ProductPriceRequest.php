<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductPriceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'price' => [
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
            'price' => '税抜販売価格',
        ];
    }
}
