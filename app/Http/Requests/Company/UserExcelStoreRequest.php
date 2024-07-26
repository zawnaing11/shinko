<?php

namespace App\Http\Requests\Company;

class UserExcelStoreRequest extends UserRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(string $user_id = null, string $email = null): array
    {
        $rules = parent::rules();

        $rules['password'] = [
            'required',
            'regex:' . config('const.password_regex'),
            function ($attribute, $value, $fail) use ($email) {
                if ($email == $value) {
                    return $fail('メールアドレスと一致しないようにしてください。');
                }
            },
        ];

        return $rules;
    }
}
