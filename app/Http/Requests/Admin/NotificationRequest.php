<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;

class NotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'max:' . config('const.default_text_maxlength'),

            ],
            'body' => [
                'nullable',
                'max:' . config('const.default_textarea_maxlength'),
            ],
            'image' => [
                'nullable',
                'image',
                'mimes:' . implode(',', config('const.upload_image_extensions')),
            ],
            'is_image' => [
                'nullable',
            ],
            'is_active' => [
                'required',
                'in:' . implode(',', array_keys(config('const.is_active'))),
            ],
            'publish_date' => [
                'required',
                'date_format:Y-m-d H:i',
            ],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // 画像以外の項目でエラーが発生した場合、画像を一時的に保存
            if ($this->file('image')) {
                if (! $validator->errors()->has('image')) {
                    // 残ったゴミファイルはcronで削除
                    Storage::putFileAs(config('const.notification_tmp_path'), $this->file('image'), $this->file('image')->getClientOriginalName());
                } else {
                    request()->merge(['is_image' => null]);
                }
            }
        });
    }

    public function attributes()
    {
        return [
            'title' => 'タイトル',
            'body' => '本文',
            'image' => '画像',
            'is_image' => '画像',
            'is_active' => '有効/無効',
            'publish_date' => '公開日時',
        ];
    }
}
