<?php

namespace App\Http\Requests\User\Api;

use App\Http\Requests\ApiFormRequest;

class UserRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'password' => [
                'required',
                'regex:' . config('const.password_regex'),
                function ($attribute, $value, $fail) {
                    if ($this->user->email == $value) {
                        return $fail('メールアドレスと一致しないようにしてください。');
                    }
                },
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
            'password' => 'パスワード',
        ];
    }

    public function messages()
    {
        return [
            'password.regex' => '大文字・小文字を含めた半角英数字記号を、6文字以上60文字以内で入力してください。'
        ];
    }
}
