<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Support\Facades\Storage;

class NotificationRequest extends BaseFormRequest
{
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
            'publish_begin_datetime' => [
                'required',
                'date_format:Y-m-d H:i'
            ],
            'publish_end_datetime' => [
                'required',
                'date_format:Y-m-d H:i',
                'after_or_equal:publish_begin_datetime'
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
                    Storage::putFileAs(config('const.notifications.tmp_path'), $this->file('image'), $this->file('image')->getClientOriginalName());
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
            'publish_begin_datetime' => '公開開始日時',
            'publish_end_datetime' => '公開終了日時',
        ];
    }
}
