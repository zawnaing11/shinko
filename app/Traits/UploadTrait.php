<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait UploadTrait
{
    public function imageUpload($tmp_file_name, $new_file_path, $tmp_file_path, $old_image = null)
    {
        if ($old_image && $old_image != $tmp_file_name) {
            Storage::delete($new_file_path . $old_image);
        }
        if ($tmp_file_name && $old_image != $tmp_file_name) {
            $new_file_name = uniqid() . '.' . pathinfo($tmp_file_name)['extension'];
            Storage::move($tmp_file_path . $tmp_file_name, $new_file_path . $new_file_name);

            return pathinfo($new_file_path . $new_file_name)['basename'];
        }

        return $tmp_file_name;
    }

}
