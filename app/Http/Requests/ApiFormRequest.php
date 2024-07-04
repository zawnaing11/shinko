<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Arr;

class ApiFormRequest extends FormRequest
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

    protected function failedValidation(Validator $validator)
    {
        $response['messages'] = $validator->errors()->toArray();
        logger()->info('failedValidation', $response);
        throw new HttpResponseException(response()->json($response, 400));
    }
}
