<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class BaseFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        logger()->info('prepareForValidation', Arr::except($this->all(), 'password'));
    }

    protected function passedValidation(): void
    {
        logger()->info('passedValidation', $this->safe()->except('password'));
    }
}
