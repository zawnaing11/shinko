<?php

namespace App\Http\Requests\Company;

class UserUpdateRequest extends UserRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(string $user_id = null): array
    {
        $rules = parent::rules($user_id);

        $rules['password'][] = 'nullable';

        return $rules;
    }
}
