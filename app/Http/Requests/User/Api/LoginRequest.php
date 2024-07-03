<?php

namespace App\Http\Requests\User\Api;

use App\Http\Requests\ApiFormRequest;

class LoginRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' =>'required',
            'password' => 'required',
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
            'email' => 'Eメールアドレス',
            'password' => 'パスワード',
        ];
    }

}
