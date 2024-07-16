<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\Validator;

class CsvUploadRequest extends BaseFormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'import_file' => [
                'required',
                'file',
                'mimes:' . implode(',', config('const.upload_csv_extensions')),
            ]
        ];
    }

    public function attributes()
    {
        return [
            'import_file' => 'CSV',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        return back()
            ->with('alert.error', 'CSVアップロードに失敗しました。');
    }
}
