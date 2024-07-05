<?php

namespace App\Http\Requests\Company;

use App\Http\Requests\BaseFormRequest;

class FileUploadRequest extends BaseFormRequest
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
            'import_file' => 'インポートファイル',
        ];
    }
}
