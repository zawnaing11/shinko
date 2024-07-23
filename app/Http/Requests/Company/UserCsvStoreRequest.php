<?php

namespace App\Http\Requests\Company;

class UserCsvStoreRequest extends UserRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(string $user_id = null): array
    {
        $rules = parent::rules();

        $rules['password'][] = 'required';

        return $rules;
    }
}
