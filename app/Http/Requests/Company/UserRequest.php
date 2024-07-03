<?php

namespace App\Http\Requests\Company;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email:rfc,dns',
                Rule::unique('users', 'email')->ignore($this->user),
            ],
            'name' => [
                'required',
                'max:' . config('const.default_text_maxlength'),
            ],
            'password' => [
                'regex:' . config('const.password_regex'),
                function ($attribute, $value, $fail) {
                    if ($this->request->get('email') == $value) {
                        return $fail('メールアドレスと一致しないようにしてください。');
                    }
                },
                'confirmed'
            ],
            'is_active' => [
                'required',
                'in:' . implode(',', array_keys(config('const.is_active'))),
            ],
        ];
    }

    public function attributes()
    {
        return [
            'email' => 'Eメールアドレス',
            'password' => 'パスワード',
            'name' => '氏名',
            'is_active' => '有効/無効',
        ];
    }

    public function messages()
    {
        return [
            'password.regex' => '大文字・小文字を含めた半角英数字記号を、6文字以上60文字以内で入力してください。'
        ];
    }
}
