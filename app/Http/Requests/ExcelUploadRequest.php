<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\Validator;

class ExcelUploadRequest extends BaseFormRequest
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
                'extensions:' . implode(',', config('const.upload_excel_extensions')),
            ]
        ];
    }

    public function attributes()
    {
        return [
            'import_file' => 'EXCEL',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        return back()
            ->with('alert.error', 'EXCELアップロードに失敗しました。');
    }
}
