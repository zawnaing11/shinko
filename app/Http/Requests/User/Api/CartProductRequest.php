<?php

namespace App\Http\Requests\User\Api;

use App\Http\Requests\ApiFormRequest;

class CartProductRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'jan_cd' => [
                'required',
                'numeric',
            ],
            'quantity' => [
                'required',
                'integer',
                'min:0',
                'max:255',
            ],
        ];
    }

    /**
    * バリデーションエラーのカスタム属性の取得
    *
    * @return array
    */
    public function attributes()
    {
        return [
            'jan_cd' => 'JANコード',
            'quantity' => '数量',
        ];
    }
}
