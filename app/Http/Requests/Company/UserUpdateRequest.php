<?php

namespace App\Http\Requests\Company;

class UserUpdateRequest extends UserRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['password'] = [
            'nullable',
            'min:' . config('const.default_password_minlength'),
            'max:' . config('const.default_password_maxlength'),
            'regex:' . config('const.password_regex'),
            function ($attribute, $value, $fail) {
                if ($this->request->get('email') == $value) {
                    return $fail('メールアドレスと一致しないようにしてください。');
                }
            },
        ];

        return $rules;
    }
}
